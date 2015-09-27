<?php
/**
 *
 * @author j
 * Date: 9/27/15
 * Time: 4:19 PM
 *
 * File: AnnotationValidatorParser.php
 */

namespace chilimatic\lib\parser;

use chilimatic\lib\interfaces\IFlyWeightParser;

/**
 * Class AnnotationValidatorParser
 *
 * @package chilimatic\lib\parser
 */
class AnnotationValidatorParser implements IFlyWeightParser
{
    /**
     * pattern for validator annotations for properties
     *
     * @const string
     */
    const PATTERN = '/(?:@validator[\s]([\w\\\]*))/s';

    /**
     * @param string $content
     *
     * @return array
     */
    public function parse($content)
    {
        if (!$content) {
            return [];
        }
        $validatorNameList = [];
        if (preg_match_all(self::PATTERN, $content, $matches)) {
            // simplify the matches and return only the parameter list
            foreach ($matches[1] as $validatorName) {
                // every className should be Uppercase in the beginning
                $validatorNameList[] = $validatorName;
            }
        }

        return $validatorNameList;
    }
}

