<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 11.11.14
 * Time: 17:34
 */
namespace chilimatic\lib\route\routesystem;
use chilimatic\lib\exception\Exception_Route;
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
     * @throws \chilimatic\lib\exception\Exception_Route
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
     * @throws Exception_Route
     */
    public function getRoute( $path = null )
    {

        $this->currentRoute = new TreeNode($this->getRoot(),$path, $this->getStandardRouting($path));

        if (!$this->currentRoute) {
            $this->currentRoute = $this->getRoot()->findTreeBranch($path, MAP::DEFAULT_URL_DELIMITER);
        }

        return $this->currentRoute->getValue();
    }

    /**
     * register a new custom Route / overwrite an old one
     *
     * @param string $uri
     * @param mixed $callback
     * @param $delimiter
     *
     * @throws Exception_Route
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
                throw new Exception_Route(sprintf(_('There is no Route entered %s'), $uri));
            }

            // class for mapping
            $route = new Map($uri, $callback, $delimiter);
            $this->rootNode->appendToBranch($uri, $route, Map::DEFAULT_URL_DELIMITER);

        }
        catch ( Exception_Route $e )
        {
            throw $e;
        }

    }
}