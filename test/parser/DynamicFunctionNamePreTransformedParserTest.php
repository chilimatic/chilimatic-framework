<?php
/**
 *
 * @author j
 * Date: 3/7/15
 * Time: 6:21 PM
 *
 * File: functioncalltest.php
 */

require_once '../autoload.php';

/**
 * Class FunctionCallParserTest
 */
class DynamicFunctionNamePreTransformedParserTest extends PHPUnit_Framework_TestCase
{
    public function testInterface() {
        $this->assertInstanceOf('\chilimatic\lib\interfaces\IFlyWeightParser', new \chilimatic\lib\parser\DynamicFunctionNamePreTransformed());
    }

    public function testWrongCharacterAtBeginningSyntax() {
        $parser = new chilimatic\lib\parser\DynamicFunctionNamePreTransformed();
        $this->assertEquals(false, $parser->parse('-methodname'));
    }

    public function testWrongCharacterAtEndSyntax() {
        $parser = new chilimatic\lib\parser\DynamicFunctionNamePreTransformed();
        $this->assertEquals(false, $parser->parse('methodname-'));
    }

    public function testWrongCaseCharacterInCallNameSyntax() {
        $parser = new chilimatic\lib\parser\DynamicFunctionNamePreTransformed();
        $this->assertEquals(false, $parser->parse('Methodname'));
    }

    public function testWrongCharactInCallNameSyntax() {
        $parser = new chilimatic\lib\parser\DynamicFunctionNamePreTransformed();
        $this->assertEquals(false, $parser->parse('$method'));
    }

    public function testGetErrorMessage() {
        $parser = new chilimatic\lib\parser\DynamicFunctionNamePreTransformed();
        $this->assertEquals(false, $parser->parse('$method'));
        $this->assertEquals($parser->getInvalidCharacters() . ' are not allowed to be in the callname', $parser->getErrorMsg());
    }

    public function testCorrectComplexCallSyntax() {
        $parser = new chilimatic\lib\parser\DynamicFunctionNamePreTransformed();
        $this->assertEquals(false, $parser->parse('my-method-name'));
    }

    public function testCorrectSimpleCallSyntax() {
        $parser = new chilimatic\lib\parser\DynamicFunctionNamePreTransformed();
        $this->assertEquals(false, $parser->parse('method'));
    }
}