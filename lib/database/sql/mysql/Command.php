<?php

namespace chilimatic\lib\database\sql\mysql;

use \chilimatic\lib\config\Config as Config;

/**
 * Class MySQLCommand
 *
 * @package chilimatic\lib\database
 */
class Command
{

    /**
     * database object
     *
     * @var Mysql
     */
    public $db = null;

    /**
     * constructor
     *
     * @param Mysql $db
     */
    public function __construct($db = null)
    {

        if (!empty($db) && is_resource($db)) {
            $this->db = $db;
        } else {
            $this->db = new Mysql(Config::get('mysql_host'), Config::get('mysql_user'), Config::get('mysql_password'), Config::get('mysql_db'));
        }
    }

    /**
     * kill a specific process
     *
     * @param int $pid
     *
     * @return boolean
     */
    public function kill_process($pid)
    {

        if (!is_numeric($pid)) return false;
        $pid = (int)$pid;
        $sql = "KILL $pid";

        return $this->db->query($sql);
    }

}
