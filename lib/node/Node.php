<?php
/**
 * Created by JetBrains PhpStorm.
 * User: j
 * Date: 26.10.13
 * Time: 19:19
 * Node System like the JSDOM
 */

namespace chilimatic\lib\node;

/**
 * Class Node
 * @package chilimatic\collections
 */
class Node
{
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
     * searching and specific calls
     *
     * @var string
     */
    protected $id = '';

    /**
     * key value is the
     *
     * @var string
     */
    protected $key = '';

    /**
     * Config Node if loaded
     * can be mixed since it should dynamic
     *
     * @var mixed
     */
    protected $value = null;

    /**
     * @var string
     */
    protected $keyDelimiter = self::DEFAULT_KEY_DELIMITER;


    /**
     * contains the list of children
     *
     * @var Collection
     */
    protected $children = null;

    /**
     * constructor
     *
     * @param Node $parentNode
     * @param $key
     * @param $value
     * @param string $comment
     * @internal param $parent_id
     */
    public function __construct(Node $parentNode = null, $key, $value, $comment = '')
    {
        // set the parent node
        $this->parentNode = $parentNode;
        // set the current key identifier
        $this->key = $key;
        $this->value = $value;
        // optional comment
        $this->comment = $comment;
        $this->children = new Collection();
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
            $this->id = "{$this->parentNode->key}".$this->keyDelimiter."{$this->key}";
        }
        $this->id = str_replace('..', '.', $this->id);
    }

    /**
     * gets the value
     *
     * @return mixed|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * gets a config variable
     * out of the depths in the chain
     *
     * @param $key
     * @return mixed|null
     */
    public function getByKey($key)
    {
        if ($this->key == $key ) return $this;
        return $this->children->getByKey($key);
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
     * set the current value
     *
     * @param $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
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
     * @param Node $node
     *
     * @return $this
     */
    public function addChild(Node $node)
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
     * @return Node_Collection
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
     */
    public function delete()
    {
        $this->parentNode->children->removeNode($this);
        return $this;
    }
}