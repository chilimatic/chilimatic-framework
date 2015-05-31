<?php
/**
 * Created by PhpStorm.
 * User: Prometheus
 * Date: 21.01.14
 * Time: 19:25
 */

namespace chilimatic\lib\http;

/**
 * Class HTTP_RequestLine
 *
 * @package chilimatic\http
 */
Class RequestLine  {

    /**
     * default protocol
     */
    const DEFAULT_PROTOCOL = 'HTTP/1.1';

    /**
     * address [ip or url]
     *
     * @var string
     */
    private $address = '';

    /**
     * HTTP Method used
     *
     * @var string
     */
    private $method = '';

    /**
     * HTTP Protocol used
     *
     * @var string
     */
    private $protocol = '';


    /**
     * constructor
     *
     * @param string $address
     * @param string $method
     * @param array $url_param
     * @param string $protocol
     *
     * @return \chilimatic\http\RequestLine
     */
    public function __construct($address, $method = Protocol::GET, $url_param = [] , $protocol = self::DEFAULT_PROTOCOL) {
        if (empty($address)) return;

        $this->address = $address;
        // set method
        $this->method = (!empty($method) ? $method : Protocol::GET);
        // init protocol
        $this->protocol = $protocol;
        // init url param
        $this->url_param = (is_array($url_param) && count($url_param) ? '?' . implode('&', $url_param) : '');
    }

    /**
     * sets the address
     *
     * @param $address
     * @return bool
     */
    public function setAddress($address) {
        $this->address = $address;
        return true;
    }

    /**
     * sets the method
     *
     * @param $method
     * @return bool
     */
    public function setMethod($method) {
        if (empty($method)) return false;
        $this->method = $method;

        return true;
    }

    /**
     * sets the protocol
     *
     * @param $protocol
     * @return bool
     */
    public function setProtocol($protocol) {
        if(empty($protocol)) return false;
        $this->protocol = $protocol;

        return true;
    }

    /**
     * sets the urlparameters
     *
     * @param $param
     * @return bool
     */
    public function setParam($param) {
        if (is_string($param)) {
            if ($param[0] == '') $this->url_param = '';
            else $this->url_param = (strpos($param, '?') !== false) ? $param : '?' . $param;
        } elseif (is_array($param) && count($param)) {
            $this->url_param = '?' . implode('&', $param);
        }
        return true;
    }

    /**
     * returns the url parameters
     *
     * @return string
     */
    public function getParam(){
        return $this->url_param;
    }

    /**
     * returns the method
     *
     * @return string
     */
    public function getMethod(){
        return $this->method;
    }

    /**
     * returns the protocol
     *
     * @return string
     */
    public function getProtocol(){
        return $this->protocol;
    }

    /**
     * returns the string
     *
     * @return string
     */
    public function __toString(){
        return "$this->method $this->address$this->url_param $this->protocol\r\n";
    }
}