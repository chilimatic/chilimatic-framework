<?php
namespace chilimatic\lib\database\sql\querybuilder\strategy;

use chilimatic\lib\database\sql\querybuilder\meta\AbstractSQLTableData;
use chilimatic\lib\interfaces\IFlyWeightTransformer;

/**
 * Class GeneratorTrait
 *
 * @package chilimatic\lib\database\sql\querybuilder\strategy
 */
Trait GeneratorTrait {

    /**
     * @var IFlyWeightTransformer
     */
    protected $transformer;

    /**
     * @param IFlyWeightTransformer $transformer
     *
     * @return $this
     */
    public function setTransformer(IFlyWeightTransformer $transformer) {
        $this->transformer = $transformer;
        return $this;
    }

    /**
     * @return IFlyWeightTransformer|null
     */
    public function getTransformer() {
        return $this->transformer;
    }


    /**
     * @param AbstractSQLTableData $tableData
     *
     * @return string
     */
    public function generateColumList(AbstractSQLTableData $tableData)
    {
        return implode(',', $tableData->getColumnNamesWithPrefix());
    }

    /**
     * @param AbstractSQLTableData $tableData
     *
     * @return string
     */
    public function generateSelectClause(AbstractSQLTableData $tableData)
    {
        return ("SELECT " . $this->generateColumList($tableData) . " FROM " . $tableData->getTableNameWithPrefix());
    }

    /**
     * @param AbstractSQLTableData $tableData
     *
     * @return string
     */
    public function generateInsertClause(AbstractSQLTableData $tableData) {
        return "INSERT INTO " . $tableData->getTableName();
    }

    /**
     * @param AbstractSQLTableData $tableData
     *
     * @return string
     */
    public function generateDeleteClause(AbstractSQLTableData $tableData) {
        return "DELETE FROM " . $tableData->getTableName();
    }

    /**
     * @param AbstractSQLTableData $tableData
     *
     * @return string
     */
    public function generateUpdateClause(AbstractSQLTableData $tableData) {
        return "UPDATE " . $tableData->getTableName();
    }

    /**
     * @param $fieldList
     *
     * @return string
     */
    public function generateSetClause($fieldList) {
        return "SET " .  implode(', ', $this->generatePredicateList($fieldList));
    }

    /**
     * @param $fieldList
     *
     * @return string
     */
    public function generateWhereClause($fieldList) {
        $predicateList = $this->generatePredicateList($fieldList);

        return ($predicateList) ? "WHERE " . implode(' AND ', $predicateList) : '';
    }

    /**
     * @param $fieldList
     *
     * @return string
     */
    public function generatePredicateList($fieldList)
    {
        if (!$this->transformer) {
            throw new \LogicException('Missing transformer for generic ids in SQL!');
        }

        $predicateList = [];
        foreach ($fieldList as $name) {
            $predicateList[] = "$name = " . $this->transformer->transform($name);
        }

        return $predicateList;
    }
}