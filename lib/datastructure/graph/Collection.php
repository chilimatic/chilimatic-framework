<?php
/**
 * Created by JetBrains PhpStorm.
 * User: j
 * Date: 01.11.13
 * Time: 16:47
 * NodeList -> JSDOM Collection
 */

namespace chilimatic\lib\datastructure\graph;

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
    public $list = null;

    /**
     * list of all ids WITHIN ALL CHILD AN PARENT NODES !!!! this is a reference ! :)
     * @var array|null
     */
    public $idList = null;

    /**
     * the id list is an reference through the whole graph system
     *
     * @param null $idList
     */
    public function __construct(&$idList = null) {

        if ($idList !== null) {
            $this->idList = &$idList;
        } else {
            $this->idList = [];
        }
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
        if (isset($this->idList[$node->id])) {
            $node->setId($this->getNextPossibleIdinContext($node));
        }

        $this->list[$node->key] = $node;
        $this->idList[$node->id] = $node;

        return $this;
    }

    /**
     * this method will iterate over all sibl
     *
     * @param Node $node
     *
     * @return mixed|string
     * @throws \Exception
     */
    public function getNextPossibleIdInContext(Node $node)
    {
        if (!isset($this->idList[$node->id])) {
            return $node->id;
        }

        $newId = false;
        $id = $node->id;
        for ($i = 0; $newId == false; $i++) {
            $newId = $id . Node::MULTIPLE_ID_ENTRY_DELIMITER . "$i";

            if ($id > 100) {
                throw new \Exception('Are you crazy? 1000 child elements with the same ID ?');
            }
        }

        return $newId;
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
        $this->idList[$node->id] = $node;

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
        if (isset($this->idList[$id])) {
            return $this->idList[$id];
        }

        foreach($this->idList as $node)
        {
            /* @var $node Node */
            if ( ($ret = $node->getById($id)) !== null) return $ret;
        }

        return null;
    }

    /**
     * fuzzy search option based on the id
     * -> it's a strpos comparison so every hit is returned
     *
     * @param $key
     * @return \SplDoublyLinkedList()|null
     */
    public function getByIdFuzzy($key)
    {
        if (!$key) {
            return null;
        }

        $resultSet = new \SplDoublyLinkedList();
        foreach ($this->idList as $id => $node) {
            if (strpos($id, $key) !== false) {
                $resultSet->push($node);
            }
        }
        return $resultSet;
    }

    /**
     * walks through the child nodes
     *
     * @param $key
     *
     * @return mixed
     */
    public function getLastByKey($key)
    {
        if (count($this->list) == 0) return null;

        if (($set = $this->getByIdFuzzy($key)) && count($set)) {
            return $set->pop();
        }

        return null;
    }


    /**
     * iterates through all the child nodes
     * returns an object storage in the end
     *
     * @param $key
     *
     * @return mixed
     */
    public function getByKey($key, \chilimatic\lib\datastructure\graph\filter\AbstractFilter $filter = null)
    {

        $result = new \SplObjectStorage();

        if (count($this->list) == 0) return $result;

        /**
         * @var Node $node
         */
        foreach ($this->list as $node) {
            if ($node->key == $key && !$result->contains($node)) {
                $result->attach($node);
            }


            if ($subSet = $node->getByKey($key, $filter))
            {
                if (!$subSet->count()) continue;

                foreach ($subSet as $cNode) {
                    if (!$result->contains($cNode)) {
                        $result->attach($cNode);
                    }
                }
            }
        }

        if ($result->count() && $filter) {
            return $filter($result);
        }

        return $result;
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