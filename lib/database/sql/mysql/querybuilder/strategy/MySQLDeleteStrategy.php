<?php
namespace chilimatic\lib\database\sql\mysql\querybuilder\strategy;

/**
 *
 * @author j
 * Date: 7/14/15
 * Time: 11:25 PM
 *
 * File: MySQLDeleteStrategy.php
 */

class MySQLDeleteStrategy extends AbstractStrategy {
    /**
     * trait for generation of code
     */
    use GeneratorTrait;

    /**
     * @return string
     */
    public function generateSQLStatement()
    {
        return implode(' ', [
            $this->generateDeleteClause($this->tableData),
            $this->generateWhereClause($this->generateKeyList())
        ]);
    }
}