<?php

namespace chilimatic\lib\database\sql\mysql\connection;

use chilimatic\lib\database\sql\connection\AbstractSqlConnectionSettings;

/**
 * Class MysqlConnectionSettings
 *
 * @package chilimatic\lib\database\sql\mysql\connection
 */
class MysqlConnectionSettings extends AbstractSqlConnectionSettings
{
    /**
     * Mysql default Port
     *
     * @var int
     */
    CONST MYSQL_DEFAULT_PORT = 3306;

    /**
     * connection type
     *
     * @var bool
     */
    private $persistent = false;

    /**
     * @var string
     */
    private $charset;

    /**
     * @var array
     */
    private $options;

    /**
     * @param $host
     * @param $username
     * @param $password
     * @param null $database
     * @param null $port
     * @param array $settingList
     *
     * @return mixed
     */
    public function setConnectionParam($host, $username, $password, $database = null, $port = null,  $settingList = [])
    {
        parent::setConnectionParam($host, $username, $password, $database, $port,  $settingList);

        // if there is no port set the default port
        if (!$this->getPort() && !$port) {
            $this->setPort(self::MYSQL_DEFAULT_PORT);
        }
    }

    /**
     * @param array $settingList
     *
     * @return void
     */
    public function setSettings($settingList = []) {
        if (!$settingList) {
            return;
        }

        if (isset($settingList['persistent'])) {
            $this->setPersistent($settingList['persistent']);
        }

        if (isset($settingList['charset'])) {
            $this->setCharset($settingList['charset']);
        }
    }

    /**
     * connectionDataIsSet
     * <p>
     * checks if at least all the needed parameters for a connection are set
     * </p>
     *
     * @return bool
     */
    public function connectionDataIsSet()
    {
        if ($this->getHost() && $this->getUsername() && $this->getPassword()) {
            return true;
        }

        return false;
    }

    /**
     * @return boolean
     */
    public function isPersistent()
    {
        return $this->persistent;
    }

    /**
     * @param boolean $persistent
     *
     * @return $this
     */
    public function setPersistent($persistent)
    {
        $this->persistent = $persistent;

        return $this;
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @param string $charset
     *
     * @return $this
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * set PDO Options here
     *
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }



}