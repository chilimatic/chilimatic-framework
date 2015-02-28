<?php
namespace chilimatic\lib\route\map;

/**
 * Class Generic
 * @package chilimatic\lib\route\map
 */
abstract class Generic implements MapCallInterface {

    /**
     * @var mixed
     */
    protected $config = null;

    /**
     * @param $config
     *
     * @internal param $type
     */
    final public function __construct($config) {
        $this->config = $config;
        $this->init();
    }

    /**
     * @return mixed
     */
    abstract function init();

    /**
     * @param $param
     * @return mixed
     */
    abstract function call($param = null);

}