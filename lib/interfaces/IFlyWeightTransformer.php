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
     * @param string $content
     * @param array $options
     *
     * @return string
     */
    public function transform($content, $options = []);

    /**
     * @param string $content
     * @param array $options
     *
     * @return string
     */
    public function __invoke($content, $options = []);
}