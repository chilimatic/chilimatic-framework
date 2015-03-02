<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 20.01.14
 * Time: 11:12
 */

namespace chilimatic\lib\http;

/**
 * Class HTTP_Protocol
 *
 * @package chilimatic\http
 */
class Protocol {

    /**
     * put method http request
     *
     * @var string
     */
    const PUT = 'PUT';

    /**
     * get method http request
     *
     * @var string
     */
    const GET = 'GET';

    /**
     * post method http request
     *
     * @var string
     */
    const POST = 'POST';

    /**
     * head method http request
     *
     * @var string
     */
    const HEAD = 'HEAD';

    /**
     * delete method http request
     *
     * @var string
     */
    const DELETE = 'DELETE';

    /**
     * trace method http request
     *
     * @var string
     */
    const TRACE = 'TRACE';

    /**
     * patch method http request
     *
     * @var string
     */
    const PATCH = 'PATCH';

    /**
     * options method http request
     *
     * @var string
     */
    const OPTIONS = 'OPTIONS';

    /**
     * connect method http request
     *
     * @var string
     */
    const CONNECT = 'CONNECT';

    /**
     * copy method
     * nonstandard extension for apps like CouchDB
     *
     * @var string
     */
    const COPY = 'COPY';


    /**
     * list of status codes in an array
     * sorted by the first number 1,2,3,4,5,9
     *
     * @var array
     */
    public $status_code = array(
        1 => array (
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing'
        ),
        2 => array (
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-Status',
            208 => 'Already Reported',
            226 => 'IM Used'
        ),
        3 => array (
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => 'Switch Proxy',
            307 => 'Temporary Redirect',
            308 => 'Permanent Redirect'
        ),
        4 => array (
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method not allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Time-out',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URL Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested range not satisfiable',
            417 => 'Expectation Failed',
            418 => 'I’m a teapot',
            419 => 'Authentication Timeout (not in RFC 2616)',
            420 => 'Policy Not Fulfilled',
            421 => 'There are too many connections from your internet address',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            425 => 'Unordered Collection',
            426 => 'Upgrade Required',
            428 => 'Precondition Required',
            429 => 'Too Many Requests',
            431 => 'Request Header Fields Too Large',
            440 => 'Login Timeout (Microsoft)',
            444 => 'No Response',
            449 => 'The request should be retried after doing the appropriate action',
            451 => 'Unavailable For Legal Reasons'
        ),
        5 => array (
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Time-out',
            505 => 'HTTP Version not supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            508 => 'Loop Detected',
            509 => 'Bandwidth Limit Exceeded',
            510 => 'Not Extended'
        ),
        9 => array (    // this can be read @
            900 => 'Request Error',
            901 => 'Request Error',
            902 => 'Request Error',
            903 => 'Request Error',
            904 => 'Request Error',
            905 => 'Request Error',
            906 => 'Transfer Error',
            950 => 'Admin Error'
        )
    );

    public function __construct(){
        $this->constant_array = $this->get_class_constants();
    }


    /**
     * get constants
     * @return array
     */
    public function get_class_constants()
    {
        $reflect = new \ReflectionClass(get_class($this));
        return $reflect->getConstants();
    }
}
