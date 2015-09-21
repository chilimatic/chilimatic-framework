<?php
namespace chilimatic\lib\database\sql\mysql\connection\adapter;

use chilimatic\lib\database\sql\connection\AbstractSqlConnectionAdapter;

/**
 * Class PDOConnectionAdapter
 *
 * @package chilimatic\lib\database\sql\mysql\connection\adapter
 */
class PDOConnectionAdapter extends AbstractSqlConnectionAdapter
{
    use MysqlConnectionTypeTrait;

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
     * @todo move into generator
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

}
