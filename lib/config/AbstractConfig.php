<?php

/**
 * Created by JetBrains PhpStorm.
 * User: J
 * Date: 26.10.13
 * Time: 17:54
 *
 * Generic abstract class to implement the Config interface
 * which than is inherited by the children
 *
 */

namespace chilimatic\lib\config;

/**
 * Class Config_Generic
 *
 * @package chilimatic\lib\config
 */
abstract class AbstractConfig implements IConfig
{

    /**
     * comment within the nodes that it's a given parameter
     * through the constructor
     *
     * @var string
     */
    const INIT_PARAMETER = 'init-param';

    /**
     * main config node
     *
     * @var Node
     */
    public $mainNode;


    /**
     * get the last use node [insert/delete/update .... and so on]
     *
     * @var Node|null
     */
    public $lastNewNode;

    /**
     * constructor
     *
     * @param mixed $param
     */
    public function __construct($param = null)
    {
        // set the main node on which all other nodes should be appended
        $this->mainNode = new Node(null, IConfig::MAIN_NODE_KEY, null);

        // add custom parameters
        if (is_array($param) && count($param)) {
            // set the given parameters
            foreach ($param as $key => $value) {
                $node = new Node($this->mainNode, $key, $value, self::INIT_PARAMETER);
                $this->mainNode->addChild($node);
            }
        }

        $this->load($param);
    }


    /**
     * loads the config based on the type / source
     *
     * @return mixed
     */
    abstract public function load();

    /**
     * deletes a config
     *
     * @param string $key
     *
     * @return mixed
     */
    public function delete($key = '')
    {
        $nodeList = $this->mainNode->getByKey($key);
        if (empty($nodeList)) {
            true;
        }

        foreach ($nodeList as $node) {
            $node->delete();
        }

        unset($node);

        return true;
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    public function deleteById($id = '')
    {
        $node = $this->mainNode->getById($id);

        if (empty($node)) {
            true;
        }

        return $node->delete();
    }

    /**
     * gets a specific parameter
     *
     * @param $var
     *
     * @return mixed
     */
    public function get($var)
    {
        if (!$this->mainNode) {
            return null;
        }

        $node = $this->mainNode->getLastByKey($var);
        if (empty($node)) return null;

        return $node->getData();
    }

    /**
     * gets a specific parameter
     *
     * @param $id
     *
     * @internal param $var
     * @return mixed
     */
    public function getById($id)
    {
        $node = $this->mainNode->getById($id);
        if (empty($node)) return null;

        return $node->getData();
    }

    /**
     * sets a specific parameter
     *
     * @param $id
     * @param $val
     *
     * @return mixed
     */
    public function setById($id, $val)
    {
        // set the variable
        if (empty($id)) return $this;

        $node = new Node($this->mainNode, $id, $val);

        $this->mainNode->addChild($node);

        return $this;

    }

    /**
     * sets a specific parameter
     *
     * @param $key
     * @param $val
     *
     * @return mixed
     */
    public function set($key, $val)
    {
        // set the variable
        if (empty($key)) return $this;

        if (!($node = $this->mainNode->getLastByKey($key))) {
            $newNode = new Node($this->mainNode, $key, $val);
            $this->mainNode->addChild($newNode);
        } else {
            $newNode = new Node($node, $key, $val);
            $node->addChild($newNode);
        }

        $this->lastNewNode = $newNode;

        return $this;

    }

    /**
     * saves the specified config
     *
     * @param Node $node
     *
     * @internal param $array ;
     *
     * @return mixed
     */
    public abstract function saveConfig(Node $node = null);
}