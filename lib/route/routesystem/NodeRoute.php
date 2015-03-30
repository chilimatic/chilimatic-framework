<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 11.11.14
 * Time: 17:34
 */
namespace chilimatic\lib\route\routesystem;

use chilimatic\lib\exception\RouteException;
use chilimatic\lib\datastructure\graph\TreeNode;
use chilimatic\lib\route\Map;
use chilimatic\lib\route\routesystem\noderoute\Node;


/**
 * Class NodeRoute
 * @package chilimatic\lib\route\routesystem
 */
class NodeRoute extends AbstractRoute
{

    /**
     * Main Node
     *
     * @var Node
     */
    private $rootNode;


    /**
     * @return Node
     * @throws \chilimatic\lib\exception\RouteException
     */
    public function getRoot() {
        if (!$this->rootNode) {
            $this->rootNode = new Node(null, '.', $this->getDefaultRoute(), 'Root Node');
        }

        return $this->rootNode;
    }

    /**
     * @param $map
     *
     * @return $this
     */
    public function setRootNode($map)
    {
        $this->rootNode = new Node(null, '.', $map, 'Root Node');
        return $this;
    }

    /**
     * the real routing should happen here
     *
     * @param mixed $path
     *
     * @return null
     *
     * @throws RouteException
     */
    public function getRoute( $path = null )
    {

        $this->currentRoute = new TreeNode($this->getRoot(),$path, $this->getStandardRouting($path));

        if (!$this->currentRoute) {
            $this->currentRoute = $this->getRoot()->findTreeBranch($path, MAP::DEFAULT_URL_DELIMITER);
        }

        return $this->currentRoute->getData();
    }

    /**
     * register a new custom Route / overwrite an old one
     *
     * @param string $uri
     * @param mixed $callback
     * @param $delimiter
     *
     * @throws RouteException
     * @return void
     */
    public function addRoute( $uri , $callback , $delimiter = Map::DEFAULT_URL_DELIMITER )
    {
        try
        {
            /**
             * if the uri is empty throw an exception
             */
            if ( empty($uri) ) {
                throw new RouteException(sprintf(_('There is no Route entered %s'), $uri));
            }

            // class for mapping
            $route = new Map($uri, $callback, $delimiter);
            $this->rootNode->appendToBranch($uri, $route, Map::DEFAULT_URL_DELIMITER);

        } catch ( RouteException $e ) {
            throw $e;
        }

    }
}