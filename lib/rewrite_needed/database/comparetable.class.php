<?php
class Database_CompareTable extends Database_CompareDB
{


    /**
     * list of table differences
     *
     * @var array
     */
    public $table_difference_list = array();


    /**
     * overlapping tables list
     *
     * @var array
     */
    public $db_selection = null;


    /**
     * total table list
     *
     * @var array
     */
    public $db_total_table_list = array();


    /**
     * table overlap list for detailed comparison
     *
     * @var array
     */
    public $table_overlap_list = array();


    /**
     * table difference list
     *
     * @var array
     */
    public $table_detail_diff_list = array();


    /**
     * contructor
     *
     * @param $db_left object           
     * @param $db_right object           
     *
     * @return void
     */
    public function __construct( Database_Mysql $db_left = null , Database_Mysql $db_right = null , $param = null )
    {

        if ( empty($db_left) || empty($db_right) ) return '';
        
        if ( !empty($param) )
        {
            $this->param = (array) $param;
        }
        
        /*
         * start a superficial comparison so only differences between the amount
         * of tables or databases will be listed [to get an starting point
         */
        self::start_comparison((object) $db_left, (object) $db_right, $this->param);
    
    }


    /**
     * detailed comparison analysis the table fields if there are differences
     *
     * @see Database_CompareDB::detailed_comparison() $param = array
     *      ('database_detailed' => array ('db_name' => array ('table_list'),
     *      .... )
     *     
     * @param $param array           
     *
     * @return boolean
     */
    public function detailed_comparison( $param = array() )
    {
        
        // check if the object is still in the memcache
        if ( $this->_memcached->get('table_detail_diff_list') && !$this->force_reload )
        {
            $this->log->write_log((string) __CLASS__ . '::' . (string) __METHOD__ . " | variables: table_detail_diff_list loaded from memcache", Config::get('log_level_low'));
            $this->table_detail_diff_list = unserialize((string) $this->_memcached->get('table_detail_diff_list'));
            return true;
        }
        
        // check if the databases are connected
        if ( empty($this->db_left) || empty($this->db_left) ) return false;
        
        if ( empty($this->key_left) && empty($this->key_right) )
        {
            /*
             * get the listing keys for the two machines that are going to be
             * compared
             */
            $this->key_right = (string) ($this->db_left->get('_master_host') != $this->db_right->get('_master_host')) ? $this->db_right->get('_master_host') : "right_" . $this->db_right->get('_master_host');
            $this->key_left = (string) ($this->db_left->get('_master_host') != $this->db_right->get('_master_host')) ? $this->db_left->get('_master_host') : "left_" . $this->db_right->get('_master_host');
        }
        
        /*
         * check if there are differences added by parameter or based on the
         * parent analysis
         */
        if ( empty($this->db_total_table_list) && empty($this->param) ) return false;
        
        // go through all databases and compare the tables
        $stack = (array) empty($this->param['database_detailed']) ? (empty($this->table_overlap_list) ? $this->db_total_table_list : $this->table_overlap_list) : $this->param['database_detailed'];
        
        // foreach database
        foreach ( $stack as $db_name => $table_list )
        {
            // go throug all listed tables within the database
            foreach ( $table_list as $table_name => $table )
            {
                if ( isset($table['status']) && $table['status'] !== (int) self::TABLE_STATUS_SCHEDULED ) continue;
                // check columns
                $sql = (string) "SHOW FULL COLUMNS FROM `{$db_name}`.`{$table_name}`";
                $left_result = (array) $this->db_left->fetch_object_list($this->db_left->query($sql));
                $right_result = (array) $this->db_right->fetch_object_list($this->db_right->query($sql));
                unset($sql);
                // go through all results and compare the column definitions
                foreach ( $left_result as $key => $value )
                {
                    /*
                     * in theory all column definitions should be the same so
                     * serialize them and compare the md5 hashes [quicker]
                     */
                    if ( empty($left_result) || empty($right_result) || !isset($left_result[(string) $key]) || !isset($right_result[(string) $key]) || md5(json_encode($left_result[(string) $key])) != md5(json_encode($right_result[(string) $key])) )
                    {
                        // table details on the left side
                        $this->table_detail_diff_list[(string) $db_name][(string) $key] = (!isset($left_result[(string) $key])) ? null : array(
                                                                                                                                            'host' => (string) $this->key_left, 
                                                                                                                                            'result_set' => $left_result[$key], 
                                                                                                                                            'status' => (int) self::DB_STATUS_SCHEDULED
                        );
                        // table details on the right side
                        $this->table_detail_diff_list[(string) $db_name][(string) $key][] = (!isset($left_result[(string) $key])) ? null : array(
                                                                                                                                                'host' => (string) $this->key_right, 
                                                                                                                                                'result_set' => $left_result[$key], 
                                                                                                                                                'status' => (int) self::DB_STATUS_SCHEDULED
                        );
                    
                    }
                }
                unset($key, $value);
            }
            unset($table_name, $table);
        }
        unset($db_name, $table_list, $stack);
        
        // memcached speichern für 15 mins
        $this->log->write_log((string) __CLASS__ . '::' . (string) __METHOD__ . " | variables: table_detail_diff_list written to memcache valid for " . (int) self::DB_MEMCACHE_TIME . " seconds", (int) Config::get('log_level_low'));
        $this->_memcached->set('table_detail_diff_list', (string) serialize((array) $this->table_detail_diff_list), (int) self::DB_MEMCACHE_TIME);
        return true;
    }


    /**
     * (non-PHPdoc)
     *
     * @see Database_CompareDB::replicate()
     */
    public function replicate( $param = array() )
    {

        return false;
    }


    /**
     * starts the comparison -> initializes the parent object as well
     *
     * @see Database_CompareDB::start_comparison()
     *
     * @return boolean
     */
    public function start_comparison( Database_Mysql $db_left = null , Database_Mysql $db_right = null , $param = null )
    {

        if ( (empty($db_left) && empty($this->db_left)) || (empty($db_right) && empty($this->db_right)) ) return false;
        
        // set them as the databases to be compared
        $this->db_left = (!empty($db_left)) ? (object) $db_left : $this->db_left;
        $this->db_right = (!empty($db_right)) ? (object) $db_right : $this->db_right;
        
        if ( !empty($param) )
        {
            $this->param = (array) $param;
        }
        
        // initialize the parent object
        parent::__construct((object) $this->db_left, (object) $this->db_right, $this->param);
        
        // start a comparison between basic databases [show databases]
        return self::structural_comparison($this->param);
    }


    /**
     * sturcutral comparison checks if a table in general is missing
     *
     * @see Database_CompareDB::structur_comparison()
     *
     * @return boolean
     */
    public function structural_comparison( $param = array() )
    {
        
        // check if the object is still in the memcache
        if ( $this->_memcached->get('table_overlap_list') && !$this->force_reload )
        {
            // load all results
            $this->table_overlap_list = unserialize((string) $this->_memcached->get('table_overlap_list'));
            $this->table_difference_list = unserialize((string) $this->_memcached->get('table_difference_list'));
            $this->db_total_table_list = unserialize((string) $this->_memcached->get('db_total_table_list'));
            return true;
        }
        
        // check if the databases to be compared are set
        if ( empty($this->db_left) || empty($this->db_right) ) return false;
        
        $res_left = null;
        $res_right = null;
        
        // assign the param to the selection list
        $this->db_selection = (array) (isset($this->param['database_selection']) ? $this->param['database_selection'] : $this->db_selection);
        
        // fetch all tables within the databases
        $db_list = (array) (empty($this->db_selection) ? $this->db_overlap_list : $this->db_selection);
        
        foreach ( $db_list as $database_name )
        {
            // little hickup -> json calls use objects as arrays [ this kinda really suxX ] 
            if (is_object($database_name))
            {
                $database_name = (array) $database_name;
            }
            
            if ( isset($database_name['status']) && $database_name['status'] != (int) self::DB_STATUS_SCHEDULED ) continue;
            
            // set the status to active
            $this->db_overlap_list[$database_name['name']]['status'] = (int) self::DB_STATUS_ACTIVE;
            // safe to memcached
            $this->_memcached->set('db_overlap_list', (string) serialize($this->db_overlap_list), (int) self::DB_MEMCACHE_TIME);
            $this->log->write_log(__CLASS__ . '::' . __METHOD__ . " | varibales: db_overlap_list updated [ {$database_name['name']} -> active ]  ", Config::get('log_level_low'));
            
            // get a list of all tables within the database
            $sql = (string) "SHOW TABLES FROM `{$database_name['name']}`";
            // query the two databases
            $left_result = (array) $this->db_left->fetch_simple_list($this->db_left->query((string) $sql));
            $right_result = (array) $this->db_right->fetch_simple_list($this->db_right->query((string) $sql));
            // cleanup the query
            unset($sql);
            
            // compare them log the left differences
            foreach ( $left_result as $table_name_left )
            {
                $this->db_total_table_list[(string) $database_name['name']][(string) $table_name_left] = (string) $table_name_left;
                // ignore table check
                if ( in_array(strtolower((string) $table_name_left), (array) $this->_ignore_table_list) ) continue;
                
                if ( in_array((string) $table_name_left, (array) $right_result) )
                {
                    $this->table_overlap_list[(string) $database_name['name']][(string) $table_name_left] = array(
                                                                                                                'name' => (string) $table_name_left, 
                                                                                                                'status' => self::TABLE_STATUS_SCHEDULED
                    );
                    continue;
                }
                $this->table_difference_list[(string) $database_name['name']][(string) $table_name_left] = array(
                                                                                                                'name' => (string) $table_name_left, 
                                                                                                                'status' => (int) self::TABLE_STATUS_SCHEDULED, 
                                                                                                                'host' => (string) $this->key_left
                );
            }
            unset($table_name_left);
            
            // compare them log the right differences
            foreach ( $right_result as $table_name_right )
            {
                $this->db_total_table_list[(string) $database_name['name']][(string) $table_name_right] = (string) $table_name_right;
                // ignore list check
                if ( in_array(strtolower((string) $table_name_right), (array) $this->_ignore_table_list) ) continue;
                
                // list the overlapping tables for comparison
                if ( in_array((string) $table_name_right, (array) $left_result) ) continue;
                
                $this->table_difference_list[(string) $database_name['name']][(string) $table_name_right] = array(
                                                                                                                'name' => (string) $table_name_right, 
                                                                                                                'status' => (int) self::TABLE_STATUS_SCHEDULED, 
                                                                                                                'host' => (string) $this->key_left
                );
            }
            // cleanup
            unset($table_name_right, $left_result, $right_result);
            // set status to done
            $this->db_overlap_list[$database_name['name']]['status'] = self::DB_STATUS_DONE;
            // write it to the memcache
            $this->_memcached->set('db_overlap_list', serialize((array) $this->db_overlap_list), (int) self::DB_MEMCACHE_TIME);
            // log it
            $this->log->write_log(__CLASS__ . '::' . __METHOD__ . " | varibales: db_overlap_list updated [ {$database_name['name']} -> done ]  ", Config::get('log_level_low'));
        }
        // log it
        $this->log->write_log(__CLASS__ . '::' . __METHOD__ . " | variables: table_overlap_list, table_difference_list, db_total_table_list written to memcache" . date('Y-m-d H:i:s') . ' - valid for ' . self::DB_MEMCACHE_TIME . ' seconds', Config::get('log_level_low'));
        // saving within memcached
        $this->_memcached->set('table_overlap_list', serialize((array) $this->table_overlap_list), (int) self::DB_MEMCACHE_TIME);
        $this->_memcached->set('table_difference_list', serialize((array) $this->table_difference_list), (int) self::DB_MEMCACHE_TIME);
        $this->_memcached->set('db_total_table_list', serialize((array) $this->db_total_table_list), (int) self::DB_MEMCACHE_TIME);
        
        return true;
    }

}
?>