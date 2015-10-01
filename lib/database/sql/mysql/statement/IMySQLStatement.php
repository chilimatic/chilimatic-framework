<?php
namespace chilimatic\lib\database\sql\mysql\statement;

use chilimatic\lib\database\connection\IDatabaseConnectionAdapter;

/**
 *
 * @author j
 * Date: 9/29/15
 * Time: 8:49 PM
 *
 * File: IMysqlStatement.php
 */

/**
 * Interface IMySQLStatement
 *
 * @package chilimatic\lib\database\sql\mysql\statement
 */
interface IMySQLStatement
{
    /**
     * binary values for the return types
     */
    const RETURN_TYPE_GENERATOR = 0b0001; // 1
    const RETURN_TYPE_ASSOC     = 0b0010; // 2
    const RETURN_TYPE_NUM       = 0b0011; // 3
    const RETURN_TYPE_BOTH      = 0b0100; // 4
    const RETURN_TYPE_OBJECT    = 0b0101; // 5



    /**
     * @param IDatabaseConnectionAdapter $connectionAdapter
     */
    public function __construct(IDatabaseConnectionAdapter $connectionAdapter);


    /**
     * @param string $name
     * @param mixed $var [reference]
     * @param array $options
     *
     * @return mixed
     */
    public function bindParam($name, &$var, array $options = []);

    /**
     * resets / rewinds the cursor position
     *
     * @return bool
     */
    public function rewind();

    /**
     * @return array
     */
    public function getErrorInfo();

    /**
     * @return int
     */
    public function getErrorCode();

    /**
     * @return int
     */
    public function rowCount();

    /**
     * @return int
     */
    public function columnCount();

    /**
     * @return bool
     */
    public function execute();

    /**
     * @param array $options
     *
     * @return mixed
     */
    public function setOptions(array $options = []);

    /**
     * @param int $postion
     *
     * @return array
     */
    public function fetchColumn($postion);

    /**
     * @return int
     */
    public function getAffectedRows();

    /**
     * @return \stdClass
     */
    public function fetchObject();

    /**
     * @param $resultType
     *
     * @return mixed
     */
    public function fetchAll($resultType);


    /**
     * @return int
     */
    public function getInsertId();
}