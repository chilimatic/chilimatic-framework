<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 11.11.14
 * Time: 17:37
 */
namespace chilimatic\lib\route\routesystem;

use chilimatic\lib\interfaces\IFlyWeightTransformer;
use chilimatic\lib\route\Map;
use chilimatic\lib\route\map\MapFactory;
use chilimatic\lib\transformer\string\DynamicFunctionCallName;

/**
 * Class RouteTrait
 *
 * @package chilimatic\lib\route\routesystem
 */
Trait RouteTrait
{
    /**
     * @var string
     */
    public $defaultModule = 'main';

    /**
     * @var string
     */
    private $defaultClass = 'Index';

    /**
     * @var string
     */
    private $defaultMethod = 'index';

    /**
     * @var string
     */
    private $notFoundMethod = 'notFound';

    /**
     * @var string
     */
    private $actionSuffix = 'Action';

    /**
     * @var string
     */
    private $defaultPath = '/';

    /**
     * @var string
     */
    private $defaultAppNameSpace = '\app\module';

    /**
     * @var string
     */
    private $applicationNameSpace = '';

    /**
     * @var string
     */
    private $defaultControllerPath = 'controller';

    /**
     * @var string
     */
    private $defaultUrlDelimiter = '/';

    /**
     * @var \chilimatic\lib\route\map\MapFactory
     */
    private $mapFactory;

    /**
     * @var DynamicFunctionCallName
     */
    private $transformer;

    /**
     * gets the default route based on my framework scheme
     *
     * @throws \chilimatic\lib\exception\RouteException
     *
     * @returns \chilimatic\lib\route\Map
     */
    public function getDefaultRoute()
    {

        try {
            return $this->buildRouteMap(
                $this->defaultUrlDelimiter,
                [
                    'object' => implode('\\', $this->generateClassName(
                        $this->getApplicationNameSpace() . $this->defaultAppNameSpace,
                        $this->defaultModule,
                        $this->defaultControllerPath,
                        $this->defaultClass)
                    ),
                    'method' => $this->defaultMethod . $this->actionSuffix
                ],
                $this->defaultUrlDelimiter
            );
        } catch (\chilimatic\lib\exception\RouteException $e) {
            throw $e;
        }
    }

    /**
     * loads the config
     */
    public function loadConfig()
    {
        $config = \chilimatic\lib\di\ClosureFactory::getInstance()->get('config');
        $ns = $config->get('application-namespace');
        if ($ns) {
            $this->applicationNameSpace = $ns;
        }
    }


    /**
     * returns the map
     *
     * @param string $path
     * @param array $config
     * @param string $delimiter
     *
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
        } catch (\chilimatic\lib\exception\RouteException $e) {
            throw $e;
        }

    }

    /**
     * declarative generator method
     *
     * @param string $namespace
     * @param string $module
     * @param string $class
     * @param string $controllerPath
     *
     * @return string
     */
    private function generateClassName($namespace, $module, $controllerPath, $class)
    {
        return [$namespace, $module, $controllerPath, ucfirst($this->transformer->transform($class))];
    }


    /**
     * @param array|null $urlParts
     *
     * @return Map|null
     */
    public function getStandardRoute(array $urlParts = null)
    {
        // if there is the slash in the positon zero remove it
        if (isset($urlParts[0]) && $urlParts[0] == '/') {
            array_shift($urlParts);
        }


        // more than 1 part means class/method/[value or param{/value}]
        if (count($urlParts) >= 1) {
            $module    = empty($urlParts[0]) ? $this->defaultModule : $urlParts[0];
            $className = empty($urlParts[1]) ? $this->defaultClass : $urlParts[1];
            $class     = implode('\\',
                $this->generateClassName(
                    $this->getApplicationNameSpace(). $this->defaultAppNameSpace,
                    $module,
                    $this->defaultControllerPath,
                    $className
                )
            );
            $urlMethod = (string)empty($urlParts[2]) ? $this->defaultMethod : $urlParts[2];
            $method    = $this->transformer->transform($urlMethod . $this->actionSuffix);

        } else {
            $className = $this->defaultClass;
            $class     = implode('\\', $this->generateClassName(
                $this->defaultAppNameSpace,
                $this->defaultModule,
                $this->defaultControllerPath,
                $this->defaultClass)
            );

            $urlMethod = (string) $this->notFoundMethod;
            $method    = $this->transformer->transform($urlMethod . $this->actionSuffix);
        }

        if (class_exists($class, true)) {
            foreach ((array) get_class_methods($class) as $cmethod) {
                if (strtolower($cmethod) != strtolower($method)) {
                    continue;
                }

                return $this->mapFactory->make(
                    strtolower("/{$module}/{$className}/{$urlMethod}"),
                    [
                        'object'    => $class,
                        'method'    => $this->transformer->transform($method),
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
    public function getActionSuffix()
    {
        return $this->actionSuffix;
    }

    /**
     * @param string $actionSuffix
     *
     * @return $this
     */
    public function setActionSuffix($actionSuffix)
    {
        $this->actionSuffix = $actionSuffix;

        return $this;
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
        return $this->defaultAppNameSpace;
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


    /**
     * @return string
     */
    public function getDefaultModule()
    {
        return $this->defaultModule;
    }

    /**
     * @param string $defaultModule
     *
     * @return $this
     */
    public function setDefaultModule($defaultModule)
    {
        $this->defaultModule = $defaultModule;

        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultControllerPath()
    {
        return $this->defaultControllerPath;
    }

    /**
     * @param string $defaultControllerPath
     *
     * @return $this
     */
    public function setDefaultControllerPath($defaultControllerPath)
    {
        $this->defaultControllerPath = $defaultControllerPath;

        return $this;
    }

    /**
     * @return DynamicFunctionCallName
     */
    public function getTransformer()
    {
        return $this->transformer;
    }

    /**
     * @param IFlyWeightTransformer $transformer
     *
     * @return $this
     */
    public function setTransformer(IFlyWeightTransformer $transformer)
    {
        $this->transformer = $transformer;

        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultAppNameSpace()
    {
        return $this->defaultAppNameSpace;
    }

    /**
     * @param string $defaultAppNameSpace
     *
     * @return $this
     */
    public function setDefaultAppNameSpace($defaultAppNameSpace)
    {
        $this->defaultAppNameSpace = $defaultAppNameSpace;

        return $this;
    }

    /**
     * @return string
     */
    public function getApplicationNameSpace()
    {
        if (!$this->applicationNameSpace) {
            $this->loadConfig();
        }

        return $this->applicationNameSpace;
    }

    /**
     * @param string $applicationNameSpace
     *
     * @return $this
     */
    public function setApplicationNameSpace($applicationNameSpace)
    {
        $this->applicationNameSpace = $applicationNameSpace;

        return $this;
    }

}