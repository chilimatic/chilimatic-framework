<?php
use chilimatic\lib\config\Config;

/**
 *
 * @author j
 * Date: 7/1/15
 * Time: 9:19 PM
 *
 * File: ConfigSingeltonTest.php
 */

class ConfigSingelton_Test extends PHPUnit_Framework_TestCase
{

    /**
     * path to the test data
     *
     * @var string
     */
    const TEST_DATA_DIR = '/../testdata/';

    /**
     * @var chilimatic\lib\config\AbstractConfig
     */
    public $config;

    /**
     * @var string
     */
    private $testDataDir;


    public function __construct()
    {
        $this->testDataDir = __DIR__ . '/../testdata/';
    }

    /**
     * @before
     */
    public function createConfigs()
    {

        $data = "value1=test\nvalue2=\"test\"\nvalue3='test'\nvalue4=123\nvalue5=12.23\nvalue6={\"test\":123}\nvalue7=a:1:{i:23;i:12;}";
        file_put_contents($this->testDataDir . '*.cfg', $data);
        $data2 = "value1=test2\nvalue7=teststring";
        file_put_contents($this->testDataDir . '*.test.com.cfg', $data2);
    }

    /**
     * @after
     */
    public function deleteConfigs()
    {
        unlink($this->testDataDir . '*.cfg');
        unlink($this->testDataDir . '*.test.com.cfg');
    }

    /**
     * @test
     *
     * @expectedException \LogicException
     * @expectedExceptionMessage Config Type was not specified in the param array $param['type']
     */
    public function checkMissingTypeException(){
        $config = Config::getInstance();
    }

    /**
     * @test
     *
     * @expectedException chilimatic\lib\exception\ConfigException
     * @expectedExceptionMessage No config file was give please, the parameter
     */
    public function checkMissingException(){
        $config = Config::getInstance(
            [
                'type' => 'Ini'
            ]
        );
    }

    /**
     * @test
     */
    public function checkGetSingeltonInstance(){
        $config = Config::getInstance(
            [
                'type' => 'File',
                \chilimatic\lib\config\File::CONFIG_PATH_INDEX => $this->testDataDir
            ]
        );

        $config2 = Config::getInstance();

        $this->assertEquals($config, $config2);
    }


    /**
     * @test
     */
    public function checkSetParam() {
        Config::getInstance(
            [
                'type' => 'File',
                \chilimatic\lib\config\File::CONFIG_PATH_INDEX => $this->testDataDir
            ]
        );

        Config::set('test', 12);


        $this->assertEquals(12, Config::get('test'));
    }

    /**
     * @test
     */
    public function checkGetParam() {
        Config::getInstance(
            [
                'type' => 'File',
                \chilimatic\lib\config\File::CONFIG_PATH_INDEX => $this->testDataDir
            ]
        );

        $this->assertEquals('test', Config::get('value1'));
    }
}