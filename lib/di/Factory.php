<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 28.10.14
 * Time: 10:38
 */
namespace chilimatic\lib\di;

/**
 * Class DIFactory
 *
 * @package chilimatic\lib\di
 */
class Factory
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
     * @param null $path
     * @param $services
     */
    private function __construct($path = null, $services = null)
    {

        $this->serviceCollection = [];

        if ($path) {
            $this->loadServiceFromFile($path);
        }

        if (!$services) return;

        foreach ($services as $key => $closure) {
            $this->set($key, $closure);
        }

        $this->pseudoSingeltonList = [];
    }

    /**
     * @return void
     */
    private function __clone() {
        return;
    }

    /**
     * @param string $path
     */
    public function loadServiceFromFile($path) {
        $serviceList = @require $path;
        $this->serviceCollection = array_merge((array) $this->serviceCollection, (array) $serviceList);
    }

    /**
     * @param null $path
     * @param $services
     *
     * @return Factory::$instance
     */
    public static function getInstance($path = null, $services = null) {
        if (!self::$instance instanceof self) {
            self::$instance = new self($path, $services);
        }

        return self::$instance;
    }

    /**
     * @param string $key
     * @param $service
     *
     * @return $this
     */
    public function set($key, $service) {
        $this->serviceCollection[$key] = $service;
        return $this;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function exists($key) {
        return isset($this->serviceCollection[$key]);
    }

    /**
     * @param  string  $key
     * @param array $setting
     * @param bool $singelton
     *
     * @return mixed
     * @throws \BadFunctionCallException
     */
    public function get($key, $setting = [], $singelton = false) {
        if (!isset($this->serviceCollection[$key])) {
            throw new \BadFunctionCallException($key . 'function is missing');
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
     * @return mixed|null
     */
    public function getClosure($key) {
        if (!isset($this->serviceCollection[$key])) return null;

        return $this->serviceCollection[$key];
    }
}