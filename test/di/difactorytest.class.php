<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 25.11.14
 * Time: 22:14
 */

require_once '../../app/general/init.php';



class DiFactory_Test extends PHPUnit_Framework_TestCase
{

    public function testDIFactorySingelton()
    {
        $this->assertInstanceOf('\chilimatic\lib\di\DIFactory', \chilimatic\lib\di\DIFactory::getInstance());
    }

    public function testTicketOfficeReservationNull()
    {
        $di = \chilimatic\lib\di\DIFactory::getInstance();
    }


}