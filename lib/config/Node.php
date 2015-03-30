<?php
/**
 * Created by JetBrains PhpStorm.
 * User: j
 * Date: 26.10.13
 * Time: 19:19
 * Node System like the JSDOM
 */

namespace chilimatic\lib\config;

/**
 * Class Node
 * @package chilimatic\lib\config
 */
class Node extends \chilimatic\lib\datastructure\graph\Node{

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
     * @param Node $parentNode
     * @param $key
     * @param $data
     * @param string $comment
     */
    public function __construct(\chilimatic\lib\config\Node $parentNode = null, $key, $data, $comment = '') {
        // get the current node
        $this->parentNode = $parentNode;
        // set the current key identifier
        $this->key = $key;
        // set the current value of the node
        $this->_initType($data);

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

        $this->children = new Collection();
    }

    /**
     * method to set the current type and initializes it
     *
     * @param $data
     *
     * @return bool
     */
    private function _initType($data)
    {
        if ( !is_string($data) ) return true;

        switch (true) {
            // check if it's not a json array or object
            case (strpos(trim($data), '[' ) === 0 || strpos(trim($data), '{' ) === 0):
                $this->data = json_decode(trim($data));
                break;
            // check if it's a string with quotes on the outside
            case ((preg_match('/^["|\']{1}(.*)["|\']{1}$/', trim($data), $match)) === 1):
                $this->data = (string) $match[1];
                break;
            case (!is_numeric(trim($data)) && preg_match('/^(true|false){1}$/', trim($data))):
                $this->data = (bool) (strpos($data, 'true') !== false) ? true : false;
                break;
            case (($tmp = @unserialize($data) !== false)):
                $this->data = $tmp;
                break;
            case !is_numeric(trim($data)):
                $this->data = (string) trim($data);
                break;
            default:
                // integer
                if (is_numeric($data) && strpos($data, '.') === false) {
                    $this->data = (int) trim($data);
                }
                // english notation for float
                elseif (is_numeric($data) && strpos($data, '.') > 1) {
                    $this->data = (float) trim($data);
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
        return is_scalar($this->getData()) ? $this->getData() : json_encode($this->getData());
    }
}