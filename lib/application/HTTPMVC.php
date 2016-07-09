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

use chilimatic\lib\Config\AbstractConfig;
use chilimatic\lib\Di\ClosureFactory;
use chilimatic\lib\handler\HTTPHandler;
use chilimatic\lib\request\Handler;
use chilimatic\lib\Route\Exception\RouteException;
use chilimatic\lib\Route\Router;


/**
 * Class HTTPMVC
 *
 * @package chilimatic\lib\application
 */
class HTTPMVC
{

    /**
     * @var string
     */
    const FETCH_DEPENDENCIES = 'addDependencies';

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Handler
     */
    protected $requestHandler;

    /**
     * @var httphandler
     */
    protected $httpHandler;

    /**
     * @var ClosureFactory
     */
    protected $di;

    /**
     * @var array
     */
    protected static $defaultDependencies = [
        'handler',
        'route',
        'request'
    ];

    /**
     * @param ClosureFactory $di
     * @param AbstractConfig $config
     * @throws \BadFunctionCallException
     */
    public function __construct(ClosureFactory $di = null, AbstractConfig $config = null)
    {
        if (!$di) {
            return;
        }

        $this->config = $config;
        $this->di     = $di;

        if ($this->di->exists(self::FETCH_DEPENDENCIES)) {
            self::$defaultDependencies = array_merge((array) self::$defaultDependencies, (array) $this->di->get(self::FETCH_DEPENDENCIES));
        }

        foreach (self::$defaultDependencies as $closure) {
            if (!$this->di->exists($closure)) {
                continue;
            }
            $this->{$closure} = $this->di->get($closure);
        }
    }

    /**
     * @return void
     * @throws \BadFunctionCallException
     * @throws RouteException
     */
    public function init()
    {
        $this->getHandler()
            ->setRoute($this->getRouter()->getRoute())
            ->setRequest($this->requestHandler);
    }

    /**
     * @return mixed
     * @throws \BadFunctionCallException
     */
    public function getRequestHandler()
    {
        if (!$this->requestHandler) {
            $this->requestHandler = $this->di->get('request-handler');
        }

        return $this->requestHandler;
    }

    /**
     * @param $requestHandler
     *
     * @return $this
     */
    public function setRequestHandler($requestHandler)
    {
        $this->requestHandler = $requestHandler;

        return $this;
    }

    /**
     * @return mixed
     * @throws \BadFunctionCallException
     */
    public function getHandler()
    {
        if (!$this->httpHandler) {
            $this->httpHandler = $this->di->get(
                'application-handler',
                [
                    'include-root'          => $this->config->get('document_root'),
                    'application-namespace' => $this->config->get('application-namespace')
                ]
            );
        }

        return $this->httpHandler;
    }

    /**
     * @param $httpHandler
     *
     * @return $this
     */
    public function setHandler($httpHandler)
    {
        $this->httpHandler = $httpHandler;

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
     * @throws \BadFunctionCallException
     */
    public function getRouter()
    {
        if (!$this->router) {
            $this->router = $this->di->get(
                'routing',
                [
                    'type' => $this->config->get('routing_type') ?: Router::DEFAULT_ROUTING_TYPE
                ]
            );
        }

        return $this->router;
    }

}