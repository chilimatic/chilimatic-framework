<?php
/**
 * Created by PhpStorm.
 * User: J
 * Date: 25.11.13
 * Time: 16:01
 */

namespace chilimatic\lib\interfaces;

/**
 * Interface SingeltonString
 *
 * @package chilimatic\lib\interfaces
 */
interface SingeltonString{
    /**
     * standard singelton method returns
     * the instance
     *
     * @param null|string $param
     *
     * @return mixed
     */
    public static function getInstance($param = '');
}