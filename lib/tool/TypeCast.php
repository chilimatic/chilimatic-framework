<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 18.10.14
 * Time: 12:55
 */


namespace chilimatic\lib\Tool;


/**
 * Class TypeCast
 *
 * @package chilimatic\lib\Tool
 */
class TypeCast
{

    const METHODPREFIX = 'cast';


    public static function castInt($var)
    {
        return (int)$var;
    }

}