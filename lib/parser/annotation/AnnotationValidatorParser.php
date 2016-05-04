<?php
/**
 *
 * @author j
 * Date: 9/27/15
 * Time: 4:19 PM
 *
 * File: AnnotationValidatorParser.php
 */

namespace chilimatic\lib\parser\annotation;

use chilimatic\lib\Interfaces\IFlyWeightParser;
use chilimatic\lib\interpreter\type\InterpreterTypeBoolean;

/**
 * Class AnnotationValidatorParser
 *
 * @package chilimatic\lib\parser\annotation
 */
class AnnotationValidatorParser implements IFlyWeightParser
{
    const INDEX_INTERFACE   = 'interface';
    const INDEX_EXPECTED    = 'expected';
    const INDEX_MANDATORY   = 'mandatory';
    const INDEX_OPERATOR    = 'operator';

    /**
     * pattern for validator annotations for properties to get them per line!
     *
     * @const string
     */
    const ANNOTATION_PATTERN = '/(?:@validator[\s]*(.*))/';

    /**
     * name of the class | method | function that will be triggered
     * for the annotation validation
     */
    const VALIDATOR_NAME_PATTERN = '/name=["\'](.*?)["\'][,]?/';

    /**
     * we only need:
     * the exclusive or "^"
     * the or           "|"
     * the and          "&"
     * the not          "~"
     */
    const VALIDATOR_OPERATOR_PATTERN = '/bitOperator=["\']([\^]|[&]|[|]|[~])["\'][,]?/';

    /**
     * the result expectation
     *
     * if it's not given we always expect it to be true!
     */
    const VALIDATOR_EXPECTATION_PATTERN = '/expect=["\'](true|false|1|0)["\'][,]?/';

    /**
     * the mandatory flag is if the field has to be set
     */
    const VALIDATOR_MANDATORY_PATTERN = '/mandatory=["\'](true|false|1|0)["\'][,]?/';

    /**
     * we usually want all validators to be true
     */
    const DEFAULT_OPERATOR = '&';

    /**
     * default expectation is true
     */
    const DEFAULT_EXPECTATION = true;

    /**
     * as a default we see all validators as mandatory
     */
    const DEFAULT_MANDATORY = true;

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

        if (strpos($content, '@validator') === false) {
            return[];
        }


        $validatorSet = [];
        if (preg_match_all(self::ANNOTATION_PATTERN, $content, $matches)) {

            $typeBoolean = new InterpreterTypeBoolean();
            // simplify the matches and return only the parameter list
            $set = [];
            foreach ($matches[1] as $validator) {
                // if there is no validator name why even bother
                if (preg_match(self::VALIDATOR_NAME_PATTERN, $validator, $match)) {
                    $set[self::INDEX_INTERFACE] = $match[1];
                } else {
                    continue;
                }

                if (preg_match(self::VALIDATOR_OPERATOR_PATTERN, $validator, $match)) {
                    $set[self::INDEX_OPERATOR] = $match[1];
                } else {
                    $set[self::INDEX_OPERATOR] = self::DEFAULT_OPERATOR;
                }

                if (preg_match(self::VALIDATOR_MANDATORY_PATTERN, $validator, $match)) {
                    $set[self::INDEX_MANDATORY] = $typeBoolean->execute($match[1]);
                } else {
                    $set[self::INDEX_MANDATORY] = self::DEFAULT_MANDATORY;
                }

                if (preg_match(self::VALIDATOR_EXPECTATION_PATTERN, $validator, $match)) {
                    $set[self::INDEX_EXPECTED] = $typeBoolean->execute($match[1]);
                } else {
                    $set[self::INDEX_EXPECTED] = self::DEFAULT_EXPECTATION;
                }

                // add the set
                if ($set) {
                    array_push($validatorSet, $set);
                }
            }
        }

        return $validatorSet;
    }
}

