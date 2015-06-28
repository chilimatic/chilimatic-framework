<?php
/**
 *
 * @author j
 * Date: 3/7/15
 * Time: 6:44 PM
 *
 * File: IFlyWeightTransformer.php
 */

namespace chilimatic\lib\interfaces;

/**
 * Interface IFlyWeightTransformer
 */
interface IFlyWeightTransformer
{

    /**
     * @param $content
     *
     * @return mixed
     */
    public function transform($content);
}