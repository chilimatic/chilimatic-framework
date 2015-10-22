<?php
namespace chilimatic\lib\interfaces;
/**
 *
 * @author j
 * Date: 10/12/15
 * Time: 4:59 PM
 *
 * File: IFactory.php
 */

Interface IFactoryOptions {

    /**
     * @param string $name
     * @param $options
     *
     * @return mixed
     */
    public function make($name, $options);


    /**
     * @param string $name
     * @param $options
     *
     * @return mixed
     */
    public function __invoke($name, $options);
}