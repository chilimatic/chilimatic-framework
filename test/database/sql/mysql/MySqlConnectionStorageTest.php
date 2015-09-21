<?php
use chilimatic\lib\database\sql\mysql\connection\MysqlConnection;
use chilimatic\lib\database\sql\mysql\connection\MysqlConnectionSettings;
use chilimatic\lib\database\sql\mysql\connection\MysqlConnectionStorage;

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
        $storage =  new MysqlConnectionStorage();

        $this->assertInstanceOf('\chilimatic\lib\database\sql\connection\ISqlConnectionStorage',$storage);
    }

    /**
     * @test
     */
    public function checkIfAMySqlConnectionCanBeAddedBySettingsToTheMysqlConnectionStorage() {
        $storage = new MysqlConnectionStorage();
        $setting = new MysqlConnectionSettings('','','');
        try {
            $storage->addConnectionBySetting($setting, 'mock');
        } catch(PDOException $e) {
            // do nothing
        }


        $this->assertEquals($setting, $storage->getConnectionByPosition(0)->getDbAdapter()->getConnectionSettings());
    }

    /**
     * @test
     */
    public function checkIfASpecificMysqlConnectionSettingsCanBeRetrieved() {
        $storage = new MysqlConnectionStorage();
        $setting = new MysqlConnectionSettings('','','');

        $storage->addConnectionBySetting(
            new MysqlConnectionSettings('','',''),
            'mock'
        );

        $storage->addConnectionBySetting(
            $setting,
            'mock'
        );

        $this->assertEquals($setting, $storage->getConnectionByPosition(1)->getDbAdapter()->getConnectionSettings());
    }


    /**
     * @test
     */
    public function checkIfAMysqlConnectionCanBeAdded() {
        $storage = new MysqlConnectionStorage();
        $con = new MysqlConnection(
            new MysqlConnectionSettings('','',''),
            'mock'
        );

        $storage->addConnection($con);

        $this->assertEquals($con, $storage->getConnectionByPosition(0));
    }


    /**
     * @test
     */
    public function checkIfAMysqlConnectionCanBeFound() {
        $storage = new MysqlConnectionStorage();
        $con = new MysqlConnection(
            new MysqlConnectionSettings('','',''),
            'mock'
        );

        $storage->addConnection($con);

        $this->assertTrue($storage->findConnection($con));
    }

    /**
     * @test
     */
    public function checkIfAMysqlConnectionCanBeRemoved() {
        $storage = new MysqlConnectionStorage();
        $con = new MysqlConnection(
            new MysqlConnectionSettings('','',''),
            'mock'
        );

        $storage->addConnection($con);
        $storage->removeConnection($con);

        $this->assertFalse($storage->findConnection($con));
    }

}