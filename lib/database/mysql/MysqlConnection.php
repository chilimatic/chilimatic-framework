<?php
/**
 *
 * @author j
 * Date: 12/22/14
 * Time: 5:26 PM
 *
 * File: mysqlconnection.php
 */

namespace chilimatic\lib\database\mysql;

use chilimatic\lib\database\AbstractConnection;

/**
 * <h1>Class MysqlConnection</h1>
 * <p>
 * all connection parameters are injected from the outside. So only the validation is from within
 * </p>
 *
 * @package chilimatic\lib\database\mysql
 */
class MysqlConnection extends AbstractConnection
{

    /**
     * Mysql standard charset
     *
     * @var string
     */
    CONST STANDARD_CHARSET = 'utf8';

    /**
     * Mysql default Port
     *
     * @var int
     */
    CONST MYSQL_DEFAULT_PORT = 3306;

    /**
     * amount of reconnect tries
     *
     * @var int
     */
    const MAX_RECONNECTS = 3;

    /**
     * if it's a master connection
     *
     * @var bool
     */
    protected $master;

    /**
     * @var int
     */
    protected $lastPing;

    /**
     * amount of current reconnects
     *
     * @var int
     */
    protected $reconnect;

    /**
     * amount of max reconnects
     *
     * @var int
     */
    protected $maxReconnects = self::MAX_RECONNECTS;

    /**
     * connection type
     *
     * @var bool
     */
    protected $persistent;

    /**
     * current status of connection
     *
     * @var bool
     */
    protected $connected = false;

    /**
     * the connection | abstraction is always injected
     *
     * @var \PDO|\Mysqli
     */
    protected $db;

    /**
     * @var string
     */
    protected $charset;

    /**
     * @param null|array $param
     */
    public function __construct($param = null)
    {
        $this->setHost($param['host']);
        $this->setPassword($param['password']);
        $this->setUsername($param['username']);
        $this->setDatabase((isset($param['database']) ? $param['database'] : null));
        $this->setPort((isset($param['port']) ? $param['port'] : self::MYSQL_DEFAULT_PORT));
        $this->setPersistent((isset($param['persistent']) ? $param['persistent'] : false));
        $this->setCharset((isset($param['charset']) ? $param['charset'] : false));

        $this->findConnectionType();
    }

    /**
     * simple check if it's a socket connection or not
     * (can be set manual as well)
     *
     * @return void
     */
    private function findConnectionType()
    {
        switch (true) {
            case (strpos($this->getHost(), 'localhost') && $this->getPort() == self::MYSQL_DEFAULT_PORT):
                $this->setSocket(true);
                break;
            case (strpos($this->getHost(), '/') === 0):
                $this->setSocket(true);
                break;
            case (@ip2long($this->getHost())):
                $this->setSocket(false);
                break;
            case (@gethostbyname($this->getHost())):
                $this->setSocket(false);
                break;
        }
    }

    /**
     * isValid
     * <p>
     * checks if at least all the needed parameters for a connection are set
     * </p>
     *
     * @return bool
     */
    public function isValid()
    {
        if ($this->getHost() && $this->getUsername() && $this->getPassword()) {
            return true;
        }

        return false;
    }

    /**
     * simplifaction for connection strings
     *
     * @return string
     */
    public function getMysqliConnectionString()
    {
        switch (true) {
            case $this->isPersistent():
                if ($this->isSocket()) {
                    return (string)null;
                }

                return (string)'p:' . $this->getHost();
                break;
            default:
                if ($this->isSocket()) {
                    return (string)null;
                }

                return (string)$this->getHost();
                break;
        }
    }

    /**
     * simplifaction for connection strings
     *
     * @return string
     */
    public function getPDOConnectionString()
    {

        $dsn = 'mysql:';

        if ($this->isSocket()) {
            $dsn .= (string)'unix_socket=' . $this->getHost() . ';';
        } else {
            $dsn .= (string)'host=' . $this->getHost() . ';';
            if ($this->getPort()) {
                $dsn .= 'port=' . $this->getPort() . ';';
            }
        }

        if ($this->getDatabase()) {
            $dsn .= "dbname=" . $this->getDatabase() . ';';
        }

        if ($this->getCharset()) {
            $dsn .= "charset=" . $this->getCharset() . ';';
        }

        return $dsn;
    }

    /**
     * Ping
     * <p>
     * reconnect to mysql if the connection was lost
     * </p>
     *
     * @return bool
     */
    public function ping()
    {
        if (method_exists($this->getDb(), 'ping')) {
            $this->getDb()->ping();
        } else if ($this->isConnected() && $this->getDb()) { // PDO condition
            try {
                $this->getDb()->query("SELECT 1");
            } catch (\PDOException $e) {
                $this->getDb()->init();
            }
        }

        return true;
    }

    /**
     * @return boolean
     */
    public function isMaster()
    {
        return $this->master;
    }

    /**
     * @param boolean $master
     *
     * @return $this
     */
    public function setMaster($master)
    {
        $this->master = $master;

        return $this;
    }

    /**
     * @return int
     */
    public function getLastPing()
    {
        return $this->lastPing;
    }

    /**
     * @param int $lastPing
     *
     * @return $this
     */
    public function setLastPing($lastPing)
    {
        $this->lastPing = $lastPing;

        return $this;
    }

    /**
     * @return int
     */
    public function getReconnect()
    {
        return $this->reconnect;
    }

    /**
     * @param int $reconnect
     *
     * @return $this
     */
    public function setReconnect($reconnect)
    {
        $this->reconnect = $reconnect;

        return $this;
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
     * @return boolean
     */
    public function isConnected()
    {
        return $this->connected;
    }

    /**
     * @param boolean $isConnected
     *
     * @return $this
     */
    public function setConnected($isConnected)
    {
        $this->connected = $isConnected;

        return $this;
    }

    /**
     * @return \Mysqli|\PDO
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * @param \Mysqli|\PDO $db
     *
     * @return $this
     */
    public function setDb($db)
    {
        $this->db = $db;

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
     * since this method is a wrapper for the tryReconnect only limited
     * we set a max amount of retries.
     * increase the counter and reconnect
     * as often as defined
     *
     * @return bool
     */
    public function tryReconnect()
    {
        if (self::MAX_RECONNECTS < $this->reconnect) return false;
        $this->ping();
        $this->reconnect++;

        return true;
    }
}