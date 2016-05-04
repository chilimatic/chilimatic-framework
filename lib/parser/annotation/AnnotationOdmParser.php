<?php
namespace chilimatic\lib\parser\annotation;
/**
 *
 * @author j
 * Date: 11/5/15
 * Time: 6:37 PM
 *
 * File: AnnotationOdmParser.php
 */
use chilimatic\lib\Interfaces\IFlyWeightParser;

/**
 * Class AnnotationValidatorParser
 *
 * @package chilimatic\lib\parser\annotation
 */
class AnnotationOdmParser implements IFlyWeightParser
{
    /**
     * get content between the brackets @ODM\Configuration (db="user", collection="user")
     */
    const CONFIGURATION_PATTERN = '/ODM[\\]{1}Configuration[\s]*(.*)/i';

    /**
     * get database inside the result of the configuration pattern
     */
    const DATABASE_PATTERN = '/(?:db|database)=[\'"](\w*?)[\'"][,]+/U';

    /**
     * get collection name inside the configuration result
     */
    const COLLECTION_PATTERN = '/(?:collection)=[\'"](\w*?)[\'"][,]?/U';

    /**
     * array indexes
     */
    const COLLECTION_INDEX = 'collection';
    const DATABASE_INDEX = 'db';

    /**
     * @param string $content
     *
     * @return array
     */
    public function parse($content)
    {
        $map = [];

        if (preg_match(self::CONFIGURATION_PATTERN, $content, $matches)) {
            if (preg_match(self::DATABASE_PATTERN, $matches[1], $database)) {
                $map[self::DATABASE_INDEX] = $database[1];
            }
            if (preg_match(self::COLLECTION_PATTERN, $matches[1], $collection)) {
                $map[self::COLLECTION_INDEX] = $collection[1];
            }
        }

        return $map;
    }
}