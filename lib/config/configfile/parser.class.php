<?php
/**
 *
 * @author j
 * Date: 2/19/15
 * Time: 12:37 AM
 *
 * File: parser.class.php
 */

namespace chilimatic\lib\config\configfile;

use chilimatic\lib\config\ConfigNode;

/**
 * Class Parser
 *
 * @package chilimatic\lib\config\configfile
 */
class Parser
{

    /**
     * default list of single line comment characters
     * separated by ,
     * -> list is exploded in the constructor
     *
     * @var string
     */
    const COMMENT_CHARACTER_LIST = '#,//';

    /**
     * an array of characters that if they are at the
     * start of a string indicate it'S a comment and should not be added to the
     * Config object
     *
     * @var array
     */
    private $_comment_character_list = array();

    /**
     *
     */
    public function __construct() {
        // set the default comment characters
        $this->_comment_character_list = explode(',', self::COMMENT_CHARACTER_LIST);
    }


    /**
     * checks if it's a comment in the config
     *
     * @param $line
     * @return bool
     */
    private function isComment($line)
    {
        // if it's an empty line you might as well skip it
        if (empty($line)) return true;

        $is_comment = false;

        foreach ($this->_comment_character_list as $comment_char)
        {
            if (strpos( trim( $line ), $comment_char ) !== false && strpos( trim( $line ), $comment_char ) <= 3) {
                $is_comment = true;
                break;
            }
        }

        return $is_comment;
    }

    /**
     * @param array $currentConfig
     * @param ConfigNode $configNode
     *
     * @return ConfigNode
     */
    public function parse(array $currentConfig, ConfigNode $configNode)
    {
        $currentComment = '';

        // loop through all lines
        for ($i = 0, $count = (int) count($currentConfig); $i < $count; $i++) {
            if ($this->isComment($currentConfig[$i])) {
                $currentComment .= $currentConfig[$i];
                continue;
            }

            if (strpos($currentConfig[$i], "=") === false) continue;

            $match = explode('=', $currentConfig[$i]);

            if ($match) {
                // append the child;
                $configNode->addChild(new ConfigNode($configNode, strtolower(trim($match[0])), trim($match [1]), $currentComment));
                // clear the comment
                $currentComment = '';
            }
        }

        return $configNode;
    }

}