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

use chilimatic\lib\config\Node;

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
    public function __construct()
    {
        // set the default comment characters
        $this->_comment_character_list = explode(',', self::COMMENT_CHARACTER_LIST);
    }


    /**
     * checks if it's a comment in the config
     *
     * @param $line
     *
     * @return bool
     */
    private function isComment($line)
    {
        // if it's an empty line you might as well skip it
        if (empty($line)) return true;

        $is_comment = false;

        foreach ($this->_comment_character_list as $comment_char) {
            $line = trim($line);
            if (strpos($line, $comment_char) !== false && strpos($line, $comment_char) <= 3) {
                $is_comment = true;
                break;
            }
        }

        return $is_comment;
    }

    /**
     * @param array $currentConfig
     * @param Node $Node
     *
     * @return Node
     */
    public function parse(array $currentConfig, Node $Node)
    {
        $currentComment = '';

        // loop through all lines
        for ($i = 0, $count = (int)count($currentConfig); $i < $count; $i++) {
            if (!$currentConfig[$i]) {
                continue;
            } elseif ($this->isComment($currentConfig[$i])) {
                $currentComment .= $currentConfig[$i];
                continue;
            }

            if (strpos($currentConfig[$i], "=") === false) continue;

            $match = explode('=', $currentConfig[$i]);

            if ($match) {
                // append the child;
                $Node->addChild(new Node($Node, strtolower(trim($match[0])), trim($match [1]), $currentComment));
                // clear the comment
                $currentComment = '';
            }
        }

        return $Node;
    }

}