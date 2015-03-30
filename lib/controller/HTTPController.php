<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 22.10.14
 * Time: 20:36
 */
namespace chilimatic\lib\controller;

use chilimatic\lib\di\ClosureFactory;
use chilimatic\lib\request\Request;

/**
 * Class HTTPController
 * @package chilimatic\lib\controller
 */
abstract class HTTPController {

    /**
     * @var string
     */
    const VIEWSERVICE_INDEX = 'view';

    /**
     * @var mixed
     */
    protected $view;

    /**
     *
     */
    public function __construct() {
        $this->view = ClosureFactory::getInstance()->get(self::VIEWSERVICE_INDEX);
    }

    /**
     * @return mixed
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param mixed $view
     *
     * @return $this
     */
    public function setView($view)
    {
        $this->view = $view;
        return $this;
    }
}