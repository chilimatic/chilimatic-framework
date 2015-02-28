<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 06.02.15
 * Time: 00:11
 */

namespace chilimatic\lib\formater;

class Log
{
    /**
     * @var string
     */
    public $format;

    /**
     * @var
     */
    public $input;

    /**
     * @param $format
     */
    public function __construct($format) {
        $this->format = $format;
    }

    /**
     * @return string
     */
    public function __toString(){
        return $this->output();
    }

    /**
     * @return string
     */
    public function output(){
        return $this->input;
    }
}