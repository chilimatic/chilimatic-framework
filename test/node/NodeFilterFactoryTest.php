<?php
/**
 *
 * @author j
 * Date: 3/7/15
 * Time: 8:04 PM
 *
 * File: NodeFilterFactoryTest.php
 */

require_once '../autoload.php';

class NodeFilterFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testNodeFilterInstances()
    {
       $this->assertInstanceOf('\chilimatic\lib\node\filter\Factory', new \chilimatic\lib\node\filter\Factory());
    }

    public function testNodeFilterFactoryStaticMakeWithParserAndTransformer()
    {
        \chilimatic\lib\node\filter\FactoryStatic::setParser(new chilimatic\lib\parser\DynamicCallNamePreTransformed());
        \chilimatic\lib\node\filter\FactoryStatic::setTransformer(new chilimatic\lib\transformer\string\DynamicObjectCallName());

        $this->assertEquals(null, \chilimatic\lib\node\filter\FactoryStatic::make('lastNode'));
        $this->assertInstanceOf('\chilimatic\lib\node\filter\AbstractFilter', \chilimatic\lib\node\filter\FactoryStatic::make('last-node'));
    }

    public function testNodeFilterFactoryStaticSetTransformer() {
        \chilimatic\lib\node\filter\FactoryStatic::setTransformer(new chilimatic\lib\transformer\string\DynamicObjectCallName());
        $this->assertInstanceOf('\chilimatic\lib\transformer\string\DynamicObjectCallName', \chilimatic\lib\node\filter\FactoryStatic::getTransformer());
    }

    public function testNodeFilterFactoryMakeWithParserAndTransformer()
    {
        $filterFactory = new \chilimatic\lib\node\filter\Factory();
        $filterFactory->setParser(new chilimatic\lib\parser\DynamicCallNamePreTransformed());
        $filterFactory->setTransformer(new chilimatic\lib\transformer\string\DynamicObjectCallName());

        $this->assertEquals(null, $filterFactory->make('lastNode'));
        $this->assertInstanceOf('\chilimatic\lib\node\filter\AbstractFilter', $filterFactory->make('last-node'));
    }

    public function testNodeFilterFactoryMake()
    {
        $filterFactory = new \chilimatic\lib\node\filter\Factory();
        $this->assertEquals(null, $filterFactory->make('last-node'));
        $this->assertInstanceOf('\chilimatic\lib\node\filter\AbstractFilter', $filterFactory->make('lastNode'));
    }

    public function testNodeFilterFactoryStaticMake()
    {
        \chilimatic\lib\node\filter\FactoryStatic::setParser(null);
        \chilimatic\lib\node\filter\FactoryStatic::setTransformer(null);
        $this->assertEquals(null, \chilimatic\lib\node\filter\FactoryStatic::make('last-node'));
        $this->assertInstanceOf('\chilimatic\lib\node\filter\AbstractFilter', \chilimatic\lib\node\filter\FactoryStatic::make('lastNode'));
    }

}