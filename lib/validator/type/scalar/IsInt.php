<?php

namespace chilimatic\lib\validator\type\scalar;

/**
 *
 * @author j
 * Date: 9/27/15
 * Time: 5:19 PM
 *
 * File: IsBool.php
 */

/**
 * Class IsInt
 *
 * @package chilimatic\lib\validator\type\scalar
 */
class IsInt implements \chilimatic\lib\Interfaces\IFlyWeightValidator
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
        return is_int($value);
    }


}