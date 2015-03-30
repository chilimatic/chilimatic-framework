<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 03.03.15
 * Time: 09:14
 */
class NodeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \chilimatic\lib\datastructure\graph\Node
     */
    protected $node;


    /**
     *
     */
    public function initNodes() {
        $this->node = new \chilimatic\lib\datastructure\graph\Node(null, '.', '');
        for ($i = 0; $i < 10; $i++) {
            $node2 = new \chilimatic\lib\datastructure\graph\Node($this->node, 'test-'.$i, $i);
            for ($x = 0; $x < 11; $x++) {
                $node3 = new \chilimatic\lib\datastructure\graph\Node($node2, 'test-'.$x, $x+4);
                for ($y = 0; $y < 5; $y++) {
                    $node4 = new \chilimatic\lib\datastructure\graph\Node($node3, 'test-'.$y, $y+10);
                    $node3->addChild($node4);
                }
                $node2->addChild($node3);

            }
            $this->node->addChild($node2);
        }
    }


    /**
     * @test
     */
    public function nodeInstances() {
        $this->assertInstanceOf('\chilimatic\lib\datastructure\graph\Node', new \chilimatic\lib\datastructure\graph\Node(null, '',''));
        $this->assertInstanceOf('\chilimatic\lib\datastructure\graph\Collection', new \chilimatic\lib\datastructure\graph\Collection());
        $this->assertInstanceOf('\chilimatic\lib\datastructure\graph\TreeNode', new \chilimatic\lib\datastructure\graph\TreeNode(null, '',''));
    }

    /**
     * @test
     */
    public function setNodeStringData() {
        $node = new \chilimatic\lib\datastructure\graph\Node(null, 'name', 'My Name');
        $this->assertEquals('name', $node->getKey());
        $this->assertEquals('My Name', $node->getData());
    }

    /**
     * @test
     */
    public function setNodeArrayData() {
        $myArray = ['adfasfdasfd'];
        $node = new \chilimatic\lib\datastructure\graph\Node(null, 'array', $myArray);
        $this->assertEquals($myArray, $node->getData());
    }

    /**
     * @test
     */
    public function getChildValueByKey() {
        $node = new \chilimatic\lib\datastructure\graph\Node(null, '.', '');
        $node->addChild(new \chilimatic\lib\datastructure\graph\Node($node, 'test', 23));
        $this->assertEquals(23, $node->getFirstByKey('test')->getData());
    }

    /**
     * @test
     */
    public function getChildValueById() {
        $node = new \chilimatic\lib\datastructure\graph\Node(null, '.', '');
        $node->addChild(new \chilimatic\lib\datastructure\graph\Node($node, 'test', 23));
        $this->assertEquals(23, $node->getById('.test')->getData());
    }

    /**
     * @test
     */
    public function getDuplicatedChildValueByKeyBehaviour() {
        $node = new \chilimatic\lib\datastructure\graph\Node(null, '.', '');
        $node->addChild(new \chilimatic\lib\datastructure\graph\Node($node, 'test', 23));
        $node->addChild(new \chilimatic\lib\datastructure\graph\Node($node, 'test', 24));
        $this->assertEquals(24, $node->getFirstByKey('test')->getData());
    }

    /**
     * @test
     */
    public function getDuplicatedChildValueByIdBehaviour() {
        $node = new \chilimatic\lib\datastructure\graph\Node(null, '.', '');
        $node->addChild(new \chilimatic\lib\datastructure\graph\Node($node, 'test', 23));
        $node->addChild(new \chilimatic\lib\datastructure\graph\Node($node, 'test', 24));
        $this->assertEquals(23, $node->getById('.test')->getData());
    }

    /**
     * @test
     */
    public function getDuplicatedChildNodeValueByIdBehaviour() {
        $node = new \chilimatic\lib\datastructure\graph\Node(null, '.', '');
        $node->addChild(new \chilimatic\lib\datastructure\graph\Node($node, 'test', 23));
        $node->addChild(new \chilimatic\lib\datastructure\graph\Node($node, 'test', 24));
        $this->assertEquals(24, $node->getById('.test-0')->getData());
    }

    /**
     * @test
     */
    public function getMultiResultByKey() {
        $this->initNodes();
        $this->assertInstanceOf('\SplObjectStorage', $this->node->getByKey('test-1'));
    }

    /**
     * @test
     */
    public function getMultiResultByKeyPreFiltered()
    {
        $filterFactory = new \chilimatic\lib\datastructure\graph\filter\Factory();
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