<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 11.11.14
 * Time: 17:37
 */
namespace chilimatic\lib\route\routesystem;

use chilimatic\lib\route\Map;
use chilimatic\lib\route\map\MapFactory;

/**
 * Class RouteTrait
 * @package chilimatic\lib\route\routesystem
 */
Trait RouteTrait {

    /**
     * @var string
     */
    private $stdView = 'view\\View';

    /**
     * @var string
     */
    private $defaultClass = 'Index';

    /**
     * @var string
     */
    private $defaultMethod = 'indexAction';

    /**
     * @var string
     */
    private $methodSuffix = 'Action';

    /**
     * @var string
     */
    private $defaultPath = '/';

    /**
     * @var string
     */
    private $defaultNameSpace = '\chilimatic\app\\controller\\';

    /**
     * @var string
     */
    private $defaultUrlDelimiter = '/';

    /**
     * @var \chilimatic\lib\route\map\MapFactory
     */
    private $mapFactory;


    /**
     * gets the default route based on my framework scheme
     *
     * @throws \chilimatic\lib\exception\RouteException
     *
     * @returns \chilimatic\lib\route\Map
     */
    public function getDefaultRoute() {
        try {
            return $this->buildRouteMap(
                $this->defaultUrlDelimiter,
                [
                    'object' => $this->defaultNameSpace . $this->defaultClass,
                    'method' => $this->defaultMethod
                ],
                $this->defaultUrlDelimiter
            );
        } catch (\chilimatic\lib\exception\RouteException $e) {
            throw $e;
        }
    }

    /**
     * returns the map
     *
     * @param string $path
     * @param array $config
     * @param string $delimiter
     * @throws \chilimatic\lib\exception\RouteException
     *
     * @return \chilimatic\lib\route\Map
     */
    public function buildRouteMap($path, $config, $delimiter = Map::DEFAULT_URL_DELIMITER)
    {
        try {
            if (!$this->getMapFactory()) {
                $this->setMapFactory(new MapFactory());
            }
            return $this->mapFactory->make($path, $config, $delimiter);
        } catch ( \chilimatic\lib\exception\RouteException $e ) {
            throw $e;
        }

    }

    /**
     * declarative generator method
     *
     * @param $namespace
     * @param $class
     * @return string
     */
    private function generateClassName($namespace, $class) {
        return $namespace . ucfirst(\chilimatic\lib\interpreter\Url::interpret($class));
    }


    /**
     * @param $path
     *
     * @return null
     */
    public function getStandardRouting($path)
    {
        // remove starting and ending slash
        $path = trim($path, $this->defaultUrlDelimiter);
        $pathPart = explode($this->defaultUrlDelimiter, $path);

        // more than 1 part means class/method/[value or param{/value}]
        if (count($pathPart) >= 1) {
            $class = $this->generateClassName($this->defaultNameSpace, empty($pathPart[0]) ? $this->defaultClass : $pathPart[0]);
            $urlMethod = (string) empty($pathPart[1]) ? $this->defaultMethod : $pathPart[1] . $this->methodSuffix;
            $method = \chilimatic\lib\interpreter\Url::interpret($urlMethod);
        } else {
            $class = $this->generateClassName($this->defaultNameSpace, $this->defaultClass);
            $urlMethod = (string) $this->defaultMethod;
            $method = \chilimatic\lib\interpreter\Url::interpret($urlMethod);
        }

        if (class_exists($class, true)) {
            foreach ((array) get_class_methods($class) as $cmethod) {
                if (strtolower($cmethod) != strtolower($method)) {
                    continue;
                }

                return $this->mapFactory->make(
                    "/{$class}/{$method}",
                    [
                        'object' => $class,
                        'method' => \chilimatic\lib\interpreter\Url::interpret($method),
                        'namespace' => null
                    ],
                    $this->defaultUrlDelimiter
                );
            }
        }

        return null;
    }

    /**
     * @return string
     */
    public function getStdView()
    {
        return $this->stdView;
    }

    /**
     * @param string $stdView
     */
    public function setStdView($stdView)
    {
        $this->stdView = $stdView;
    }

    /**
     * @return string
     */
    public function getDefaultClass()
    {
        return $this->defaultClass;
    }

    /**
     * @param string $defaultClass
     */
    public function setDefaultClass($defaultClass)
    {
        $this->defaultClass = $defaultClass;
    }

    /**
     * @return string
     */
    public function getDefaultMethod()
    {
        return $this->defaultMethod;
    }

    /**
     * @param string $defaultMethod
     */
    public function setDefaultMethod($defaultMethod)
    {
        $this->defaultMethod = $defaultMethod;
    }

    /**
     * @return string
     */
    public function getDefaultPath()
    {
        return $this->defaultPath;
    }

    /**
     * @param string $defaultPath
     */
    public function setDefaultPath($defaultPath)
    {
        $this->defaultPath = $defaultPath;
    }

    /**
     * @return \chilimatic\lib\route\map\MapFactory|null
     */
    public function getMapFactory()
    {
        return $this->mapFactory;
    }

    /**
     * @param \chilimatic\lib\route\map\MapFactory $mapFactory
     */
    public function setMapFactory(\chilimatic\lib\route\map\MapFactory $mapFactory)
    {
        $this->mapFactory = $mapFactory;
    }

    /**
     * @return string
     */
    public function getDefaultNameSpace()
    {
        return $this->defaultNameSpace;
    }

    /**
     * @param string $defaultNameSpace
     */
    public function setDefaultNameSpace($defaultNameSpace)
    {
        $this->defaultNameSpace = $defaultNameSpace;
    }

    /**
     * @return string
     */
    public function getDefaultUrlDelimiter()
    {
        return $this->defaultUrlDelimiter;
    }

    /**
     * @param string $defaultUrlDelimiter
     */
    public function setDefaultUrlDelimiter($defaultUrlDelimiter)
    {
        $this->defaultUrlDelimiter = $defaultUrlDelimiter;
    }



}