<?php

namespace chilimatic\lib\database\sql\connection;

/**
 *
 * @author j
 * Date: 9/20/15
 * Time: 10:28 PM
 *
 * File: AbstractSqlConnectionSettings.php
 */
use chilimatic\lib\database\connection\IDatabaseConnectionSettings;

/**
 * Class AbstractSqlConnectionSettings
 *
 * @package chilimatic\lib\database\sql\connection
 */
abstract class AbstractSqlConnectionSettings implements IDatabaseConnectionSettings, ISqlConnectionSettings
{

    /**
     * the host ip
     *
     * @var string
     */
    private $host;

    /**
     * the username
     *
     * @var string
     */
    private $username;

    /**
     * the password
     *
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $database;

    /**
     * @var int
     */
    private $port;


    /**
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $database
     * @param int $port
     * @param array $settingList
     */
    public function __construct($host, $username, $password, $database = null, $port = null, $settingList = []) {
       $this->setConnectionParam($host, $username, $password, $database, $port, $settingList);
    }

    /**
     * @param $host
     * @param $username
     * @param $password
     * @param string $database
     * @param int $port
     * @param array $settingList
     *
     * @return mixed
     */
    public function setConnectionParam($host, $username, $password, $database = null, $port = null,  $settingList = [])
    {
        $this->setHost($host);
        $this->setUsername($username);
        $this->setPassword($password);
        $this->setDatabase($database);
        $this->setPort($port);

        // call the specific implementation
        $this->setSettings($settingList);
    }

    /**
     * @param array $settingList
     *
     * @return mixed
     */
    abstract function setSettings($settingList = []);


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
}