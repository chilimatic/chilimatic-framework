<?php
namespace chilimatic\lib\database\orm\querybuilder\strategy;
use chilimatic\lib\database\orm\querybuilder\meta\MySQLTableData;

/**
 *
 * @author j
 * Date: 4/21/15
 * Time: 9:54 PM
 *
 * File: GeneratorTrait.php
 */
Trait GeneratorTrait {

    /**
     * @param MySQLTableData $tableData
     *
     * @return string
     */
    public function generateColumList(MySQLTableData $tableData)
    {
        return implode(',', $tableData->getColumnNamesWithPrefix());
    }

    /**
     * @param MySQLTableData $tableData
     *
     * @return string
     */
    public function generateSelectClause(MySQLTableData $tableData)
    {
        return ("SELECT " . $this->generateColumList($tableData) . " FROM " . $tableData->getTableNameWithPrefix());
    }

    /**
     * @param MySQLTableData $tableData
     *
     * @return string
     */
    public function generateInsertClause(MySQLTableData $tableData) {
        return "INSERT INTO " . $tableData->getTableNameWithPrefix();
    }

    /**
     * @param MySQLTableData $tableData
     *
     * @return string
     */
    public function generateUpdateClause(MySQLTableData $tableData) {
        return "UPDATE ". $tableData->getTableNameWithPrefix();
    }

    /**
     * @param $fieldList
     *
     * @return string
     */
    public function generateSetClause($fieldList) {
        return "SET " . $this->generatePredicateList($fieldList);
    }

    /**
     * @param $fieldList
     *
     * @return string
     */
    public function generateWhereClause($fieldList) {
        return "WHERE " . $this->generatePredicateList($fieldList);
    }

    /**
     * @param $fieldList
     *
     * @return string
     */
    public function generatePredicateList($fieldList) {
        $predicateList = [];
        foreach ($fieldList as $name) {
            $predicateList[] = "`$name` = :$name";
        }

        return implode(", ", $predicateList);
    }
}