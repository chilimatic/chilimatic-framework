<?php
namespace chilimatic\lib\database\sql\mysql\connection\adapter;

use chilimatic\lib\database\sql\connection\AbstractSqlConnectionAdapter;

/**
 * Class MySQLiConnectionAdapter
 *
 * @package chilimatic\lib\database\sql\mysql\connection\adapter
 */
class MySQLiConnectionAdapter extends AbstractSqlConnectionAdapter
{
    use MysqlConnectionTypeTrait;

    public function initResource()
    {
        $this->setResource(
            new \Mysqli(
                $this->getConnectionSettings()->getHost(),
                $this->getConnectionSettings()->getUsername(),
                $this->getConnectionSettings()->getPassword(),
                $this->getConnectionSettings()->getPort(),
                $this->getConnectionSettings()->getDatabase(),
                $this->getConnectionSettings()->getPort()
            )
        );
    }

}