<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 25.11.14
 * Time: 22:14
 */


class ClosureFactory_Test extends PHPUnit_Framework_TestCase
{

    public function testClosureFactorySingelton()
    {
        $this->assertInstanceOf('\chilimatic\lib\di\ClosureFactory', \chilimatic\lib\di\ClosureFactory::getInstance());
    }

    public function testGetDefaultService()
    {
        $di = \chilimatic\lib\di\ClosureFactory::getInstance();
        $di->loadServiceFromFile(__DIR__ .'../lib/general/config/default-service-collection.php');
    }



}