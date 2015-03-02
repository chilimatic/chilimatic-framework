<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 6/6/14
 * Time: 9:23 AM
 */

namespace chilimatic\lib\node;

/**
 * Class Config
 * @package chilimatic\collection
 */
abstract class AbstractNode {

    /**
     * main config node
     *
     * @var Node
     */
    public $main_node = null;

    /**
     * constructor
     *
     * @param mixed $param
     */
    public function __construct($param = null)
    {
        $this->init($param);
    }


    /**
     * loads the config based on the type / source
     *
     * @param mixed $param
     *
     * @return mixed
     */
    abstract public function init($param = null);

    /**
     * deletes a config
     *
     * @param string $key
     * @return mixed
     */
    public function delete($key = '')
    {
        $node = $this->main_node->getByKey($key);
        if (empty($node)) return $this->main_node;
        $this->main_node->getChildren()->removeNode($node);
        unset($node);
        return $this->main_node;
    }

    /**
     * gets a specific parameter
     *
     * @param $var
     * @return mixed
     */
    public function get($var)
    {
        $node = $this->main_node->getByKey($var);
        if (empty($node)) return NULL;
        return $node->getValue();
    }

    /**
     * gets a specific parameter
     *
     * @param $id
     * @internal param $var
     * @return mixed
     */
    public function getById($id)
    {
        $node = $this->main_node->getById($id);
        if (empty($node)) return NULL;
        return $node->getValue();
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
        if ( empty( $id ) ) return $this;

        $node = new Node($this->main_node, $id, $val);

        $this->main_node->addChild($node);

        return $this;
    }

    /**
     * sets a specific parameter
     *
     * @param $key
     * @param $val
     * @return mixed
     */
    public function set($key, $val)
    {
        // set the variable
        if ( empty($key) ) return $this;

        $node = new Node($this->main_node, $key, $val);
        $this->main_node->addChild($node);

        return $this;
    }
} 