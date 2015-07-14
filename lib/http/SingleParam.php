<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 21.01.14
 * Time: 16:59
 */

namespace chilimatic\lib\http;


/**
 * Class HTTP_SingleParam
 *
 * @package chilimatic\http
 */
class SingleParam
{

    /**
     * field name
     *
     * @var string
     */
    private $field = '';


    /**
     * parameter for the request
     *
     * @example Age: 123
     *
     * @var string
     */
    private $param = '';

    /**
     * constructor for a single field
     *
     * @param $field
     * @param string $param
     */
    public function __construct($field, $param = '')
    {
        if (empty($field)) return;

        $this->field = $field;
        $this->param = $param;
    }

    /**
     * gets the parameter
     *
     * @return string
     */
    public function getParam()
    {
        return $this->param;
    }

    /**
     * sets the parameter
     *
     * @param $value
     *
     * @return bool
     */
    public function setParam($value)
    {
        $this->param = $value;

        return true;
    }

    /**
     * creates the HTTP string
     *
     * @return string
     */
    public function __toString()
    {
        return "$this->field: $this->param\r\n";
    }
}