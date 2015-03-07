<?php
/**
 *
 * @author j
 * Date: 3/7/15
 * Time: 1:27 PM
 *
 * File: AbstractNodeFilter.php
 */

namespace chilimatic\lib\node\filter;

use chilimatic\lib\interfaces\IFlyWeightFilter;

abstract class AbstractFilter implements IFlyWeightFilter {
    /**
     * @param $param
     *
     * @return mixed
     */
    abstract function filter($param = null);
}