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

use chilimatic\lib\database\sql\connection\AbstractSqlConnection;
use chilimatic\lib\database\sql\connection\AbstractSqlConnectionSettings;


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
     * if it's a master connection
     *
     * @var bool
     */
    protected $master;


    /**
     * the currently available connection Interfaces
     *
     * @var string
     */
    const CONNECTION_API_PDO = 'PDO';
    const CONNECTION_API_MYSQLI = 'MYSQL';

    /**
     * @var string
     */
    private $api = self::CONNECTION_API_PDO;

    public function prepareConnectionMetaData()
    {
        // TODO: Implement prepareConnectionMetaData() method.
    }

    public function connectionSettingsAreValid()
    {
        // TODO: Implement connectionSettingsAreValid() method.
    }


    /**
     * simplifaction for connection strings
     * @todo move into generator
     *
     * @return string
     */
    public function getPDOConnectionString()
    {

        $dsn = 'mysql:';

        if ($this->isSocket()) {
            $dsn .= (string)'unix_socket=' . $this->getHost() . ';';
        } else {
            $dsn .= (string)'host=' . $this->getHost() . ';';
            if ($this->getPort()) {
                $dsn .= 'port=' . $this->getPort() . ';';
            }
        }

        if ($this->getDatabase()) {
            $dsn .= "dbname=" . $this->getDatabase() . ';';
        }

        if ($this->getCharset()) {
            $dsn .= "charset=" . $this->getCharset() . ';';
        }

        return $dsn;
    }

    /**
     * simple check if it's a socket connection or not
     * (can be set manual as well)
     *
     * @return void
     */
    private function findConnectionType()
    {
        switch (true) {
            /**
             * Unix only setting the localhost will try to connect directly through a domainsocket
             */
            case ($this->getHost() == 'localhost' && mb_stripos(PHP_OS, 'win') === false):
                $this->setSocket(true);
                break;
            /**
             * if it's a path we can asume it's a socket
             */
            case (strpos($this->getHost(), '/') === 0):
                $this->setSocket(true);
                break;
            /**
             * everything else is a TCP connection
             */
            default:
                $this->setSocket(false);
                break;
        }
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