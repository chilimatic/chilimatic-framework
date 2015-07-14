<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 25.11.13
 * Time: 14:55
 */

namespace chilimatic\lib\database;

use \chilimatic\lib\interfaces\ISingeltonString;

/**
 * Class Collection
 *
 * @package chilimatic\lib\database
 */
class Collection implements ISingeltonString
{

    /**
     * collection of opened database objects
     *
     * @var array
     */
    public $_collection = array();


    /**
     * singelton
     *
     * @var Pool
     */
    public static $instance = null;

    /**
     * @var mixed
     */
    public $current = null;

    private function __construct($database)
    {
        $this->_checkdatabase($database);
    }

    /**
     * singelton constructor
     *
     * @param mixed $database
     *
     * @return Pool
     */
    public static function getInstance($database = null)
    {

        if (self::$instance instanceof Pool) {
            self::$instance->add($database);

            return self::$instance;
        }

        self::$instance = new Pool($database);

        return self::$instance;
    }

    public static function add($database)
    {

    }

    /**
     * checks if a database already exists
     *
     * @param $database
     *
     * @return $this
     */
    private function _checkdatabase($database)
    {

        if (count($this->_collection) == 0) {
            $this->_collection[$database] = new $database();

            return $this->_collection[$database];
        }

        $new = false;
        foreach ($this->_collection as $db) {
            if ($db === $database) continue;
            $new = true;
        }

        if ($new === true) {
            $this->_collection[$database] = new $database();
        }

        return false;
    }
}