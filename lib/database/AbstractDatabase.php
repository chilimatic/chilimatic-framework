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
use chilimatic\lib\database\connection\AbstractConnection;

/**
 * Class AbstractDatabase
 *
 * @package chilimatic\lib\database
 */
abstract class AbstractDatabase implements DatabaseInterface
{
    /**
     * @param AbstractConnection $masterConnection
     * @param AbstractConnection $slaveConnection
     */
    abstract public function __construct(AbstractConnection $masterConnection, AbstractConnection $slaveConnection = null);

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