<?php
/**
 *
 * @author j
 * Date: 3/20/15
 * Time: 4:05 PM
 *
 * File: ORMParser.php
 */

namespace chilimatic\lib\parser\annotation;

use chilimatic\lib\interfaces\IFlyWeightParser;

class ORMParser implements IFlyWeightParser
{
    /**
     * @var string
     */
    const PATTERN = '/@ORM[\s]*(\w*)=(.*);/';

    /**
     * @param string $content
     *
     * @return null|\SplFixedArray
     */
    public function parse($content)
    {
        if (strpos($content, '@ORM') === false) {
            return null;
        }

        if (preg_match(self::PATTERN, $content, $matches)) {
            return (new \SplFixedArray(2))->fromArray([$matches[1], $matches[2]]);
        }

        return null;
    }
}