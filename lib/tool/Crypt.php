<?php

namespace chilimatic\lib\tool;

/**
 * Class Crypt
 *
 * @package chilimatic\lib\tool
 */
Class Crypt{

    public static function encrypt($type, $value) {
        return hash($type, $value);
    }

>>>>>>> 336a13ce09982d8ab14d6fadbb1d8dd97927b344
}