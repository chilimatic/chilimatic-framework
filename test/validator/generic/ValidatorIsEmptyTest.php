<?php

/**
 *
 * @author j
 * Date: 10/22/15
 * Time: 9:03 PM
 *
 */
class ValidatorIsEmptyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function checkIfValidatorImplementsTheInterface() {
        $validator = new \chilimatic\lib\validator\generic\IsEmpty();
        $this->assertInstanceOf('\chilimatic\lib\Interfaces\IFlyweightValidator', $validator);
    }

    /**
     * @test
     */
    public function checkIfEmptyStringIsValid() {
        $validator = new \chilimatic\lib\validator\generic\IsEmpty();
        $ret = $validator->validate('');

        $this->assertTrue($ret);
    }

    /**
     * @test
     */
    public function checkIfEmptyIntIsValid() {
        $validator = new \chilimatic\lib\validator\generic\IsEmpty();
        $ret = $validator->validate(0);

        $this->assertTrue($ret);
    }

    /**
     * @test
     */
    public function checkIfEmptyArrayIsValid() {
        $validator = new \chilimatic\lib\validator\generic\IsEmpty();
        $ret = $validator->validate([]);

        $this->assertTrue($ret);
    }

    /**
     * @test
     */
    public function checkIfEmptyObjectIsValid() {
        $validator = new \chilimatic\lib\validator\generic\IsEmpty();
        $ret = $validator->validate(new stdClass());

        $this->assertFalse($ret);
    }

    /**
     * @test
     */
    public function checkIfNullIsValid() {
        $validator = new \chilimatic\lib\validator\generic\IsEmpty();
        $ret = $validator->validate(null);

        $this->assertTrue($ret);
    }
}