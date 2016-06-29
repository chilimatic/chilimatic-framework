<?php
namespace chilimatic\lib\log;

/**
 * Interface Log
 *
 * @package chilimatic\log
 */
Interface ILog
{
    /**
     * binary error code
     */
    const T_ERROR = 0b000001;

    /**
     * binary warning code
     */
    const T_WARNING = 0b000010;

    /**
     * binary info code
     */
    const T_INFO = 0b000011;

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
     * @param $log_path  string
     * @param $file_name string
     */
    public function __construct($file_name = '', $log_path = '');


    /**
     * write to the log file
     *
     * @param $msg string
     * @param int $log_level
     *
     * @return
     */
    public function write_log($msg = '', $log_level = 0);
}