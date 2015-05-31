<<<<<<< HEAD
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
interface ISingelton {

    /**
     * standard singelton method returns
     * the instance
     *
     * @param null|mixed $param
     *
     * @return mixed
     */
    public static function getInstance($param = null);
=======
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
interface ISingelton {

    /**
     * standard singelton method returns
     * the instance
     *
     * @param null|mixed $param
     *
     * @return mixed
     */
    public static function getInstance($param = null);
>>>>>>> 336a13ce09982d8ab14d6fadbb1d8dd97927b344
}