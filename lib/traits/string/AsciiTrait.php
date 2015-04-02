<?php
/**
 *
 * @author j
 * Date: 3/14/15
 * Time: 4:24 PM
 *
 * File: AsciiTrait.php
 */
namespace chilimatic\lib\traits\string;

/**
 * Class AsciiTrait
 *
 * @package chilimatic\lib\traits\string
 */
Trait AsciiTrait
{
    /**
     * @param $str
     *
     * @return bool|string
     */
    public function getBinaryRepresentation($str)
    {
        $out= '';
        for($i=0; $i < strlen($str); $i++)
        {
            $dec = ord($str[$i]); //determine symbol ASCII-code
            $out .= sprintf('%08d', base_convert($dec, 10, 2)); //convert to binary representation and add leading zeros
        }
        return $out;
    }

    /**
     * additive value of all characters in a lex
     *
     * @param $str
     *
     * @return int
     */
    public function getAsciiStringCharacterValueSum($str)
    {
        $sum = 0;
        for($i=0; $i < strlen($str); $i++)
        {
            $sum += ord($str[$i]);
        }
        return $sum;
    }
}