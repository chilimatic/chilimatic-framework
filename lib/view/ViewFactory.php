<?php
namespace chilimatic\lib\view;
use chilimatic\lib\Interfaces\IFactoryOptions;
use chilimatic\lib\Traits\ClassExists;

/**
 *
 * @author j
 * Date: 10/12/15
 * Time: 4:57 PM
 *
 * File: ViewFactory.php
 */

class ViewFactory implements IFactoryOptions
{

    /**
     * trait that checks if a class does exist
     */
    use ClassExists;

    /**
     * @param string $name
     * @param $options
     *
     * @return null
     */
    public function make(string $name, $options = [])
    {
        if (!$name || !$this->exists($name)) {
            return null;
        }

        return $name($options);
    }

    /**
     * @param string $name
     * @param $options
     *
     * @return mixed|null
     */
    public function __invoke(string $name, $options)
    {
        return $this->make($name, $options);
    }
}