<?php
use chilimatic\lib\transformer\string\AnnotationValidatorPrependNameSpace;

/**
 *
 * @author j
 * Date: 9/27/15
 * Time: 7:55 PM
 *
 * File: AnnotationValidatorPrependNameSpaceTest.php
 */
class AnnotationValidatorPrependNameSpaceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function checkIfTheNameSpaceIsPrependedIfTheClassHasNoPrefix() {
        $transformer = new AnnotationValidatorPrependNameSpace();
        $className = $transformer->transform('class');
        $this->assertEquals('\\class', $className);
    }

    /**
     * @test
     */
    public function checkIfTheNameSpaceIsNotPrependedIfTheClassHasAPrefix() {
        $transformer = new AnnotationValidatorPrependNameSpace();
        $className = $transformer->transform('\\class');
        $this->assertEquals('\\class', $className);
    }


    /**
     * @test
     */
    public function checkIfASpecificNameSpaceIsPrePendedIfPassedAsOptionParameter() {
        $transformer = new AnnotationValidatorPrependNameSpace();
        $className = $transformer->transform('class', [AnnotationValidatorPrependNameSpace::NAMESPACE_OPTION_INDEX => 'this']);
        $this->assertEquals('\\this\\class', $className);
    }

    /**
     * @test
     */
    public function checkIfATheNameSpaceDelimiterWillNotGetAttachedTwice() {
        $transformer = new AnnotationValidatorPrependNameSpace();
        $className = $transformer->transform('class', [AnnotationValidatorPrependNameSpace::NAMESPACE_OPTION_INDEX => '\this']);
        $this->assertEquals('\\this\\class', $className);
    }
}