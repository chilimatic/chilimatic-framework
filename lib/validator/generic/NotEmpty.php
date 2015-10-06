<?php

namespace chilimatic\lib\validator\generic;

/**
 * Class NotEmpty
 *
 * @package chilimatic\lib\validator\type\functions
 */
class NotEmpty implements \chilimatic\lib\interfaces\IFlyWeightValidator
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
        return !empty($value);
    }
}