<?php
namespace chilimatic\lib\database\sql\connection;

use chilimatic\lib\database\connection\IDatabaseConnectionAdapter;
use chilimatic\lib\database\connection\IDatabaseConnectionSettings;

/**
 * Class AbstractSqlConnectionAdapter
 *
 * @package chilimatic\lib\database\sql\connection
 */
abstract class AbstractSqlConnectionAdapter implements IDatabaseConnectionAdapter
{

    /**
     * @var bool
     */
    private $socket = false;

    /**
     * @var \PDO|\Mysqli
     */
    private $resource;

    /**
     * @var IDatabaseConnectionSettings
     */
    private $connectionSettings;

    /**
     * @param IDatabaseConnectionSettings $connectionSettings
     */
    public function __construct(IDatabaseConnectionSettings $connectionSettings) {
        $this->setConnectionSettings($connectionSettings);
        $this->initResource();
    }

    /**
     * @return mixed
     */
    abstract public function initResource();


    /**
     * @return mixed
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @param mixed $resource
     *
     * @return $this
     */
    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getConnectionSettings()
    {
        return $this->connectionSettings;
    }

    /**
     * @param mixed $connectionSettings
     *
     * @return $this
     */
    public function setConnectionSettings(IDatabaseConnectionSettings $connectionSettings)
    {
        $this->connectionSettings = $connectionSettings;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isSocket()
    {
        return (bool) $this->socket;
    }

    /**
     * @param boolean $socket
     *
     * @return $this
     */
    public function setSocket($socket)
    {
        $this->socket = (bool) $socket;

        return $this;
    }

}