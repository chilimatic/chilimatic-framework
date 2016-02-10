<?php
namespace chilimatic\lib\database\mock\adapter;

/**
 *
 * @author j
 * Date: 9/27/15
 * Time: 4:58 PM
 *
 * File: MockMysqlConnectionAdapter.php
 */
use chilimatic\lib\database\sql\connection\AbstractSQLConnectionAdapter;

/**
 * Class MockMysqlConnectionAdapter
 *
 * @package chilimatic\lib\database\mock\adapter\mock
 */
class MockMysqlConnectionAdapter extends AbstractSQLConnectionAdapter
{
    /**
     * @var bool
     */
    private $inTransaction = false;


    public function initResource()
    {
        return true;
    }

    public function ping()
    {
        return true;
    }

    public function query($sql, $options = [])
    {
        // TODO: Implement query() method.
    }

    /**
     * @param $sql
     * @param array $options
     *
     * @return bool
     */
    public function prepare($sql, $options = [])
    {
        return true;
    }

    /**
     * @param string $sql
     *
     * @return bool
     */
    public function execute($sql)
    {
        return true;
    }

    /**
     * @return bool
     */
    public function beginTransaction()
    {
        $this->inTransaction = true;
        return true;
    }

    /**
     * @return bool
     */
    public function inTransaction()
    {
        return $this->inTransaction;
    }

    /**
     * @return bool
     */
    public function rollback()
    {
        $this->inTransaction = false;
        return true;
    }

    /**
     * @return bool
     */
    public function commit()
    {
        $this->inTransaction = false;
        return true;
    }

    /**
     * @return int
     */
    public function getErrorCode()
    {
        // return an integer value
        return 12345;
    }

    /**
     * @return array
     */
    public function getErrorInfo()
    {
        return [];
    }

    /**
     * @return mixed
     */
    public function getLastInsertId()
    {
        return 12;
    }

}