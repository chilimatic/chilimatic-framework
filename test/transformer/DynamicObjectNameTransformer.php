<?php
/**
 *
 * @author j
 * Date: 3/7/15
 * Time: 7:49 PM
 *
 * File: DynamicObjectNameTranformer.php
 *
 * Class FunctionCallParserTest
 */
class DynamicObjectNamePreTransformedParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function doesTheClassUseTheCorrectInterface() {
        $this->assertInstanceOf('\chilimatic\lib\interfaces\IFlyWeightTransformer', new \chilimatic\lib\transformer\string\DynamicObjectCallName());
    }

    /**
     * @test
     */
    public function simpleMethodCallString() {
        $transformer = new \chilimatic\lib\transformer\string\DynamicObjectCallName();
        $this->assertEquals('MyObject', $transformer->transform('myObject'));
    }

    /**
     * @test
     */
    public function complexMethodCallString() {
        $transformer = new \chilimatic\lib\transformer\string\DynamicObjectCallName();
        $this->assertEquals('MyObjectCallName', $transformer->transform('my-object-call-name'));
    }

    /**
     * @test
     */
    public function complexEmptyCallString() {
        $transformer = new \chilimatic\lib\transformer\string\DynamicObjectCallName();
        $this->assertEquals('', $transformer->transform(''));
    }
}