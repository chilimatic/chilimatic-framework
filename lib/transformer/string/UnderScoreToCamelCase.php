<?php

namespace chilimatic\lib\transformer\string;
use chilimatic\lib\interfaces\IFlyWeightTransformer;

/**
 * Class UnderScoreToCamelCase
 * @package chilimatic\framework\transformer\string
 */
class UnderScoreToCamelCase implements IFlyWeightTransformer
{
    /**
     * @param string $content
     * @param array $options
     * @return string
     */
    public function transform($content, $options = [])
    {
        if (strpos($content, '_') === false) {
            return $content;
        }

        $tmp = explode('_', $content);

        $tmp = array_map(function($element){
            return ucfirst($element);
        }, $tmp);


        return lcfirst((string) implode('', (array) $tmp));
    }

    /**
     * @param string $content
     * @param array $options
     * @return string
     */
    public function __invoke($content, $options = [])
    {
        return $this->transform($content, $options);
    }
}