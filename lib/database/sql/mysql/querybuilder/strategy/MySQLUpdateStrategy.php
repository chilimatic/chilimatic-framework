<?php
/**
 *
 * @author j
 * Date: 4/21/15
 * Time: 11:50 PM
 *
 * File: MySQLUpdateStrategy.php
 */
namespace chilimatic\lib\database\sql\mysql\querybuilder\strategy;

/**
 * Class MySQLUpdateStrategy
 *
 * @package chilimatic\lib\database\orm\querybuilder\strategy
 */
class MySQLUpdateStrategy extends AbstractStrategy
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
            $this->generateUpdateClause($this->tableData),
            $this->generateSetClause($this->generateFieldList()),
            $this->generateWhereClause($this->generateKeyList()),
        ]);
    }
}