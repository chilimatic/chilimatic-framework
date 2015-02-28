<?php
namespace chilimatic\lib\log;

/**
 * Interface Log
 *
 * @package chilimatic\log
 */
Interface Log
{


    /**
     * Default Log format
     *
     * @var string
     */
    CONST LOG_DATE_FORMAT = 'Y-m-d H:i:s';


    /**
     * default log date filename
     * 
     * @var string
     */
    CONST LOG_DATE_FILE = 'Y-m-d';


    /**
     * constructor
     *
     * @param $log_path string           
     * @param $file_name string           
     */
    public function __construct( $file_name = '' , $log_path = '' );


    /**
     * write to the log file
     *
     * @param $msg string
     * @param int $log_level
     *
     * @return
     */
    public function write_log( $msg = '' , $log_level = 0 );


    /**
     * destructor calls the destructor of the file object
     */
    public function __destruct();
}