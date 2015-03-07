<?php
/**
 *
 * @author j
 * Date: 3/7/15
 * Time: 8:04 PM
 *
 * File: NodeFilterFactoryTest.php
 */

class NodeTest extends PHPUnit_Framework_TestCase
{
    public function testNodeFilterInstances()
    {
        $this->assertInstanceOf('\chilimatic\lib\node\filter\Factory', new \chilimatic\lib\node\filter\Factory());
    }
}