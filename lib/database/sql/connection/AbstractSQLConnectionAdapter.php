<?php
namespace chilimatic\lib\database\sql\connection;

use chilimatic\lib\database\connection\IDatabaseConnectionAdapter;
use chilimatic\lib\database\connection\IDatabaseConnectionSettings;

/**
 * Class AbstractSqlConnectionAdapter
 *
 * @package chilimatic\lib\database\sql\connection
 */
abstract class AbstractSQLConnectionAdapter implements IDatabaseConnectionAdapter
{

    /**
     * index definitiosn for the options array
     *
     * @var string
     */
    const RESULT_TYPE_INDEX = 'type';
    const RESULT_MODE_INDEX = 'result_mode';
    const RESULT_RETURN_MODE_INDEX = 'return_mode';

    /**
     * possible return types
     *
     * @var string
     */
    const RETURN_TYPE_OBJ = 'object';
    const RETURN_TYPE_ASSOC = 'assoc';
    const RETURN_TYPE_NUM = 'num';
    const RETURN_TYPE_MYSQLI_ROW = 'row';
    const RETURN_TYPE_BOTH = 'both';

    /**
     * return value types
     *
     * @var string
     */
    const RESULT_TYPE_GENERATOR = 'generator';
    const RESULT_TYPE_ASSOC_ARRAY = 'assoc_array';
    const RESULT_TYPE_NUM_ARRAY = 'num_array';
    const RESULT_TYPE_OBJECT_ARRAY = 'obj_array';
    const RESULT_TYPE_ITERATOR = 'iterator';

    /**
     * @var mixed
     */
    private $result;

    /**
     * @var bool
     */
    private $socket = false;

    /**
     * @var \PDO|\Mysqli
     */
    private $resource;

    /**
     * @var IDatabaseConnectionSettings
     */
    private $connectionSettings;

    /**
     * @param IDatabaseConnectionSettings $connectionSettings
     */
    public function __construct(IDatabaseConnectionSettings $connectionSettings) {
        $this->setConnectionSettings($connectionSettings);
        $this->initResource();
    }

    /**
     * @return mixed
     */
    abstract public function initResource();


    /**
     * checks if the current connection is pingable
     *
     * @return bool
     */
    abstract public function ping();

    /**
     * executes a query direct in the connection
     *
     * @param string $sql
     * @param array $options
     *
     * @return mixed
     */
    abstract public function query($sql, $options = []);

    /**
     * prepares a statement optional are values
     *
     * @param $sql
     * @param array $options
     *
     * @return mixed
     */
    abstract public function prepare($sql, $options = []);

    /**
     * excutes the query / inserts an deletes
     *
     * @param string $sql
     *
     * @return bool
     */
    abstract public function execute($sql);

    /**
     * starts an transaction
     *
     * @return mixed
     */
    abstract public function beginTransaction();

    /**
     * @return mixed
     */
    abstract public function inTransaction();

    /**
     * rollbacks a transaction
     *
     * @return mixed
     */
    abstract public function rollback();

    /**
     * commits the transaction
     *
     * @return mixed
     */
    abstract public function commit();

    /**
     * @return mixed
     */
    abstract public function getErrorCode();

    /**
     * generalized for all mysql adapters
     * @todo mysqli adapter function that wraps the interface needs to be built
     *
     * @return array
     *
     * 0 SQLSTATE error code (a five characters alphanumeric identifier defined in the ANSI SQL standard).
     * 1 Driver-specific error code.
     * 2 Driver-specific error message.
     *
     */
    abstract public function getErrorInfo();



    /**
     * @return mixed
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @param mixed $resource
     *
     * @return $this
     */
    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getConnectionSettings()
    {
        return $this->connectionSettings;
    }

    /**
     * @param mixed $connectionSettings
     *
     * @return $this
     */
    public function setConnectionSettings(IDatabaseConnectionSettings $connectionSettings)
    {
        $this->connectionSettings = $connectionSettings;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isSocket()
    {
        return (bool) $this->socket;
    }

    /**
     * @param boolean $socket
     *
     * @return $this
     */
    public function setSocket($socket)
    {
        $this->socket = (bool) $socket;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param mixed $result
     *
     * @return $this
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }


}