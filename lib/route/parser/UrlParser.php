<?php
/**
 *
 * @author j
 * Date: 4/6/15
 * Time: 8:02 PM
 *
 * File: Path.php
 */

namespace chilimatic\lib\route\parser;
use chilimatic\lib\interfaces\IFlyWeightParser;

/**
 * Class UrlParser
 */
class UrlParser implements IFlyWeightParser
{

    /**
     * @var string
     */
    private $delimiter = '/';


    /**
     * @param $path
     *
     * @return string
     */
    private function getCleanPath($path)
    {
        // remove the first slash for safety reasons [delimitor mapping] based on the web-server Rewrite
        if (mb_strpos($path, $this->delimiter) === 0) {
            $path = mb_substr($path, 1);
        }

        // if the last character is a delimiter remove it as well
        if (mb_strpos($path, $this->delimiter) == mb_strlen($path) - 1) {
            $path = mb_substr($path, 0, -1);
        }

        //remove the get parameter so it's clean
        if (($ppos = mb_strpos($path, '?')) && $ppos > 0) {
            $path = mb_substr($path, 0, $ppos);
        }

        unset($ppos);


        return $path;
    }

    /**
     * parse method that fills the collection
     * @param string $content
     *
     * @return array
     */
    public function parse($content) {
        // if there is no path it's not needed to try to get a clean one
        if ( empty($content) ) return [];

        $path = $this->getCleanPath($content);

        // check if there is even a need for further checks
        if (mb_strpos($path, $this->delimiter) === false) {
            if (!$path) {
                return [$this->delimiter];
            }

            // set the root and the path
            $pathParts = array(
                $this->delimiter,
                $path
            );
            return $pathParts;
        }

        // if there's a deeper path it's time to walk through it and clean the empty parts etc
        $pathParts = explode($this->delimiter, $path);

        // walk through the array and remove the empty entries
        for ($i = 0, $c = count($pathParts); $i < $c; $i++ ) {
            if ( empty($pathParts[$i]) ) unset($pathParts[$i]);
        }

        // reindex them sort them
        sort($pathParts);
        // prepend the default delimiter
        array_unshift($pathParts, $this->delimiter);

        // path parts
        return $pathParts;
    }
}