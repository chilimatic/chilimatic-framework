<?php
/**
 *
 * @author j
 * Date: 2/27/14
 * Time: 2:03 PM
 *
 * File: interface.class.php
 */

namespace chilimatic\lib\interfaces;

/**
 * Interface Controller
 *
 * @package chilimatic\lib\interfaces
 */
Interface Controller
{


    /**
     * generic constructor
     */
    public function __construct();


    /**
     * basic init for the default needed
     * classes within the controler
     *
     *
     * @throws \Exception
     * @return boolean
     */
    public function init();


    /**
     * generic load method
     *
     * @param $request array
     *
     * @throws \Exception
     * @return mixed
     */
    public function load( $request = array() );


    /**
     * generic ajax load method
     *
     * @param array $request
     *
     * @throws \Exception
     * @return mixed
     */
    public function ajax_load( $request = array() );
}