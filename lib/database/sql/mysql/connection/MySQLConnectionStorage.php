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
use chilimatic\lib\database\sql\connection\AbstractSQLConnection;
use chilimatic\lib\database\sql\connection\AbstractSQLConnectionSettings;
use chilimatic\lib\database\sql\connection\ISQLConnectionStorage;
use JMS\Serializer\Exception\InvalidArgumentException;

/**
 * Class MysqlConnectionStorage
 *
 * @package chilimatic\lib\database\sql\mysql\connection
 */
class MySQLConnectionStorage implements ISQLConnectionStorage
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
     * @param AbstractSQLConnection $connection
     *
     * @return void
     */
    public function addConnection(AbstractSQLConnection $connection){
        $this->storage->attach($connection);
    }

    /**
     * @param AbstractSQLConnectionSettings $connectionSettings
     *
     * @return void
     */
    public function addConnectionBySetting(AbstractSQLConnectionSettings $connectionSettings, $adapterName = '') {
        $this->storage->attach(
            new MySQLConnection($connectionSettings, $adapterName)
        );
    }

    /**
     * @param $pos
     *
     * @return null|AbstractSQLConnection
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
     * @param AbstractSQLConnection $connection
     *
     * @return bool
     */
    public function findConnection(AbstractSQLConnection $connection)
    {
        if ($this->storage->contains($connection)) {
            return true;
        }

        return false;
    }


    /**
     * removes a connection of the pool
     *
     * @param AbstractSQLConnection $connection
     *
     * @return void
     */
    public function removeConnection(AbstractSQLConnection $connection)
    {
        $this->storage->detach($connection);
    }
}