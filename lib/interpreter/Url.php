<?php

/**
 * Created by PhpStorm.
 * User: j
 * Date: 9/6/14
 * Time: 12:42 PM
 */
namespace chilimatic\lib\interpreter;

/**
 * Class Url
 *
 * @package chilimatic\lib\interpreter
 */
class Url {

    /**
     * @var string
     */
    const DEFAULT_ACTION_DELIMITER = '-';

    /**
     * @param $url
     * @param string $action_delimiter
     *
     * @return string
     */
    public static function interpret($url, $action_delimiter = self::DEFAULT_ACTION_DELIMITER)
    {
        if (mb_stripos($url, $action_delimiter) === false) {
            return $url;
        }

        $action_parts = array_map(function($val) { return ucfirst($val); }, explode($action_delimiter, $url));

        return implode('', $action_parts);
    }
}