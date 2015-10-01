<?php
use chilimatic\lib\database\sql\mysql\connection\MySQLConnection;
use chilimatic\lib\database\sql\mysql\connection\MySQLConnectionSettings;
use chilimatic\lib\database\sql\mysql\connection\MySQLConnectionStorage;

/**
 *
 * @author j
 * Date: 9/21/15
 * Time: 12:11 AM
 *
 * File: MySqlConnectionStorageTest.php
 */

class MysqlConnectionStorageTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function checkIfMySqlConnectionStorageImplementsTheStorageInterface() {
        $storage =  new MySQLConnectionStorage();

        $this->assertInstanceOf('\chilimatic\lib\database\sql\connection\ISqlConnectionStorage',$storage);
    }

    /**
     * @test
     */
    public function checkIfAMySqlConnectionCanBeAddedBySettingsToTheMysqlConnectionStorage() {
        $storage = new MySQLConnectionStorage();
        $setting = new MySQLConnectionSettings('','','');
        try {
            $storage->addConnectionBySetting($setting, MySQLConnection::CONNECTION_MOCK);
        } catch(PDOException $e) {
            // do nothing
        }


        $this->assertEquals($setting, $storage->getConnectionByPosition(0)->getDbAdapter()->getConnectionSettings());
    }

    /**
     * @test
     */
    public function checkIfASpecificMysqlConnectionSettingsCanBeRetrieved() {
        $storage = new MySQLConnectionStorage();
        $setting = new MySQLConnectionSettings('','','');

        $storage->addConnectionBySetting(
            new MySQLConnectionSettings('','',''),
            MySQLConnection::CONNECTION_MOCK
        );

        $storage->addConnectionBySetting(
            $setting,
            MySQLConnection::CONNECTION_MOCK
        );

        $this->assertEquals($setting, $storage->getConnectionByPosition(1)->getDbAdapter()->getConnectionSettings());
    }


    /**
     * @test
     */
    public function checkIfAMysqlConnectionCanBeAdded() {
        $storage = new MySQLConnectionStorage();
        $con = new MySQLConnection(
            new MySQLConnectionSettings('','',''),
            MySQLConnection::CONNECTION_MOCK
        );

        $storage->addConnection($con);

        $this->assertEquals($con, $storage->getConnectionByPosition(0));
    }


    /**
     * @test
     */
    public function checkIfAMysqlConnectionCanBeFound() {
        $storage = new MySQLConnectionStorage();
        $con = new MySQLConnection(
            new MySQLConnectionSettings('','',''),
            MySQLConnection::CONNECTION_MOCK
        );

        $storage->addConnection($con);

        $this->assertTrue($storage->findConnection($con));
    }

    /**
     * @test
     */
    public function checkIfAMysqlConnectionCanBeRemoved() {
        $storage = new MySQLConnectionStorage();
        $con = new MySQLConnection(
            new MySQLConnectionSettings('','',''),
            MySQLConnection::CONNECTION_MOCK
        );

        $storage->addConnection($con);
        $storage->removeConnection($con);

        $this->assertFalse($storage->findConnection($con));
    }

}