<?php
/**
 * @author j
 * Date: 2/2/14
 * Time: 2:19 PM
 *
 * File: statement.class.php
 *
 * Class Statement
 * implements prepared Statements for the normal database wrapper
 */

namespace chilimatic\lib\database\sql\mysql;

use chilimatic\lib\exception\DatabaseException;


/**
 * Class Statement
 *
 * @package chilimatic\lib\database
 */
class Statement
{

    /**
     * boolean parameter
     *
     * @var int
     */
    const TYPE_BOOL = 0;
    /**
     * int param
     *
     * @var int
     */
    const TYPE_INT = 1;

    /**
     * string param
     *
     * @var int
     */
    const TYPE_STRING = 2;

    /**
     * float param
     *
     * @var int
     */
    const TYPE_FLOAT = 3;

    /**
     * null param
     *
     * @var int
     */
    const TYPE_NULL = 4;

    /**
     * blob param
     *
     * @var int
     */
    const TYPE_BLOB = 5;


    /**
     * prepared statement SQL
     *
     * @var string
     */
    public $sql = '';

    /**
     * list of bound parameters
     *
     * @var array
     */
    protected $param_list = array();

    /**
     * standard types
     *
     * @var array
     */
    private $_type_list = array(self::TYPE_BOOL, self::TYPE_INT, self::TYPE_FLOAT, self::TYPE_STRING, self::TYPE_NULL, self::TYPE_BLOB);


    /**
     * database handler
     *
     * @var MySQL
     */
    private $_db = null;


    /**
     * database resource
     *
     * @var resource
     */
    private $res = null;


    /**
     * prepared SQL statement
     *
     * @var string
     */
    public $prepared_sql = null;

    /**
     * type list
     *
     * @var array
     */
    public $type_list = array();


    /**
     * constructor adds the SQL and the database object
     *
     * @param $sql
     * @param $db
     */
    public function __construct($sql, $db)
    {
        $this->prepared_sql = $sql;
        $this->_db          = $db;
    }

    /**
     * binds a parameter
     *
     * @param string $placeholder
     * @param mixed $var
     * @param null $type
     *
     * @return bool
     */
    public function bindParam($placeholder, &$var, $type = null)
    {

        $this->param_list[$placeholder] = &$var;
        if (!empty($type) && in_array($type, $this->_type_list)) {
            $this->type_list[$placeholder] = &$type;
        }


        return true;
    }


    /**
     * execute the query
     *
     * @throws \chilimatic\lib\exception\DatabaseException
     * @throws \Exception
     */
    public function execute()
    {
        try {
            $sql = $this->prepared_sql;

            foreach ($this->param_list as $placeholder => $param) {
                if (strpos($this->prepared_sql, $placeholder) === false) {
                    throw new DatabaseException(__METHOD__ . " missing bound placeholder: $placeholder in sql $this->prepared_sql", MySQL::ERR_CONN, MySQL::SEVERITY_LOG, __FILE__, __LINE__);
                }


                $t = (isset($this->type_list[$placeholder]) ? $this->type_list[$placeholder] : 999999);

                switch ($t) {
                    case self::TYPE_BLOB:
                    case self::TYPE_STRING:
                        $val = "'" . mysql_real_escape_string($param, $this->_db->db) . "'";
                        break;
                    case self::TYPE_INT:
                    case self::TYPE_BOOL:
                        $val = (int)$param;
                        break;
                    case self::TYPE_FLOAT:
                        $val = (float)$param;
                        break;
                    case self::TYPE_NULL:
                        $val = 'NULL';
                        break;
                    default:
                        $val = "'" . (mysql_real_escape_string($param, $this->_db->db)) . "'";
                        break;
                }
                $sql = str_replace($placeholder, $val, $sql);
            }
        } catch (DatabaseException $e) {
            throw $e;
        }

        unset($t, $val, $param);

        /**
         * set the sql for debugging purposes
         */
        $this->sql = $sql;

        /**
         * query the database
         */
        $this->res = $this->_db->query($sql);

        if (!$this->res) return false;

        return $this;
    }

    /**
     * fetch all option
     *
     * @param null $type
     *
     * @return array
     * @throws DatabaseException
     * @throws \Exception
     */
    public function fetchAll($type = null)
    {
        try {
            if ($this->res === false) {
                throw new DatabaseException(__METHOD__ . " No ressource has been given", MySQL::NO_RESSOURCE, MySQL::SEVERITY_DEBUG, __FILE__, __LINE__);
            }

            switch ($type) {
                case MySQL::FETCH_ASSOC:
                    return $this->_db->fetch_assoc_list($this->res);
                    break;
                case MySQL::FETCH_NUM:
                    return $this->_db->fetch_num_list($this->res);
                    break;
                case MySQL::FETCH_OBJ:
                default:
                    return $this->_db->fetch_object_list($this->res);
                    break;
            }
        } catch (DatabaseException $e) {
            throw $e;
        }


    }

}