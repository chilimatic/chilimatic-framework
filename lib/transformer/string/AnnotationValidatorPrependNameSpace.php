<?php
namespace chilimatic\lib\transformer\string;
/**
 *
 * @author j
 * Date: 9/27/15
 * Time: 7:13 PM
 *
 * File: AnnotationValidatorPrependNameSpace.php
 */

class AnnotationValidatorPrependNameSpace implements \chilimatic\lib\interfaces\IFlyWeightTransformer
{
    /**
     * @var string
     */
    const NAMESPACE_DELIMITER = '\\';

    /**
     * @var string
     */
    const NAMESPACE_OPTION_INDEX = 'namespace';


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

        // prepend slashes if necessary
        if (strpos((string) $content, self::NAMESPACE_DELIMITER) != 0) {
            $content = self::NAMESPACE_DELIMITER . $content;
        }



        if (empty($options[self::NAMESPACE_OPTION_INDEX])) {
            return $content;
        }

        if (strpos($content, $options[self::NAMESPACE_OPTION_INDEX]) === false) {
            $content = self::NAMESPACE_DELIMITER . $options[self::NAMESPACE_OPTION_INDEX] . $content;
        }

        return $content;
    }

    /**
     * @param string $content
     * @param array $options
     *
     * @return mixed|void
     */
    public function __invoke($content, $options = [])
    {
        return $this->transform($content, $options);
    }

}