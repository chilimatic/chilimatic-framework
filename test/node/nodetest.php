<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 03.03.15
 * Time: 09:14
 */
require_once '../autoload.php';

class NodeTest extends PHPUnit_Framework_TestCase
{

    public function testNodeInstances() {
        $this->assertInstanceOf('\Chilimatic\Lib\Node\Node', new \chilimatic\lib\node\Node(null, '',''));
        $this->assertInstanceOf('\Chilimatic\Lib\Node\Collection', new \chilimatic\lib\node\Collection());
        $this->assertInstanceOf('\Chilimatic\Lib\Node\TreeNode', new \chilimatic\lib\node\TreeNode(null, '',''));
    }

    public function testSetNodeStringData() {
        $node = new \chilimatic\lib\node\Node(null, 'name', 'My Name');
        $this->assertEquals('name', $node->getKey());
        $this->assertEquals('My Name', $node->getData());
    }

    public function testSetNodeArrayData() {
        $myArray = ['adfasfdasfd'];
        $node = new \chilimatic\lib\node\Node(null, 'array', $myArray);
        $this->assertEquals($myArray, $node->getData());
    }

    public function testGetChildValueByKey() {
        $node = new \chilimatic\lib\node\Node(null, '.', '');
        $node->addChild(new \chilimatic\lib\node\Node($node, 'test', 23));
        $this->assertEquals(23, $node->getFirstByKey('test')->getData());
    }

    public function testGetChildValueById() {
        $node = new \chilimatic\lib\node\Node(null, '.', '');
        $node->addChild(new \chilimatic\lib\node\Node($node, 'test', 23));
        $this->assertEquals(23, $node->getById('.test')->getData());
    }

    public function testGetDuplicatedChildValueByKeyBehaviour() {
        $node = new \chilimatic\lib\node\Node(null, '.', '');
        $node->addChild(new \chilimatic\lib\node\Node($node, 'test', 23));
        $node->addChild(new \chilimatic\lib\node\Node($node, 'test', 24));
        $this->assertEquals(24, $node->getFirstByKey('test')->getData());
    }

    public function testGetDuplicatedChildValueByIdBehaviour() {
        $node = new \chilimatic\lib\node\Node(null, '.', '');
        $node->addChild(new \chilimatic\lib\node\Node($node, 'test', 23));
        $node->addChild(new \chilimatic\lib\node\Node($node, 'test', 24));
        $this->assertEquals(23, $node->getById('.test')->getData());
    }

    public function testGetDuplicatedChildNodeValueByIdBehaviour() {
        $node = new \chilimatic\lib\node\Node(null, '.', '');
        $node->addChild(new \chilimatic\lib\node\Node($node, 'test', 23));
        $node->addChild(new \chilimatic\lib\node\Node($node, 'test', 24));
        $this->assertEquals(24, $node->getById('.test-0')->getData());
    }

    public function testGetMultiNestedChildNodeValueByIdKey() {
        $node = new \chilimatic\lib\node\Node(null, '.', '');
        $node2 = new \chilimatic\lib\node\Node($node, 'test', 23);
        $node->addChild($node2);
        $node3 = new \chilimatic\lib\node\Node($node2, 'test', 24);
        $node2->addChild($node3);
        $this->assertEquals(24, $node->getBykey('test')->getData());
    }

}