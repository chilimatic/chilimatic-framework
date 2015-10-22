<?php

namespace chilimatic\lib\view;

/**
 *
 * @author j
 * Date: 10/12/15
 * Time: 6:07 PM
 *
 * File: Builder.php
 */

class ViewBuilder
{
    /**
     * @var \chilimatic\lib\view\ViewFactory
     */
    private $viewFactory;


    /**
     * @param string $viewName
     * @param string $viewTemplateFile
     * @param ...$options
     *
     * @throws \chilimatic\lib\exception\ViewException
     */
    public function build($viewName, $viewTemplateFile = '', ...$options) {
        if (!$viewName) {
            throw new \chilimatic\lib\exception\ViewException('No view name was selected');
        }

        $viewFactory = $this->getViewFactory();
        /**
         * @var \chilimatic\lib\view\AbstractView $view
         */
        $view = $viewFactory->make($viewName);
        $view->setConfigVariable('templateFile', $viewTemplateFile);

        $view->set();

    }

    /**
     * @return ViewFactory
     */
    public function getViewFactory()
    {
        if (!$this->viewFactory) {
            $this->viewFactory = new ViewFactory();
        }

        return $this->viewFactory;
    }

    /**
     * @param ViewFactory $viewFactory
     *
     * @return $this
     */
    public function setViewFactory($viewFactory)
    {
        $this->viewFactory = $viewFactory;

        return $this;
    }
}