<?php
/**
 *
 * @author j
 * Date: 3/7/15
 * Time: 6:21 PM
 *
 * File: DashToCamelCase.php
 */

namespace chilimatic\lib\transformer\string;

use chilimatic\lib\interfaces\IFlyWeightTransformer;

/**
 * Class DynamicFunctionCallName
 *
 * @package chilimatic\lib\transformer\string
 */
class DynamicFunctionCallName implements IFlyWeightTransformer
{

    /**
     * the local Parser
     *
     * @var string
     */
    const TRANSFORM_DELIMITER = '-';

    /**
     * @param string $content
     * @param array $options
     *
     * @return string
     */
    public function __invoke($content, $options = []) {
        return $this->transform($content);
    }

    /**
     * @param string $content
     * @param array $options
     *
     * @return string
     */
    public function transform($content, $options = [])
    {
        if (!$content) {
            return $content;
        }

        if (strpos($content, self::TRANSFORM_DELIMITER) === false) {
            return lcfirst($content);
        }

        $tmp = explode('-', $content);
        array_walk($tmp, function (&$a) {
            $a = ucfirst($a);
        });

        return lcfirst((string)implode('', $tmp));
    }

}