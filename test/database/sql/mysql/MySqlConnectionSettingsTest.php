<?php
use chilimatic\lib\database\sql\mysql\connection\MySQLConnectionSettings;

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
        $connectionSettings = new MySQLConnectionSettings('','','');

        $this->assertInstanceOf('\chilimatic\lib\database\connection\IDatabaseConnectionSettings', $connectionSettings);
    }

    /**
     * @test
     */
    public function checkIfTheSqlConnectionSettingsInterfaceIsImplemented() {
        $connectionSettings = new MySQLConnectionSettings('','','');

        $this->assertInstanceOf('\chilimatic\lib\database\sql\connection\ISqlConnectionSettings', $connectionSettings);
    }

    /**
     * @test
     */
    public function checkIfThePersitentFlagIsOff() {
        $connectionSettings = new MySQLConnectionSettings('','','');

        $this->assertFalse($connectionSettings->isPersistent());
    }

    /**
     * @test
     */
    public function checkIfTheCharsetIsNullPerDefault() {
        $connectionSettings = new MySQLConnectionSettings('','','');

        $this->assertNull($connectionSettings->getCharset());
    }

    /**
     * @test
     */
    public function checkIfTheAbstractSqlConnectionSettingsAreExtended() {
        $connectionSettings = new MySQLConnectionSettings('','','');

        $this->assertInstanceOf('\chilimatic\lib\database\sql\connection\AbstractSqlConnectionSettings', $connectionSettings);
    }

    /**
     * @test
     */
    public function checkIfTheDefaultPortHasBeenSetIfNoPortHasBeenGivenInTheSettingList() {
        $connectionSettings = new MySQLConnectionSettings('','','');

        $this->assertEquals(3306, $connectionSettings->getPort());
    }

    /**
     * @test
     */
    public function checkIfTheHostHasBeenSetAsParameter(){
        $connectionSettings = new MySQLConnectionSettings('localhost','','');

        $this->assertEquals('localhost', $connectionSettings->getHost());
    }

    /**
     * @test
     */
    public function checkIfTheUsernameHasBeenSetAsParameter() {
        $connectionSettings = new MySQLConnectionSettings('','username','');

        $this->assertEquals('username', $connectionSettings->getUsername());
    }

    /**
     * @test
     */
    public function checkIfThePasswordHasBeenSetAsParameter() {
        $connectionSettings = new MySQLConnectionSettings('','','password');

        $this->assertEquals('password', $connectionSettings->getPassword());
    }

    /**
     * @test
     */
    public function checkIfTheDatabaseHasBeenSetAsParameter() {
        $connectionSettings = new MySQLConnectionSettings('','','', 'myDatabase');

        $this->assertEquals('myDatabase', $connectionSettings->getDatabase());
    }

    /**
     * @test
     */
    public function checkIfThePortHasBeenSetAsParameter() {
        $connectionSettings = new MySQLConnectionSettings('','','', '', 1234);

        $this->assertEquals(1234, $connectionSettings->getPort());
    }

    /**
     * @test
     */
    public function checkIfThePersitentFlagHasBeenSetAsSettingListParameter() {
        $connectionSettings = new MySQLConnectionSettings('','','', null, null, ['persistent' => true]);

        $this->assertTrue($connectionSettings->isPersistent());
    }

    /**
     * @test
     */
    public function checkIfTheCharsetHasBeenSetAsSettingListParameter() {
        $connectionSettings = new MySQLConnectionSettings('','','', null, null, ['charset' => 'utf8'] );

        $this->assertEquals('utf8', $connectionSettings->getCharset());
    }

    /**
     * @test
     */
    public function checkIfTheGeneratorIsProvideForValidation()
    {
        $connectionSettings = new MySQLConnectionSettings('','','', null, null, ['charset' => 'utf8'] );
        $this->assertInstanceOf('\Generator', $connectionSettings->getParameterGenerator());
    }

}