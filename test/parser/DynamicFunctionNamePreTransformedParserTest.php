<?php
/**
 *
 * @author j
 * Date: 3/7/15
 * Time: 6:21 PM
 *
 * File: DynamicFunctionNamePreTransformedParserTest.php
 *
 * Class DynamicFunctionNamePreTransformedParserTest
 */
class DynamicFunctionNamePreTransformedParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testInterface() {
        $this->assertInstanceOf('\chilimatic\lib\interfaces\IFlyWeightParser', new \chilimatic\lib\parser\DynamicCallNamePreTransformed());
    }

    /**
     * @test
     */
    public function wrongCharacterAtBeginningSyntax() {
        $parser = new chilimatic\lib\parser\DynamicCallNamePreTransformed();
        $this->assertEquals(false, $parser->parse('-methodname'));
    }

    /**
     * @test
     */
    public function wrongCharacterAtEndSyntax() {
        $parser = new chilimatic\lib\parser\DynamicCallNamePreTransformed();
        $this->assertEquals(false, $parser->parse('methodname-'));
    }

    /**
     * @test
     */
    public function wrongCaseCharacterInCallNameSyntax() {
        $parser = new chilimatic\lib\parser\DynamicCallNamePreTransformed();
        $this->assertEquals(false, $parser->parse('Methodname'));
    }

    /**
     * @test
     */
    public function wrongCharactInCallNameSyntax() {
        $parser = new chilimatic\lib\parser\DynamicCallNamePreTransformed();
        $this->assertEquals(false, $parser->parse('$method'));
    }

    /**
     * @test
     */
    public function getErrorMessage() {
        $parser = new chilimatic\lib\parser\DynamicCallNamePreTransformed();
        $this->assertEquals(false, $parser->parse('$method'));
        $this->assertEquals($parser->getInvalidCharacters() . ' are not allowed to be in the callname', $parser->getErrorMsg());
    }

    /**
     * @test
     */
    public function correctComplexCallSyntax() {
        $parser = new chilimatic\lib\parser\DynamicCallNamePreTransformed();
        $this->assertEquals(true, $parser->parse('my-method-name'));
    }

    /**
     * @test
     */
    public function correctSimpleCallSyntax() {
        $parser = new chilimatic\lib\parser\DynamicCallNamePreTransformed();
        $this->assertEquals(true, $parser->parse('method'));
    }
}