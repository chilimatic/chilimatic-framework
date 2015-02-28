<?php
class Database_CompareDB implements Database_Compare
{


    /**
     * first database object
     *
     * @var object
     */
    public $db_left = null;


    /**
     * right datbase object
     *
     * @var object
     */
    public $db_right = null;


    /**
     * list of database differences
     * of the first database
     *
     * @var array
     */
    public $db_diff_list_left = array();


    /**
     * list of database differences
     * of the right database
     *
     * @var array
     */
    public $db_diff_list_right = array();


    /**
     * database configuration differences
     *
     * @var array
     */
    public $db_detail_diff_list = null;


    /**
     * the overlapping databases
     *
     * @var array
     */
    public $db_overlap_list = array();


    /**
     * the list of all databases in both tables
     *
     * @var array
     */
    public $db_complete_list = array();


    /**
     * force parameter for the detailed comparison
     *
     * @var boolean
     */
    public $force_compare_all = false;


    /**
     * list of tables that should be ignored
     *
     * @var array
     */
    protected $_ignore_table_list = array();


    /**
     * list of databases that should be ignored
     *
     * @var unknown_type
     */
    protected $_ignore_database_list = array();


    /**
     * process status
     *
     * @var object
     */
    public $system = null;


    /**
     * memcached object
     *
     * @var object
     */
    protected $_memcached = null;


    /**
     * forces reload even with cached results
     *
     * @var boolean
     */
    public $force_reload = false;


    /**
     * log object
     *
     * @var object
     */
    public $log = null;


    /**
     * mongo database
     *
     * @var object
     */
    public $mongo_db = null;


    /**
     * parameters array from the outside
     *
     * @var array
     */
    public $param = array();


    /**
     * constructor initializes the start comparison method
     *
     * @param $db_left object           
     * @param $db_right object           
     */
    public function __construct( Database_Mysql $db_left = null , Database_Mysql $db_right = null , $param = null )
    {
        
        // for process control
        $this->system = new System_SelfInfo();
        
        /*
         * own memcache object inside this is for the child classes the detailed
         * comparison of 200 fields in array shouldnt be a problem in realtime
         */
        $this->_memcached = new Memcached();
        $this->_memcached->addServer((string) Config::get('memcached_server'), (string) Config::get('memcached_port'));
        
        if ( !empty($GLOBALS['mongo']) )
        {
            // mongo database for the current database comparison
            $this->mongo_db = $GLOBALS['mongo']->selectDB((string) self::MONGO_DATABASE);
            // authenticate
            $auth = $this->mongo_db->authenticate((string) Config::get('mongo_db_user'), (string) Config::get('mongo_db_password'));
            // check for auth
            if ( $auth['ok'] != 1 )
            {
                $this->mongo_db = false;
            }
        }
        
        // ignore lists are loaded from config
        $this->_ignore_table_list = Config::get('ignore_table_list');
        $this->_ignore_database_list = Config::get('ignore_database_list');
        
        $this->log = new Log_Process(self::PROCESS_LOG_FILE);
        
        if ( empty($db_left) || empty($db_right) ) return;
        
        /*
         * get the listing keys for the two machines that are going to be
         * compared
         */
        $this->key_right = (string) ($this->db_left->get('_master_host') != $this->db_right->get('_master_host')) ? $this->db_right->get('_master_host') : "right_" . $this->db_right->get('_master_host');
        $this->key_left = (string) ($this->db_left->get('_master_host') != $this->db_right->get('_master_host')) ? $this->db_left->get('_master_host') : "left_" . $this->db_right->get('_master_host');
        
        if ( !empty($param) )
        {
            $this->param = (array) $param;
        }
        
        /*
         * start a superficial comparison so only differences between the amount
         * of tables or databases will be listed [to get an starting point]
         */
        self::start_comparison((object) $db_left, (object) $db_right, $this->param);
    
    }


    /**
     * checks the database details for differences
     *
     * @see Database_Compare::detailed_comparison()
     *
     * @param $param array           
     *
     * @return boolean
     */
    public function detailed_comparison( $param = array() )
    {
        
        // first check if the database objects exist
        if ( empty($this->db_left) || empty($this->db_right) ) return false;
        
        if ( empty($this->key_left) && empty($this->key_right) )
        {
            /*
             * get the listing keys for the two machines that are going to be
             * compared
             */
            $this->key_right = (string) ($this->db_left->get('_master_host') != $this->db_right->get('_master_host')) ? $this->db_right->get('_master_host') : "right_" . $this->db_right->get('_master_host');
            $this->key_left = (string) ($this->db_left->get('_master_host') != $this->db_right->get('_master_host')) ? $this->db_left->get('_master_host') : "left_" . $this->db_right->get('_master_host');
        }
        
        if ( !empty($param) )
        {
            $this->param = (array) $param;
        }
        
        /*
         * then check if parameters have been given otherwise set an empty param
         * array
         */
        if ( empty($this->param) || !is_array($this->param) )
        {
            $param = array();
        }
        
        // if there has been set no mode set it to default [for php warnings]
        if ( empty($this->param['mode']) )
        {
            $this->param['mode'] = 'default';
        }
        
        switch ( $this->param['mode'] )
        {
            default :
                $db_detail_left = (object) $this->db_left->get_database_detail();
                $db_detail_right = (object) $this->db_right->get_database_detail();
                
                foreach ( $db_detail_left as $key => $value )
                {
                    if ( !isset($db_detail_right->$key) || $db_detail_right->$key != $value )
                    {
                        
                        $this->db_detail_diff_list[(string) $key][(string) $this->key_right] = (string) (property_exists($db_detail_right, $key)) ? $db_detail_right->$key : '';
                        $this->db_detail_diff_list[(string) $key][(string) $this->key_left] = (string) $value;
                    }
                }
                break;
        }
        return true;
    }


    /**
     * (non-PHPdoc)
     *
     * @see Database_Compare::replicate()
     */
    public function replicate( $param = array() )
    {

        return true;
    }


    /**
     * starts the comparison process
     *
     * @param $db_left object           
     * @param $db_right object           
     * @param $param array           
     *
     * @see Database_Compare::start_comparison()
     *
     * @return boolean
     */
    public function start_comparison( Database_Mysql $db_left = null , Database_Mysql $db_right = null , $param = null )
    {

        if ( (empty($db_left) && empty($this->db_left)) || (empty($db_right) && empty($this->db_right)) ) return false;
        
        // set them as the databases to be compared
        $this->db_left = (!empty($db_left)) ? (object) $db_left : $this->db_left;
        $this->db_right = (!empty($db_right)) ? (object) $db_right : $this->db_right;
        
        /*
         * get the listing keys for the two machines that are going to be
         * compared
         */
        $this->key_right = (string) ($this->db_left->get('_master_host') != $this->db_right->get('_master_host')) ? $this->db_right->get('_master_host') : "right_" . $this->db_right->get('_master_host');
        $this->key_left = (string) ($this->db_left->get('_master_host') != $this->db_right->get('_master_host')) ? $this->db_left->get('_master_host') : "left_" . $this->db_right->get('_master_host');
        
        if ( !empty($param) )
        {
            $this->param = (array) $param;
        }
        
        // start a comparison between basic databases [show databases]
        return self::structural_comparison($this->param);
    }


    /**
     * superficial comparison
     * (non-PHPdoc)
     *
     * @param $param array           
     *
     * @see Database_Compare::structural_comparison()
     *
     * @return boolean
     */
    public function structural_comparison( $param = array() )
    {
        
        // check if the last structural comparison still exists
        if ( $this->_memcached->get('db_overlap_list') && !$this->force_reload )
        {
            $this->log->write_log(__CLASS__ . '::' . __METHOD__ . " | Use Memcached Result of db_overlap_list, db_diff_list_right, db_diff_list_left", Config::get('log_level_low'));
            // safe open from memcached for procedure
            $this->db_diff_list_left = (array) unserialize((string) $this->_memcached->get('db_diff_list_left'));
            $this->db_diff_list_right = (array) unserialize((string) $this->_memcached->get('db_diff_list_right'));
            $this->db_overlap_list = (array) unserialize((string) $this->_memcached->get('db_overlap_list'));
            
            return true;
        }
        
        if ( empty($this->db_left) || empty($this->db_right) ) return false;
        
        if ( !empty($param) )
        {
            $this->param = (array) $param;
        }
        
        // list all available databases
        $sql = (string) 'SHOW DATABASES';
        // query the two database servers for database differences
        $res_left = $this->db_left->query($sql);
        $res_right = $this->db_right->query($sql);
        
        if ( empty($res_left) || empty($res_right) )
        {
            $this->db_left->free($res_left);
            $this->db_left->free($res_right);
            return false;
        }
        
        // fetch the left result
        $result_left = (array) $this->db_left->fetch_assoc_list($res_left, (string) 'Database');
        $result_right = (array) $this->db_right->fetch_assoc_list($res_right, (string) 'Database');
        
        if ( empty($result_left) && empty($result_right) )
        {
            return false;
        }
        
        // clean the mysql sets
        $this->db_left->free($res_left);
        $this->db_right->free($res_right);
        // unset them
        unset($res_left, $res_right);
        
        // reset differences
        $this->db_diff_list_left = array();
        $this->db_diff_list_right = array();
        $this->db_complete_list = array();
        $this->log->write_log(__CLASS__ . '::' . __METHOD__ . " | varibales: db_overlap_list, db_diff_list_right, db_diff_list_left cleared", Config::get('log_level_low'));
        // check if there is a left result
        if ( !empty($result_left) )
        {
            // check differences between the left database and the mirror
            foreach ( $result_left as $name => $database )
            {
                $this->db_complete_list[(string) $name] = (string) $database['Database'];
                // ignore list check
                if ( in_array(strtolower($name), $this->_ignore_database_list) ) continue;
                
                // get the overlapping entries for lateron system based
                // comparison
                if ( in_array((array) $database, (array) $result_right) )
                {
                    $this->db_overlap_list[$database['Database']] = array(
                                                                        'name' => (string) $database['Database'], 
                                                                        'status' => (int) self::DB_STATUS_SCHEDULED
                    );
                    $this->log->write_log((string) __CLASS__ . '::' . (string) __METHOD__ . " | varibales: db_overlap_list updated [ {$database['Database']} -> scheduled ]  ", Config::get('log_level_low'));
                    continue;
                }
                // add the differences
                $this->db_diff_list_left[(string) $name] = array(
                                                                'name' => (string) $database['Database'], 
                                                                'status' => (int) self::DB_STATUS_SCHEDULED
                );
                $this->log->write_log((string) __CLASS__ . '::' . (string) __METHOD__ . " | varibales: db_diff_list_left updated [ {$database['Database']} -> scheduled ]  ", Config::get('log_level_low'));
            }
            unset($name, $database);
        }
        
        // if the right result is empty
        if ( !empty($result_right) )
        {
            
            // check the other way around
            foreach ( $result_right as $name => $database )
            {
                // add the tables that are not within the left database
                $this->db_complete_list[(string) $name] = (string) $database['Database'];
                
                // ignore list check
                if ( in_array(strtolower((string) $name), (array) $this->_ignore_database_list) ) continue;
                // skip the overlapping ones because the overlaps should be
                // covered by the last loop
                if ( in_array((array) $database, (array) $result_left) ) continue;
                // add the differences
                $this->db_diff_list_right[(string) $name] = array(
                                                                'name' => (string) $database['Database'], 
                                                                'status' => (int) self::DB_STATUS_SCHEDULED
                );
                $this->log->write_log((string) __CLASS__ . '::' . __METHOD__ . " | varibales: db_diff_list_right updated [ {$database['Database']} -> scheduled ]  ", Config::get('log_level_low'));
            }
        }
        unset($name, $database, $result_left, $result_right);
        
        // safe to memcached for procedure
        $this->log->write_log((string) __CLASS__ . '::' . __METHOD__ . " | variables: db_overlap_list, db_diff_list_right, db_diff_list_left written to memcache valid for " . self::DB_MEMCACHE_TIME . "seconds", Config::get('log_level_low'));
        
        $this->_memcached->set('db_overlap_list', (string) serialize($this->db_overlap_list), (int) self::DB_MEMCACHE_TIME);
        $this->_memcached->set('db_diff_list_right', (string) serialize($this->db_diff_list_right), (int) self::DB_MEMCACHE_TIME);
        $this->_memcached->set('db_diff_list_left', (string) serialize($this->db_diff_list_left), (int) self::DB_MEMCACHE_TIME);
        
        return true;
    }

}
?>