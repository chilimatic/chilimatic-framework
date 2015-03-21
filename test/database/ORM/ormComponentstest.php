<?php
/**
 *
 * @author j
 * Date: 12/30/14
 * Time: 2:21 PM
 *
 * File: menucontroller.php
 */
require_once '../../../app/general/init.php';

class ORM_Test extends PHPUnit_Framework_TestCase {

    public function getDI() {
        \chilimatic\lib\di\ClosureFactory::getInstance(
            realpath('../../../app/config/serviceCollection.php')
        );
    }

    /**
     * @return \chilimatic\lib\database\mysql\MysqlConnection
     */
    protected function getConnection() {
        return $con = new \chilimatic\lib\database\mysql\MysqlConnection(
            [
                'host' => 'localhost',
                'username' => 'root',
                'password' => '$karpunk1',
                'database' => 'chilimatic',
            ]
        );
    }


    /**
     * @test
     */
    public function mysqlConnectionInstance() {
        $this->assertInstanceOf('\chilimatic\lib\database\mysql\MysqlConnection', $this->getConnection());
    }

    /**
     * @test
     */
    public function mysqlDatabase() {
        $db = new \chilimatic\lib\database\mysql\Mysql($this->getConnection());
        $this->assertInstanceOf('\chilimatic\lib\database\mysql\Mysql', $db);
    }

    public function testMysqlDatabaseConnected() {
        $db = new \chilimatic\lib\database\mysql\Mysql($this->getConnection());
        $this->assertTrue($db->isConnected());
    }

    public function testORMEntityManager() {
        $db = new \chilimatic\lib\database\mysql\Mysql($this->getConnection());
        $this->assertInstanceOf('\chilimatic\lib\database\ORM\EntityManager', new \chilimatic\lib\database\orm\EntityManager($db));
    }

    public function testORMMysqlQueryBuilder() {
        $this->assertInstanceOf('\chilimatic\lib\database\ORM\MysqlQueryBuilder', new \chilimatic\lib\database\orm\MysqlQueryBuilder());
    }

    public function testORMMysqlQueryBuilderFindOneBy() {
        /**
         * @var \chilimatic\lib\database\orm\entitymanager $em
         */
        $em = \chilimatic\lib\di\Factory::getInstance()->get('entity-manager' , [
            'host' => 'localhost',
            'username' => 'root',
            'password' => '$karpunk1',
            'database' => 'chilimatic',
        ]);

        $this->assertInstanceOf('\chilimatic\lib\database\ORM\AbstractModel', $em->findOneBy(new \chilimatic\app\model\menu(), []));
    }

    public function testORMMysqlQueryBuilderFindBy() {
        $em = \chilimatic\lib\di\Factory::getInstance()->get('entity-manager' , [
            'host' => 'localhost',
            'username' => 'root',
            'password' => '$karpunk1',
            'database' => 'chilimatic',
        ]);

        $this->assertInstanceOf('\chilimatic\lib\database\ORM\EntityObjectStorage', $em->findBy(new \chilimatic\app\model\menu(), []));
    }

    public function testORMMysqlQueryBuilderGenerateForModel() {
        $em = \chilimatic\lib\di\Factory::getInstance()->get('entity-manager' , [
            'host' => 'localhost',
            'username' => 'root',
            'password' => '$karpunk1',
            'database' => 'chilimatic',
        ]);
        $expectedResult = "SELECT * FROM `menu`";
        //$this->assertEquals($expectedResult, $em->generateForModel(new \chilimatic\app\model\menu()));
    }
}