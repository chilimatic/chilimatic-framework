<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 22.10.14
 * Time: 20:58
 */

namespace chilimatic\lib\route\map;

use chilimatic\lib\route\Map;

/**
 * Class MapFactory
 *
 * @package chilimatic\lib\route\map
 */
class MapFactory
{
    /**
     * @param string $path
     * @param mixed $callback
     * @param $delimiter
     *
     * @return Map
     */
    public function make($path, $callback, $delimiter)
    {
        return new Map($path, $callback, $delimiter);
    }
}