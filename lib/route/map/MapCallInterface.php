<?php
namespace chilimatic\lib\route\map;

/**
 * Created by PhpStorm.
 * User: j
 * Date: 6/14/14
 * Time: 8:15 PM
 */
use chilimatic\lib\interfaces\IFlyWeightParser;

/**
 * Interface MapCallInterface
 * @package chilimatic\lib\route\map
 */
Interface MapCallInterface {

    /**
     * @param mixed $config
     * @param IFlyWeightParser $parser
     */
    public function __construct($config, IFlyWeightParser $parser = null);

    /**
     * @return mixed
     */
    public function init();


    /**
     * @param $param
     * @return mixed
     */
    public function call($param);
}