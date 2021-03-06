<?php
/**
 * Created by JetBrains PhpStorm.
 * User: j
 * Date: 26.10.13
 * Time: 19:19
 * Node System like the JSDOM
 */

namespace chilimatic\lib\config;

use chilimatic\lib\datastructure\graph\INode;

/**
 * Class Node
 *
 * @package chilimatic\lib\config
 */
class Node extends \chilimatic\lib\datastructure\graph\Node
{
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
     * @param INode $parentNode
     * @param $key
     * @param $data
     * @param string $comment
     */
    public function __construct(INode $parentNode = null, $key, $data, $comment = '')
    {
        // get the current node
        $this->parentNode = $parentNode;
        // set the current key identifier
        $this->key = $key;
        // set the current value of the node
        if ($data && !$this->initType($data)) {
            $this->data = $data;
        }

        if (!$this->parentNode || !$this->parentNode->key) {
            $this->id = (string)self::DEFAULT_KEY_DELIMITER . $key . self::DEFAULT_KEY_DELIMITER;
        } else {
            $this->id = (string)$this->parentNode->key . self::DEFAULT_KEY_DELIMITER . $key . self::DEFAULT_KEY_DELIMITER;
        }

        $this->id = preg_replace('/[#]{2,}/', self::DEFAULT_KEY_DELIMITER, $this->id);

        // optional comment
        $this->comment = $comment;
        $this->initChildren();
    }


    /**
     * method to set the current type and initializes it
     *
     * @param $data
     *
     * @return bool
     */
    private function initType($data)
    {
        if (!is_string($data)) return false;

        $data = trim($data);
        switch (true) {
            case (in_array($data, ['true', 'false'])):
                $this->data = (bool)(strpos($data, 'true') !== false) ? true : false;
                break;
            case !is_numeric($data):
                if ($res = json_decode($data)) {
                    $this->data = $res;
                } else if (($res = @unserialize($data)) !== false) {
                    $this->data = $res;
                } else if ((preg_match('/^["|\']{1}(.*)["|\']{1}$/', $data, $match)) === 1) {
                    $this->data = (string)$match[1];
                } else {
                    $this->data = (string)$data;
                }
                break;

            default:
                // integer
                if (is_numeric($data) && strpos($data, '.') === false) {
                    $this->data = (int)$data;
                } else {
                    $this->data = (float)$data;
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
    public function __toString()
    {
        return is_scalar($this->getData()) ? $this->getData() : json_encode($this->getData());
    }
}