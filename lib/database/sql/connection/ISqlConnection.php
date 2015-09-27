<?php

namespace chilimatic\lib\database\sql\connection;

use chilimatic\lib\database\connection\IDatabaseConnectionAdapter;

/**
 * Interface IDatabaseSqlConnection
 *
 * @package chilimatic\lib\database\sql\connection
 */
interface ISqlConnection
{
    /**
     * @return boolean
     */
    public function isActive();

    /**
     * @param boolean $active
     *
     * @return $this
     */
    public function setActive($active);

    /**
     * @return boolean
     */
    public function isSocket();

    /**
     * @param boolean $socket
     *
     * @return $this
     */
    public function setSocket($socket);

    /**
     * increments the reconnect counter
     */
    public function increaseReconnectCount();

    /**
     * @return int
     */
    public function getLastPing();

    /**
     * @param int $lastPing
     *
     * @return $this
     */
    public function setLastPing($lastPing);

    /**
     * @return int
     */
    public function getReconnectCount();

    /**
     * @param int $reconnectCount
     *
     * @return $this
     */
    public function setReconnectCount($reconnectCount);

    /**
     * @return int
     */
    public function getMaxReconnects();

    /**
     * @param int $maxReconnects
     *
     * @return $this
     */
    public function setMaxReconnects($maxReconnects);

    /**
     * @return boolean
     */
    public function isConnected();

    /**
     * @param boolean $connected
     *
     * @return $this
     */
    public function setConnected($connected);

    /**
     * @return IDatabaseConnectionAdapter
     */
    public function getDbAdapter();

    /**
     * @param IDatabaseConnectionAdapter $dbAdapter
     *
     * @return $this
     */
    public function setDbAdapter(IDatabaseConnectionAdapter $dbAdapter);


    /**
     * @return int
     */
    public function getConnectionRole();

    /**
     * @param int $connectionRole
     *
     * @return $this
     */
    public function setConnectionRole($connectionRole);
}