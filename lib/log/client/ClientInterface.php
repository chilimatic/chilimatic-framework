<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 06.02.15
 * Time: 00:00
 */

namespace chilimatic\lib\log\client;

/**
 * Interface ClientInterface
 *
 * @package chilimatic\lib\log\client
 */
Interface ClientInterface
{

    /**
     * @param \chilimatic\lib\formatter\Log $format
     */
    public function __construct(\chilimatic\lib\formatter\Log $format = null);

    /**
     * @param string $message
     * @param mixed $data
     *
     * @return self
     */
    public function log($message, $data);

    /**
     * @param string $message
     * @param mixed $data
     *
     * @return self
     */
    public function info($message, $data);

    /**
     * @param string $message
     * @param $mixed $data
     *
     * @return self
     */
    public function warn($message, $data);

    /**
     * @param string $message
     * @param mixed $data
     *
     * @return self
     */
    public function error($message, $data);

    /**
     * @param string $message
     * @param mixed $data
     *
     * @return self
     */
    public function trace($message, $data);

    /**
     * @return mixed
     */
    public function showError();

    /**
     * @param \chilimatic\lib\formatter\Log $format
     *
     * @return mixed
     */
    public function setFormat(\chilimatic\lib\formatter\Log $format);

    /**
     * @return \chilimatic\lib\formatter\Log|null
     */
    public function getFormat();

    /**
     * @return mixed
     */
    public function send();
}