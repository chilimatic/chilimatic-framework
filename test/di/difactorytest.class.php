<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 25.11.14
 * Time: 22:14
 */


class DIFactory_Test extends PHPUnit_Framework_TestCase
{

    public function testDIFactorySingelton()
    {
        $this->assertInstanceOf('\chilimatic\lib\di\DIFactory', \chilimatic\lib\di\Factory::getInstance());
    }

    public function testGetDefaultService()
    {
        $di = \chilimatic\lib\di\Factory::getInstance();
        $di->loadServiceFromFile(__DIR__ .'../lib/general/config/default-service-collection.php');
    }

}