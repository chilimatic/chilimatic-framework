<?php
/**
 *
 * @author j
 * Date: 4/22/15
 * Time: 9:52 PM
 *
 * File: ErrorLogTrait.php
 */

namespace chilimatic\lib\database;

use chilimatic\lib\log\client\ToFile;
use chilimatic\lib\log\ILog;

trait ErrorLogTrait
{


    /**
     * @var ToFile()
     */
    protected $log;


    /**
     * @param $type
     * @param $message
     * @param null $data
     */
    public function log($type, $message, $data = null)
    {
        if (!$this->log) {
            $this->initLog();
        }

        switch ($type) {
            case ILog::T_ERROR:
                $this->log->error($message, $data);
                break;
            case ILog::T_INFO:
                $this->log->info($message, $data);
                break;
            case ILog::T_WARNING:
                $this->log->warn($message, $data);
                break;
        }
    }

    /**
     *
     */
    public function initLog()
    {
        $this->log = new ToFile();
    }
}