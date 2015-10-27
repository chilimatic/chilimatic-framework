<?php

namespace chilimatic\lib\interfaces;

/**
 * Interface IFlyWeightExecute
 *
 * @package chilimatic\lib\interfaces
 */
interface IFlyWeightExecute
{

    /**
     * @param $input
     *
     * @return mixed
     */
    public function execute($input);

    /**
     * @param mixed $param
     *
     * @return mixed
     */
    public function __invoke($input);
}
