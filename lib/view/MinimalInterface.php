<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 28.10.14
 * Time: 10:07
 */
namespace chilimatic\lib\view;

/**
 * Interface minimalInterface
 * @package chilimatic\lib\view
 */
interface MinimalInterface
{

    /**
     * initializes the specific render presets
     * @return mixed
     */
    public function initRender();

    /**
     * @return mixed
     */
    public function render();

}