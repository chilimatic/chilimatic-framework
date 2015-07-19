<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 19.07.15
 * Time: 13:34
 */

class ConfigCollectionTest extends PHPUnit_Framework_TestCase {


    /**
     * @test
     */
    public function testConfigCollectionInstanceOf() {
        $this->assertInstanceOf('\chilimatic\lib\config\Collection', new \chilimatic\lib\config\Collection());

    }

    /**
     * @test
     */
    public function testGraphCollectionInstanceOf() {
        $this->assertInstanceOf('\chilimatic\lib\dataStructure\graph\Collection', new chilimatic\lib\config\Collection());
    }

    /**
     * @test
     */
    public function testGraphCollectionAddNode() {
        $collection = new chilimatic\lib\config\Collection();
        $collection->addNode(new \chilimatic\lib\config\Node(null, null, null));

        $this->assertEquals(1, $collection->count());
    }

    /**
     * @test
     */
    public function testGraphCollectionAddAndRemoveNode() {
        $node = new \chilimatic\lib\config\Node(null, null, null);
        $collection = new chilimatic\lib\config\Collection();
        $collection->addNode($node);

        $collection->removeNode($node);
        $this->assertEquals(0, $collection->count());
    }


    /**
     * @test
     */
    public function testGraphCollectionAddAndGetSameNode() {
        $node = new \chilimatic\lib\config\Node(null, '*', null);
        $collection = new chilimatic\lib\config\Collection();
        $collection->addNode($node);

        $retNode = $collection->getLastByKey('*');

        $this->assertEquals($node, $retNode);
    }

    /**
     * @test
     */
    public function testGraphCollectionAddAndGetObjectStorage() {
        $node = new \chilimatic\lib\config\Node(null, '*', null);
        $collection = new chilimatic\lib\config\Collection();
        $collection->addNode($node);

        $retStorage = $collection->getByKey('*');

        $this->assertInstanceOf('\SPLObjectStorage', $retStorage);
    }


}