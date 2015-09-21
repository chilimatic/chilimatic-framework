<?php
/**
 *
 * @author j
 * Date: 12/22/14
 * Time: 5:26 PM
 *
 * File: mysqlconnection.php
 */

namespace chilimatic\lib\database\sql\mysql\connection;

use chilimatic\lib\database\connection\IDatabaseConnectionSettings;
use chilimatic\lib\database\mock\MockConnectionAdapter;
use chilimatic\lib\database\sql\connection\AbstractSqlConnection;
use chilimatic\lib\database\sql\mysql\connection\adapter\MySQLiConnectionAdapter;
use chilimatic\lib\database\sql\mysql\connection\adapter\PDOConnectionAdapter;
use chilimatic\lib\exception\DatabaseException;


/**
 * <h1>Class MysqlConnection</h1>
 * <p>
 * all connection parameters are injected from the outside. So only the validation is from within
 * </p>
 *
 * Class MysqlConnection
 *
 * @package chilimatic\lib\database\sql\mysql\connection
 */
class MysqlConnection extends AbstractSqlConnection
{
    /**
     * the currently available connection Interfaces
     * and the mocking interface
     *
     * @var string
     */
    const CONNECTION_PDO = 'pdo';
    const CONNECTION_MYSQLI = 'mysqli';
    const CONNECTION_MOCK = 'mock';


    /**
     * @param IDatabaseConnectionSettings $connectionSettings
     * @param string $adapterName
     *
     * @throws DatabaseException
     *
     * @return mixed
     */
    public function prepareConnectionMetaData(IDatabaseConnectionSettings $connectionSettings, $adapterName)
    {
        if (!$adapterName) {
            throw new \InvalidArgumentException('The AdapterName was not specified, this field is not Optional in the MySQL Connection');
        }

        switch ($adapterName) {
            case self::CONNECTION_PDO:
                $this->setDbAdapter(new PDOConnectionAdapter($connectionSettings));
            break;
            case self::CONNECTION_MYSQLI:
                $this->setDbAdapter(new MySQLiConnectionAdapter($connectionSettings));
                break;
            case self::CONNECTION_MOCK:
                $this->setDbAdapter(new MockConnectionAdapter($connectionSettings));
                break;
            default:
                throw new \InvalidArgumentException('The AdapterName was wrong only pdo and mysqli are allowed');
        }
    }

    public function connectionSettingsAreValid()
    {
        // TODO: Implement connectionSettingsAreValid() method.
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
     * since this method is a wrapper for the tryReconnect only limited
     * we set a max amount of retries.
     * increase the counter and reconnect
     * as often as defined
     *
     * @return bool
     */
    public function reconnect()
    {
        if ($this->getMaxReconnects() < $this->getReconnectCount()) {
            return false;
        }

        $this->ping();
        $this->increaseReconnectCount();

        return true;
    }


}