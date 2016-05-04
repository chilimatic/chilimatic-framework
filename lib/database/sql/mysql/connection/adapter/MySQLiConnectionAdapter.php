<?php
namespace chilimatic\lib\database\sql\mysql\connection\adapter;

use chilimatic\lib\database\sql\connection\AbstractSQLConnectionAdapter;

/**
 * Class MySQLiConnectionAdapter
 *
 * @package chilimatic\lib\database\sql\mysql\connection\adapter
 */
class MySQLiConnectionAdapter extends AbstractSQLConnectionAdapter
{
    /**
     * Connection Trait
     */
    use MySQLConnectionTypeTrait;

    /**
     * @var bool
     */
    private $inTransaction = false;


    public function initResource()
    {
        $this->setResource(
            new \Mysqli(
                $this->getConnectionSettings()->getHost(),
                $this->getConnectionSettings()->getUsername(),
                $this->getConnectionSettings()->getPassword(),
                $this->getConnectionSettings()->getPort(),
                $this->getConnectionSettings()->getDatabase(),
                $this->isSocket()
            )
        );
    }

    /**
     * @return bool
     */
    public function ping()
    {
        return $this->getResource()->ping();
    }

    /**
     * @return \Generator
     */
    public function rowGenerator(\mysqli_result $result, $returnMode)
    {
        switch ($returnMode) {
            case self::RETURN_TYPE_ASSOC:
                yield $result->fetch_assoc();
                break;
            case self::RETURN_TYPE_NUM:
                yield $result->fetch_array(MYSQLI_NUM);
                break;
            case self::RETURN_TYPE_BOTH:
                yield $result->fetch_array(MYSQLI_BOTH);
                break;
            case self::RETURN_TYPE_MYSQLI_ROW:
                yield $result->fetch_row();
                break;
            case self::RETURN_TYPE_OBJ:
                yield $result->fetch_object();
                break;
            default:
                yield $result->fetch_assoc();
                break;
        }
    }


    /**
     * @param string $sql
     * @param array $options
     *
     * @return array|\ArrayIterator|bool|\Generator
     */
    public function query($sql, $options = [])
    {
        $resultMode = isset($options[self::RESULT_MODE_INDEX]) ? (int) $options[self::RESULT_MODE_INDEX] : MYSQLI_STORE_RESULT;
        /**
         * the RETURN_MODE_INDEX should contain one of the following constants http://php.net/manual/en/mysqli-result.fetch-all.php
         */
        $returnMode = isset($options[self::RESULT_RETURN_MODE_INDEX]) ? (int) $options[self::RESULT_RETURN_MODE_INDEX] : MYSQLI_BOTH;
        try {
            /**
             * @var \mysqli_result $result
             */
            $result = $this->getResource()->query($sql, $resultMode);
            switch($options[self::RESULT_TYPE_INDEX])
            {
                case self::RESULT_TYPE_GENERATOR:
                    return $this->rowGenerator($result, $returnMode);
                    break;

                case self::RESULT_TYPE_NUM_ARRAY:
                case self::RESULT_TYPE_ASSOC_ARRAY;
                    return $result->fetch_all($returnMode);
                    break;

                case self::RESULT_TYPE_ITERATOR:
                    return new \ArrayIterator($result->fetch_all($returnMode));
                    break;

                case self::RESULT_TYPE_OBJECT_ARRAY:
                    $gen = $this->rowGenerator($result, $returnMode);
                    $result = [];
                    foreach ($gen as $row) {
                        $result[] = $row;
                    }
                    return $result;

                default:
                    return $this->getResult()->fetch_all();
                    break;
            }
        } catch (\mysqli_sql_exception $e) {
            return false;
        }
    }

    /**
     * prepares a query
     *
     * @param string $sql
     * @param array $options
     *
     * @return AbstractStatement $statement
     */
    public function prepare($sql, $options = [])
    {
        return new MysqlIStatement($this->getResource(), $sql, $options);
    }

    /**
     * @param string $sql
     *
     * @return bool
     */
    public function execute($sql)
    {
        return $this->getResource()->real_query($sql);
    }

    /**
     * @return bool
     */
    public function beginTransaction()
    {
        $this->inTransaction = true;
        return $this->getResource()->begin_transaction();
    }

    /**
     * @return bool
     */
    public function rollback()
    {
        $this->inTransaction = false;
        return $this->getResource()->rollback();
    }

    /**
     * @return bool
     */
    public function commit()
    {
        $this->inTransaction = false;
        return $this->getResource()->commit();
    }

    /**
     * @return bool
     */
    public function inTransaction()
    {
        return (bool) $this->inTransaction;
    }

    /**
     * @return int
     */
    public function getErrorCode()
    {
        return (int) $this->getResource()->errorno;
    }

    /**
     * @return mixed
     */
    public function getErrorInfo()
    {
        return $this->getResource()->error_list;
    }

    /**
     * @return mixed
     */
    public function getLastInsertId()
    {
        return $this->getResource()->insert_id;
    }

    /**
     * @return int
     */
    public function getAffectedRows($stmt = null)
    {
        return $this->getResource()->affected_rows;
    }

}