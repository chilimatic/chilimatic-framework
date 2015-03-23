<?php
/**
 *
 * @author j
 * Date: 3/14/15
 * Time: 3:31 PM
 *
 * File: StringValueBiggerThan.php
 */
namespace chilimatic\lib\comperator\traits;
use chilimatic\lib\string\traits\AsciiTrait;

/**
 * Class StringValueBiggerThan
 *
 * @package chilimatic\lib\comparator
 */
Trait StringValueBiggerThan
{
    use AsciiTrait;

    /**
     * @param string $str1
     * @param string $str2
     *
     * @return bool
     */
    public function compare($str1, $str2)
    {
        switch (true) {
            case (!$str1 && $str2):
                return true;
            case ($str1 && !$str2):
                return false;
            case ($this->getAsciiStringCharacterValueSum($str1) > $this->getAsciiStringCharacterValueSum($str2)):
                return true;
            case ($this->getAsciiStringCharacterValueSum($str1) < $this->getAsciiStringCharacterValueSum($str2)):
                return false;
        }

        return true;
    }


}