<?php
namespace chilimatic\lib\database\sql\mysql\connection\adapter;
/**
 *
 * @author j
 * Date: 9/21/15
 * Time: 11:30 PM
 *
 * File: AbstractMysqlConnectionAdapter.php
 */

Trait MySQLConnectionTypeTrait
{
    /**
     * simple check if it's a socket connection or not
     * (can be set manual as well)
     *
     * @return void
     */
    protected function findConnectionType()
    {
        switch (true) {
            /**
             * Unix only setting the localhost will try to connect directly through a domainsocket
             */
            case ($this->getConnectionSettings()->getHost() == 'localhost' && mb_stripos(PHP_OS, 'win') === false):
                $this->setSocket(true);
                break;
            /**
             * if it's a path we can asume it's a socket
             */
            case (strpos($this->getConnectionSettings()->getHost(), '/') === 0):
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
}