<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 21.01.14
 * Time: 15:07
 *
 *
 * Class HTTP_MultiParam
 *
 * constructs a string like
 * @example Accept: text/plain; q=0.5; level=0; ....
 *
 */

namespace chilimatic\lib\http;

/**
 * Class HTTP_MultiParam
 *
 * @package chilimatic\http
 */
Class HTTP_MultiParam {

    /**
     * sets the default delimiter based on the specs
     */
    const DEFAULT_DELIMITER = ' ';

    /**
     * Field name of the HTTP Param
     *
     * @var string
     */
    private $field = '';

    /**
     * list of parameters
     *
     * this array can contain multiple arrays
     * -> the key is the value for the field they're coma seperated
     * -> every part of the value arrays will be used in the following manner array($KEY => $VALUE) == ;$key=$value
     * -> it can contain multiple arrays for multiple parameters
     *
     * array( 'value' => array('q' => null) )
     *
     * @var array
     */
    private $list = array();


    /**
     * constructor
     *
     * @param $field
     * @param $param
     * @param null $delimiter
     *
     * @return \chilimatic\http\HTTP_MultiParam
     */
    public function __construct($field, $param, $delimiter = null) {
        // if there is no name abort!
        if (empty($field) || empty($param)) return;

        $this->delimiter = (empty($delimiter) ? self::DEFAULT_DELIMITER : $delimiter );

        $this->field = $field;
        $this->list = $param;
    }

    /**
     * gets a specific parameter array
     *
     * @param $key
     * @return array|bool
     */
    public function getParam($key){
        if (isset($this->list[$key])) return $this->list[$key];
        return false;
    }

    /**
     * return all parameters
     *
     * @return array
     */
    public function getAllParam() {
        return $this->list;
    }

    /**
     * removeParameter
     *
     * @param $key
     * @return bool
     */
    public function removeParam($key) {
        if(!isset($this->list[$key])) return false;
        unset($this->list[$key]);
        return true;
    }

    /**
     * add a parameter
     *
     * @param $key
     * @param null $settings
     *
     * @return bool
     */
    public function addParameter($key, $settings = null) {
        if (empty($key)) return false;
        $this->list[$key] = (is_array($settings)) ? $settings : null;
        return true;
    }

    /**
     * to string -> used if there is an echo call on this object
     *
     * @return string
     */
    public function __toString() {
        // if there is no fieldname don't add the values
        if (empty($this->field) || !count($this->list)) return '';

        // add field name
        $string = "$this->field: ";
        // walk through mail parameters
        foreach ($this->list as $key => $value) {
            $string .= "$key";
            if (!is_array($value)) {
                $string .= ', ';
                continue;
            }
            // walk through subparameters
            foreach($value as $key2 => $value2){
                $string .= ";$key2=$value2 ";
            }
            $string .= ', ';
        }
        // remove the last comma and add the linebreak
        $string = substr($string, 0,-2) . "\r\n";

        return $string;
    }
}