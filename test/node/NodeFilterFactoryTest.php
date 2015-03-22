<?php
/**
 *
 * @author j
 * Date: 3/7/15
 * Time: 8:04 PM
 *
 * File: NodeFilterFactoryTest.php
 */

class NodeFilterFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testNodeFilterInstances()
    {
       $this->assertInstanceOf('\chilimatic\lib\datastructure\graph\filter\Factory', new \chilimatic\lib\datastructure\graph\filter\Factory());
    }

    public function testNodeFilterFactoryStaticMakeWithParserAndTransformer()
    {
        \chilimatic\lib\datastructure\graph\filter\FactoryStatic::setParser(new chilimatic\lib\parser\DynamicCallNamePreTransformed());
        \chilimatic\lib\datastructure\graph\filter\FactoryStatic::setTransformer(new chilimatic\lib\transformer\string\DynamicObjectCallName());

        $this->assertEquals(null, \chilimatic\lib\datastructure\graph\filter\FactoryStatic::make('lastNode'));
        $this->assertInstanceOf('\chilimatic\lib\datastructure\graph\filter\AbstractFilter', \chilimatic\lib\datastructure\graph\filter\FactoryStatic::make('last-node'));
    }

    public function testNodeFilterFactoryStaticSetTransformer() {
        \chilimatic\lib\datastructure\graph\filter\FactoryStatic::setTransformer(new chilimatic\lib\transformer\string\DynamicObjectCallName());
        $this->assertInstanceOf('\chilimatic\lib\transformer\string\DynamicObjectCallName', \chilimatic\lib\datastructure\graph\filter\FactoryStatic::getTransformer());
    }

    public function testNodeFilterFactoryMakeWithParserAndTransformer()
    {
        $filterFactory = new \chilimatic\lib\datastructure\graph\filter\Factory();
        $filterFactory->setParser(new chilimatic\lib\parser\DynamicCallNamePreTransformed());
        $filterFactory->setTransformer(new chilimatic\lib\transformer\string\DynamicObjectCallName());

        $this->assertEquals(null, $filterFactory->make('lastNode'));
        $this->assertInstanceOf('\chilimatic\lib\datastructure\graph\filter\AbstractFilter', $filterFactory->make('last-node'));
    }

    public function testNodeFilterFactoryMake()
    {
        $filterFactory = new \chilimatic\lib\datastructure\graph\filter\Factory();
        $this->assertEquals(null, $filterFactory->make('last-node'));
        $this->assertInstanceOf('\chilimatic\lib\datastructure\graph\filter\AbstractFilter', $filterFactory->make('lastNode'));
    }

    public function testNodeFilterFactoryStaticMake()
    {
        \chilimatic\lib\datastructure\graph\filter\FactoryStatic::setParser(null);
        \chilimatic\lib\datastructure\graph\filter\FactoryStatic::setTransformer(null);
        $this->assertEquals(null, \chilimatic\lib\datastructure\graph\filter\FactoryStatic::make('last-node'));
        $this->assertInstanceOf('\chilimatic\lib\datastructure\graph\filter\AbstractFilter', \chilimatic\lib\datastructure\graph\filter\FactoryStatic::make('lastNode'));
    }

}