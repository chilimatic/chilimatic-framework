<?php
/**
 * Created by JetBrains PhpStorm.
 * User: j
 * Date: 19.10.13
 * Time: 11:21
 * Get Request Object
 */

namespace chilimatic\lib\request;

/**
 * Class Get
 * @package chilimatic\lib\request
 */
class Get extends Generic implements RequestInterface{

    /**
     * singelton constructor
     *
     * @param array $array
     *
     * @return \chilimatic\lib\request\Get
     */
    public static function getInstance(array $array = array())
    {
        if (!(self::$instance instanceof Get))
        {
            // construct the object
            self::$instance = new Get($array);
        }

        // return the object
        return self::$instance;
    }

}