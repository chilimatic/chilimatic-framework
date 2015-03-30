<?php
namespace chilimatic\lib\log;

use chilimatic\lib\config\Config;
use chilimatic\lib\exception\LogException;
use chilimatic\lib\file\File;

/**
 * Class Log_Process
 *
 * @package chilimatic\log
 */
class Process implements ILog
{


    /**
     * path to the logs
     *
     * @var string
     */
    protected $_log_path = '';


    /**
     * name of the file
     *
     * @var string
     */
    protected $_file_name = '';


    /**
     * file object
     *
     * @var object
     */
    public $file = null;


    /**
     * loglevel which messages are going to be logged
     *
     * @var int
     */
    private $log_level = 0;


    /**
     * constructor
     *
     * @param $file_name string
     * @param $log_path  string
     *
     */
    public function __construct( $file_name = '' , $log_path = '' )
    {

        $this->_log_path = (string) (!empty($log_path) ? $log_path : Config::get('process_log_path'));
        $this->_file_name = (string) (!empty($file_name) ? $file_name : 'process_log_' . date(self::LOG_DATE_FILE) . '.log');
        $this->_log_level = (int) Config::get('process_log_level');
        
        if ( strpos($this->_file_name, '.log') === false )
        {
            $this->_file_name .= (string) '_' . (string) date(self::LOG_DATE_FILE) . '.log';
        }
        $this->file = new File();
    }


    /**
     * write to the log file
     *
     * @param $msg string
     *
     * @param int $log_level
     *
     * @return bool
     */
    public function write_log( $msg = '' , $log_level = 0 )
    {

        // log level check
        if ( $this->log_level > (int) $log_level ) return true;
        
        try
        {
            if ( !$this->file->open( (string) "$this->_log_path/$this->_file_name") && !$this->file->create_file("$this->_log_path/$this->_file_name") )
            {
                // $message = null, $code = null, $previous = null
                throw new LogException( (string) "file: $this->_log_path/$this->_file_name couldn't be created.");
            }
            
            // check for the 2nd time
            $this->file->open("$this->_log_path/$this->_file_name");
            
            // if it doesn't exist add next line @ the end of the msg
            if ( strpos($msg, "\n") === false )
            {
                $msg = "[" . (string) date(self::LOG_DATE_FORMAT) . (string) "] $msg " . "\n";
            }
            else
            {
                $msg = "[" . (string) date(self::LOG_DATE_FORMAT) . (string) "] $msg";
            }
            

            if ( $this->file->append($msg) === false )
            {
                // some code
            }
            /*
             * while ( $this->file->append($msg) === false ) { /* // sleep 1
             * second sleep(1); if ( $start_time + 10 == time() ) { throw new
             * LogException("Sleep time for file:
             * $this->_log_path/$this->_file_name exceeded 10
             * seconds\n[msg]$msg"); break; } }
             */
        
        }
        catch ( LogException $e )
        {
            error_log($e->getMessage());
        }
        
        return true;
    }


    public function __destruct()
    {

        if ( !empty($this->file) )
        {
            $this->file->__destruct();
        }
    }
}