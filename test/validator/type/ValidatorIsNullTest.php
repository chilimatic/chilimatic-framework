<?php
/**
 *
 * @author j
 * Date: 10/22/15
 * Time: 9:35 PM
 *
 * File: ValidatorIsNullTest.php
 */

class ValdiatorIsNullTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function checkIfValidatorImplementsTheInterface() {
        $validator = new \chilimatic\lib\validator\type\scalar\IsNull();
        $this->assertInstanceOf('\chilimatic\lib\interfaces\IFlyweightValidator', $validator);
    }

    /**
     * @test
     */
    public function checkIfStringIsValid() {
        $validator = new \chilimatic\lib\validator\type\scalar\IsNull();
        $ret = $validator->validate('asdasdasd');

        $this->assertFalse($ret);
    }

    /**
     * @test
     */
    public function checkIfIntIsValid() {
        $validator = new \chilimatic\lib\validator\type\scalar\IsNull();
        $ret = $validator->validate(1230);

        $this->assertFalse($ret);
    }

    /**
     * @test
     */
    public function checkIfFloatIsValid() {
        $validator = new \chilimatic\lib\validator\type\scalar\IsNull();
        $ret = $validator->validate(1230.23);

        $this->assertFalse($ret);
    }

    /**
     * @test
     */
    public function checkIfBinaryIntIsValidValid() {
        $validator = new \chilimatic\lib\validator\type\scalar\IsNull();
        $ret = $validator->validate(0b1100000011101001010);

        $this->assertFalse($ret);
    }

    /**
     * @test
     */
    public function checkIfArrayIsValid() {
        $validator = new \chilimatic\lib\validator\type\scalar\IsNull();
        $ret = $validator->validate([]);

        $this->assertFalse($ret);
    }

    /**
     * @test
     */
    public function checkIfObjectIsValid() {
        $validator = new \chilimatic\lib\validator\type\scalar\IsNull();
        $ret = $validator->validate(new stdClass());

        $this->assertFalse($ret);
    }

    /**
     * @test
     */
    public function checkIfBoolIsValid() {
        $validator = new \chilimatic\lib\validator\type\scalar\IsNull();
        $ret = $validator->validate(true);

        $this->assertFalse($ret);
    }

    /**
     * @test
     */
    public function checkIfNullIsValid() {
        $validator = new \chilimatic\lib\validator\type\scalar\IsNull();
        $ret = $validator->validate(null);

        $this->assertTrue($ret);
    }
}