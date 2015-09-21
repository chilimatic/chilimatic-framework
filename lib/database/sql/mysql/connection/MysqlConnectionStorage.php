<?php
/**
 *
 * @author j
 * Date: 12/22/14
 * Time: 4:00 PM
 *
 * File: mysqlconnection.php
 */

namespace chilimatic\lib\database\sql\mysql\connection;
use chilimatic\lib\database\sql\connection\AbstractSqlConnection;
use chilimatic\lib\database\sql\connection\AbstractSqlConnectionSettings;
use chilimatic\lib\database\sql\connection\ISqlConnectionStorage;
use JMS\Serializer\Exception\InvalidArgumentException;

/**
 * Class MysqlConnectionStorage
 *
 * @package chilimatic\lib\database\sql\mysql\connection
 */
class MysqlConnectionStorage implements ISqlConnectionStorage
{
    /**
     * @var \SplObjectStorage
     */
    protected $storage;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->storage = new \SplObjectStorage();
    }


    /**
     * @param AbstractSqlConnection $connection
     *
     * @return void
     */
    public function addConnection(AbstractSqlConnection $connection){
        $this->storage->attach($connection);
    }

    /**
     * @param AbstractSqlConnectionSettings $connectionSettings
     *
     * @return void
     */
    public function addConnectionBySetting(AbstractSqlConnectionSettings $connectionSettings, $adapterName = '') {
        $this->storage->attach(
            new MysqlConnection($connectionSettings, $adapterName)
        );
    }

    /**
     * @param $pos
     *
     * @return null|AbstractSqlConnection
     */
    public function getConnectionByPosition($pos)
    {
        $this->storage->rewind();
        for ($i = 0; $this->storage->count() > $i; $i++) {
            if ($i == $pos) {
                return $this->storage->current();
            }
            $this->storage->next();
        }

        return null;
    }

    /**
     * @param AbstractSqlConnection $connection
     *
     * @return bool
     */
    public function findConnection(AbstractSqlConnection $connection)
    {
        if ($this->storage->contains($connection)) {
            return true;
        }

        return false;
    }


    /**
     * removes a connection of the pool
     *
     * @param AbstractSqlConnection $connection
     *
     * @return void
     */
    public function removeConnection(AbstractSqlConnection $connection)
    {
        $this->storage->detach($connection);
    }
}