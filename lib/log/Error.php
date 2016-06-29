<?php
namespace chilimatic\lib\log;

use chilimatic\lib\Config\Config;
use chilimatic\lib\log\exception\LogException;
use chilimatic\lib\file\File;

/**
 * Class Log_Error
 *
 * @package chilimatic\log
 */
class Error implements ILog
{


    const XML_HEAD = '<?xml version="1.0" encoding="UTF-8" ?>';


    /**
     * Default fallback
     */
    const ERROR_LOG_DEFAULT_PATH = '/var/log/chilimatic';


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
     * message string
     *
     * @var string
     */
    public $msg = null;


    /**
     * logging type
     *
     * @var string
     */
    public $log_type = 'xml';


    /**
     * loglevel which messages are going to be logged
     *
     * @var int
     */
    private $log_level = 0;


    /**
     * Constructor
     *
     * @param $file_name string
     * @param $log_path  string
     */
    public function __construct($file_name = '', $log_path = '')
    {

        $this->_log_path  = (string)(!empty($log_path) ? $log_path : (!Config::get('error_log_path') ? self::ERROR_LOG_DEFAULT_PATH : Config::get('error_log_path')));
        $this->_log_level = (string)(!empty($log_path) ? $log_path : Config::get('error_log_level'));
        $this->_file_name = (string)(!empty($file_name) ? $file_name : 'error_Log_' . date('Y-m-d') . '.log');

        if (strpos($this->_file_name, '.log') === false && strpos($this->_file_name, '.xml') === false) {
            $this->_file_name .= (string)'_' . (string)date('Y-m-d') . '.log';
        }
        $this->file = new File();
    }


    /**
     * (non-PHPdoc)
     *
     * @see Log::write_log()
     *
     * @param string $msg
     * @param int $log_level
     *
     * @return bool
     */
    public function write_log($msg = '', $log_level = 0)
    {
        // log level check
        if ($this->log_level > (int)$log_level) {
            return true;
        }

        try {
            if (!$this->file->open((string)"$this->_log_path/$this->_file_name")) {
                if (!$this->file->create_file("$this->_log_path/$this->_file_name")) {
                    // $message = null, $code = null, $previous = null
                    throw new LogException((string)"file: $this->_log_path/$this->_file_name couldn't be created.");
                }
                $this->file->open((string)"$this->_log_path/$this->_file_name");
            }

            switch (Config::get('log_type')) {
                case 'xml' :
                    $this->msg = '';
                    if ($this->file->read() == "") {
                        $this->msg = self::XML_HEAD . "\n";
                    }
                    $this->msg .= $msg;
                    break;
                default :
                    // if it doesn't exist add next line @ the end of the msg
                    if (strpos($msg, "\n") === false) {
                        $this->msg = "[" . (string)date(self::LOG_DATE_FORMAT) . (string)"] $msg " . "\n";
                    } else {
                        $this->msg = "[" . (string)date(self::LOG_DATE_FORMAT) . (string)"] $msg";
                    }
                    break;
            }

            /**
             *
             * @todo add a good solid solution for writting errors
             *       -> multithread issues
             */
            if ($this->file->append($this->msg) === false) {
                // some code that does something :9
            }
        } catch (LogException $e) {
            error_log($e->getMessage());

            return false;
        }

        return true;
    }
}