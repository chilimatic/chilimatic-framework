<?php
/**
 *
 * @author j
 * Date: 2/18/15
 * Time: 10:58 PM
 *
 * File: psqlquerybuildertest.php
 */

require_once '../../../app/general/init.php';
\chilimatic\lib\di\Factory::getInstance(
    realpath('../../../app/config/serviceCollection.php')
);


class PSQL_Query_Builder_Test extends PHPUnit_Framework_TestCase{

    public function testAbstraction() {
        $queryBuilder = new \chilimatic\lib\database\orm\PgsqlQueryBuilder();
        $this->assertInstanceOf('\chilimatic\lib\database\orm\AbstractQueryBuilder', $queryBuilder);
    }

    public function testConnection() {

    }
}