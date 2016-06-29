<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Prometheus
 * Date: 25.10.13
 * Time: 17:01
 * To change this template use File | Settings | File Templates.
 */
namespace chilimatic\lib\session\engine;


use chilimatic\lib\config\Config;
use chilimatic\lib\Database\Mysql as MysqlDb;
use chilimatic\lib\Database\Traits\DatabaseConnection;


class Mysql extends GenericEngine
{

    /**
     * database trait to establish
     * the database connection when needed
     */
    use DatabaseConnection;

    /**
     * special session db name if
     * a specific one is created
     *
     * @var string
     */
    private $_db_name = '';

    /**
     * constructor
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        // checks if the table exists
        if (!$this->init($config)) {
            return;
        }


        // Read the maxlifetime setting from PHP
        $this->_session_life_time = get_cfg_var("session.gc_maxlifetime");
        // Register this object as the session handler
        session_set_save_handler(array(
            &$this,
            "session_open"
        ), array(
            &$this,
            "session_close"
        ), array(
            &$this,
            "session_read"
        ), array(
            &$this,
            "session_write"
        ), array(
            &$this,
            "session_destroy"
        ), array(
            &$this,
            "session_gc"
        ));

        // override über die config [optional]
        $this->_session_life_time = (($tmp = Config::get('session_lifetime')) != '') ? $tmp : $this->_session_life_time;
    }

    /**
     * session init
     *
     * @return bool;
     */
    public function init($config = [])
    {
        $this->__init_database();

        $this->_db_name = (string)(($db = $config['session_db_name']) ? $db : $config['db_name']);

        if (!($this->db instanceof MysqlDb)) {
            return false;
        }

        // check if the sql table does exist
        $sql         = (string)"SELECT COUNT(`session_id`) FROM `{$this->_db_name}`.`user_session`";
        $session_res = $this->db->query((string)$sql);
        // if not try to create the session table
        if ($session_res === false) {
            $sql = (string)"CREATE TABLE `{$this->_db_name}`.`user_session` (
                    `session_id` varchar(100) NOT NULL default '',
                    `session_data` LONGTEXT NOT NULL,
                    `expires` int(11) NOT NULL default '0',
                    `created` DATETIME,
                    `updated` DATETIME,
                     PRIMARY KEY (`session_id`)
                    ) ENGINE=InnoDB Collate=utf8_general_ci";

            return $this->db->query((string)$sql);
        }

        return true;
    }


    /**
     * read....
     *
     * @param $session_id string
     *
     * @return bool array
     */
    public function session_read($session_id)
    {
        // Set empty result
        $this->session_data = (string)'';

        // Fetch session data from the selected database with the correct
        // timestamp
        $time       = (int)time();
        $session_id = mysql_real_escape_string($session_id);
        $sql        = (string)"SELECT `session_data` FROM `{$this->_db_name}`.`user_session` WHERE `session_id` = '$session_id' AND `expires` > $time";

        $session_res = $this->db->query((string)$sql);
        // if no session data exists return false
        if (empty($session_res)) return false;
        $this->session_data = $this->db->fetch_string($session_res);
        $this->db->free($session_res);

        // session data is set uncompressed
        return $this->session_data;
    }


    /**
     * write the session data to the database
     *
     * @param $session_id   string
     * @param $session_data string
     *
     * @return bool
     */
    public function session_write($session_id, $session_data)
    {
        // set the garbage collection timestamp
        $time = (int)time() + (int)$this->_session_life_time;
        // bzip the data
        // replace the whole session_data
        $session_id   = mysql_real_escape_string($session_id, $this->db->db);
        $session_data = mysql_real_escape_string($session_data);
        $date         = date('Y-m-d H:i:s');

        $sql = (string)"REPLACE INTO `{$this->_db_name}`.`user_session` (`session_id`,`session_data`,`expires`, `created`, `updated`) VALUES ('$session_id','$session_data', $time , '$date', '$date')";
        if (!$this->db->query($sql)) return false;

        $this->session_id = (string)$session_id;

        return true;
    }


    /**
     * garbage collector deletes the session
     *
     * @return bool;
     */
    public function session_gc()
    {
        // Garbage Collection
        // Build DELETE query. Delete all records who have passed the expiration
        // time
        $sql = (string)"DELETE FROM `{$this->_db_name}`.`user_session` WHERE `expires` < UNIX_TIMESTAMP();";
        $this->db->query((string)$sql);

        // Always return TRUE
        return true;
    }


    /**
     * destroy the session
     *
     * @param $session_id {string}
     *
     * @return bool
     */
    public function session_destroy($session_id)
    {

        if (empty($session_id)) return false;

        // Build query
        $escaped_id = (string)addslashes(stripslashes($session_id));
        $sql        = (string)"DELETE FROM `{$this->_db_name}`.`user_session` WHERE `session_id` = '$escaped_id'";

        return $this->db->query((string)$sql);
    }

    /**
     * destruct
     */
    public function __destruct()
    {

        if (empty($this->db)) {
            $this->__init_database();
        }
        session_write_close();
        // garbage collector should work instantly
        $this->session_gc();
    }
}