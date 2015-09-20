<?php
namespace chilimatic\lib\database\sql\connection;

use chilimatic\lib\database\connection\IDatabaseConnection;
use chilimatic\lib\database\connection\IDatabaseConnectionSettings;
use chilimatic\lib\interfaces\IFlyWeightValidator;

/**
 * Class AbstractSqlConnection
 *
 * @package chilimatic\lib\database\sql
 */
abstract class AbstractSqlConnection implements IDatabaseConnection{

    /**
     * if it's active (in use)
     *
     * @var bool
     */
    protected $active;

    /**
     * the socket
     *
     * @var bool
     */
    private $socket;

    /**
     * @var int
     */
    private $lastPing;

    /**
     * amount of current reconnects
     *
     * @var int
     */
    private $reconnectCount;

    /**
     * amount of max reconnects
     *
     * @var int
     */
    private $maxReconnects = self::MAX_DEFAULT_RECONNECTS;

    /**
     * current status of connection
     *
     * @var bool
     */
    private $connected = false;

    /**
     * the connection
     *
     * @var IDatabase
     */
    private $db;

    /**
     * @var IDatabaseConnectionSettings
     */
    private $connectionSettings;

    /**
     * @var IFlyweightValidator
     */
    private $validator;

    /**
     * @param IDatabaseConnectionSettings $connectionSettings
     */
    public function __construct(IDatabaseConnectionSettings $connectionSettings) {
        $this->setConnectionSettings($connectionSettings);
        $this->prepareConnectionMetaData();
    }

    /**
     * @return bool
     */
    public function connectionDataIsValid() {
       return $this->getValidator()->validate($this->getConnectionSettings());
    }

    /**
     * @return mixed
     */
    abstract public function prepareConnectionMetaData();

    /**
     * @return mixed
     */
    abstract public function ping();


    /**
     * @return mixed
     */
    abstract public function reconnect();

    /**
     * @return IFlyWeightValidator
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @param IFlyWeightValidator $validator
     *
     * @return $this
     */
    public function setValidator($validator)
    {
        $this->validator = $validator;

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

    /**
     * @return boolean
     */
    public function isSocket()
    {
        return $this->socket;
    }

    /**
     * @param boolean $socket
     *
     * @return $this
     */
    public function setSocket($socket)
    {
        $this->socket = $socket;

        return $this;
    }

    /**
     * @return IDatabaseConnectionSettings
     */
    public function getConnectionSettings()
    {
        return $this->connectionSettings;
    }

    /**
     * @param IDatabaseConnectionSettings $connectionSettings
     *
     * @return $this
     */
    public function setConnectionSettings(IDatabaseConnectionSettings $connectionSettings)
    {
        $this->connectionSettings = $connectionSettings;

        return $this;
    }

    /**
     * increments the reconnect counter
     */
    public function increaseReconnectCount() {
        $this->reconnectCount++;
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
    public function getReconnectCount()
    {
        return $this->reconnectCount;
    }

    /**
     * @param int $reconnectCount
     *
     * @return $this
     */
    public function setReconnectCount($reconnectCount)
    {
        $this->reconnectCount = (int) $reconnectCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxReconnects()
    {
        return $this->maxReconnects;
    }

    /**
     * @param int $maxReconnects
     *
     * @return $this
     */
    public function setMaxReconnects($maxReconnects)
    {
        $this->maxReconnects = (int) $maxReconnects;

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
     * @param boolean $connected
     *
     * @return $this
     */
    public function setConnected($connected)
    {
        $this->connected = $connected;

        return $this;
    }

    /**
     * @return IDatabase
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * @param IDatabase $db
     *
     * @return $this
     */
    public function setDb($db)
    {
        $this->db = $db;

        return $this;
    }
}