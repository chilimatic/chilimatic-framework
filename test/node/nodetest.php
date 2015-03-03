<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 03.03.15
 * Time: 09:14
 */


class NodeTest extends PHPUnit_Framework_TestCase {

    public function testNodeInstances() {
        $this->assertInstanceOf('\Chilimatic\Lib\AbstractNode', null);
    }
}