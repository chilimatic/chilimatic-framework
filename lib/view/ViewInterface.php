<?php
namespace chilimatic\lib\view;

/**
 * Interface ViewInterface
 * @package chilimatic\lib\view
 */
Interface ViewInterface
{

    /**
     * constructor
     */
    public function __construct();

    /**
     * initializes the specific render presets
     * @return mixed
     */
    public function initRender();


    /**
     * renders per engine differently
     * @param string $templateFile
     */
    public function render( $templateFile = '');



    /**
     * generic destructor
     */
    public function __destruct();
}