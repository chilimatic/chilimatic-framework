<?php
/**
 *
 * @author j
 * Date: 4/10/15
 * Time: 6:30 PM
 *
 * File: BinaryNode.php
 */
namespace chilimatic\lib\datastructure\graph\tree\binary;
use chilimatic\lib\traits\string\AsciiTrait;

/**
 * Class BinaryNode
 *
 * @package chilimatic\lib\datastructure\graph\tree\binary
 */
class BinaryNode
{
    use AsciiTrait;

    /**
     * @var string
     */
    public $key;

    /**
     * @var mixed
     */
    public $data;

    /**
     * @var BinaryNode
     */
    public $leftNode;

    /**
     * @var BinaryNode
     */
    public $rightNode;


    /**
     * @param string $key
     * @param null $data
     */
    public function __construct($key, $data = null)
    {
        $this->key = $key;
        $this->data = $data;
    }

    /**
     * @return bool|string
     */
    public function getKeySum()
    {
        return $this->getAsciiStringCharacterValueSum($this->key);
    }

    /**
     * @param $key
     *
     * @return mixed|null
     */
    public function findByKey($key, &$result = false)
    {
        if ($result) {
            return $result;
        }

        if ($this->key == $key) {
            $result = $this->data;
            return $this->data;
        }

        $retNode = null;

        if ($this->rightNode !== null) {
            $retNode = $this->rightNode->findByKey($key, $result);
        }

        if ($this->leftNode !== null) {
            $retNode = $this->leftNode->findByKey($key, $result);
        }

        return $retNode;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return BinaryNode
     */
    public function getLeftNode()
    {
        return $this->leftNode;
    }

    /**
     * @param BinaryNode $leftNode
     *
     * @return $this
     */
    public function setLeftNode($leftNode)
    {
        $this->leftNode = $leftNode;

        return $this;
    }

    /**
     * @return BinaryNode
     */
    public function getRightNode()
    {
        return $this->rightNode;
    }

    /**
     * @param BinaryNode $rightNode
     *
     * @return $this
     */
    public function setRightNode($rightNode)
    {
        $this->rightNode = $rightNode;

        return $this;
    }
}