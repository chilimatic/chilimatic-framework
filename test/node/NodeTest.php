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
    /**
     * @var \chilimatic\lib\node\Node
     */
    protected $node;


    /**
     *
     */
    public function initNodes() {
        $this->node = new \chilimatic\lib\node\Node(null, '.', '');
        for ($i = 0; $i < 10; $i++) {
            $node2 = new \chilimatic\lib\node\Node($this->node, 'test-'.$i, $i);
            for ($x = 0; $x < 11; $x++) {
                $node3 = new \chilimatic\lib\node\Node($node2, 'test-'.$x, $x+4);
                for ($y = 0; $y < 5; $y++) {
                    $node4 = new \chilimatic\lib\node\Node($node3, 'test-'.$y, $y+10);
                    $node3->addChild($node4);
                }
                $node2->addChild($node3);

            }
            $this->node->addChild($node2);
        }
    }


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

    public function testGetMultiResultByKey() {
        $this->initNodes();
        $this->assertInstanceOf('\SplObjectStorage', $this->node->getByKey('test-1'));
    }

    public function testGetMultiResultByKeyPreFiltered()
    {
        $filterFactory = new \chilimatic\lib\node\filter\Factory();
        $filterFactory->setParser(new chilimatic\lib\parser\DynamicCallNamePreTransformed());
        $filterFactory->setTransformer(new chilimatic\lib\transformer\string\DynamicObjectCallName());

        $this->initNodes();
        $resultSet = $this->node->getByKey('test-1', $filterFactory->make('last-node'));
        $resultSet->rewind();
        $result = $resultSet->current();

        $this->assertInstanceOf('\SplObjectStorage', $resultSet);
        $this->assertEquals(".test-9.test-10.test-1", $result->getId());
        $this->assertCount(1, $resultSet);
    }

}