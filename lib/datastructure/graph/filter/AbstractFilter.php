<?php
/**
 *
 * @author j
 * Date: 3/7/15
 * Time: 1:27 PM
 *
 * File: AbstractNodeFilter.php
 */

namespace chilimatic\lib\datastructure\graph\filter;

use chilimatic\lib\interfaces\IFlyWeightFilter;

abstract class AbstractFilter implements IFlyWeightFilter
{
    /**
     * @param null|mixed $param
     *
     * @return mixed
     */
    abstract function filter($param = null);

    /**
     * @param null|mixed $param
     *
     * @return mixed
     */
    public function __invoke($param = null) {
        return $this->filter($param);
    }
}