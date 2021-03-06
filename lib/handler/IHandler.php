<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 22.10.14
 * Time: 19:59
 */
namespace chilimatic\lib\handler;

/**
 * Interface HandlerInterface
 *
 * @package chilimatic\lib\handler
 */
interface IHandler
{

    /**
     * @param null $param
     */
    public function __construct($param = null);


    /**
     * @return mixed
     */
    public function getContent();

    /**
     * @param $request
     *
     * @return mixed
     */
    public function setRequest($request);

    /**
     * @return mixed
     */
    public function getRequest();

    /**
     * @param $route
     *
     * @return mixed
     */
    public function setRoute($route);

    /**
     * @return mixed
     */
    public function getRoute();
}