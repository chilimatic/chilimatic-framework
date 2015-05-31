<<<<<<< HEAD
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
 * Class Request_Post
 *
 * @package chilimatic\lib\request
 */
class Post extends Generic implements RequestInterface{


    /**
     * singelton constructor
     *
     * @param array $array
     *
     * @return \chilimatic\lib\request\Post
     */
    public static function getInstance(array $array = array())
    {
        if (!(self::$instance instanceof Post))
        {
            // construct the object
            self::$instance = new Post($array);
        }

        // return the object
        return self::$instance;
    }
=======
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
 * Class Request_Post
 *
 * @package chilimatic\lib\request
 */
class Post extends Generic implements RequestInterface{


    /**
     * singelton constructor
     *
     * @param array $array
     *
     * @return \chilimatic\lib\request\Post
     */
    public static function getInstance(array $array = array())
    {
        if (!(self::$instance instanceof Post))
        {
            // construct the object
            self::$instance = new Post($array);
        }

        // return the object
        return self::$instance;
    }
>>>>>>> 336a13ce09982d8ab14d6fadbb1d8dd97927b344
}