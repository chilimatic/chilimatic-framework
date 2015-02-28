<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 20.01.14
 * Time: 17:45
 */

namespace chilimatic\lib\http;

Class HTTP_Socket {

    /**
     * default port for http
     *
     * @var int
     */
    const DEFAULT_PORT = 80;


    /**
     * http transfer protocol
     *
     * @var string
     */
    const DEFAULT_PROTOCOL = 'http://';

    /**
     * username
     *
     * @var string
     */
    protected  $_user = '';

    /**
     * password
     *
     * @var string
     */
    protected  $_password = '';

    /**
     * host
     *
     * @var string
     */
    protected $_host = '127.0.0.1';

    /**
     * port
     *
     * @var int
     */
    protected $_port = 5984;

    /**
     * procotol
     *
     * @var string
     */
    protected $_protocol = 'http://';


    /**
     * fsocket stream
     *
     * @var null|resource
     */
    private $_socket = null;


    /**
     * error number
     *
     * @var int
     */
    public $errorno = null;

    /**
     * error string
     *
     * @var string
     */
    public $error = null;

    /**
     * the whole http request data as a string
     * @var string
     */
    public $request = '';

    /**
     * header of the return stream of couchdb
     * @var string
     */
    public $header = '';

    /**
     * result body as a string
     *
     * @var string
     */
    public $body = '';


    /**
     * result json decoded
     *
     * @var mixed
     */
    public $result = null;

    /**
     * http protocol is the data object
     * for http requests
     *
     * @var \chilimatic\http\HTTP_Protocol
     */
    public $http_protocol = null;


    /**
     * state of the current connection
     *
     * @var bool
     */
    public $is_connected = false;


    /**
     * http request
     *
     * @var null
     */
    public $http_request = null;


    /**
     * constructor
     *
     * @param \stdClass $param
     *
     * @return \chilimatic\http\HTTP_Socket
     */
    public function __construct($param){
        // get the data object for http request based on the current standards
        $this->httpprotocol = new HTTP_Protocol();
        $this->init($param);
    }

    /**
     * init method to change param
     *
     * @param \stdClass $param
     *
     * @return bool
     */
    public function init($param) {
        // check for valid host
        if ($param->host && !$this->_validate_host($param->host)) return false;

        // add all the settings
        $this->_host = $param->host;
        $this->_user = (empty($param->user) ? null : $param->user);
        $this->_password = (empty($param->password) ? null : $param->password);
        $this->_port = (empty($param->port)) ? self::DEFAULT_PORT : $param->port;
        $this->_protocol = (isset($param->protocol) && $this->_validate_protocol($param->protocol)) ? $param->protocol : self::DEFAULT_PROTOCOL;

        // start the socket connection
        $this->_socket = pfsockopen($this->_host, $this->_port, $this->errorno, $this->error);

        return $this->is_connected = (is_resource($this->_socket) ? true : false);
    }

    /**
     * validates the host url
     * valid IP, url
     *  - 12.3.1.4
     *  - cb.example.com / example.com / example.co.com
     * @param string $host
     *
     * @return bool
     */
    private function _validate_host($host) {
        if (empty($host)) return false;
        // if it's an ip address
        if (preg_match('/^\d{1,3}[.]\d{1,3}[.]\d{1,3}[.]\d{1,3}$/', $host)) return true;
        // if it's a domain based on this definition -> cb.example.com / example.com / example.co.com
        elseif (preg_match('/^((?:\w{1,3}[.])?\w{1,}[.](?:(?:\w{1,3})?(?:[.]\w{1,3})?))$/', $host)) return true;

        return false;
    }

    /**
     * returns true if it's https or http
     *
     * @param $protocol
     * @return bool
     */
    private function _validate_protocol($protocol) {
        if (empty($protocol)) return false;
        // protocol validation only http or https are valid for
        return (preg_match('#(https://|http://)#', $protocol) ? true : false);
    }


    public function generateHTTPRequest(){

    }

    /**
     * basic send method via socket connection
     *
     * @param $method
     * @param $url
     * @param null $data
     *
     * @return string
     */
    public function send($method, $url, $data = null)
    {
        $p = new \stdClass();
        $p->method = $method;
        $p->url = $url;
        $p->data = $data;
        $p->host = $this->_host;
        $p->protocol = "HTTP/1.1";


        $this->request = new HTTP_Request($p);


        // check if there is a user add user & pw base64 encoded
        if ($this->_user) {
            $this->request->header = new HTTP_SingleParam("Authorization", "Basic " .base64_encode("$this->_user:$this->_password"));
        }
        $r = $this->request->__toString();
        // write it to the buffer
        @fwrite($this->_socket, $r, strlen($this->request));

        // define the response variable
        $response = stream_get_contents($this->_socket);

        // response data
        if ( empty($response) ) return false;

        // explode the body and the header of the http request
        list($header, $this->body) = explode("\r\n\r\n", $response);
        // init the headers
        $this->_parse_header($header);
        // json decode the result body
        $this->result = json_decode($this->body);
        //return it
        return $this->result;
    }

    /**
     * parses the header and
     * replaces the header property with a stdClass with the information
     *
     * @param $header
     * @return bool
     */
    private function _parse_header($header) {
        if (empty($header)) return false;

        $lines = explode("\r\n", $header);
        $tmp = array_shift($lines);
        $tmp = explode(" ", $tmp);
        // set the first params
        $this->header = new \stdClass();
        $this->header->protocol = $tmp[0];
        $this->header->status = $tmp[1];
        $this->header->status_text = $tmp[2];

        foreach ($lines as $line) {
            $tmp = explode(':', $line);
            $key = strtolower($tmp[0]);
            $value = trim($tmp[1]);
            // check if there are semicolon seperated values
            if (strpos($value, ';')) {
                $tmp2 = explode(';', $value);
                $value = trim($tmp2[0]);
                // check if there is an assignment
                if (strpos($tmp2[1], '=')) {
                    $tmp2 = explode('=', $tmp2[1]);
                    $key2 = strtolower(trim($tmp2[0]));
                    $this->header->$key2 = trim($tmp2[1]);
                }
            }

            $this->header->$key = $value;
        }


        return true;
    }

    /**
     * destruct
     */
    public function __destruct(){
        fclose($this->_socket);
    }
}