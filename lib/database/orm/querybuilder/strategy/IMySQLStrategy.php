<?php
/**
 *
 * @author j
 * Date: 4/21/15
 * Time: 11:38 PM
 *
 * File: IMySQLStrategy.php
 */

namespace chilimatic\lib\database\orm\querybuilder\strategy;

use chilimatic\lib\database\orm\querybuilder\meta\MySQLTableData;

Interface IMySQLStrategy
{
    /**
     * @param MySQLTableData $tableData
     * @param array $modelData
     */
    public function __construct(MySQLTableData $tableData = null, array $modelData = null);

    /**
     * @return mixed
     */
    public function __toString();

    /**
     * @return string
     */
    public function generateSQLStatement();
}