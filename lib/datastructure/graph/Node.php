<?php
/**
 * Created by JetBrains PhpStorm.
 * User: j
 * Date: 26.10.13
 * Time: 19:19
 * Node System like the JSDOM
 */

namespace chilimatic\lib\datastructure\graph;

/**
 * Class Node
 * @package chilimatic\collections
 */
class Node implements INode
{
    /**
     * this is the delimiter for multiple entries
     * since the ID should be unique so if you insert 2 or more nodes with the same
     * key in the same child collection
     *
     * id1 would be <key>
     * id2 would be <key>-0
     * id3 would be <key>-1
     *
     * @var string
     */
    const MULTIPLE_ID_ENTRY_DELIMITER = '-';

    /**
     * @var string
     */
    const DEFAULT_KEY_DELIMITER = '.';

    /**
     * parent node -> objects are references
     * so it should not blow up the memory space
     * for accessing the parent node is much easier
     *
     * @var Node
     */
    protected $parentNode = null;

    /**
     * Id of this node -> needed for the
     * searching it's unique in context
     *
     * @var string
     */
    public $id;

    /**
     * key identifier -> this is basically the
     * search word
     *
     * @var string
     */
    public $key;

    /**
     * Config Node if loaded
     * can be mixed since it should dynamic
     *
     * @var mixed
     */
    public $data;

    /**
     * @var string
     */
    protected $keyDelimiter = self::DEFAULT_KEY_DELIMITER;

    /**
     * contains the list of children
     *
     * @var Collection
     */
    public $children;

    /**
     * constructor
     *
     * @param INode $parentNode
     * @param $key
     * @param $data
     * @param string $comment
     */
    public function __construct(INode $parentNode = null, $key, $data, $comment = '')
    {
        // set the parent node
        $this->parentNode = $parentNode;
        // set the current key identifier
        $this->key = $key;
        $this->data = $data;
        // optional comment
        $this->comment = $comment;

        if ($this->parentNode) {
            new Collection($parentNode->children->idList);
        } else {
            new Collection();
        }

        $this->updateId();
    }

    /**
     * updates the current key (based on the parent element)
     * call for the moveNode method in the collections
     */
    public function updateId()
    {
        if ( empty($this->parentNode->key)) {
            $this->id = $this->key;
        } else {
            $this->id = "{$this->parentNode->id}$this->keyDelimiter{$this->key}";
        }
        $this->id = str_replace('..', '.', $this->id);
    }



    /**
     * @return string
     */
    public function __toString(){
        return json_encode($this->getData());
    }

    /**
     * gets a config variable
     * out of the depths in the chain
     *
     * @param $key
     * @param \chilimatic\lib\datastructure\graph\filter\AbstractFilter $filter
     *
     * @return \SplObjectStorage
     */
    public function getByKey($key, \chilimatic\lib\datastructure\graph\filter\AbstractFilter $filter = null)
    {
        return $this->children->getByKey($key, $filter);
    }

    /**
     * @param $key
     *
     * @return Node|null
     */
    public function getLastByKey($key) {
        if ($this->key == $key ) return $this;
        return $this->children->getLastByKey($key);
    }


    /**
     * gets a config variable
     * out of the depths in the chain
     *
     * @param $id
     * @return mixed|null
     */
    public function getById($id)
    {
        if ($this->id == $id ) return $this;
        return $this->children->getById($id);
    }


    /**
     * gets the Data
     *
     * @return mixed|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * set the current data
     *
     * @param $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * set the parent id
     *
     * @param Node $node
     * @return $this
     */
    public function setParent(Node $node = null)
    {
        $this->parentNode = $node;
        return $this;
    }

    /**
     * get the parent id
     *
     * @return int|mixed
     */
    public function getParent()
    {
        return $this->parentNode;
    }

    /**
     * get the id
     *
     * @return mixed|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * set the id
     *
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * get the id
     *
     * @return mixed|string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * set the id
     *
     * @param $key
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * adds/replace a node to node children list
     *
     * @param INode $node
     *
     * @return $this
     */
    public function addChild(INode $node)
    {
        $this->children->addNode($node);

        return $this;
    }


    /**
     * adds/replace a node to node children list
     *
     * @param Node $node
     * @return bool|null
     */
    public function removeChild(Node $node)
    {
        return $this->children->removeNode($node);
    }

    /**
     * gets the children
     *
     * @return Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * removes all of the children
     *
     * @return $this
     */
    public function deleteChildren()
    {
        $this->children->removeAll();
        return $this;
    }

    /**
     * deletes the current node and everything
     *
     * @return $this
     */
    public function delete()
    {
        $this->parentNode->children->removeNode($this);
        return $this;
    }
}