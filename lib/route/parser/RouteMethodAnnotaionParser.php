<?php
/**
 *
 * @author j
 * Date: 4/8/15
 * Time: 12:51 AM
 *
 * File: PHPDocParser.php
 */

namespace chilimatic\lib\route\parser;

use chilimatic\lib\interfaces\IFlyWeightParser;


class RouteMethodAnnotaionParser implements IFlyWeightParser
{
    /**
     * @var string
     */
    const TYPE_CLASS = 'class';

    /**
     * @var string
     */
    const TYPE_SCALAR = 'scalar';

    /**
     * @var string
     */
    private $pattern = '/@(\w*)[\s]*([a-zA-Z\\\\]*)/';

    /**
     * @param string $content
     *
     * @return array
     */
    public function parse($content)
    {
        $result = [];
        if (strpos($content, '@view') === false) {
            return $result;
        }

        if (preg_match_all($this->pattern, $content, $matches)) {

            for ($i = 0, $c = count($matches[0]); $i < $c; $i++) {
                $result[] = [
                    'property' => $matches[1][$i],
                    'value'    => $matches[2][$i],
                    'type'     => $this->getType($matches[2][$i])
                ];
            }

            return $result;
        }

        return $result;
    }


    /**
     * for the beginning keep it simple -> is a new object
     * or just a value
     *
     * @param string $value
     *
     * @return string
     */
    public function getType($value)
    {
        if (strpos($value, '\\') !== false && class_exists($value)) {
            return self::TYPE_CLASS;
        } else {
            return self::TYPE_SCALAR;
        }
    }

}