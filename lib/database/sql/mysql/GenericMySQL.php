<?php
/**
 * Created by PhpStorm.
 * User: J
 * Date: 24.01.14
 * Time: 14:08
 *
 * Generic Database Model -> it queries the database
 * and returns a data-object with the basic crud functionality
 *
 */

namespace chilimatic\lib\database\sql\mysql;

use chilimatic\lib\traits\Database;

/**
 * Class GenericMySQL
 *
 * @package chilimatic\lib\database
 */
class GenericMySQL
{

    /**
     * database trait init the db handler
     */
    use Database;

    /**
     * default limit
     *
     * @var int
     */
    const DEFAULT_LIMIT = 1000;

    /**
     * default field
     *
     * @var string
     */
    const DEFAULT_FIELD = '*';

    /**
     * database handler
     *
     * @var \PDO|Mysql
     */
    public $db = null;

    /**
     * table name for the query
     *
     * @var string
     */
    private $_table = '';

    /**
     * fieldnames that should be in the result list
     * default is all
     *
     * @var array
     */
    private $_field_list = array(self::DEFAULT_FIELD);

    /**
     * where list
     *
     * @var array
     */
    private $_where_list = array();


    /**
     * limit string
     *
     * @var string
     */
    private $_limit = array('1000');

    /**
     * grouping options
     *
     * @var array
     */
    private $_group_by = array();

    /**
     * list of parameters for the prepared statement
     *
     * @var array
     */
    private $_param_list = array();


    /**
     * last query
     *
     * @var string
     */
    public $last_sql = '';

    /**
     * insert list
     *
     * @var array
     */
    public $insert_list = array();

    /**
     * result of the query
     *
     * @var string
     */
    public $result = '';

    /**
     * @param string $table
     */
    public function __construct($table = '')
    {
        $this->_table = $table;
    }

    /**
     * insert queue for the inserts
     *
     * @param $insert_array
     *
     * @return $this
     */
    public function insertQueue($insert_array)
    {
        if (empty($insert_array)) return $this;
        $this->insert_list[] = $insert_array;

        return $this;
    }

    /**
     * reset the parameters
     *
     * @return $this
     */
    public function resetQuery()
    {
        $this->_field_list = array(self::DEFAULT_FIELD);
        $this->_param_list = array();
        $this->_limit      = array(self::DEFAULT_LIMIT);
        $this->_where_list = array();

        return $this;
    }

    /**
     * set the table name
     *
     * @param string $table
     *
     * @return $this
     */
    public function setTable($table = '')
    {
        $this->_table = $table;

        return $this;
    }

    /**
     * set the limit
     *
     * @param string $limit
     *
     * @return $this
     */
    public function setLimit($limit = '')
    {
        $this->_limit = $limit;

        return $this;
    }

    /**
     * set the field list
     *
     * @param array $field_list
     *
     * @return $this
     */
    public function setFieldList($field_list = array())
    {
        if (empty($field_list)) $this->_field_list = array('*');
        $this->_field_list = (array)$field_list;

        return $this;
    }

    /**
     * set the "where list"
     *
     * @param array $field_list
     *
     * @return $this
     */
    public function setWhereList($field_list = array())
    {
        if (empty($field_list)) $this->_where_list = array();
        $this->_where_list = (array)$field_list;

        return $this;
    }

    /**
     * get all from the table
     *
     * @return $this
     */
    public function getAll()
    {
        $sql = "SELECT * FROM `$this->_table`";
        $this->__init_database();

        if ($stmt = $this->db->prepare($sql)) return $this;

        $this->result = $stmt->fetchAll(Mysql::FETCH_OBJ);

        return $this;
    }

    /**
     * get the result based on the built query
     *
     * @return $this
     */
    public function get()
    {
        $sql = "SELECT * FROM `$this->_table` ";

        $this->_param_list = array();

        if (!empty($this->_where_list)) {
            $sql .= "WHERE ";
            $first = true;
            foreach ($this->_where_list as $field_name => $field_settings) {
                $type          = isset($field_settings['type']) ? $field_settings['type'] : false;
                $field_value   = $field_settings['value'];
                $field_connect = isset($field_settings['op']) ? $field_settings['op'] : ' AND ';

                switch ($type) {
                    case 'like':
                        $sql .= ($first !== true ? "$field_connect" : "") . "$field_name like '%:$field_name%'";
                        $this->_param_list[] = array(":$field_name", $field_value);
                        break;
                    case 'in':
                        $sql .= ($first !== true ? "$field_connect" : "") . "$field_name IN (:$field_name)";
                        $this->_param_list[] = array(":$field_name", $field_value);
                        break;
                    default:
                        $sql .= ($first !== true ? "$field_connect" : "") . "$field_name = :$field_name";
                        $this->_param_list[] = array(":$field_name", $field_value);
                        break;
                }

                // we can assume that after the first iteration it's false
                $first = false;
            }
        }
        unset($field_connect, $field_name, $field_value, $type, $field_settings);

        if (!empty($this->_group)) {
            if (is_array($this->_group)) {
                $this->_limit = implode(',', $this->_group_by);
            }

            $sql .= ' GROUP BY ' . $this->_group_by;
        }

        if (!empty($this->_limit)) {
            if (is_array($this->_limit)) {
                $this->_limit = implode(',', $this->_limit);
            }

            $sql .= ' LIMIT ' . $this->_limit;
        }

        $this->last_sql = $sql;

        $this->__init_database();

        $stmt = $this->db->prepare($sql);
        // set the prepared statement
        for ($i = 0, $c = count($this->_param_list); $i < $c; $i++) {
            $stmt->bindParam($this->_param_list[$i][0], $this->_param_list[$i][1]);
        }


        if (!$stmt->execute()) return;

        $this->result = $stmt->fetchAll(Mysql::FETCH_OBJ);
    }

    /**
     * fetch a direct query
     *
     * @param $sql
     * @param $param array of bind parameters
     *
     * @return $this
     */
    public function query($sql, $param = null)
    {
        $this->__init_database();
        // direct wrapper for fetch
        $stmt = $this->db->prepare($sql);

        for ($i = 0, $c = count($param); $i < $c; $i++) {
            $stmt->bindParam($param[$i][0], $param[$i][1]);
        }

        $this->last_sql = $sql;
        if (!$stmt->execute()) return $this;

        $this->result = $stmt->fetchAll(Mysql::FETCH_OBJ);

        return $this;
    }

    /**
     * get the result set
     *
     * @return bool|string
     */
    public function getResult()
    {
        if (!empty($this->result)) return $this->result;

        return false;
    }
}