<?php
/**
 *
 * @author j
 * Date: 3/7/15
 * Time: 1:18 PM
 *
 * File: FilterFactory.php
 */
namespace chilimatic\lib\node\filter;
use chilimatic\lib\interfaces\IFlyWeightParser;
use chilimatic\lib\transformer\string\DynamicObjectCallName;

/**
 * Class Factory
 *
 * @package chilimatic\lib\node\filter
 */
class Factory
{
    /**
     * @param $filterName
     * @param IFlyWeightParser $parser
     *
     * @return null|AbstractFilter
     */
    public static function make($filterName, IFlyWeightParser $parser = null)
    {
        if ($parser && !$parser->parse($filterName)) {
            return null;
        }

        $transformer = new DynamicObjectCallName();
        $class = __NAMESPACE__ . '\\' . $transformer->transform($filterName);
        if (!class_exists($class)) {
            return null;
        }

        return new $class();
    }
}
