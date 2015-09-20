<?php
/**
 *
 * @author j
 * Date: 4/21/15
 * Time: 11:40 PM
 *
 * File: AbstractStrategy.php
 */

namespace chilimatic\lib\database\sql\querybuilder\strategy;

use chilimatic\lib\database\sql\querybuilder\meta\AbstractSQLTableData;

/**
 * Class AbstractStrategy
 *
 * @package chilimatic\lib\database\sql\querybuilder\strategy
 */
abstract class AbstractStrategy implements ISQLStrategy
{

    /**
     * @var AbstractSQLTableData
     */
    protected $tableData;

    /**
     * @var array
     */
    protected $modelData = [];

    /**
     * @param AbstractSQLTableData $tableData
     */
    public function __construct(AbstractSQLTableData $tableData = null, array $modelData = null) {
        $this->tableData = $tableData;
        $this->modelData = $modelData;
    }

    /**
     * @return array
     */
    public function generateFieldList()
    {
        $fieldList = [];
        foreach ($this->modelData as $column) {
            $fieldList[] = $column['name'];
        }

        return $fieldList;
    }

    /**
     * @return array
     */
    public function generateKeyList() {
        return $this->tableData->getPrimaryKey();
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->generateSQLStatement();
    }

    /**
     * @return mixed
     */
    public function getModelData()
    {
        return $this->modelData;
    }

    /**
     * @param mixed $modelData
     *
     * @return $this
     */
    public function setModelData($modelData)
    {
        $this->modelData = $modelData;

        return $this;
    }

    /**
     * @return AbstractSQLTableData
     */
    public function getTableData()
    {
        return $this->tableData;
    }

    /**
     * @param AbstractSQLTableData $tableData
     *
     * @return $this
     */
    public function setTableData($tableData)
    {
        $this->tableData = $tableData;

        return $this;
    }

}