<?php
/**
 *
 * @author j
 * Date: 3/7/15
 * Time: 7:36 PM
 *
 * File: DynamicFunctionNameTransformerTest.php
 *
 * Class FunctionCallParserTest
 */
class DynamicFunctionNamePreTransformerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function doesTheClassUseTheCorrectInterface() {
        $this->assertInstanceOf('\chilimatic\lib\interfaces\IFlyWeightTransformer', new \chilimatic\lib\transformer\string\DynamicFunctionCallName());
    }

    /**
     * @test
     */
    public function simpleMethodCallString() {
        $transformer = new \chilimatic\lib\transformer\string\DynamicFunctionCallName();
        $this->assertEquals('myMethod', $transformer->transform('MyMethod'));
    }

    /**
     * @test
     */
    public function complexMethodCallString() {
        $transformer = new \chilimatic\lib\transformer\string\DynamicFunctionCallName();
        $this->assertEquals('myMethodCallName', $transformer->transform('my-method-call-name'));
    }

    /**
     * @test
     */
    public function complexEmptyCallString() {
        $transformer = new \chilimatic\lib\transformer\string\DynamicFunctionCallName();
        $this->assertEquals('', $transformer->transform(''));
    }
}