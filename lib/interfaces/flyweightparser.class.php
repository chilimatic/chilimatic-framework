<?php
namespace chilimatic\lib\interfaces;

/**
 * Interface FlyWeightParser
 *
 * @package chilimatic\lib\interfaces
 */
interface FlyWeightParser {

    /**
     * @param string $content
     */
    public function __construct($content);

    /**
     * parse method that fills the collection
     * @param string $content
     */
    public function parse($content);
}