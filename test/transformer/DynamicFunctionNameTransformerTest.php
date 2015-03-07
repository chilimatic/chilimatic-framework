<?php
/**
 *
 * @author j
 * Date: 3/7/15
 * Time: 7:36 PM
 *
 * File: DynamicFunctionNameTransformerTest.php
 */

require_once '../autoload.php';

/**
 * Class FunctionCallParserTest
 */
class DynamicFunctionNamePreTransformedParserTest extends PHPUnit_Framework_TestCase
{
    public function testInterface() {
        $this->assertInstanceOf('\chilimatic\lib\interfaces\IFlyWeightTransformer', new \chilimatic\lib\transformer\string\DynamicFunctionCallName());
    }

    public function testSimpleMethodCallString() {
        $transformer = new \chilimatic\lib\transformer\string\DynamicFunctionCallName();
        $this->assertEquals('myMethod', $transformer->transform('MyMethod'));
    }

    public function testComplexMethodCallString() {
        $transformer = new \chilimatic\lib\transformer\string\DynamicFunctionCallName();
        $this->assertEquals('myMethodCallName', $transformer->transform('my-method-call-name'));
    }

    public function testComplexEmptyCallString() {
        $transformer = new \chilimatic\lib\transformer\string\DynamicFunctionCallName();
        $this->assertEquals('', $transformer->transform(''));
    }
}