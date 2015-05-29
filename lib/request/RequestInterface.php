<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 21.10.14
 * Time: 18:54
 */
namespace chilimatic\lib\request;


/**
 * Interface RequestInterface
 * @package chilimatic\lib\request
 */
interface RequestInterface {

    /**
     * @param array $param
     * @return mixed
     */
    public static function getInstance(array $param = array());
}