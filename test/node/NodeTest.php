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
        for ($i = 0; $i < 4; $i++) {
            $node2 = new \chilimatic\lib\datastructure\graph\Node($this->node, 'test-'.$i, $i);
            for ($x = 0; $x < 4; $x++) {
                $node3 = new \chilimatic\lib\datastructure\graph\Node($node2, 'test-'.$x, $x+4);
                for ($y = 0; $y < 4; $y++) {
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
        $this->assertEquals(23, $node->getLastByKey('test')->getData());
    }

    /**
     * @test
     */
    public function getChildValueById() {
        $node = new \chilimatic\lib\datastructure\graph\Node(null, '.', '');
        $node->addChild(new \chilimatic\lib\datastructure\graph\Node($node, 'test', 23));
        $id = "{$node->keyDelimiter}.{$node->keyDelimiter}test{$node->keyDelimiter}";
        $this->assertEquals(23, $node->getById($id)->getData());
    }

    /**
     * @test
     */
    public function getDuplicatedChildValueByKeyBehaviour() {
        $node = new \chilimatic\lib\datastructure\graph\Node(null, '.', '');
        $node->addChild(new \chilimatic\lib\datastructure\graph\Node($node, 'test', 23));
        $node->addChild(new \chilimatic\lib\datastructure\graph\Node($node, 'test', 24));
        $this->assertEquals(24, $node->getLastByKey('test')->getData());
    }

    /**
     * @test
     */
    public function getChildNodeByAmigiousKeyString() {
        $node = new \chilimatic\lib\datastructure\graph\Node(null, '.', '');
        $node->addChild(new \chilimatic\lib\datastructure\graph\Node($node, 'test', 23));
        $node->addChild(new \chilimatic\lib\datastructure\graph\Node($node, 'test_ing', 24));
        $this->assertEquals(23, $node->getLastByKey('test')->getData());

    }

    /**
     * @test
     */
    public function getDuplicatedChildValueByIdBehaviour() {
        $node = new \chilimatic\lib\datastructure\graph\Node(null, '.', '');
        $node->addChild(new \chilimatic\lib\datastructure\graph\Node($node, 'test', 23));
        $node->addChild(new \chilimatic\lib\datastructure\graph\Node($node, 'test', 24));
        $id = "{$node->keyDelimiter}.{$node->keyDelimiter}test{$node->keyDelimiter}";
        $this->assertEquals(23, $node->getById($id)->getData());
    }

    /**
     * @test
     */
    public function getDuplicatedChildNodeValueByIdBehaviour() {
       $node = new \chilimatic\lib\datastructure\graph\Node(null, '.', '');
       $node->addChild(new \chilimatic\lib\datastructure\graph\Node($node, 'test', 23));
       $node->addChild(new \chilimatic\lib\datastructure\graph\Node($node, 'test', 24));
       $this->assertEquals(24, $node->getById('#.#test-0#')->getData());
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
        $filterFactory->setValidator(new chilimatic\lib\validator\DynamicCallNamePreTransformed());
        $filterFactory->setTransformer(new chilimatic\lib\transformer\string\DynamicObjectCallName());

        $this->initNodes();
        $resultSet = $this->node->getByKey('test-1', $filterFactory->make('last-node'));
        $resultSet->rewind();
        $result = $resultSet->current();

        $this->assertInstanceOf('\SplObjectStorage', $resultSet);

        $this->assertEquals("#.#test-3#test-3#test-1#", $result->getId());
        $this->assertCount(1, $resultSet);
    }

}