<?php
/**
 *
 * @author j
 * Date: 9/27/15
 * Time: 7:06 PM
 *
 * File: validatorNameSpaceClassName.php
 */

namespace chilimatic\lib\transformer\string;

use chilimatic\lib\interfaces\IFlyWeightTransformer;

/**
 * Class ValidatorNameSpaceClassName
 *
 * @package chilimatic\lib\transformer\string
 */
class AnnotationValidatorClassName implements IFlyWeightTransformer
{
    /**
     * @var string
     */
    const NAMESPACE_DELIMITER = '\\';

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
        // if there is no namespace we assume the first letter is uppercase
        if (!$content || strpos($content, self::NAMESPACE_DELIMITER) === false) {
            return ucfirst($content);
        }

        // get the last character after the last backslash and set it to uppercase
        if (($pos = strrpos($content, self::NAMESPACE_DELIMITER)) !== false) {
            $content[$pos+1] = ucfirst($content[$pos+1]);
        }

        return $content;
    }

}