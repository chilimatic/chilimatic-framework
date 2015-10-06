<?php
namespace chilimatic\lib\database\sql\mysql;


use \chilimatic\lib\exception\DatabaseException;

/**
 * Class MysqlStatistic
 *
 * @package chilimatic\lib\database
 */
class MySQLStatistic extends MySQL
{


    /**
     * disk usage
     *
     * @var array
     */
    public $disk_usage = '';


    /**
     * amount of table
     *
     * @var int
     */
    public $table_amount = '';


    /**
     * size of table
     *
     * @var int
     */
    public $table_size = '';


    /**
     * db object
     *
     * @var object
     */
    public $db = null;

    /**
     * constructor
     *
     * @param MySQL $db
     */
    public function __construct(MySQL $db = null)
    {

        if (empty($db)) return;

        // assign database object
        $this->db = $db;

    }

    /**
     * get processlist
     *
     * @throws DatabaseException
     * @return mixed:
     */
    public function get_processlist()
    {

        try {
            if (empty($this->db)) {
                throw new DatabaseException(__METHOD__ . 'No Database Object has been given', MySQL::ERR_NO_CREDENTIALS, MySQL::SEVERITY_LOG, __FILE__, __LINE__);
            }

            $sql = (string)"SHOW FULL PROCESSLIST";
            $res = $this->db->query($sql);

            if (empty($res)) return array();

            return $this->db->fetch_object_list($res);
        } catch (DatabaseException $e) {
            throw $e;
        }
    }

    /**
     * show table listing
     *
     * @throws DatabaseException
     *
     * @return array:
     */
    public function show_table_listing()
    {

        try {
            if (empty($this->db)) {
                throw new DatabaseException(__METHOD__ . 'No Database Object has been given', MySQL::ERR_NO_CREDENTIALS, MySQL::SEVERITY_LOG, __FILE__, __LINE__);
            }

            $sql = (string)"SELECT * FROM `information_schema`.`tables`";
            $res = $this->db->query($sql);

            if (empty($res)) return array();

            return $this->db->fetch_object_list($res);

        } catch (DatabaseException $e) {
            throw $e;
        }
    }
}