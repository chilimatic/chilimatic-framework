<?php
namespace chilimatic\lib\log;

use chilimatic\lib\config\Config;
use chilimatic\lib\exception\LogException;
use chilimatic\lib\file\File;

/**
 * Class Log_Generic
 *
 * @package chilimatic\log
 */
class Generic implements ILog
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
     * @throws LogException
     */
    public function __construct( $file_name = '' , $log_path = '' )
    {
        if( empty($file_name) ) 
        {
            throw new LogException("No filename given for logging!");
        }
        $this->_file_name = $file_name;
        
        $this->_log_path = (string) (!empty($log_path) ? $log_path : Config::get('logbase'));
        
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
     * @param int $log_level
     *
     * @return bool
     */
    public function write_log( $msg = '' , $log_level = 1 )
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
                throw new LogException( (string) "$msg was not appended to file: $this->_log_path/$this->_file_name.");
            }
        
        }
        catch ( LogException $e )
        {
            error_log($e->getMessage());
        }
        
        return true;
    }


    /**
     * destructor
     */
    public function __destruct()
    {

        if ( !empty($this->file) )
        {
            $this->file->__destruct();
        }
    }
}