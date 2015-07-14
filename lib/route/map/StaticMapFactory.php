<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 18.10.14
 * Time: 15:42
 */
namespace chilimatic\lib\route\map;

/**
 * Interface StaticMapFactory
 *
 * @package chilimatic\lib\route\map
 */
Interface StaticMapFactory
{

    /**
     * @param $type
     * @param $config
     *
     * @return mixed
     */
    public static function make($type, $config);

}