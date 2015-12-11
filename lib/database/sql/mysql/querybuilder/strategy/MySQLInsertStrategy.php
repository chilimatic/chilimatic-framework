<?php
/**
 *
 * @author j
 * Date: 4/21/15
 * Time: 9:53 PM
 *
 * File: MySQLInsertStrategy.php
 */
namespace chilimatic\lib\database\sql\mysql\querybuilder\strategy;
use chilimatic\lib\database\sql\querybuilder\strategy\AbstractStrategy;
use chilimatic\lib\database\sql\querybuilder\strategy\GeneratorTrait;
/**
 * Class MySQLInsertStrategy
 *
 * @package chilimatic\lib\database\orm\querybuilder\strategy
 */
class MySQLInsertStrategy extends AbstractStrategy
{
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
            $this->generateInsertClause($this->tableData),
            $this->generateSetClause($this->generateFieldList()),
            $this->generateWhereClause($this->generateKeyList()),
        ]);
    }
}