<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 11.11.14
 * Time: 17:34
 */
namespace chilimatic\lib\route\routesystem;

use chilimatic\lib\datastructure\graph\tree\binary\BinaryTree;
use chilimatic\lib\exception\RouteException;
use chilimatic\lib\route\Map;


/**
 * Class NodeRoute
 * @package chilimatic\lib\route\routesystem
 */
class NodeRoute extends AbstractRoute
{

    /**
     * Main Node
     *
     * @var BinaryTree
     */
    private $binaryTree;


    public function __construct($path) {
        $this->binaryTree = new BinaryTree();
        parent::__construct($path);
    }

    /**
     * @return BinaryTree
     * @throws \chilimatic\lib\exception\RouteException
     */
    public function getRoot() {
        if ($this->binaryTree->isEmpty()) {
            $this->binaryTree->insert('/', $this->getDefaultRoute());
        }

        return $this->binaryTree->getRoot();
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

        if (($map = $this->binaryTree->findByKey($path))) {
            return $map;
        }

        if (($map = $this->getStandardRouting($path))) {
            return $map;
        }
        
        return $this->getRoot()->getData();
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