<?php
/**
 *
 * @author j
 * Date: 3/17/15
 * Time: 8:37 PM
 *
 * File: ServiceFactory.php
 */

class ServiceFactory
{
    /**
     * @var array serviceCollection of all services [closure]
     */
    private $serviceCollection;

    /**
     * @var \chilimatic\lib\interfaces\IFlyWeightParser
     */
    private $parser;

    /**
     * @var \chilimatic\lib\interfaces\IFlyWeightTransformer
     */
    private $transformer;
    /**
     * @param $parser
     * @param $transformer
     */
    public function __construct(\chilimatic\lib\interfaces\IFlyWeightParser $parser = null, \chilimatic\lib\interfaces\IFlyWeightTransformer $transformer = null)
    {
        $this->parser = $parser;
        $this->transformer = $transformer;
    }


    /**
     * @return void
     */
    public function __clone() {
        $this->pseudoSingeltonList = [];
    }

    /**
     * @param string $key
     * @param IService $service
     *
     * @return $this
     */
    public function set($key, IService $service)
    {
        $this->serviceCollection[$key] = $service;
        return $this;
    }

    /**
     * @param $key
     * @param null $settings
     * @param bool $asSingelton
     *
     * @return null|void
     */
    public function get($key, $settings = null, $asSingelton = false)
    {
        if ($this->parser && !$this->parser->parse($key)) {
            return null;
        }

        if ($this->serviceCollection[$key]) {
            $this->serviceCollection[$key];
        }

        if ($asSingelton && isset($this->pseudoSingeltonList)) {

        }


        return ;
    }


}