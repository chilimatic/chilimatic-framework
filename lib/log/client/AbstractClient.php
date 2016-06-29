<?php
namespace chilimatic\lib\log\client;

abstract class AbstractClient implements ClientInterface
{
    /**
     * @var \chilimatic\lib\formatter\Log
     */
    protected $format;

    /**
     * @var \SPLQueue
     */
    protected $logMessages;

    /**
     * @param \chilimatic\lib\formatter\Log $format
     */
    public function __construct(\chilimatic\lib\formatter\Log $format = null)
    {
        $this->format      = $format;
        $this->logMessages = new \SplQueue();
    }

    public function __toString()
    {
        return '';
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
     * @param \chilimatic\lib\formatter\Log $format
     *
     * @return mixed
     */
    public function setFormat(\chilimatic\lib\formatter\Log $format)
    {
        $this->format = $format;
    }

    /**
     * @return \chilimatic\lib\formatter\Log|null
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @return mixed
     */
    abstract public function send();


    public function __destruct() {
        // if the logger is destroyed we always send the errors :)
        $this->send();
    }
}