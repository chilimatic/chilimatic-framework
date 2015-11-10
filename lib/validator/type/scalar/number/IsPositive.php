<?php
/**
 *
 * @author j
 * Date: 11/9/15
 * Time: 7:17 PM
 *
 * File: IsPositive.php
 */

namespace chilimatic\lib\validator\type\scalar\number;

/**
 * Class IsPositive
 *
 * @package chilimatic\lib\validator\type\scalar
 */
class IsPositive implements \chilimatic\lib\interfaces\IFlyWeightValidator
{
    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function __invoke($value)
    {
        return $this->validate($value);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function validate($value)
    {
        return (is_numeric($value) && $value >= 0);
    }


}