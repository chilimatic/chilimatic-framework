<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 11.11.14
 * Time: 18:22
 */
namespace chilimatic\lib\route\routesystem;

use chilimatic\lib\route\Map;
use chilimatic\lib\route\map\MapFactory;
use chilimatic\lib\route\routesystem\noderoute\Node;
use chilimatic\lib\transformer\string\DynamicFunctionCallName;

/**
 * Class AbstractRoute
 *
 * @package chilimatic\lib\route\routesystem
 */
abstract class AbstractRoute
{

    const PATH_ROOT = '/';
    /**
     * Trait
     */
    use RouteTrait;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var Node
     */
    protected $currentRoute;

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
        $this->setTransformer(new DynamicFunctionCallName());
        $this->setMapFactory(new MapFactory());
    }


    /**
     * @param array $urlParts
     *
     * @return mixed
     */
    abstract public function getRoute(array $urlParts = []);

    /**
     * @param $uri
     * @param $callback
     * @param string $delimiter
     *
     * @return mixed
     */
    abstract public function addRoute($uri, $callback, $delimiter = Map::DEFAULT_URL_DELIMITER);

}