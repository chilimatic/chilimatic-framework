<?php
namespace chilimatic\lib\interfaces;

/**
 * Interface IFlyWeightParser
 *
 * @package chilimatic\lib\interfaces
 */
interface IFlyWeightParser
{
    /**
     * parse method that fills the collection
     *
     * @param string $content
     *
     * @return mixed
     */
    public function parse($content);
}