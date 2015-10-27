<?php
use chilimatic\lib\database\sql\mysql\connection\MySQLConnection;
use chilimatic\lib\database\sql\mysql\connection\MySQLConnectionSettings;

/**
 *
 * @author j
 * Date: 9/25/15
 * Time: 2:27 PM
 *
 * File: MysqlConnectionTest.php
 */
class MysqlConnectionTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        if (!extension_loaded('mysqli')) {
            $this->markTestSkipped('The MySQLi extension is not available.');
        }
    }

    /**
     * @return MySQLConnectionSettings
     */
    public function generateMysqlSettings() {
        return new MySQLConnectionSettings(
            '127.0.0.1',
            'unittest',
            'test123123',
            'unittest',
            null,
            $settingList = []
        );
    }

    /**
     * @test
     */
    public function implementsISqlConnection() {
        $con = new MySQLConnection($this->generateMysqlSettings(), MySQLConnection::CONNECTION_MOCK);
        $this->assertInstanceOf('\chilimatic\lib\database\sql\connection\ISqlConnection', $con);
    }


    /**
     * @test
     */
    public function extendsAbstractSqlConnection() {
        $con = new MySQLConnection($this->generateMysqlSettings(), MySQLConnection::CONNECTION_MOCK);
        $this->assertInstanceOf('\chilimatic\lib\database\sql\connection\AbstractSqlConnection', $con);
    }


    /**
     * @test
     */
    public function checkIfTheConnectionIsActive() {
        $con = new MySQLConnection($this->generateMysqlSettings(), MySQLConnection::CONNECTION_MOCK);
        $this->assertFalse($con->isActive());
    }

    /**
     * @test
     */
    public function checkIfReconnectIsImpossibleIfTheLimitIsSetToTwoAndThreeRetries() {
        $con = new MySQLConnection($this->generateMysqlSettings(), MySQLConnection::CONNECTION_MOCK);

        $con->setMaxReconnects(2);
        $con->increaseReconnectCount();
        $con->increaseReconnectCount();
        $con->increaseReconnectCount();

        $this->assertFalse($con->reconnect());
    }


    /**
     * @test
     */
    public function checkIfReconnectIsPossibleIfTheLimitIsSetToFiveAndThreeRetries() {
        $con = new MySQLConnection($this->generateMysqlSettings(), MySQLConnection::CONNECTION_MOCK);

        $con->setMaxReconnects(5);
        $con->increaseReconnectCount();
        $con->increaseReconnectCount();
        $con->increaseReconnectCount();

        $this->assertTrue($con->reconnect());
    }

    /**
     * @test
     */
    public function checkIfPingIsWorking() {
        $con = new MySQLConnection($this->generateMysqlSettings(), MySQLConnection::CONNECTION_MOCK);

        $this->assertTrue($con->ping());
    }

    /**
     * @test
     */
    public function checkIfValidatorGeneratorIsWorking() {
        $con = new MySQLConnection($this->generateMysqlSettings(), MySQLConnection::CONNECTION_MOCK);

        $this->assertFalse($con->connectionSettingsAreValid());
    }

    /**
     * @test
     */
    public function connectToARealDatabaseWithThePdoAdapter() {
        try {
            $con = new MySQLConnection($this->generateMysqlSettings(), MySQLConnection::CONNECTION_PDO);
        } catch (\Exception $e) { // will only work with >=php7
            $this->fail($e->getMessage());
        }

        $this->assertTrue($con->isConnected());
    }

    /**
     * @test
     *
     * this will fail if mariadb is used because of library inconsistencies
     */
    public function connectToARealDatabaseWithTheMysqliAdapter() {
        try {
            $con = new MySQLConnection($this->generateMysqlSettings(), MySQLConnection::CONNECTION_MYSQLI);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->assertTrue($con->isConnected());
    }

}