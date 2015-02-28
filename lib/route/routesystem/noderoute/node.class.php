<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 11.02.14
 * Time: 12:22
 */

namespace chilimatic\lib\route\routesystem\noderoute;
use chilimatic\lib\route\Map;

/**
 * Class Node
 *
 * Routing node system
 *
 * @package chilimatic\lib\route\routesystem\noderoute
 */
class Node extends \chilimatic\lib\node\TreeNode
{

    /**
     * maybe some comments for the nodes
     *
     * @var string
     */
    private $comment = '';

    /**
     * @param \chilimatic\lib\route\Node $parentNode
     * @param $key
     * @param \chilimatic\lib\route\Map $map
     * @param string $comment
     */
    public function __construct(Node $parentNode = null, $key, Map $map, $comment = '')
    {
        // set the parent node
        $this->parentNode = $parentNode;
        // set the current path identifier
        $this->key = $key;
        // set the current value of the node

        if ( empty($this->parentNode->$key)) {
            $this->id = (string) $key;
        } else {
            $this->id = "{$this->parentNode->$key}.{$key}";
        }


        // add the map
        $this->value = $map;

        /**
         * remove double slashes
         */
        $this->id = str_replace('//', '/', $this->id);

        // optional comment
        $this->comment = $comment;

        /**
         * add a Route_NodeList for the children
         */
        $this->children = new NodeCollection();
    }

    public function getMap() {
        return $this->getValue();
    }

    /**
     * gets a comment
     *
     * @return string
     */
    public function getComment(){
        return $this->comment;
    }

    /**
     * sets a comment
     *
     * @param $comment
     *
     * @return $this
     */
    public function setComment($comment) {
        $this->comment = $comment;
        return $this;
    }


    public function createTreePath($urlParts = array()) {



    }

}