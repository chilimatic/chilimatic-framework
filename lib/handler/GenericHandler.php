<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 22.10.14
 * Time: 19:56
 */

namespace chilimatic\lib\handler;

/**
 * Class GenericHandler
 * @package chilimatic\lib\handler
 */
abstract class GenericHandler implements IHandler
{

    /**
     * @var \chilimatic\lib\request\generic
     */
    protected $request;

    /**
     * @var \chilimatic\lib\route\Map
     */
    protected $route;

    /**
     * @var $view
     */
    protected $view;

    /**
     *
     */
    public function __construct($param = null) {
        // do something
    }

    /**
     * @return mixed
     */
    abstract public function getContent();

    /**
     * @return mixed
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param mixed $route
     * @return $this|mixed
     */
    public function setRoute($route)
    {
        $this->route = $route;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param mixed $request
     * @return $this|mixed
     */
    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return \chilimatic\lib\view\generic
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param \chilimatic\lib\view\generic $view
     *
     * @return $this
     */
    public function setView($view)
    {
        $this->view = $view;
        return $this;
    }


}