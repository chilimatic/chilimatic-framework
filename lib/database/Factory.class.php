<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 25.11.13
 * Time: 14:48
 */

namespace chilimatic\lib\database;


/**
 * Class Factory
 *
 * @package chilimatic\lib\database
 */
class Factory
{

    /**
     * instance
     *
     * @var array
     */
    private static $_instance = null;

    /**
     * private constructor
     */
    private function __construct()
    {
        return;
    }

    /**
     * factory class
     *
     *
     * @param $type
     * @param null $param
     *
     * @return array
     */
    public static function make($type, $param = null)
    {

        if (self::$_instance) return self::$_instance;
    }
} 