<?php
/**
 * Created by JetBrains PhpStorm.
 * User: j
 * Date: 01.11.13
 * Time: 16:47
 * NodeList -> JSDOM Collection
 */

namespace chilimatic\lib\node;

/**
 * Class NodeList
 * @package chilimatic\Collection
 */
class Collection
{
    /**
     * list of all nodes
     * @var array|null
     */
    private $list = null;

    /**
     * list of all ids
     * @var array|null
     */
    private $idList = null;

    /**
     * @return Collection
     */
    public function __construct(){
        $this->list = [];
        $this->idList = [];
    }

    /**
     * counts the amount of children
     *
     * @return int
     */
    public function count() {
        return count($this->list);
    }

    /**
     * @param Node $node
     *
     * @return $this
     */
    public function addNode(Node $node)
    {
        $pos = $node->getId();
        $this->list[$node->getKey()] = $node;
        $this->idList[$pos] = $node;

        return $this;
    }

    /**
     * move the node within the current schema
     *
     * @param Node $node
     * @param Node $parent
     * @return $this
     */
    public function moveNode(Node $node, Node $parent)
    {
        $node->setParent($parent)->updateId();
        $this->idList[$node->getId()] = $node;

        return $this;
    }

    /**
     * gets a node by its unique id
     *
     * @param $id
     * @return mixed|null
     */
    public function getById($id)
    {
        if (isset($this->idList[$id])) return $this->idList[$id];

        foreach($this->idList as $node)
        {
            /* @var $node Node */
            if ( ($ret = $node->getById($id)) !== null) return $ret;
        }

        return null;
    }

    /**
     * fuzzy search option based on the key
     *
     * @param $key
     * @return array|null
     */
    private function _fuzzySearch($key)
    {
        if (!$key) {
            return null;
        }

        $hit_array = null;
        $idList = array_reverse($this->idList);
        foreach ($idList as $id => $node) {
            if (strpos($id, $key) !== false) {
                return $node;
            }
        }

        return null;
    }

    /**
     * walks through the child nodes
     *
     * @param $key
     *
     * @return mixed
     */
    public function getByKey($key)
    {
        if (count($this->list) == 0) return null;

        if (($node = $this->_fuzzySearch($key)) !== null) {
            return $node;
        }

        foreach ($this->list as $node) {
            if ($node->getKey() == $key) {
                return $node;
            }

            /**
             * @var Node $node
             */
            foreach ($node->getChildren()->getList() as $cnode) {
                if ($cnode->getKey() == $key) {
                    return $cnode;
                }

                if (($rnode = $cnode->getChildren()->getByKey($key)) !== null) {
                    return $rnode;
                }
            }
        }

        return null;
    }

    /**
     * @return array|null
     */
    public function getList() {
        return $this->list;
    }

    /**
     * removes all children nodes
     *
     * @return $this
     */
    public function removeAll(){
        $this->list = array();
        return $this;
    }

    /**
     * removes a specific node
     *
     * @param Node $node
     * @return $this
     */
    public function removeNode(Node $node = null)
    {
        if (empty($node)) return $this;

        unset($this->list[$node->getKey()]);

        return $this;
    }
}