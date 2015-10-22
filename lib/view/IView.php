<?php
namespace chilimatic\lib\view;

/**
 * Interface ViewInterface
 *
 * @package chilimatic\lib\view
 */
Interface IView
{
    /**
     * initializes the specific render presets
     *
     * @return mixed
     */
    public function initRender();


    /**
     * renders per engine differently
     *
     * @param string $templateFile
     */
    public function render($templateFile = '');
}