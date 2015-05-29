<?php
/**
 *
 * @author j
 * Date: 3/9/15
 * Time: 6:53 PM
 *
 * File: FactoryStatic.php
 */
namespace chilimatic\lib\datastructure\graph\filter;
use chilimatic\lib\interfaces\IFlyWeightValidator;
use chilimatic\lib\interfaces\IFlyWeightTransformer;

/**
 * Class FactoryStatic
 *
 * @package chilimatic\lib\datastructure\graph\filter
 */
class FactoryStatic {

    /**
     * @var null|IFlyWeightTransformer
     */
    private static $transformer;

    /**
     * @var null|IFlyWeightValidator
     */
    private static $validator;


    /**
     * @param $filterName
     *
     * @return null|AbstractFilter
     */
    public static function make($filterName)
    {
        if (self::$validator && !self::$validator->validate($filterName)) {
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
     * @return IFlyWeightTransformer|null
     */
    public static function getTransformer()
    {
        return self::$transformer;
    }

    /**
     * @param IFlyWeightTransformer|null $transformer
     */
    public static function setTransformer(IFlyWeightTransformer $transformer = null)
    {
        self::$transformer = $transformer;
    }

    /**
     * @return IFlyWeightValidator|null
     */
    public static function getValidator()
    {
        return self::$validator;
    }

    /**
     * @param IFlyWeightValidator|null $validator
     */
    public static function setValidator(IFlyWeightValidator $validator = null)
    {
        self::$validator = $validator;
    }
}