<?php
/**
 *
 * @author j
 * Date: 12/22/14
 * Time: 4:00 PM
 *
 * File: mysqlconnection.php
 */

namespace chilimatic\lib\database\mysql;

/**
 * Class MysqlConnection
 *
 * @package chilimatic\lib\database
 */
class MysqlConnectionStorage
{
    /**
     * Mysql standard Port
     *
     * @var int
     */
    CONST MYSQL_DEFAULT_PORT = 3306;

    /**
     * @var \SplObjectStorage
     */
    protected $storage;

    /**
     * constructor
     */
    public function __construct() {
        $this->storage = new \SplObjectStorage();
    }

    /**
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $database
     * @param int $port
     */
    public function addConnection($host, $username, $password, $database = '', $port = self::MYSQL_DEFAULT_PORT, $charset = '') {
        $param = [
            'host' => $host,
            'username' => $username,
            'password' => $password,
            'database' => $database,
            'port' => $port,
            'charset' => $charset
        ];

        $this->storage->attach(
            new MysqlConnection($param)
        );
    }

    /**
     * @param $pos
     *
     * @return null|object
     */
    public function getConnection($pos) {
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
     * removes a connection of the pool
     *
     * @param $connection
     */
    public function removeConnection($connection) {
        $this->storage->detach($connection);
    }
}