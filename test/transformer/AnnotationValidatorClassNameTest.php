<?php
use chilimatic\lib\transformer\string\AnnotationValidatorClassName;

/**
 *
 * @author j
 * Date: 9/27/15
 * Time: 7:55 PM
 *
 * File: AnnotationValidatorClassNameTest.php
 */
class AnnotationValidatorClassNameTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function checkIfTheFirstLetterIsUpperCase() {
        $transformer = new AnnotationValidatorClassName();
        $className = $transformer->transform('class');
        $this->assertEquals('Class', $className);
    }

    /**
     * @test
     */
    public function checkIfFirstLetterAfterADelimiterIsUpperCase() {
        $transformer = new AnnotationValidatorClassName();
        $className = $transformer('\class');
        $this->assertEquals('\Class', $className);
    }

    /**
     * @test
     */
    public function checkIfTheBeginningLetterOfTheClassNameIsUpperCaseAfterTheLastBackslash() {
        $transformer = new AnnotationValidatorClassName();
        $className = $transformer('\my\test\class');
        $this->assertEquals('\my\test\Class', $className);
    }
}