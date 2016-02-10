<?php
namespace chilimatic\lib\database\sql\mysql\connection\adapter;

use chilimatic\lib\database\sql\connection\AbstractSQLConnectionAdapter;

/**
 * Class PDOConnectionAdapter
 *
 * @package chilimatic\lib\database\sql\mysql\connection\adapter
 */
class PDOConnectionAdapter extends AbstractSQLConnectionAdapter
{
    /**
     * trait that contains similar code for
     * both PDO and Mysqli
     */
    use MySQLConnectionTypeTrait;

    public function initResource()
    {
        $this->findConnectionType();

        $this->setResource(
            new \PDO(
                $this->getConnectionString(),
                $this->getConnectionSettings()->getUsername(),
                $this->getConnectionSettings()->getPassword(),
                $this->getConnectionSettings()->getOptions()
            )
        );
    }


    /**
     * simplifaction for connection strings
     *
     * @return string
     */
    public function getConnectionString()
    {

        $dsn = 'mysql:';

        // get the connection settings local so there is less function call overhead
        $connectionSettings = $this->getConnectionSettings();
        if ($this->isSocket()) {
            $dsn .= (string)'unix_socket=' . $connectionSettings->getHost() . ';';
        } else {
            $dsn .= (string)'host=' . $connectionSettings->getHost() . ';';
            if ($connectionSettings->getPort()) {
                $dsn .= 'port=' . $connectionSettings->getPort() . ';';
            }
        }

        if ($connectionSettings->getDatabase()) {
            $dsn .= "dbname=" . $connectionSettings->getDatabase() . ';';
        }

        if ($connectionSettings->getCharset()) {
            $dsn .= "charset=" . $connectionSettings->getCharset() . ';';
        }

        return $dsn;
    }

    /**
     * @return bool
     */
    public function ping()
    {
        try {
           $this->getResource()->query("SELECT 1");
        } catch (\PDOException $e){
           $this->initResource();
        }

        return true;
    }


    /**
     * @param string $sql
     * @param array $options
     *
     * generates a small set an executes the query accordingly
     *
     * @return mixed
     */
    public function query($sql, $options = [])
    {
        return $this->getResource()->query($sql);
    }

    /**
     * @param string $sql
     * @param array $options
     *
     * @return mixed
     */
    public function prepare($sql, $options = [])
    {
        return $this->getResource()->prepare($sql, $options);
    }

    /**
     * @param $sql
     *
     * @return int
     */
    public function execute($sql)
    {
        return $this->getResource()->exec($sql);
    }

    /**
     * @return bool
     */
    public function beginTransaction()
    {
        return $this->getResource()->beginTransaction();
    }

    /**
     * @return bool
     */
    public function rollback()
    {
        return $this->getResource()->rollback();
    }

    /**
     * @return bool
     */
    public function commit()
    {
        return $this->getResource()->commit();
    }

    /**
     * @return mixed
     */
    public function inTransaction()
    {
        return $this->getResource()->inTransaction();
    }

    /**
     * @return mixed
     */
    public function getErrorCode()
    {
        return $this->getResource()->errorCode();
    }

    /**
     * @return mixed
     */
    public function getErrorInfo()
    {
        return $this->getResource()->errorInfo();
    }

    /**
     * @return mixed
     */
    public function getLastInsertId()
    {
        return $this->getResource()->lastInsertId();
    }
}
