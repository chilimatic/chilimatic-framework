<?php
/**
 *
 * @author j
 * Date: 4/22/15
 * Time: 12:59 AM
 *
 * File: MySQLSelectStrategy.php
 */

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
class MySQLSelectStrategy extends AbstractStrategy
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
            $this->generateSelectClause($this->tableData),
            $this->generateWhereClause($this->modelData),
        ]);
    }
}