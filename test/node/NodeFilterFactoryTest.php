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
    /**
     * @test
     */
    public function nodeFilterInstances()
    {
       $this->assertInstanceOf('\chilimatic\lib\datastructure\graph\filter\Factory', new \chilimatic\lib\datastructure\graph\filter\Factory());
    }

    /**
     * @test
     */
    public function nodeFilterFactoryStaticMakeWithParserAndTransformer()
    {
        \chilimatic\lib\datastructure\graph\filter\FactoryStatic::setValidator(new chilimatic\lib\validator\DynamicCallNamePreTransformed());
        \chilimatic\lib\datastructure\graph\filter\FactoryStatic::setTransformer(new chilimatic\lib\transformer\string\DynamicObjectCallName());

        $this->assertEquals(null, \chilimatic\lib\datastructure\graph\filter\FactoryStatic::make('lastNode'));
        $this->assertInstanceOf('\chilimatic\lib\datastructure\graph\filter\AbstractFilter', \chilimatic\lib\datastructure\graph\filter\FactoryStatic::make('last-node'));
    }

    /**
     * @test
     */
    public function nodeFilterFactoryStaticSetTransformer() {
        \chilimatic\lib\datastructure\graph\filter\FactoryStatic::setTransformer(new chilimatic\lib\transformer\string\DynamicObjectCallName());
        $this->assertInstanceOf('\chilimatic\lib\transformer\string\DynamicObjectCallName', \chilimatic\lib\datastructure\graph\filter\FactoryStatic::getTransformer());
    }

    /**
     * @test
     */
    public function nodeFilterFactoryMakeWithParserAndTransformer()
    {
        $filterFactory = new \chilimatic\lib\datastructure\graph\filter\Factory();
        $filterFactory->setValidator(new chilimatic\lib\validator\DynamicCallNamePreTransformed());
        $filterFactory->setTransformer(new chilimatic\lib\transformer\string\DynamicObjectCallName());

        $this->assertEquals(null, $filterFactory->make('lastNode'));
        $this->assertInstanceOf('\chilimatic\lib\datastructure\graph\filter\AbstractFilter', $filterFactory->make('last-node'));
    }

    /**
     * @test
     */
    public function nodeFilterFactoryMake()
    {
        $filterFactory = new \chilimatic\lib\datastructure\graph\filter\Factory();
        $this->assertEquals(null, $filterFactory->make('last-node'));
        $this->assertInstanceOf('\chilimatic\lib\datastructure\graph\filter\AbstractFilter', $filterFactory->make('lastNode'));
    }

    /**
     * @test
     */
    public function nodeFilterFactoryStaticMake()
    {
        \chilimatic\lib\datastructure\graph\filter\FactoryStatic::setValidator(null);
        \chilimatic\lib\datastructure\graph\filter\FactoryStatic::setTransformer(null);
        $this->assertEquals(null, \chilimatic\lib\datastructure\graph\filter\FactoryStatic::make('last-node'));
        $this->assertInstanceOf('\chilimatic\lib\datastructure\graph\filter\AbstractFilter', \chilimatic\lib\datastructure\graph\filter\FactoryStatic::make('lastNode'));
    }

}