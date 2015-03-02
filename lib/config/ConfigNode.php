<?php
/**
 * Created by JetBrains PhpStorm.
 * User: j
 * Date: 26.10.13
 * Time: 19:19
 * Node System like the JSDOM
 */

namespace chilimatic\lib\config;
use chilimatic\lib\node\Node;
/**
 * Class ConfigNode
 * @package chilimatic\lib\config
 */
class ConfigNode extends Node{

    /**
     * Config Node if loaded
     * can be mixed since it should dynamic
     *
     * @var mixed
     */
    protected $comment = null;

    /**
     * constructor
     *
     * @param ConfigNode $parentNode
     * @param $key
     * @param $value
     * @param string $comment
     */
    public function __construct(ConfigNode $parentNode = null, $key, $value, $comment = '') {
        // get the current node
        $this->parentNode = $parentNode;
        // set the current key identifier
        $this->key = $key;
        // set the current value of the node
        $this->_initType($value);

        if ( empty($this->parentNode->key))
        {
            $this->id = "{$key}";
        }
        else
        {
            $this->id = "{$this->parentNode->key}.{$key}";
        }

        $this->id = str_replace('..', '.', $this->id);

        // optional comment
        $this->comment = $comment;

        $this->children = new ConfigNodeCollection();
    }

    /**
     * method to set the current type and initializes it
     *
     * @param $value
     *
     * @return bool
     */
    private function _initType($value)
    {
        if ( !is_string($value) ) return true;

        switch (true) {
            // check if it's not a json array or object
            case (strpos(trim($value), '[' ) === 0 || strpos(trim($value), '{' ) === 0):
                $this->value = json_decode(trim($value));
                break;
            // check if it's a string with quotes on the outside
            case ((preg_match('/^["|\']{1}(.*)["|\']{1}$/', trim($value), $match)) === 1):
                $this->value = (string) $match[1];
                break;
            case (!is_numeric(trim($value)) && preg_match('/^(true|false){1}$/', trim($value))):
                $this->value = (bool) (strpos($value, 'true') !== false) ? true : false;
                break;
            case (($tmp = @unserialize($value) !== false)):
                $this->value = $tmp;
                break;
            case !is_numeric(trim($value)):
                $this->value = (string) trim($value);
                break;
            default:
                // integer
                if (is_numeric($value) && strpos($value, '.') === false) {
                    $this->value = (int) trim($value);
                }
                // english notation for float
                elseif (is_numeric($value) && strpos($value, '.') > 1) {
                    $this->value = (float) trim($value);
                }
                break;
        }

        return true;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return mixed|null|string
     */
    public function __toString() {
        return is_scalar($this->getValue()) ? $this->getValue() : json_encode($this->getValue());
    }
}