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

/**
 * Class Factory
 *
 * @package chilimatic\lib\node\filter
 */
class Factory
{
    /**
     * @var null|\chilimatic\lib\interfaces\IFlyWeightParser
     */
    private $parser;

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
        if ($this->parser && !$this->parser->parse($filterName)) {
            return null;
        }

        if ($this->transformer) {
            $class = __NAMESPACE__ . '\\' . $this->transformer->transform($filterName);
        } else {
            $class =  __NAMESPACE__ . '\\' . $filterName;
        }

        echo $class;
        if (!class_exists($class)) {
            return null;
        }

        return new $class();
    }

    /**
     * @return \chilimatic\lib\interfaces\IFlyWeightParser|null
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * @param \chilimatic\lib\interfaces\IFlyWeightParser $parser
     *
     * @return $this
     */
    public function setParser(\chilimatic\lib\interfaces\IFlyWeightParser $parser)
    {
        $this->parser = $parser;

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
