<?php
class Database_CompareContent extends Database_CompareTable
{


    /**
     * list of the content differences between the
     * selected tables or with no specific values of the whole database
     *
     * @var array
     */
    public $content_difference_list = array();


    /**
     * selection given via param array
     *
     * @var array
     */
    public $content_selection = array();


    /**
     * lists the opposit rows
     *
     * @var array
     */
    public $content_detail_diff = array();


    /**
     * count of the current table
     *
     * @var int
     */
    private $current_table_count_left = 0;


    /**
     * count of the current table
     *
     * @var int
     */
    private $current_table_count_right = 0;


    /**
     * positon [for limit <pos>,<iteration_limit>]
     *
     * @var int
     */
    private $current_table_position = 0;


    /**
     * limit for the iterations
     *
     * @var int
     */
    private $iteration_limit = 1000;


    /**
     * represents the current fieldset of the detailed
     * compared tables
     *
     * @var array
     */
    private $current_field_set = null;


    /**
     * primary key of the current table
     *
     * @var string
     */
    private $primary_key = null;


    /**
     * contructor
     *
     * @param $db_left object           
     * @param $db_right object           
     */
    public function __construct( Database_Mysql $db_left = null , Database_Mysql $db_right = null , $param = null )
    {

        if ( empty($db_left) || empty($db_right) ) return '';
        
        if ( !empty($param) )
        {
            $this->param = (array) $param;
        }
        
        self::start_comparison((object) $db_left, (object) $db_right, $this->param);
    
    }


    /**
     * filters some properties for comparison
     *
     * @return boolean
     */
    private function analyse_table()
    {

        if ( empty($this->current_field_set) )
        {
            return false;
        }
        
        $primary_key = null;
        
        foreach ( $this->current_field_set as $row => $field )
        {
            if ( isset($field->Key) && $field->Key == "PRI" )
            {
                $primary_key[] = (string) $field->Field;
            }
        }
        
        $this->primary_key = $primary_key;
        
        return true;
    }


    /**
     * (non-PHPdoc)
     *
     * @see Database_CompareTable::detailed_comparison() param to work in this
     *      branch should look like follows
     *     
     *      $param = array ('content_selection' => array ('db_name' => array
     *      ('table_list'), .... )
     *     
     */
    public function detailed_comparison( $param = null )
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
        
        // param external
        $this->content_selection = (array) (isset($this->param['content_selection']) ? $this->param['content_selection'] : $this->content_selection);
        
        // get all tables within the database or selection
        $stack = (array) (empty($this->content_selection)) ? (empty($this->table_overlap_list) ? $this->db_total_table_list : $this->table_overlap_list) : $this->content_selection;
        
        if ( !$this->mongo_db ) return false;
        
        // getting the Mongo Collection [like a mysl table] for this array field
        $collection = new MongoCollection($this->mongo_db, 'content_detail_diff');
        
        /*
         * now it get interessting question about put them into 10000 rows
         * packages seams to make sense not now but maybe we grow much more
         * bigger.
         */
        foreach ( $stack as $db_name => $table_list )
        {
            // check for the ignore list
            if ( in_array(strtolower($db_name), (array) $this->_ignore_database_list) ) continue;
            
            $total_start_time = microtime(true);
            
            foreach ( $table_list as $table_name => $table )
            {
                $content_detail_diff = null;
                // check for the ignore list
                if ( in_array(strtolower($table_name), (array) $this->_ignore_table_list) ) continue;
                
                // check if the table has been queued
                if ( isset($table[(string) 'status']) && ($table[(string) 'status'] !== self::CONTENT_STATUS_SCHEDULED && $table[(string) 'status'] !== self::CONTENT_STATUS_ACTIVE) ) continue;
                
                // set active in the stack
                $this->table_overlap_list[(string) $db_name][(string) $table_name][(string) 'status'] = (int) self::CONTENT_STATUS_ACTIVE;
                $this->_memcached->set('table_overlap_list', serialize((array) $this->table_overlap_list), (int) self::DB_MEMCACHE_TIME);
                $this->log->write_log((string) __CLASS__ . '::' . (string) __METHOD__ . " | variable: table_overlap_list [$db_name][$table_name] updated => active ", Config::get('log_level_low'));
                
                // refresh memory info
                $this->system->memory_info();
                if ( $this->system->current_mem_use / 1024 / 1024 > 1024 )
                {
                    $this->_memcached->set('final_status', "Exceeded 1024MB -> break current loop '{$table_name}'", self::DB_MEMCACHE_TIME);
                    $this->log->write_log((string) __CLASS__ . '::' . (string) __METHOD__ . " | Exceeded 1024MB -> break current loop '{$table_name}'", Config::get('log_level_low'));
                    
                    // stopp the process
                    die((string) __CLASS__ . '::' . (string) __METHOD__ . " | Exceeded 1024MB -> break current loop '{$table_name}'");
                }
                
                // set the array
                $content_detail_diff = array();
                
                // first get the count of the current tables in both databases
                $sql = (string) "SELECT COUNT(*) FROM `{$db_name}`.`{$table_name}`";
                $this->current_table_count_left = (int) $this->db_left->fetch_string($this->db_left->query((string) $sql));
                $this->current_table_count_right = (int) $this->db_right->fetch_string($this->db_right->query((string) $sql));
                
                // get the current field set <-> KEY comparison
                $sql = (string) "SHOW FULL COLUMNS FROM `{$db_name}`.`{$table_name}`";
                if ( ($this->current_field_set = (array) $this->db_left->fetch_object_list($res = $this->db_left->query((string) $sql))) === false )
                {
                    $this->current_field_set = (array) $this->db_left->fetch_object_list($res = $this->db_left->query((string) $sql));
                }
                
                unset($res);
                
                // get the current keys for the table
                $this->analyse_table();
                
                // set the default positon again to zero
                $this->current_table_position = (int) 0;
                
                $order_key_sql = (string) '';
                
                if ( !empty($this->primary_key) )
                {
                    if ( is_array($this->primary_key) )
                    {
                        $key_sequence = (string) implode(',', $this->primary_key);
                        $order_key_sql = (string) " ORDER BY $key_sequence";
                    }
                    else
                    {
                        $key_sequence = (string) $this->primary_key;
                        $order_key_sql = (string) " ORDER BY $this->primary_key";
                    }
                }
                
                $start_time = microtime(true);
                
                $load_pos = 0;
                
                // check if it'S going to resume the last iteration of the table
                if ( $this->_memcached->get("current_table_position-$table_name") )
                {
                    $load_pos = (int) $this->_memcached->get("current_table_position-$table_name");
                }
                
                /*
                 * try not to exceed the max memory size
                 */
                for ( $this->current_table_position = $load_pos ; ($this->current_table_position < $this->current_table_count_left && $this->current_table_position < $this->current_table_count_right) ; $this->current_table_position = $this->current_table_position + $this->iteration_limit )
                {
                    // select the first package in both databases
                    $sql = (string) "SELECT * FROM `{$db_name}`.`{$table_name}` {$order_key_sql} LIMIT {$this->current_table_position},{$this->iteration_limit}";
                    $left_result = (array) $this->db_left->fetch_object_list($res_left = $this->db_left->query((string) $sql), $this->primary_key);
                    $right_result = (array) $this->db_right->fetch_object_list($res_right = $this->db_right->query((string) $sql), $this->primary_key);
                    
                    // cleanup
                    unset($res_right, $res_left);
                    
                    if ( empty($left_result) ) break;
                    
                    foreach ( $left_result as $key => $row )
                    {
                        // check for memory overflow
                        if ( $this->system->current_mem_use / 1024 / 1024 > 1024 )
                        {
                            $this->log->write_log((string) __CLASS__ . '::' . (string) __METHOD__ . " | Exceeded 1024MB -> break current loop $this->table_name {$this->current_table_positon}", Config::get('log_level_low'));
                            // stopp the process
                            die((string) __CLASS__ . '::' . (string) __METHOD__ . " | Exceeded 1024MB -> break current loop $this->table_name {$this->current_table_positon}");
                        }
                        
                        // md5 comparison based on the same key
                        if ( empty($left_result) || empty($right_result) || !isset($left_result[(string) $key]) || !isset($right_result[(string) $key]) || md5(json_encode($left_result[(string) $key])) != md5(json_encode($right_result[(string) $key])) )
                        {
                            $content_detail_diff[(string) $db_name][(string) $table_name][(string) $this->key_left][] = array(
                                                                                                                            'diff' => (isset($left_result[(string) $key]) ? $left_result[(string) $key] : null), 
                                                                                                                            'status' => self::CONTENT_STATUS_SCHEDULED
                            );
                            $content_detail_diff[(string) $db_name][(string) $table_name][(string) $this->key_right][] = array(
                                                                                                                            'diff' => (isset($right_result[(string) $key]) ? $right_result[(string) $key] : null), 
                                                                                                                            'status' => self::CONTENT_STATUS_SCHEDULED
                            );
                        }
                        
                        // save the current table position for further
                        // processing if it breaks
                        $this->_memcached->set("current_table_position-$table_name", (int) $this->current_table_position, (int) self::DB_MEMCACHE_TIME);
                        
                        /*
                         * unset the compared row -> for the garbage collector
                         * only leaves the ones that are not similar on the
                         * right side
                         */
                        $left_result[(string) $key] = null;
                        $right_result[(string) $key] = null;
                        unset($left_result[(string) $key], $right_result[(string) $key]);
                    }
                    // cleanup memcached
                    $this->_memcached->delete("current_table_position-$table_name");
                    /*
                     * work through the remaining entries on the right side
                     */
                    if ( !empty($right_result) )
                    {
                        foreach ( $right_result as $key => $row )
                        {
                            $content_detail_diff[(string) $db_name][(string) $table_name][(string) $this->key_left][] = array(
                                                                                                                            'diff' => (isset($left_result[(string) $key]) ? $left_result[(string) $key] : null), 
                                                                                                                            'status' => self::CONTENT_STATUS_SCHEDULED
                            );
                            $content_detail_diff[(string) $db_name][(string) $table_name][(string) $this->key_right][] = array(
                                                                                                                            'diff' => (isset($right_result[(string) $key]) ? $right_result[(string) $key] : null), 
                                                                                                                            'status' => self::CONTENT_STATUS_SCHEDULED
                            );
                        }
                    }
                    
                    if ( !empty($content_detail_diff) )
                    {
                        // write to couch db
                        $collection->insert((array) $content_detail_diff, array(
                                                                                "safe" => true
                        ));
                    }
                    
                    unset($left_result, $right_result, $key, $row, $sql);
                    $this->log->write_log((string) __CLASS__ . '::' . (string) __METHOD__ . " | $table_name {$this->current_table_position} || [iteration_limit:$this->iteration_limit start_pos:$this->current_table_position]", Config::get('log_level_low'));
                }
                
                $end_time = microtime(true);
                $total_time = $end_time - $start_time;
                $this->log->write_log((string) __CLASS__ . '::' . (string) __METHOD__ . " | $db_name || $table_name [$total_time ms] done", Config::get('log_level_low'));
                
                // set done in the stack
                $this->table_overlap_list[(string) $db_name][(string) $table_name]['status'] = (int) self::CONTENT_STATUS_DONE;
                $this->log->write_log((string) __CLASS__ . '::' . (string) __METHOD__ . " | variable: table_overlap_list [$db_name][$table_name] updated => done ", Config::get('log_level_low'));
                
                $this->_memcached->set('table_overlap_list', (string) serialize((array) $this->table_overlap_list), (int) self::DB_MEMCACHE_TIME);
                
                // cleanup the foreach
                unset($left_result, $right_result, $key, $row, $order_key_sql, $start_time, $end_time, $total_time, $key_sequence);
                
                // reset the current positon of the table [lower memory use]
                $content_detail_diff = array();
            }
            
            $total_end_time = microtime(true);
            $total_time = $total_end_time - $total_start_time;
            $this->log->write_log((string) __CLASS__ . '::' . (string) __METHOD__ . " | total time whole detailed comparison: $total_time", Config::get('log_level_low'));
        }
        // cleanup
        unset($stack, $start_time, $end_time);
        $this->log->write_log((string) __CLASS__ . '::' . (string) __METHOD__ . " | written to memcache" . date('Y-m-d H:i:s') . ' - valid for ' . self::DB_MEMCACHE_TIME . ' seconds', Config::get('log_level_low'));
        // return true
        return true;
    }


    /**
     * (non-PHPdoc)
     *
     * @see Database_CompareTable::replicate()
     */
    public function replicate( $param = null )
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
        
        // start a comparison between table content like entries
        return self::structural_comparison($this->param);
    }


    /**
     * structural comparison in this case it compares the table counts
     * so if there are differences between the counts it will add the tables to
     * the
     * superficial list
     *
     * @see Database_CompareTable::structural_comparison()
     *
     * @return boolean
     */
    public function structural_comparison( $param = array() )
    {
        
        // check if the object is still in the memcache
        if ( $this->_memcached->get('content_difference_list') && !$this->force_reload )
        {
            $this->content_difference_list = unserialize((string) $this->_memcached->get('content_difference_list'));
            // logging
            $this->log->write_log((string) __CLASS__ . '::' . (string) __METHOD__ . " | Use Memcached Result of content_difference_list", Config::get('log_level_low'));
            return true;
        }
        
        // check if the databases to be compared are set
        if ( empty($this->db_left) || empty($this->db_right) ) return false;
        
        if ( !empty($param) )
        {
            $this->param = (array) $param;
        }
        
        // set the res to null for the beginning
        $res_left = null;
        $res_right = null;
        
        // param external
        $this->content_selection = (isset($this->param['table_selection']) ? $this->param['table_selection'] : $this->content_selection);
        
        // get all tables within the database or selection
        $stack = (empty($this->content_selection)) ? $this->table_overlap_list : $this->content_selection;
        // go through all database entries
        foreach ( $stack as $db_name => $table_list )
        {
            // go throug all tables
            foreach ( $table_list as $table_name => $table )
            {
                // check for queueing
                if ( isset($table['status']) && $table['status'] !== self::TABLE_STATUS_SCHEDULED ) continue;
                
                // mark the current entry as aktiv
                $this->table_overlap_list[(string) $db_name][(string) $table_name][(string) 'status'] = self::TABLE_STATUS_ACTIVE;
                $this->_memcached->set('table_overlap_list', serialize((array) $this->table_overlap_list), self::DB_MEMCACHE_TIME);
                $this->log->write_log((string) __CLASS__ . '::' . (string) __METHOD__ . " | table_overlap_list $table_name Active", Config::get('log_level_low'));
                // ignore table check
                if ( in_array(strtolower($table_name), (array) $this->_ignore_table_list) ) continue;
                
                // first check superficial for count differences
                $sql = (string) "SELECT COUNT(*) FROM `{$db_name}`.`{$table_name}`";
                $left_result = (string) $this->db_left->fetch_string($this->db_left->query((string) $sql));
                $right_result = (string) $this->db_right->fetch_string($this->db_right->query((string) $sql));
                
                if ( $left_result != $right_result )
                {
                    $this->content_difference_list[(string) $db_name][(string) $table_name][(string) 'count'] = array(
                                                                                                                    (string) $this->key_left => (int) $left_result, 
                                                                                                                    (string) $this->key_right => (int) $right_result
                    );
                    $this->content_difference_list[(string) $db_name][(string) $table_name][(string) 'status'] = self::CONTENT_STATUS_SCHEDULED;
                }
                
                // then analyse the table and get the primary key
                $sql = (string) "SHOW FULL COLUMNS FROM `{$db_name}`.`{$table_name}`";
                
                // get the field set description of the table
                $this->current_field_set = (array) $this->db_left->fetch_object_list($this->db_left->query((string) $sql));
                
                // if it doesn't exists in the left database
                if ( empty($this->current_field_set) )
                {
                    $this->current_field_set = $this->db_right->fetch_object_list($this->db_left->query((string) $sql));
                }
                
                // if it doesn't exist at all skip it [would be lunatic but just
                // to be safe]
                if ( !$this->current_field_set ) continue;
                
                foreach ( (array) $this->current_field_set as $field )
                {
                    // if the primary key is numeric [int]
                    if ( isset($field->Key) && $field->Key == "PRI" && (strpos(strtolower($field->Type), 'int') !== false) )
                    {
                        // check the max value of the field
                        $sql = (string) "SELECT MAX({$field->Field}) FROM `{$db_name}`.`{$table_name}`";
                        $left_result = (string) $this->db_left->fetch_string($this->db_left->query($sql));
                        $right_result = (string) $this->db_right->fetch_string($this->db_right->query($sql));
                        // if the max ids don't work out add it to the list
                        if ( $left_result != $right_result )
                        {
                            $this->content_difference_list[(string) $db_name][(string) $table_name][(string) 'max'] = array(
                                                                                                                            (string) $this->key_left => (int) $left_result, 
                                                                                                                            (string) $this->key_right => (int) $right_result
                            );
                        }
                        
                        /*
                         * once the key was compared exit <-> this is based on
                         * the assumption that if one key missmatches a combined
                         * primary will as well [not really save but this is
                         * just superficial comparison] mark the current entry
                         * as ready for detailed Content comparison
                         */
                        
                        $this->table_overlap_list[(string) $db_name][(string) $table_name][(string) 'status'] = (int) self::CONTENT_STATUS_SCHEDULED;
                        $this->log->write_log((string) __CLASS__ . '::' . (string) __METHOD__ . " | table_overlap_list $table_name scheduled for content", Config::get('log_level_low'));
                        $this->_memcached->set('table_overlap_list', serialize((array) $this->table_overlap_list), (int) self::DB_MEMCACHE_TIME);
                        break;
                    }
                }
                $this->table_overlap_list[(string) $db_name][(string) $table_name][(string) 'status'] = (int) self::CONTENT_STATUS_SCHEDULED;
                $this->log->write_log((string) __METHOD__ . "table_overlap_list $table_name scheduled for content", Config::get('log_level_low'));
                $this->_memcached->set('table_overlap_list', serialize((array) $this->table_overlap_list), (int) self::DB_MEMCACHE_TIME);
            }
        }
        $this->log->write_log((string) __CLASS__ . '::' . (string) __METHOD__ . (string) " | content_difference_list written to memcache" . date('Y-m-d H:i:s') . ' - valid for ' . self::DB_MEMCACHE_TIME . ' seconds', Config::get('log_level_low'));
        $this->_memcached->set('content_difference_list', serialize((array) $this->content_difference_list), (int) self::DB_MEMCACHE_TIME);
        return true;
    }

}
?>