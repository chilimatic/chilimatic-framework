<?php
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
class DynamicFunctionNamePreTransformedTransformerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testInterface() {
        $this->assertInstanceOf('\chilimatic\lib\interfaces\IFlyWeightTransformer', new \chilimatic\lib\transformer\string\DynamicFunctionCallName());
    }

    /**
     * @test
     */
    public function wrongCharacterAtBeginningSyntax() {
        $tranformer = new \chilimatic\lib\transformer\string\DynamicFunctionCallName();
        $this->assertEquals(false, $tranformer->transform('-methodname'));
    }

    /**
     * @test
     */
    public function wrongCharacterAtEndSyntax() {
        $tranformer = new \chilimatic\lib\transformer\string\DynamicFunctionCallName();
        $this->assertEquals(false, $tranformer->transform('methodname-'));
    }

    /**
     * @test
     */
    public function wrongCaseCharacterInCallNameSyntax() {
        $tranformer = new \chilimatic\lib\transformer\string\DynamicFunctionCallName();
        $this->assertEquals(false, $tranformer->transform('Methodname'));
    }

    /**
     * @test
     */
    public function wrongCharactInCallNameSyntax() {
        $tranformer = new \chilimatic\lib\transformer\string\DynamicFunctionCallName();
        $this->assertEquals(false, $tranformer->transform('$method'));
    }

    /**
     * @test
     */
    public function getErrorMessage() {
        $tranformer = new \chilimatic\lib\transformer\string\DynamicFunctionCallName();
        $this->assertEquals(false, $tranformer->transform('$method'));
        $this->assertEquals($tranformer->getInvalidCharacters() . ' are not allowed to be in the callname', $tranformer->getErrorMsg());
    }

    /**
     * @test
     */
    public function correctComplexCallSyntax() {
        $tranformer = new \chilimatic\lib\transformer\string\DynamicFunctionCallName();
        $this->assertEquals(true, $tranformer->transform('my-method-name'));
    }

    /**
     * @test
     */
    public function correctSimpleCallSyntax() {
        $tranformer = new \chilimatic\lib\transformer\string\DynamicFunctionCallName();
        $this->assertEquals(true, $tranformer->transform('method'));
    }
}