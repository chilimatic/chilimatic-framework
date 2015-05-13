<?php
use chilimatic\lib\validator\DynamicCallNamePreTransformed;

/**
 *
 * @author j
 * Date: 3/7/15
 * Time: 6:21 PM
 *
 * File: DynamicFunctionNamePreTransformedTransformerTest.php
 *
 * Class DynamicFunctionNamePreTransformedTransformerTest
 */
class DynamicCallNamePreTransformedValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testInterface() {
        $this->assertInstanceOf('\chilimatic\lib\interfaces\IFlyWeightValidator', new DynamicCallNamePreTransformed());
    }

    /**
     * @test
     */
    public function wrongCharacterAtBeginningSyntax() {
        $tranformer = new DynamicCallNamePreTransformed();
        $this->assertEquals(false, $tranformer->validate('-methodname'));
    }

    /**
     * @test
     */
    public function wrongCharacterAtEndSyntax() {
        $tranformer = new DynamicCallNamePreTransformed();
        $this->assertEquals(false, $tranformer->validate('methodname-'));
    }

    /**
     * @test
     */
    public function wrongCaseCharacterInCallNameSyntax() {
        $tranformer = new DynamicCallNamePreTransformed();
        $this->assertEquals(false, $tranformer->validate('Methodname'));
    }

    /**
     * @test
     */
    public function wrongCharactInCallNameSyntax() {
        $tranformer = new DynamicCallNamePreTransformed();
        $this->assertEquals(false, $tranformer->validate('$method'));
    }

    /**
     * @test
     */
    public function getErrorMessage() {
        $tranformer = new DynamicCallNamePreTransformed();
        $this->assertEquals(false, $tranformer->validate('$method'));
        $this->assertEquals($tranformer->getInvalidCharacters() . ' are not allowed to be in the callname', $tranformer->getErrorMsg());
    }

    /**
     * @test
     */
    public function correctComplexCallSyntax() {
        $tranformer = new DynamicCallNamePreTransformed();
        $this->assertEquals(true, $tranformer->validate('my-method-name'));
    }

    /**
     * @test
     */
    public function correctSimpleCallSyntax() {
        $tranformer = new DynamicCallNamePreTransformed();
        $this->assertEquals(true, $tranformer->validate('method'));
    }
}