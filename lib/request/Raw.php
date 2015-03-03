<?php
/**
 * Created by JetBrains PhpStorm.
 * User: j
 * Date: 19.10.13
 * Time: 11:19
 * Post Object
 */

namespace chilimatic\lib\request;

/**
 * Class Raw
 *
 * @package chilimatic\lib\request
 */
class Raw extends Generic implements RequestInterface {


    /**
     * singelton constructor
     *
     * @param array $array
     *
     * @return \chilimatic\lib\request\Post
     */
    public static function getInstance(array $array = array())
    {
        if (!(self::$instance instanceof Raw))
        {
            // construct the object
            self::$instance = new Raw($array);
        }

        // return the object
        return self::$instance;
    }
}