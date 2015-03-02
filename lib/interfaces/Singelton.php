<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 25.11.13
 * Time: 15:18
 *
 * this is so all singelton Classes use the same get method
 * u might think this is to much, you're right still it's needed
 */

namespace chilimatic\lib\interfaces;

/**
 * Interface Singelton
 *
 * @package chilimatic\lib\interfaces
 */
interface Singelton {

    /**
     * standard singelton method returns
     * the instance
     *
     * @param null|\stdClass $param
     *
     * @return mixed
     */
    public static function getInstance(\stdClass $param = null);
}