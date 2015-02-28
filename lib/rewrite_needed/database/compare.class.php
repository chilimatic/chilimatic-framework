<?php
Interface Database_Compare
{


    /**
     * mongodb collection
     *
     * @var string
     */
    CONST MONGO_DATABASE = 'database_comparison';


    /**
     * Constant for process comparison file
     *
     * @var string
     */
    CONST PROCESS_LOG_FILE = 'compare_process';


    /**
     * Constant for scheduled content comparison
     *
     * @var int
     */
    CONST CONTENT_STATUS_SCHEDULED = 7;


    /**
     * Constant for current active content comparison
     *
     * @var int
     */
    CONST CONTENT_STATUS_ACTIVE = 8;


    /**
     * Constant for done content comparison
     *
     * @var int
     */
    CONST CONTENT_STATUS_DONE = 9;


    /**
     * Constant for scheduled table comparison
     *
     * @var int
     */
    CONST TABLE_STATUS_SCHEDULED = 4;


    /**
     * Constant for the current active table comparison
     *
     * @var int
     */
    CONST TABLE_STATUS_ACTIVE = 5;


    /**
     * Constant for the done table comparison
     *
     * @var int
     */
    CONST TABLE_STATUS_DONE = 6;


    /**
     * Constant for database scheduled status
     *
     * @var int
     */
    CONST DB_STATUS_SCHEDULED = 1;


    /**
     * Constant for database active status
     *
     * @var int
     */
    CONST DB_STATUS_ACTIVE = 2;


    /**
     * Constant for database done status
     *
     * @var int
     */
    CONST DB_STATUS_DONE = 3;


    /**
     * Constant for database error status
     *
     * @var int
     */
    CONST DB_STATUS_ERROR = -1;


    /**
     * Constant for memcached timeout
     * in sekunden -> 900 / 60 = 15 min
     *
     * @var int
     */
    CONST DB_MEMCACHE_TIME = 900;


    /**
     * constructor
     *
     * @param $db_main object           
     * @param $db_second object           
     */
    public function __construct( Database_Mysql $db_main = null , Database_Mysql $db_second = null , $param = null );


    /**
     * gets a more detailed analysis based on the selection
     *
     * @param $param array           
     *
     * @return boolean
     */
    public function detailed_comparison( $param = array() );


    /**
     * Replicate Datatabase/table/field/entries
     *
     * @param $param array           
     *
     * @return boolean
     */
    public function replicate( $param = array() );


    /**
     * starts the comparison process (init)
     *
     * @param $db_left object           
     * @param $db_right object           
     * @param $param array           
     *
     * @return boolean
     */
    public function start_comparison( Database_Mysql $db_left = null , Database_Mysql $db_right = null , $param = null );


    /**
     * compares superficial the structur
     *
     * @param $param array           
     *
     * @return boolean
     */
    public function structural_comparison( $param = array() );

}

?>