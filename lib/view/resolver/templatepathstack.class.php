<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 28.11.14
 * Time: 19:48
 */

namespace chilimatic\lib\view\resolver;


/**
 * Class templatePathStack
 * @package chilimatic\lib\view\resolver
 */
class templatePathStack
{
    /**
     * @var \SplStack
     */
    private $pathStack;

    /**
     * @param $setting
     */
    public function __construct($setting) {
        $this->pathStack = new \SplStack();
    }

    /**
     * @param $string
     * @return $this
     */
    public function addPath($string) {
        $this->pathStack->add($this->pathStack->count(), $string);
        return $this;
    }

    /**
     * @return mixed|string
     */
    public function getTemplate() {
        foreach ($this->pathStack as $path) {
            if (file_exists($path)) {
                return $path;
            }
            echo $path;
        }
        return '';
    }
} 