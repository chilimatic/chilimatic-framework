<?php

namespace chilimatic\lib\interfaces;

/**
 * Interface FlyWeightFilter
 *
 * @package chilimatic\lib\interfaces
 */
interface IFlyWeightFilter {

    /**
     * @param $input
     *
     * @return mixed
     */
    public function filter($input);

    /**
     * @param null|mixed $param
     *
     * @return mixed
     */
    public function __invoke($param = null);
}
