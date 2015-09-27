<?php
namespace chilimatic\lib\database\sql\connection;

use chilimatic\lib\database\connection\IDatabaseConnection;
use chilimatic\lib\database\connection\IDatabaseConnectionAdapter;
use chilimatic\lib\database\connection\IDatabaseConnectionSettings;
use chilimatic\lib\database\sql\mysql\connection\MysqlConnectionSettings;
use chilimatic\lib\exception\DatabaseException;
use chilimatic\lib\parser\AnnotationValidatorParser;
use chilimatic\lib\validator\AnnotationPropertyValidatorFactory;

/**
 * Class AbstractSqlConnection
 *
 * @package chilimatic\lib\database\sql
 */
abstract class AbstractSqlConnection implements IDatabaseConnection, ISqlConnection {

    /**
     * if it's active (in use)
     *
     * @var bool
     */
    protected $active = false;

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
     * connection Role
     * @var int
     */
    private $connectionRole;

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
     * @var IDatabaseConnectionAdapter
     */
    private $dbAdapter;


    /**
     * @param IDatabaseConnectionSettings $connectionSettings
     * @param string $adapterName
     */
    public function __construct(IDatabaseConnectionSettings $connectionSettings, $adapterName = '') {
        // initializes the needed steps for the Connection
        $this->prepareConnectionMetaData($connectionSettings, $adapterName);
    }

    /**
     * @param IDatabaseConnectionSettings $connectionSettings
     * @param $adapterName
     *
     * @throws DatabaseException
     *
     * @return mixed
     */
    abstract public function prepareConnectionMetaData(IDatabaseConnectionSettings $connectionSettings, $adapterName);

    /**
     * a database connection needs certain parameters to work
     *
     * Host | Username | Password these are the minimum requirements which have to be checked
     * the secondary parameters like database and port need to be checked if they're set as well
     *
     * @return bool
     */
    public function connectionSettingsAreValid()
    {
        // if there is no adapter how can we initialize the correct validator to check it
        if (!$this->getDbAdapter()) {
            return false;
        }

        /**
         * @var MysqlConnectionSettings $connectionSettings
         */
        $connectionSettings = $this->getDbAdapter()->getConnectionSettings();
        if (!$connectionSettings) {
            return false;
        }

        $validatorFactory = new AnnotationPropertyValidatorFactory(
            new AnnotationValidatorParser()
        );

        /**
         * @var $property \ReflectionProperty
         */
        foreach ($connectionSettings->getParameterGenerator() as $property) {
            $validatorObject = $validatorFactory->make($property);
            if (!$validatorObject->count()) {
                continue;
            }

            $validatorObject->rewind();

            foreach ($validatorObject->current() as $validator) {
                $property->setAccessible(true); //
                $property->getValue($connectionSettings);
                if (!$validator($property->getValue($connectionSettings))) {
                    return false;
                }
            }
        }

        return true;
    }


    /**
     * @return mixed
     */
    abstract public function ping();


    /**
     * @return mixed
     */
    abstract public function reconnect();


    /**
     * @return boolean
     */
    public function isActive()
    {
        return (bool) $this->active;
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
     * @return AbstractSqlConnectionAdapter
     */
    public function getDbAdapter()
    {
        return $this->dbAdapter;
    }

    /**
     * @param IDatabaseConnectionAdapter $dbAdapter
     *
     * @return $this
     */
    public function setDbAdapter(IDatabaseConnectionAdapter $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;

        return $this;
    }

    /**
     * @return int
     */
    public function getConnectionRole()    {
        return $this->connectionRole;
    }

    /**
     * @param int $connectionRole
     *
     * @return $this
     */
    public function setConnectionRole($connectionRole)
    {
        $this->connectionRole = $connectionRole;

        return $this;
    }
}