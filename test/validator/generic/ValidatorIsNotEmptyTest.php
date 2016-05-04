<?php

/**
 *
 * @author j
 * Date: 10/22/15
 * Time: 9:03 PM
 *
 */
class ValidatorIsNotEmptyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function checkIfValidatorImplementsTheInterface() {
        $validator = new \chilimatic\lib\validator\generic\NotEmpty();
        $this->assertInstanceOf('\chilimatic\lib\Interfaces\IFlyweightValidator', $validator);
    }

    /**
     * @test
     */
    public function checkIfEmptyStringIsValid() {
        $validator = new \chilimatic\lib\validator\generic\NotEmpty();
        $ret = $validator->validate('');

        $this->assertFalse($ret);
    }

    /**
     * @test
     */
    public function checkIfEmptyIntIsValid() {
        $validator = new \chilimatic\lib\validator\generic\NotEmpty();
        $ret = $validator->validate(0);

        $this->assertFalse($ret);
    }

    /**
     * @test
     */
    public function checkIfEmptyArrayIsValid() {
        $validator = new \chilimatic\lib\validator\generic\NotEmpty();
        $ret = $validator->validate([]);

        $this->assertFalse($ret);
    }

    /**
     * @test
     */
    public function checkIfEmptyObjectIsValid() {
        $validator = new \chilimatic\lib\validator\generic\NotEmpty();
        $ret = $validator->validate(new stdClass());

        $this->assertTrue($ret);
    }

    /**
     * @test
     */
    public function checkIfNullIsValid() {
        $validator = new \chilimatic\lib\validator\generic\NotEmpty();
        $ret = $validator->validate(null);

        $this->assertFalse($ret);
    }
}