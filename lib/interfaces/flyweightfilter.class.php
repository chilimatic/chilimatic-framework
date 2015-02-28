<?php

namespace chilimatic\lib\interfaces;

/**
 * Interface FlyWeightFilter
 *
 * @package chilimatic\lib\interfaces
 */
interface FlyWeightFilter {

    /**
     * @param mixed $input
     * @param mixed $param
     */
    public function __construct($input = null, $param = null);

    /**
     * @return mixed
     */
    public function filter();
}
