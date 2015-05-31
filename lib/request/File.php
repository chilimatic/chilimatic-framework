<?php
/**
 * Created by JetBrains PhpStorm.
 * User: j
 * Date: 19.10.13
 * Time: 11:23
 * Files request object
 */

namespace chilimatic\lib\request;

/**
 * Class File
 * @package chilimatic\lib\request
 */
class File extends Generic implements RequestInterface {


    /**
     * singelton constructor
     *
     * @param array $array
     *
     * @return \chilimatic\lib\request\File
     */
    public static function getInstance(array $array = array())
    {
        if (!(self::$instance instanceof File))
        {
            // construct the object
            self::$instance = new File($array);
        }

        // return the object
        return self::$instance;
    }
}