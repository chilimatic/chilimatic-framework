<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 10.01.14
 * Time: 15:38
 */

namespace chilimatic\lib\socket;

/**
 * Class Socket
 *
 * @package chilimatic\lib\socket
 */
class Socket
{

    public $header;

    public $socket = null;

    public $message = [];

    public function __construct($address, $port = 80)
    {
        try {
            $this->bind($address, $port);
        } catch (HttpSocketException $e) {
            throw $e;
        }
    }


    public function bind($address, $port)
    {

        if (!$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) throw new HttpSocketException(__METHOD__ . "socket_create() failed");
        if (!socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1)) throw new HttpSocketException(__METHOD__ . "socket_set_option() failed");
        if (!socket_bind($this->socket, $address, $port)) throw new HttpSocketException(__METHOD__ . "socket_bind() failed");
        if (!socket_listen($this->socket, 20)) throw new HttpSocketException(__METHOD__ . "socket_bind() failed");
        $this->message[] = "Server Started : " . date('Y-m-d H:i:s') . "\n";
        $this->message[] = "Master socket  : " . $this->message . "\n";
        $this->message[] = "Listening on   : " . $address . " port " . $port . "\n\n";
    }

    public function connect()
    {

    }
}