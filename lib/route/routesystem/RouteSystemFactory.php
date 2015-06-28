<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 11.11.14
 * Time: 18:27
 */

namespace chilimatic\lib\route\routesystem;

/**
 * Class RouteSystemFactory
 *
 * @package chilimatic\lib\route\routesystem
 */
class RouteSystemFactory
{

    /**
     * @param string $type
     *
     * @return AbstractRoute
     */
    public static function make($type = 'Default', $param)
    {
        $className = __NAMESPACE__ . "\\{$type}Route";

        if (!class_exists($className)) return null;

        return new $className($param);
    }
}