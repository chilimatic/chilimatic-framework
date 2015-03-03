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
    protected $requestControl;

    /**
     * @var \chilimatic\lib\handler\httphandler
     */
    protected $handle;

    /**
     * @var \chilimatic\lib\di\DIFactory
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
     * @param \chilimatic\lib\di\DIFactory $di
     * @param AbstractConfig               $config
     */
    public function __construct(\chilimatic\lib\di\Factory $di = null, AbstractConfig $config = null)
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
        $this->getHandle()
            ->setRoute($this->getRouter()->getRoute())
            ->setRequest($this->requestControl);
    }

    /**
     * @return mixed
     */
    public function getRequestControl()
    {
        if (!$this->requestControl) {
            $this->requestControl = $this->di->get('request-handler');
        }

        return $this->requestControl;
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
    public function getHandle()
    {
        if (!$this->handle) {
            $this->handle = $this->di->get('application-handler');
        }

        return $this->handle;
    }

    /**
     * @param $handle
     *
     * @return $this
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;
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
            $this->router = $this->di->get('routing',
                [
                    'type' => $this->config->get('routingType') ?  $this->config->get('routingType') : Router::DEFAULT_ROUTING_TYPE
                ]
            );
        }

        return $this->router;
    }

}