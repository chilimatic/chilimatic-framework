<?php
use chilimatic\lib\database\sql\mysql\connection\MysqlConnection;
use chilimatic\lib\database\sql\mysql\connection\MysqlConnectionSettings;

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

    /**
     * @return MysqlConnectionSettings
     */
    public function generateMysqlSettings() {
        return new MysqlConnectionSettings(
            'localhost',
            '',
            '',
            null,
            null,
            $settingList = []
        );
    }

    /**
     * @test
     */
    public function implementsISqlConnection() {
        $con = new MysqlConnection($this->generateMysqlSettings(), MysqlConnection::CONNECTION_MOCK);
        $this->assertInstanceOf('\chilimatic\lib\database\sql\connection\ISqlConnection', $con);
    }


    /**
     * @test
     */
    public function extendsAbstractSqlConnection() {
        $con = new MysqlConnection($this->generateMysqlSettings(), MysqlConnection::CONNECTION_MOCK);
        $this->assertInstanceOf('\chilimatic\lib\database\sql\connection\AbstractSqlConnection', $con);
    }


    /**
     * @test
     */
    public function checkIfTheConnectionIsActive() {
        $con = new MysqlConnection($this->generateMysqlSettings(), MysqlConnection::CONNECTION_MOCK);
        $this->assertFalse($con->isActive());
    }

    /**
     * @test
     */
    public function checkIfReconnectIsImpossibleIfTheLimitIsSetToTwoAndThreeRetries() {
        $con = new MysqlConnection($this->generateMysqlSettings(), MysqlConnection::CONNECTION_MOCK);

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
        $con = new MysqlConnection($this->generateMysqlSettings(), MysqlConnection::CONNECTION_MOCK);

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
        $con = new MysqlConnection($this->generateMysqlSettings(), MysqlConnection::CONNECTION_MOCK);

        $this->assertTrue($con->ping());
    }

    /**
     * @test
     */
    public function checkIfValidatorGeneratorIsWorking() {
        $con = new MysqlConnection($this->generateMysqlSettings(), MysqlConnection::CONNECTION_MOCK);

        $this->assertFalse($con->connectionSettingsAreValid());
    }


}