<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 05.02.15
 * Time: 23:48
 */

namespace chilimatic\lib\log\client;

abstract class AbstractClient implements ClientInterface
{
    /**
     * @var \chilimatic\lib\formater\Log
     */
    protected $format;

    /**
     * @var \SPLQueue
     */
    protected $logMessages;

    /**
     * @param \chilimatic\lib\formater\Log $format
     */
    public function __construct(\chilimatic\lib\formater\Log $format = null)
    {
        $this->format      = $format;
        $this->logMessages = new \SPLQueue();
    }

    function __toString()
    {
        // TODO: Implement __toString() method.
    }

    /**
     * @param string $message
     * @param mixed $data
     *
     * @return $this
     */
    public function log($message, $data)
    {
        // TODO: Implement log() method.
        $this->logMessages->enqueue([
            'date'    => date('Y-m-d H:i:s'),
            'message' => $message,
            'data'    => $data
        ]);

        return $this;
    }

    /**
     * @param string $message
     * @param mixed $data
     *
     * @return $this
     */
    public function info($message, $data)
    {
        $this->logMessages->enqueue([
            'prefix'  => 'info',
            'date'    => date('Y-m-d H:i:s'),
            'message' => $message,
            'data'    => $data
        ]);

        return $this;
    }

    /**
     * @param string $message
     * @param $mixed $data
     *
     * @return self
     */
    public function warn($message, $data)
    {
        $this->logMessages->enqueue([
            'prefix'  => 'warn',
            'date'    => date('Y-m-d H:i:s'),
            'message' => $message,
            'data'    => $data
        ]);

        return $this;
    }

    /**
     * @param string $message
     * @param mixed $data
     *
     * @return self
     */
    public function error($message, $data)
    {
        // TODO: Implement error() method.
        $this->logMessages->enqueue([
            'prefix'  => 'error',
            'date'    => date('Y-m-d H:i:s'),
            'message' => $message,
            'data'    => $data
        ]);

        return $this;
    }

    /**
     * @param string $message
     * @param mixed $data
     *
     * @return self
     */
    public function trace($message, $data)
    {
        // TODO: Implement trace() method.
        $this->logMessages->enqueue([
            'date'    => date('Y-m-d H:i:s'),
            'message' => $message,
            'data'    => $data
        ]);

        return $this;
    }

    /**
     * @return mixed
     */
    public function showError()
    {
        return true;
    }

    /**
     * @param \chilimatic\lib\formater\Log $format
     *
     * @return mixed
     */
    public function setFormat(\chilimatic\lib\formater\Log $format)
    {
        // TODO: Implement setFormat() method.
    }

    /**
     * @return \chilimatic\lib\formater\Log|null
     */
    public function getFormat()
    {
        // TODO: Implement getFormat() method.
    }

    /**
     * @return mixed
     */
    abstract public function send();


}