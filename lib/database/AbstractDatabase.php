<?php
/**
 *
 * @author j
 * Date: 12/22/14
 * Time: 3:44 PM
 *
 * File: abstractdatabase.class.php
 */

namespace chilimatic\lib\database;

/**
 * Class AbstractDatabase
 *
 * @package chilimatic\lib\database
 */
abstract class AbstractDatabase implements DatabaseInterface
{
    /**
     * @param \chilimatic\lib\database\AbstractConnection $masterConnection
     * @param \chilimatic\lib\database\AbstractConnection $slaveConnection
     */
    abstract public function __construct(\chilimatic\lib\database\AbstractConnection $masterConnection, \chilimatic\lib\database\AbstractConnection $slaveConnection = null);

    /**
     * @param string $query
     *
     * @return mixed
     */
    abstract public function query($query);

    /**
     * @return mixed
     */
    abstract public function lastQuery();

    /**
     * @param string $query
     *
     * @return mixed
     */
    abstract public function execute($query);

    /**
     * @return mixed
     */
    abstract public function prepare();
}