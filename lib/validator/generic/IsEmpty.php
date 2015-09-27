<?php

namespace chilimatic\lib\validator\generic;

/**
 *
 * @author j
 * Date: 9/27/15
 * Time: 5:19 PM
 *
 * File: IsEmpty.php
 */

/**
 * Class IsEmpty
 *
 * @package chilimatic\lib\validator\type\functions
 */
class IsEmpty implements \chilimatic\lib\interfaces\IFlyWeightValidator
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
        return empty($value);
    }
}