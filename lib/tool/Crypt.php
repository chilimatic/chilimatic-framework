<?php

namespace chilimatic\lib\tool;

/**
 * Class Crypt
 *
 * @package chilimatic\lib\tool
 */
Class Crypt
{

    public static function encrypt($type, $value)
    {
        return hash($type, $value);
    }
}