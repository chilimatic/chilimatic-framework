<?php
use chilimatic\lib\database\sql\mysql\connection\MysqlConnectionSettings;

/**
 *
 * @author j
 * Date: 9/20/15
 * Time: 11:44 PM
 *
 * File: MySqlConnectionSettingsTest.php
 */
class MysqlConnectionSettingsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function checkIfTheConnectionSettingsInterfaceIsImplemented() {
        $connectionSettings = new MysqlConnectionSettings('','','');

        $this->assertInstanceOf('\chilimatic\lib\database\connection\IDatabaseConnectionSettings', $connectionSettings);
    }

    /**
     * @test
     */
    public function checkIfTheSqlConnectionSettingsInterfaceIsImplemented() {
        $connectionSettings = new MysqlConnectionSettings('','','');

        $this->assertInstanceOf('\chilimatic\lib\database\sql\connection\ISqlConnectionSettings', $connectionSettings);
    }

    /**
     * @test
     */
    public function checkIfThePersitentFlagIsOff() {
        $connectionSettings = new MysqlConnectionSettings('','','');

        $this->assertFalse($connectionSettings->isPersistent());
    }

    /**
     * @test
     */
    public function checkIfTheCharsetIsNullPerDefault() {
        $connectionSettings = new MysqlConnectionSettings('','','');

        $this->assertNull($connectionSettings->getCharset());
    }

    /**
     * @test
     */
    public function checkIfTheAbstractSqlConnectionSettingsAreExtended() {
        $connectionSettings = new MysqlConnectionSettings('','','');

        $this->assertInstanceOf('\chilimatic\lib\database\sql\connection\AbstractSqlConnectionSettings', $connectionSettings);
    }

    /**
     * @test
     */
    public function checkIfTheDefaultPortHasBeenSetIfNoPortHasBeenGivenInTheSettingList() {
        $connectionSettings = new MysqlConnectionSettings('','','');

        $this->assertEquals(3306, $connectionSettings->getPort());
    }

    /**
     * @test
     */
    public function checkIfTheHostHasBeenSetAsParameter(){
        $connectionSettings = new MysqlConnectionSettings('localhost','','');

        $this->assertEquals('localhost', $connectionSettings->getHost());
    }

    /**
     * @test
     */
    public function checkIfTheUsernameHasBeenSetAsParameter() {
        $connectionSettings = new MysqlConnectionSettings('','username','');

        $this->assertEquals('username', $connectionSettings->getUsername());
    }

    /**
     * @test
     */
    public function checkIfThePasswordHasBeenSetAsParameter() {
        $connectionSettings = new MysqlConnectionSettings('','','password');

        $this->assertEquals('password', $connectionSettings->getPassword());
    }

    /**
     * @test
     */
    public function checkIfTheDatabaseHasBeenSetAsParameter() {
        $connectionSettings = new MysqlConnectionSettings('','','', 'myDatabase');

        $this->assertEquals('myDatabase', $connectionSettings->getDatabase());
    }

    /**
     * @test
     */
    public function checkIfThePortHasBeenSetAsParameter() {
        $connectionSettings = new MysqlConnectionSettings('','','', '', 1234);

        $this->assertEquals(1234, $connectionSettings->getPort());
    }

    /**
     * @test
     */
    public function checkIfThePersitentFlagHasBeenSetAsSettingListParameter() {
        $connectionSettings = new MysqlConnectionSettings('','','', null, null, ['persistent' => true]);

        $this->assertTrue($connectionSettings->isPersistent());
    }

    /**
     * @test
     */
    public function checkIfTheCharsetHasBeenSetAsSettingListParameter() {
        $connectionSettings = new MysqlConnectionSettings('','','', null, null, ['charset' => 'utf8'] );

        $this->assertEquals('utf8', $connectionSettings->getCharset());
    }

    /**
     * @test
     */
    public function checkIfTheGeneratorIsProvideForValidation()
    {
        $connectionSettings = new MysqlConnectionSettings('','','', null, null, ['charset' => 'utf8'] );
        $this->assertInstanceOf('\Generator', $connectionSettings->getParameterGenerator());
    }

}