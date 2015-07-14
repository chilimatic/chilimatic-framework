<?php
/**
 *
 * @author j
 * Date: 3/7/15
 * Time: 1:18 PM
 *
 * File: FilterFactory.php
 */
namespace chilimatic\lib\datastructure\graph\filter;

/**
 * Class Factory
 *
 * @package chilimatic\lib\datastructure\graph\filter
 */
class Factory
{
    /**
     * @var null|\chilimatic\lib\interfaces\IFlyWeightValidator
     */
    private $validator;

    /**
     * @var null|\chilimatic\lib\interfaces\IFlyWeightTransformer
     */
    private $transformer;

    /**
     * @param $filterName
     *
     * @return null|AbstractFilter
     */
    public function make($filterName)
    {
        if ($this->validator && !$this->validator->validate($filterName)) {
            return null;
        }

        if ($this->transformer) {
            $class = __NAMESPACE__ . '\\' . $this->transformer->transform($filterName);
        } else {
            $class = __NAMESPACE__ . '\\' . $filterName;
        }

        if (!class_exists($class)) {
            return null;
        }

        return new $class();
    }

    /**
     * @return \chilimatic\lib\interfaces\IFlyWeightValidator|null
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @param \chilimatic\lib\interfaces\IFlyWeightValidator $validator
     *
     * @return $this
     */
    public function setValidator(\chilimatic\lib\interfaces\IFlyWeightValidator $validator)
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * @return \chilimatic\lib\interfaces\IFlyWeightTransformer|null
     */
    public function getTransformer()
    {
        return $this->transformer;
    }

    /**
     * @param \chilimatic\lib\interfaces\IFlyWeightTransformer $transformer
     *
     * @return $this
     */
    public function setTransformer(\chilimatic\lib\interfaces\IFlyWeightTransformer $transformer)
    {
        $this->transformer = $transformer;

        return $this;
    }
}
