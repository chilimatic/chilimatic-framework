<?php
/**
 *
 * @author j
 * Date: 3/9/15
 * Time: 6:53 PM
 *
 * File: FactoryStatic.php
 */
namespace chilimatic\lib\node\filter;

/**
 * Class FactoryStatic
 *
 * @package chilimatic\lib\node\filter
 */
class FactoryStatic {

    /**
     * @var null|\chilimatic\lib\interfaces\IFlyWeightTransformer
     */
    private static $transformer;

    /**
     * @var null|\chilimatic\lib\interfaces\IFlyWeightParser
     */
    private static $parser;


    /**
     * @param $filterName
     *
     * @return null|AbstractFilter
     */
    public static function make($filterName) {
        if (self::$parser && !self::$parser->parse($filterName)) {
            return null;
        }

        if (self::$transformer) {
            $class = __NAMESPACE__ . '\\' . self::$transformer->transform($filterName);
        } else {
            $class =  __NAMESPACE__ . '\\' . $filterName;
        }

        if (!class_exists($class)) {
            return null;
        }

        return new $class();
    }

    /**
     * @return \chilimatic\lib\interfaces\IFlyWeightTransformer|null
     */
    public static function getTransformer()
    {
        return self::$transformer;
    }

    /**
     * @param \chilimatic\lib\interfaces\IFlyWeightTransformer|null $transformer
     */
    public static function setTransformer($transformer)
    {
        self::$transformer = $transformer;
    }

    /**
     * @return \chilimatic\lib\interfaces\IFlyWeightParser|null
     */
    public static function getParser()
    {
        return self::$parser;
    }

    /**
     * @param \chilimatic\lib\interfaces\IFlyWeightParser|null $parser
     */
    public static function setParser($parser)
    {
        self::$parser = $parser;
    }
}