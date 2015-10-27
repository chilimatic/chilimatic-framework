<?php
/**
 * Mysql database abstraction class
 * please feel free to add or modify this class as you like
 * as long you don't spoil the old functionality
 *
 * @author  j
 * @version $id$
 *
 */

namespace chilimatic\lib\database\sql\mysql;

use chilimatic\lib\database\AbstractDatabase;
use chilimatic\lib\config\Config;
use chilimatic\lib\database\connection\IDatabaseConnection;
use chilimatic\lib\database\sql\mysql\connection\MySQLConnection;
use chilimatic\lib\exception\DatabaseException;

/**
 * Class Mysql
 *
 * @package chilimatic\lib\database
 */
class MySQL extends AbstractDatabase
{

    /**
     * only in cases of intense debug
     * this can be activated
     *
     * @var int
     */
    CONST SEVERITY_DEBUG = 0;


    /**
     * only log
     *
     * @var int
     */
    CONST SEVERITY_LOG = 1;


    /**
     * mail it as well
     *
     * @var int
     */
    CONST SEVERITY_MAIL = 2;


    /**
     * login error
     *
     * @var int
     */
    CONST ERR_NO_CREDENTIALS = 1;


    /**
     * Connection error
     *
     * @var int
     */
    CONST ERR_CONN = 2;


    /**
     * query error
     *
     * @var int
     */
    CONST ERR_EXEC = 3;


    /**
     * no ressource given
     *
     * @var int
     */
    CONST NO_RESSOURCE = 4;

    /**
     * to be pdo compatible -> i just copied the pdo variables
     *
     * Specifies that the fetch method shall return each row as an object with
     * variable names that correspond to the column names returned in the result
     * set. <b>PDO::FETCH_LAZY</b> creates the object variable names as they are accessed.
     * Not valid inside <b>PDOStatement::fetchAll</b>.
     *
     * @link http://php.net/manual/en/pdo.constants.php
     */
    const FETCH_LAZY = 1;

    /**
     * Specifies that the fetch method shall return each row as an array indexed
     * by column name as returned in the corresponding result set. If the result
     * set contains multiple columns with the same name,
     * <b>PDO::FETCH_ASSOC</b> returns
     * only a single value per column name.
     *
     * @link http://php.net/manual/en/pdo.constants.php
     */
    const FETCH_ASSOC = 2;

    /**
     * Specifies that the fetch method shall return each row as an array indexed
     * by column number as returned in the corresponding result set, starting at
     * column 0.
     *
     * @link http://php.net/manual/en/pdo.constants.php
     */
    const FETCH_NUM = 3;

    /**
     * Specifies that the fetch method shall return each row as an array indexed
     * by both column name and number as returned in the corresponding result set,
     * starting at column 0.
     *
     * @link http://php.net/manual/en/pdo.constants.php
     */
    const FETCH_BOTH = 4;

    /**
     * Specifies that the fetch method shall return each row as an object with
     * property names that correspond to the column names returned in the result
     * set.
     *
     * @link http://php.net/manual/en/pdo.constants.php
     */
    const FETCH_OBJ = 5;

    /**
     * Specifies that the fetch method shall return TRUE and assign the values of
     * the columns in the result set to the PHP variables to which they were
     * bound with the <b>PDOStatement::bindParam</b> or
     * <b>PDOStatement::bindColumn</b> methods.
     *
     * @link http://php.net/manual/en/pdo.constants.php
     */
    const FETCH_BOUND = 6;

    /**
     * Specifies that the fetch method shall return only a single requested
     * column from the next row in the result set.
     *
     * @link http://php.net/manual/en/pdo.constants.php
     */
    const FETCH_COLUMN = 7;

    /**
     * Specifies that the fetch method shall return a new instance of the
     * requested class, mapping the columns to named properties in the class.
     * The magic
     * <b>__set</b>
     * method is called if the property doesn't exist in the requested class
     *
     * @link http://php.net/manual/en/pdo.constants.php
     */
    const FETCH_CLASS = 8;

    /**
     * Specifies that the fetch method shall update an existing instance of the
     * requested class, mapping the columns to named properties in the class.
     *
     * @link http://php.net/manual/en/pdo.constants.php
     */
    const FETCH_INTO = 9;

    /**
     * Allows completely customize the way data is treated on the fly (only
     * valid inside <b>PDOStatement::fetchAll</b>).
     *
     * @link http://php.net/manual/en/pdo.constants.php
     */
    const FETCH_FUNC = 10;

    /**
     * Group return by values. Usually combined with
     * <b>PDO::FETCH_COLUMN</b> or
     * <b>PDO::FETCH_KEY_PAIR</b>.
     *
     * @link http://php.net/manual/en/pdo.constants.php
     */
    const FETCH_GROUP = 65536;

    /**
     * Fetch only the unique values.
     *
     * @link http://php.net/manual/en/pdo.constants.php
     */
    const FETCH_UNIQUE = 196608;

    /**
     * Fetch a two-column result into an array where the first column is a key and the second column
     * is the value. Available since PHP 5.2.3.
     *
     * @link http://php.net/manual/en/pdo.constants.php
     */
    const FETCH_KEY_PAIR = 12;

    /**
     * Determine the class name from the value of first column.
     *
     * @link http://php.net/manual/en/pdo.constants.php
     */
    const FETCH_CLASSTYPE = 262144;

    /**
     * As <b>PDO::FETCH_INTO</b> but object is provided as a serialized string.
     * Available since PHP 5.1.0.
     *
     * @link http://php.net/manual/en/pdo.constants.php
     */
    const FETCH_SERIALIZE = 524288;

    /**
     * Available since PHP 5.2.0
     *
     * @link http://php.net/manual/en/pdo.constants.php
     */
    const FETCH_PROPS_LATE = 1048576;

    /**
     * Specifies that the fetch method shall return each row as an array indexed
     * by column name as returned in the corresponding result set. If the result
     * set contains multiple columns with the same name,
     * <b>PDO::FETCH_NAMED</b> returns
     * an array of values per column name.
     *
     * @link http://php.net/manual/en/pdo.constants.php
     */
    const FETCH_NAMED = 11;

    /**
     * Database details object
     * for the connected server
     *
     * @var object
     */
    public $db_detail = null;


    /**
     * db resource
     *
     * @var MysqlConnection
     */
    public $db = false;

    /**
     * amount of affected rows
     *
     * @var int
     */
    public $affected_rows = 0;


    /**
     * error description
     *
     * @var string
     */
    public $error = '';


    /**
     * error number
     *
     * @var int
     */
    public $errorno = '';


    /**
     * mysql client encoding
     *
     * @var string
     */
    public $mysqli_client_encoding = '';


    /**
     * the last query
     *
     * @var string
     */
    public $lastSql = '';


    /**
     * new insert id
     *
     * @var int
     */
    public $insert_id = 0;

    /**
     * enables the detail object for the databas object
     *
     * @var bool
     */
    private $_get_detail = false;

    /**
     * @var MysqlConnection
     */
    protected $masterConnection;

    /**
     * @var MysqlConnection
     */
    protected $slaveConnection;

    /**
     * @param IDatabaseConnection $masterConnection
     * @param IDatabaseConnection $slaveConnection
     *
     * @throws DatabaseException
     * @throws \Exception
     */
    public function __construct(IDatabaseConnection $masterConnection, IDatabaseConnection $slaveConnection = null)
    {
        if (!$masterConnection->connectionSettingsAreValid()) {
            throw new \Exception('connection Data is not Valid');
        }

        $this->masterConnection = $masterConnection;
        $this->connect($this->masterConnection);

        if ($slaveConnection && $slaveConnection->connectionDataIsSet()) {
            $this->slaveConnection = $slaveConnection;
            $this->connect($this->slaveConnection);
        }
    }


    /**
     * create new database connection
     */
    final public function __clone()
    {
        if (empty($this->_master_host)) return;
        $this->connect($this->masterConnection);

        if (empty($this->_slave_host)) return;
        $this->connect($this->slaveConnection);
    }


    /**
     * reconnect to db after serialisation
     */
    final public function __wakeup()
    {
        if ($this->masterConnection) return;
        $this->connect($this->masterConnection);

        if ($this->slaveConnection) return;
        $this->connect($this->slaveConnection);
    }


    /**
     * commit the transaction
     *
     * @return bool
     */
    public function commit()
    {
        return $this->query('commit');
    }

    /**
     * connects to the db based on the mysql Connection
     *
     * @param MysqlConnection $connection
     *
     * @throws \chilimatic\lib\exception\DatabaseException|\Exception
     *
     * @return bool
     */
    public function connect(MysqlConnection $connection)
    {
        try {

            // if no connection to master and slave is possible
            if (!$connection->isConnected()) {
                throw new DatabaseException(__METHOD__ . "\nConnection failure no ressource given", self::NO_RESSOURCE, self::SEVERITY_LOG, __FILE__, __LINE__);
            }

            // get the mysql (only master) server details
            if ($this->_get_detail === true) {
                $this->db_detail = $this->getDatabaseDetail($this);
            }

        } catch (DatabaseException $ed) {
            throw $ed;
        }

        if (!$this->db) {
            $this->db = $connection;
        }

        return true;
    }


    /**
     * fetches an array with only 1 dimension
     * "SELECT <col_name> FROM <tablename>
     *
     * array($col_entry1,$col_entry2)
     *
     * @param \PDOStatement $res
     * @param int $col
     *
     * @throws DatabaseException|\Exception
     *
     * @return array
     */
    public function fetchSimpleList(\PDOStatement $res, $col = 0)
    {
        if (!$col) {
            $col = 0;
        }

        $result = array();
        try {
            if (!$res) {
                throw new DatabaseException(__METHOD__ . " No ressource has been given", self::NO_RESSOURCE, self::SEVERITY_LOG, __FILE__, __LINE__);
            }

            while ($row = $res->fetch(\PDO::FETCH_ASSOC)) {
                $result[] = $row[$col];
            }
        } catch (DatabaseException $e) {
            throw $e;
        }

        return $result;
    }


    /**
     * fetches an associative array
     *
     * @param \PDOStatement $res
     *
     * @throws DatabaseException|\Exception
     * @return bool array
     */
    public function fetchAssoc(\PDOStatement $res)
    {
        return $res->fetchAll(\PDO::FETCH_ASSOC);
    }


    /**
     * fetches an array list of associative arrays
     * which can be assigned to a specific key from the row as well
     *
     * @param \PDOStatement $res
     * @param $assign_by string
     *
     * @throws DatabaseException|\Exception
     *
     * @return array bool
     */
    public function fetchAssocList(\PDOStatement $res, $assign_by = null)
    {
        $result = array();
        try {
            while ($row = $res->fetch(\PDO::FETCH_ASSOC)) {
                if (!empty($assign_by) && !is_array($assign_by)) {
                    $result[$row[$assign_by]] = (array)$row;
                } elseif (!empty($assign_by) && is_array($assign_by)) {
                    $key = array();
                    foreach ($assign_by as $key_w) {
                        if (isset($row[$key_w])) {
                            $key[] = $row[$key_w];
                        }
                    }

                    if (empty($key)) {
                        $result[] = (object)$row;
                    } else {
                        // removes the first '- '
                        $key          = implode('-', $key);
                        $result[$key] = (object)$row;
                    }
                } else {
                    $result[] = (array)$row;
                }
            }
        } catch (DatabaseException $e) {
            throw $e;
        }

        return $result;
    }


    /**
     * fetches an array list of numeric arrays
     *
     * @param \PDOStatement $res
     *
     * @throws \chilimatic\lib\exception\DatabaseException
     * @throws \Exception
     *
     * @return \SplFixedArray
     */
    public function fetchNumericList(\PDOStatement $res)
    {
        $result = new \SplFixedArray($res->rowCount());
        try {
            while ($row = $res->fetch(\PDO::FETCH_NUM)) {
                $result[] = (array)$row;
            }
        } catch (DatabaseException $e) {
            throw $e;
        }

        return $result;
    }


    /**
     * fetches an object of the current row
     *
     * @param \PDOStatement $res
     *
     * @throws DatabaseException|\Exception
     * @return bool object
     */
    public function fetchObject(\PDOStatement $res)
    {
        return $res->fetchAll(\PDO::FETCH_OBJ);
    }


    /**
     * fetches an object list of associative arrays
     * which can be assigned to a specific key from the row as well
     *
     *
     * @param \PDOStatement $res
     * @param string $assign_by
     *
     * @throws DatabaseException|\Exception
     * @return array bool
     */
    public function fetchObjectList(\PDOStatement $res, $assign_by = null)
    {
        try {
            $result = [];

            while ($row = $res->fetch(\PDO::FETCH_OBJ)) {
                if (!empty($assign_by) && !is_array($assign_by)) {
                    $result[$row->$assign_by] = (object)$row;
                } elseif (!empty($assign_by) && is_array($assign_by)) {
                    $key = array();
                    foreach ($assign_by as $key_w) {
                        if (property_exists($row, $key_w)) {
                            $key[] = $row->$key_w;
                        }
                    }

                    if (empty($key)) {
                        $result[] = (object)$row;
                    } else {
                        // removes the first '- '
                        $key          = implode('-', $key);
                        $result[$key] = (object)$row;
                    }
                } else {
                    $result[] = (object)$row;
                }
            }
        } catch (DatabaseException $e) {
            throw $e;
        }

        return $result;
    }


    /**
     * fetches a string
     *
     * @param \PDOStatement $res
     *
     * @return string
     */
    public function fetchString(\PDOStatement $res)
    {
        return (string)$res->fetch(\PDO::FETCH_NUM)[0];
    }


    /**
     * free mysql resource
     *
     * @param \mysqli_result $res
     *
     * @throws \chilimatic\lib\exception\DatabaseException|\Exception
     * @return bool
     */
    public function free($res)
    {
        $res->free();

        return true;
    }

    /**
     *
     * @param string $query
     *
     * @return \PDOStatement
     * @throws \chilimatic\lib\exception\DatabaseException
     * @throws \Exception
     */
    public function prepare($query = '')
    {
        try {
            if (empty($query)) return false;

            if (empty($this->db)) {
                throw new DatabaseException(__METHOD__ . " No Database Connection opened", self::NO_RESSOURCE, self::SEVERITY_DEBUG, __FILE__, __LINE__);
            }

            return $this->db->getDbAdapter()->prepare($query);
        } catch (DatabaseException $e) {
            throw $e;
        }
    }


    /**
     * wrapper for the db_detail object the parameter should be a valid db
     * object
     *
     * @param \chilimatic\lib\database\sql\mysql\mysql|object $db
     *
     * @return MysqlDetail
     */
    public function getDatabaseDetail(MySQL $db = null)
    {

        if (empty($db) || empty($db->db)) {
            $db = $this;
        }

        return new MysqlDetail($db);
    }

    /**
     * @param string $query
     *
     * @return bool
     */
    public function execute($query)
    {
        return (bool)$this->getDb()->getDb()->exec($query);
    }

    /**
     * @return string
     */
    public function lastQuery()
    {
        return (string)$this->lastSql;
    }

    /**
     * @return bool
     */
    public function isConnected()
    {
        return $this->db->isConnected();
    }

    /**
     * @return resource
     */
    public function getDb()
    {
        return $this->db->getDbAdapter()->getResource();
    }

    /**
     * sends the query / update / insert statement to the database and returns
     * a resource of the table
     *
     * @throws DatabaseException
     *
     * @param $query string
     *
     * @return resource
     */
    public function query($query)
    {
        $db = $this->getDb();
        if (empty($query) || !$db) return false;


        // if the resource type is not a mysql link it should try to reconnect
        if (!$db->isConnected()) $db->ping();

        // the last sql query
        $this->lastSql = (string)$query;


        // if the master is down a select may query the slave but it should not
        // insert anything
        if ($this->slaveConnection === $this->getDb() && stripos(trim($query), 'SELECT') !== 0) {
            throw new DatabaseException(__METHOD__ . ' no Select on slave, abort !!', self::ERR_CONN, self::SEVERITY_LOG, __FILE__, __LINE__);
        }

        // tries execute the query and fetches the result
        $res             = $db->getDb()->query($query);
        $this->insert_id = $db->getDb()->insert_id;

        try {
            // in cases of errors
            if (!$res) {
                $this->error   = $db->getDb()->errorCode();
                $this->errorno = $db->getDb()->errorCode();
                throw new DatabaseException(__METHOD__ . "\nsql: $this->lastSql\nsql_error:$this->error\nsql_errorno:$this->errorno", self::ERR_EXEC, self::SEVERITY_LOG, __FILE__, __LINE__);
            }
        } catch (DatabaseException $e) {
            if (Config::get('use_exception') !== true) return false;
            throw $e;
        }

        // reset old errors
        $this->error         = '';
        $this->errorno       = 0;
        $this->affected_rows = $db->getDb()->affected_rows;

        return $res;
    }


    /**
     * rollback
     *
     * @return bool
     */
    public function rollback()
    {
        return $this->query('rollback;');
    }


    /**
     * selects a db
     *
     * @param \PDO $db
     * @param string $dbname
     *
     * @throws DatabaseException
     *
     * @return bool
     */
    public function selectDb($dbname, \PDO $db)
    {
        if (empty($db) || empty($dbname)) return false;
        try {
            if (!$db->query("USE `$dbname`")) {
                $this->error   = (string)$db->errorInfo();
                $this->errorno = (int)$db->errorCode();
                throw new DatabaseException(__METHOD__ . "\nsql_error: $this->error\nsql_errorno:$this->errorno", self::ERR_EXEC, self::SEVERITY_LOG, __FILE__, __LINE__);
            }
        } catch (DatabaseException $e) {
            throw $e;
        }

        return true;
    }


    /**
     * sets a different charset
     *
     * @param string $charset
     * @param \PDO $db
     *
     * @throws DatabaseException|\Exception
     * @return bool
     */
    public function setCharset($charset = 'UTF8', \PDO $db)
    {
        try {
            if (empty($charset) || !$db) {
                throw new DatabaseException(__METHOD__ . "\ncharset:$charset\nressource:" . print_r($this->db, true), self::ERR_EXEC, self::SEVERITY_LOG, __FILE__, __LINE__);
            }

            $this->mysqli_client_encoding = (string)$charset;

            if ($this->db_detail) {
                $this->db_detail->character_set_client = (string)$charset;
            }
        } catch (DatabaseException $e) {
            throw $e;
        }

        $db->exec("set names $charset");

        return true;
    }


    /**
     * if there should be transactions
     *
     * @return bool
     */
    public function beginTransaction()
    {
        $this->getDb()->beginTransaction();

        return true;
    }

    /**
     * if there should be transactions
     *
     * @return bool
     */
    public function endTransaction()
    {
        $this->getDb()->commit();

        return true;
    }

    /**
     * destructor
     *
     * @return void
     */
    public function __destruct()
    {
        if (is_resource($this->masterConnection)) mysqli_close($this->masterConnection->getDb());
        if (is_resource($this->slaveConnection)) mysqli_close($this->slaveConnection->getDb());
    }
}