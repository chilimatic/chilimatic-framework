<?php
/**
 *
 * @author j
 * Date: 6/4/15
 * Time: 5:34 PM
 *
 * File: ConfigFileTest.php
 */

class ConfigFile_Test extends PHPUnit_Framework_TestCase {

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

    private $testDataDir;


    public function __construct() {
        $this->testDataDir = __DIR__ . '/../testdata/';
    }

    /**
     * @before
     */
    public function createConfigs() {

        $data = "value1=test;\nvalue2=\"test\"\nvalue3='test'\nvalue4=123\nvalue5=12.23\nvalue6={\"test\":123}\nvalue7=a:1:{i:23;i:12;}";
        file_put_contents($this->testDataDir . '*.cfg', $data);
        $data2 = "value1=test2\nvalue7=teststring";
        file_put_contents($this->testDataDir . '*.test.com.cfg', $data2);

        $this->config = \chilimatic\lib\config\ConfigFactory::make('File', [
            \chilimatic\lib\config\File::CONFIG_PATH_INDEX => $this->testDataDir,
            'host_id' => 'www.test.com'
        ]);


    }

    /**
     * @after
     */
    public function deleteConfigs() {
        unlink($this->testDataDir . '*.cfg');
        unlink($this->testDataDir . '*.test.com.cfg');
    }

    /**
     * @test
     */
    public function configFileInstanceTest() {
        $this->assertInstanceOf('\chilimatic\lib\config\File', $this->config);
    }

    /**
     * @test
     */
    public function getHirachicalValue1AsString() {
        $this->assertEquals('test2', $this->config->get('value1'));
    }

    /**
     * @test
     */
    public function addConfigSet() {
        $this->config->set('value1', 12);
        $this->assertEquals(12, $this->config->get('value1'));
    }

}
