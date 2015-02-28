<?php
namespace chilimatic\lib\route\map;

/**
 * Created by PhpStorm.
 * User: j
 * Date: 6/14/14
 * Time: 8:15 PM
 */

/**
 * Interface MapCallInterface
 * @package chilimatic\lib\route\map
 */
Interface MapCallInterface {

    /**
     * @param $config
     */
    public function __construct($config);

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