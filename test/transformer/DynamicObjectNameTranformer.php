<?php
/**
 *
 * @author j
 * Date: 3/7/15
 * Time: 7:49 PM
 *
 * File: DynamicObjectNameTranformer.php
 */

require_once '../autoload.php';

/**
 * Class FunctionCallParserTest
 */
class DynamicObjectNamePreTransformedParserTest extends PHPUnit_Framework_TestCase
{
    public function testInterface() {
        $this->assertInstanceOf('\chilimatic\lib\interfaces\IFlyWeightTransformer', new \chilimatic\lib\transformer\string\DynamicObjectCallName());
    }

    public function testSimpleMethodCallString() {
        $transformer = new \chilimatic\lib\transformer\string\DynamicObjectCallName();
        $this->assertEquals('MyObject', $transformer->transform('myObject'));
    }

    public function testComplexMethodCallString() {
        $transformer = new \chilimatic\lib\transformer\string\DynamicObjectCallName();
        $this->assertEquals('MyObjectCallName', $transformer->transform('my-object-call-name'));
    }

    public function testComplexEmptyCallString() {
        $transformer = new \chilimatic\lib\transformer\string\DynamicObjectCallName();
        $this->assertEquals('', $transformer->transform(''));
    }
}