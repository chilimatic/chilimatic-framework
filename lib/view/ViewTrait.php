<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 28.10.14
 * Time: 10:13
 */

namespace chilimatic\lib\view;

/**
 * Class Generic
 *
 * @package chilimatic\lib\view
 */
trait ViewTrait
{

    /**
     * Setting object
     *
     * @var \stdClass
     */
    public $setting;


    /**
     * engine variables (the ones who need to be displayed)
     *
     * @var \stdClass
     */
    public $engineVarList = null;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->setting       = new \stdClass();
        $this->engineVarList = new \stdClass();

        return;
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return $this|bool
     */
    public function setConfigVariable($key, $value)
    {

        if (!$key) {
            return false;
        }

        $this->setting->$key = $value;

        return $this;
    }


    /**
     * @param array $param
     *
     * @return $this|bool
     */
    public function setConfigVariableList(array $param)
    {

        if (!is_array($param)) return false;

        foreach ($param as $key => $value) {
            if (!$key) continue;
            $this->setting->$key = $value;
        }

        return $this;
    }


    /**
     * @param string $key
     * @param mixed $value
     *
     * @return $this|bool
     */
    public function set($key, $value)
    {

        if (empty($key)) return false;

        $this->engineVarList->$key = $value;

        return $this;
    }

    /**
     * just adds a variable to the others
     *
     * @param string $key
     * @param mixed $value
     *
     * @return $this|bool
     */
    public function add($key, $value)
    {
        if (empty($key)) return false;

        if (empty($this->engineVarList->$key)) {
            $this->engineVarList->$key = $value;

            return $this;
        }

        if (is_array($this->engineVarList->$key)) {
            array_merge($this->engineVarList->$key, $value);
        }

        return $this;
    }


    /**
     * @param array $param
     *
     * @return $this|bool
     */
    public function setList(array $param)
    {

        if (!is_array($param)) return false;

        foreach ($param as $key => $value) {
            if (!$key) continue;
            $this->engineVarList->$key = $value;
        }

        return $this;
    }

    /**
     * @param $param
     *
     * @return bool
     */
    public function get($param = '')
    {

        if (empty($param)) return false;

        if (!is_array($param) || !property_exists($this->engineVarList, $param)) return false;

        return $this->engineVarList->$param;
    }

    /**
     * @return mixed
     */
    public function getAll()
    {
        return $this->engineVarList;
    }


    /**
     * @param $variable
     *
     * @return bool
     */
    public function getConfigVariable($variable)
    {
        if (empty($variable)) return false;

        if (!property_exists($this->setting, $variable)) return false;

        return $this->setting->$variable;
    }


    /**
     * @param array $param
     *
     * @return array|bool
     */
    public function getConfigVariableList(array $param)
    {

        if (empty($param) || !is_array($param)) return false;

        $list = array();

        foreach ($param as $key) {
            $list[$key] = $this->setting->$key;
        }

        return $list;
    }

}
