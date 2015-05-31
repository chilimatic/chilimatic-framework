<<<<<<< HEAD
<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 21.01.14
 * Time: 12:22
 *
 *  $this->request = "$method $url HTTP/1.0\r\nHost: {$this->_host}\r\n";

// check if there is a user add user & pw base64 encoded
if ($this->_user) {
$this->request .= "Authorization: Basic " . base64_encode("$this->_user:$this->_password") . "\r\n";
}
// check if there is data to be appended
if ($data) {
$this->request .= "Content-Length: ". strlen($data) ."\r\n\r\n";
$this->request .= "$data\r\n";
} else {
$this->request .= "\r\n";
}
 */
namespace chilimatic\lib\http;


class Request {

    /**
     * default HTTP Protocol
     */
    const DEFAULT_PROTOCOL = 'HTTP';

    /**
     * default http protocol version
     */
    const DEFAULT_PROTOCOL_VERSION = '1.0';

    /**
     * default Method
     */
    const DEFAULT_METHOD = Protocol::GET;

    /**
     * method for HTTP-Request
     *
     * @var string
     */
    public $method = Protocol::GET;

    /**
     * url of the request/response
     *
     * @var string
     */
    public $url = '';

    /**
     * the attached content
     *
     * @var string
     */
    public $content = '';

    /**
     * HTTP protocol used
     *
     * @var string
     */
    public $protocol = '';

    /**
     * url parameters
     *
     * @var string
     */
    public $url_param = '';

    /**
     * @var array
     */
    public $header = array();

    /**
     * request string
     *
     * @var string
     */
    public $request = '';

    /**
     * request line
     *
     * @var null
     */
    public $request_line = null;

    /**
     * request body
     *
     * @var null
     */
    public $body = null;


    /**
     * constructor
     *
     * @param \stdClass $param
     */
    public function __construct($param = null) {
        if (empty($param)) return;

        $this->init($param);
    }

    /**
     * set the parameters
     *
     * @param $param
     * @return bool
     */
    public function init($param) {

        if (empty($param)) return false;

        $this->header = '';

        $param_list = new ParamList();
        foreach ($param as $key => $value) {
            if (property_exists($this, $key)) $this->$key = $value;
            elseif (property_exists($param_list, strtolower(str_replace('-','_', $key)))) {
                if (is_string($value)) {
                    $this->header[] = new SingleParam($key, $value);
                } elseif(is_array($value)) {
                    $this->header[] = new MultiParam($key, $value);
                }
            }

        }



        return true;
    }

    /**
     * generates the according http string
     *
     * @return string
     */
    public function __toString(){
        // first line of the request
        $this->request_line = new RequestLine($this->url, $this->method, $this->url_param, $this->protocol);
        $this->request .= $this->request_line;

        if ($this->content) {
            $this->header[] = new SingleParam("Content-Length", strlen($this->content));
            $this->body = "this->content\r\n";
        }

        for($i = 0, $c = count($this->header); $i < $c; $i++) {
            $this->request .= $this->header[$i] . "\r\n";
        }
        $this->request .= $this->body;

        return $this->request;
    }

=======
<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 21.01.14
 * Time: 12:22
 *
 *  $this->request = "$method $url HTTP/1.0\r\nHost: {$this->_host}\r\n";

// check if there is a user add user & pw base64 encoded
if ($this->_user) {
$this->request .= "Authorization: Basic " . base64_encode("$this->_user:$this->_password") . "\r\n";
}
// check if there is data to be appended
if ($data) {
$this->request .= "Content-Length: ". strlen($data) ."\r\n\r\n";
$this->request .= "$data\r\n";
} else {
$this->request .= "\r\n";
}
 */
namespace chilimatic\lib\http;


class Request {

    /**
     * default HTTP Protocol
     */
    const DEFAULT_PROTOCOL = 'HTTP';

    /**
     * default http protocol version
     */
    const DEFAULT_PROTOCOL_VERSION = '1.0';

    /**
     * default Method
     */
    const DEFAULT_METHOD = Protocol::GET;

    /**
     * method for HTTP-Request
     *
     * @var string
     */
    public $method = Protocol::GET;

    /**
     * url of the request/response
     *
     * @var string
     */
    public $url = '';

    /**
     * the attached content
     *
     * @var string
     */
    public $content = '';

    /**
     * HTTP protocol used
     *
     * @var string
     */
    public $protocol = '';

    /**
     * url parameters
     *
     * @var string
     */
    public $url_param = '';

    /**
     * @var array
     */
    public $header = array();

    /**
     * request string
     *
     * @var string
     */
    public $request = '';

    /**
     * request line
     *
     * @var null
     */
    public $request_line = null;

    /**
     * request body
     *
     * @var null
     */
    public $body = null;


    /**
     * constructor
     *
     * @param \stdClass $param
     */
    public function __construct($param = null) {
        if (empty($param)) return;

        $this->init($param);
    }

    /**
     * set the parameters
     *
     * @param $param
     * @return bool
     */
    public function init($param) {

        if (empty($param)) return false;

        $this->header = '';

        $param_list = new ParamList();
        foreach ($param as $key => $value) {
            if (property_exists($this, $key)) $this->$key = $value;
            elseif (property_exists($param_list, strtolower(str_replace('-','_', $key)))) {
                if (is_string($value)) {
                    $this->header[] = new SingleParam($key, $value);
                } elseif(is_array($value)) {
                    $this->header[] = new MultiParam($key, $value);
                }
            }

        }



        return true;
    }

    /**
     * generates the according http string
     *
     * @return string
     */
    public function __toString(){
        // first line of the request
        $this->request_line = new RequestLine($this->url, $this->method, $this->url_param, $this->protocol);
        $this->request .= $this->request_line;

        if ($this->content) {
            $this->header[] = new SingleParam("Content-Length", strlen($this->content));
            $this->body = "this->content\r\n";
        }

        for($i = 0, $c = count($this->header); $i < $c; $i++) {
            $this->request .= $this->header[$i] . "\r\n";
        }
        $this->request .= $this->body;

        return $this->request;
    }

>>>>>>> 336a13ce09982d8ab14d6fadbb1d8dd97927b344
}