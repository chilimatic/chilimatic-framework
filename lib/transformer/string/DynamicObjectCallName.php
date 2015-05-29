<?php
/**
 *
 * @author j
 * Date: 3/7/15
 * Time: 7:47 PM
 *
 * File: DynamicClassCallName.php
 */

namespace chilimatic\lib\transformer\string;
use chilimatic\lib\interfaces\IFlyWeightTransformer;

/**
 * Class DynamicFunctionCallName
 *
 * @package chilimatic\lib\transformer\string
 */
class DynamicObjectCallName implements IFlyWeightTransformer
{

    /**
     * the local Parser
     *
     * @var string
     */
    const TRANSFORM_DELIMITER = '-';


    /**
     * @param $content
     *
     * @return string
     */
    public function transform($content)
    {
        if (!$content) {
            return $content;
        }

        if (strpos($content, self::TRANSFORM_DELIMITER) === false) {
            return ucfirst($content);
        }

        $tmp = explode('-', $content);
        array_walk($tmp, function(&$a) {
            $a = ucfirst($a);
        });

        return (string) implode('', $tmp);
    }

}