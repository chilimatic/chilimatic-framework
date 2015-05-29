<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 02.11.13
 * Time: 14:05
 */

// parent class and interface is needed first (interpreter issue)
include_once ( (string) '../../lib/config/interface.class.php' );
include_once ( (string) '../../lib/config/generic.class.php' );
include_once ( (string) '../../lib/config/node.class.php');
include_once ( (string) '../../lib/config/nodelist.class.php');
include_once ( (string) '../../lib/config/file.class.php' );


use chilimatic\lib\config\Config_File;


class Config_File_Test extends PHPUnit_Framework_TestCase
{

    const NAMESPACE_CONFIG = "\\chilimatic\\config\\";

    /**
     * Config_File instance
     *
     * @var Config_File
     */
    public $config = null;

    public function __construct($name = NULL, array $data = array(), $dataName = ''){
        $this->config = new Config_File();
    }

    public function testLoad()
    {
        $this->config->load();
        // checks for the main node
        $this->assertAttributeInstanceOf( self::NAMESPACE_CONFIG . 'Config_Node', 'main_node', $this->config);
    }

    /**
     * @depends testLoad
     */
    public function testSet()
    {
        $stdClass = new stdClass();
        $stdClass->first = 'property_string';
        $stdClass->second = 1;
        $stdClass->third = ['field1','field2','field3'];
        $stdClass->fouth = true;
        $array = array('first' => 'string', 'second' => 1, 'third' => true, 'fourth' => new stdClass());

        $this->assertInstanceOf(self::NAMESPACE_CONFIG . 'Config_File', $this->config->set('string','test_string'));
        $this->assertInstanceOf(self::NAMESPACE_CONFIG . 'Config_File', $this->config->set('array', $array));
        $this->assertInstanceOf(self::NAMESPACE_CONFIG . 'Config_File', $this->config->set('object', $stdClass ));
        $this->assertInstanceOf(self::NAMESPACE_CONFIG . 'Config_File', $this->config->set('bool', true));
        $this->assertInstanceOf(self::NAMESPACE_CONFIG . 'Config_File', $this->config->set('int', 1));
        $this->assertInstanceOf(self::NAMESPACE_CONFIG . 'Config_File', $this->config->set('float', 1.34242));
    }

    /**
     * @depends testSet
     */
    public function testGet() {
        $this->assertTrue(is_string($this->config->get('string')));
        $this->assertTrue(is_array($this->config->get('array')));
        $this->assertTrue(is_object($this->config->get('object')));
        $this->assertTrue(is_bool($this->config->get('bool')));
        $this->assertTrue(is_int($this->config->get('int')));
        $this->assertTrue(is_float($this->config->get('float')));

    }
}
