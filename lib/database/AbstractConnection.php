<?php
/**
 *
 * @author j
 * Date: 12/22/14
 * Time: 6:08 PM
 *
 * File: abastractconnection.php
 */

namespace chilimatic\lib\database;

/**
 * Class AbstractConnection
 *
 * @package chilimatic\lib\database
 */
abstract class AbstractConnection
{
    /**
     * the host ip
     *
     * @var string
     */
    protected $host;

    /**
     * the socket
     *
     * @var bool
     */
    protected $socket;

    /**
     * the username
     *
     * @var string
     */
    protected $username;

    /**
     * the password
     *
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $database;

    /**
     * the port number
     *
     * @var int
     */
    protected $port;

    /**
     * if it's active (in use)
     *
     * @var bool
     */
    protected $active;

    /**
     * <p>
     *  since different databases need to implement different
     *  interfaces the param is an array described in every constructor
     *  some settings are necessary some are optional
     * </p>
     *
     * @param $param
     */
    abstract public function __construct($param);


    /**
     * @return bool
     */
    abstract public function isValid();

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     *
     * @return $this
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @return string
     */
    public function isSocket()
    {
        return (bool)$this->socket;
    }

    /**
     * @param string $socket
     *
     * @return $this
     */
    public function setSocket($socket)
    {
        $this->socket = $socket;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @param string $database
     *
     * @return $this
     */
    public function setDatabase($database)
    {
        $this->database = $database;

        return $this;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param int $port
     *
     * @return $this
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     *
     * @return $this
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }
}