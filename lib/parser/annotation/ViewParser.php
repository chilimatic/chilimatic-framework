<?php
namespace chilimatic\lib\parser\annotation;

use chilimatic\lib\interfaces\IFlyWeightParser;

/**
 *
 * @author j
 * Date: 10/11/15
 * Time: 4:20 PM
 *
 * File: ViewParser.php
 */
class ViewParser implements IFlyWeightParser
{

    /**
     * pattern for matching
     *
     * @view should only be the first <- we don't override inside of a doc block
     */
    const VIEW_RENDER           = '/@view[\s]+(.*)/';

    /**
     * template file name
     *
     * here as well only the first value does count
     */
    const VIEW_TEMPLATE_FILE    = '/@viewTemplate[\s]+(.*)/';

    /**
     * these can be n-times
     *
     * they always should be key=value the value can be any type
     */
    const VIEW_PARAMETERS       = '/@viewParam[\s]+(\w*)[\s]*[=]{1}[\s]*(.*)/';


    /**
     * @param string $content
     *
     * @return array
     */
    public function parse($content)
    {
        if (!$content || strpos($content, '@') === false) {
            return [];
        }
        $ret = [];

        if (preg_match(self::VIEW_RENDER, $content, $match)) {
            $ret[0] = array_pop($match);
        } else {
            $ret[0] = null;
        }

        if (preg_match(self::VIEW_TEMPLATE_FILE, $content, $match)) {
            $ret[1] = array_pop($match);
        } else {
            $ret[1] = null;
        }

        if (preg_match_all(self::VIEW_PARAMETERS, $content, $match)) {

            foreach($match as $subMatch) {
                $ret[] = [$subMatch[0] => $subMatch[1]];
            }
        }

        return $ret;
    }
}