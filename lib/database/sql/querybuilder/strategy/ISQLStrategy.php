<?php
namespace chilimatic\lib\database\sql\querybuilder\strategy;
use chilimatic\lib\database\sql\querybuilder\meta\AbstractSQLTableData;

/**
 * Interface ISQLStrategy
 *
 * @package chilimatic\lib\database\mysql\querybuilder\strategy
 */
Interface ISQLStrategy
{
    /**
     * @param AbstractSQLTableData $tableData
     * @param array $modelData
     */
    public function __construct(AbstractSQLTableData $tableData = null, array $modelData = null);

    /**
     * @return mixed
     */
    public function __toString();

    /**
     * @return string
     */
    public function generateSQLStatement();
}