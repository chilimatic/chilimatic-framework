<?php
/**
 *
 * @author j
 * Date: 2/27/14
 * Time: 2:01 PM
 *
 * File: class.php
 */

namespace chilimatic\lib\application;

use chilimatic\lib\config\AbstractConfig;
use chilimatic\lib\route\Router;

/**
 * Class HTTPMVC
 * @package chilimatic\lib\application
 */
class HTTPMVC {

    /**
     * @var string
     */
    const FETCH_DEPENDENCIES = 'addDependencies';


    /**
     * @var \chilimatic\lib\route\Router
     */
    protected $router;

    /**
     * @var \chilimatic\lib\request\Handler
     */
    protected $requestHandler;

    /**
     * @var \chilimatic\lib\handler\httphandler
     */
    protected $applicationHandler;

    /**
     * @var \chilimatic\lib\di\ClosureFactory
     */
    protected $di;

    /**
     * @var array
     */
    protected $defaultDependencies = [
        'handler',
        'route',
        'request'
    ];

    /**
     * @param \chilimatic\lib\di\ClosureFactory $di
     * @param AbstractConfig               $config
     */
    public function __construct(\chilimatic\lib\di\ClosureFactory $di = null, AbstractConfig $config = null)
    {
        if (!$di) return;

        $this->config = $config;
        $this->di = $di;

        if ($di->exists(self::FETCH_DEPENDENCIES)) {
            $this->defaultDependencies = array_merge((array) $this->defaultDependencies, (array) $di->get(self::FETCH_DEPENDENCIES));
        }

        foreach ($this->defaultDependencies as $closure) {
            if (!$di->exists($closure)) continue;
            $this->$closure = $di->get($closure);
        }
    }

    /**
     * @return void
     */
    public function init()
    {
        $this->getHandler()
            ->setRoute($this->getRouter()->getRoute())
            ->setRequest($this->requestHandler);
    }

    /**
     * @return mixed
     */
    public function getRequestControl()
    {
        if (!$this->requestHandler) {
            $this->requestHandler = $this->di->get('request-handler');
        }

        return $this->requestHandler;
    }

    /**
     * @param $requestControl
     *
     * @return $this
     */
    public function setRequestControl($requestControl)
    {
        $this->requestControl = $requestControl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHandler()
    {
        if (!$this->applicationHandler) {
            $this->applicationHandler = $this->di->get('application-handler');
        }

        return $this->applicationHandler;
    }

    /**
     * @param $applicationHandler
     *
     * @return $this
     */
    public function setHandler($applicationHandler)
    {
        $this->applicationHandler = $applicationHandler;
        return $this;
    }

    /**
     * @param $router
     *
     * @return $this
     */
    public function setRouter($router)
    {
        $this->router = $router;
        return $this;
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        if (!$this->router) {
            $this->router = $this->di->get(
                'routing',
                [
                    'type' => $this->config->get('routingType') ?  $this->config->get('routingType') : Router::DEFAULT_ROUTING_TYPE
                ]
            );
        }

        return $this->router;
    }

}