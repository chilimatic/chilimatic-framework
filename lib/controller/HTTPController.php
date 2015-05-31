<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 22.10.14
 * Time: 20:36
 */
namespace chilimatic\lib\controller;

use chilimatic\lib\di\ClosureFactory;
use chilimatic\lib\view\AbstractView;

/**
 * Class HTTPController
 * @package chilimatic\lib\controller
 */
abstract class HTTPController {

    /**
     * @var AbstractView mixed
     */
    protected $view;

    /**
     * @param AbstractView|null $view
     */
    public function __construct(AbstractView $view = null) {
        $this->view = $view;
    }

    /**
     * @return mixed
     */
    public function getView()
    {
        if (!$this->view) {
            $this->view = ClosureFactory::getInstance()->get('view');
        }

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