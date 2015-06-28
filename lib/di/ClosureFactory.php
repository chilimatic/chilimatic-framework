<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 28.10.14
 * Time: 10:38
 */
namespace chilimatic\lib\di;

/**
 * Class ClosureFactory
 *
 * @package chilimatic\lib\di
 */
class ClosureFactory
{
    /**
     * @var self
     */
    private static $instance;

    /**
     * @var array serviceCollection of all services [closure]
     */
    private $serviceCollection;

    /**
     * @var array
     */
    private $pseudoSingeltonList;

    /**
     * @param string|null $path
     * @param array|null $serviceList
     */
    private function __construct($path = null, $serviceList = null)
    {

        $this->serviceCollection = [];

        if ($path) {
            $this->loadServiceFromFile($path);
        }

        if ($serviceList) {
            $this->setServiceList($serviceList);
        }

        $this->pseudoSingeltonList = [];
    }

    /**
     * sets services to the closure factory
     *
     * @param array $serviceList
     *
     * @return void
     */
    public function setServiceList($serviceList = [])
    {
        if (!$serviceList) return;

        foreach ($serviceList as $key => $closure) {
            $this->set($key, $closure);
        }
    }


    /**
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * @param string $path
     */
    public function loadServiceFromFile($path)
    {
        if (!$path) {
            return;
        }

        $serviceList             = @require (string)$path;
        $this->serviceCollection = array_merge((array)$this->serviceCollection, (array)$serviceList);
    }

    /**
     * @param string|null $path
     * @param array|null $serviceList
     *
     * @return self::$instance
     */
    public static function getInstance($path = null, $serviceList = null)
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self($path, $serviceList);
        }

        if ($path) {
            self::$instance->loadServiceFromFile($path);
        }

        if ($serviceList) {
            self::$instance->setServiceList($serviceList);
        }

        return self::$instance;
    }

    /**
     * the set null is to avoid the php GC
     *
     * it remove the singelton instance so the next get instance rebuilds it from scratch
     *
     * @return void
     */
    public static function destroyInstance()
    {
        self::$instance = null;
    }


    /**
     * @param string $key
     * @param \Closure $service
     *
     * @return $this
     */
    public function set($key, $service)
    {
        $this->serviceCollection[$key] = $service;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function remove($key)
    {
        unset($this->serviceCollection[$key]);
        unset($this->pseudoSingeltonList[$key]);

        return $this;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function exists($key)
    {
        return isset($this->serviceCollection[$key]);
    }

    /**
     * @param  string $key
     * @param array $setting
     * @param bool $singelton
     *
     * @return mixed
     * @throws \BadFunctionCallException
     */
    public function get($key, $setting = [], $singelton = false)
    {
        if (!isset($this->serviceCollection[$key])) {
            throw new \BadFunctionCallException($key . ' closure is missing');
        }

        /**
         * if its not a singelton just return the new instance
         */
        if (!$singelton) {
            return $this->serviceCollection[$key]($setting);
        }

        /**
         * if this should be used as a "singelton" service provide the already
         * instantiated service object
         */
        if (isset($this->pseudoSingeltonList[$key])) {
            return $this->pseudoSingeltonList[$key];
        }

        /**
         * if the service never has been initiated before initiate it
         */
        $this->pseudoSingeltonList[$key] = $this->serviceCollection[$key]($setting);

        return $this->pseudoSingeltonList[$key];
    }

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    public function getClosure($key)
    {
        if (!isset($this->serviceCollection[$key])) return null;

        return $this->serviceCollection[$key];
    }
}