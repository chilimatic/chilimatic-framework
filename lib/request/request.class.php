<?php
/**
 * Created by JetBrains PhpStorm.
 * User: j
 * Date: 10/19/13
 * Time: 10:22 PM
 * To change this template use File | Settings | File Templates.
 */

namespace chilimatic\lib\request;

/**
 * Class Request
 * @package chilimatic\lib\request
 */
class Request extends Generic {


    /**
     * singelton constructor
     *
     * @param array $array
     * @return \chilimatic\lib\request\Request
     */
    public static function getInstance(array $array = array())
    {
        if (!(self::$instance instanceof Generic))
        {
            // construct the object
            self::$instance = new Request($array);
        }

        // return the object
        return self::$instance;
    }
}