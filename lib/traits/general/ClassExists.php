<?php

namespace chilimatic\lib\traits\general;
/**
 *
 * @author j
 * Date: 10/12/15
 * Time: 5:02 PM
 *
 * File: ClassExists.php
 */

Trait ClassExists {

    /**
     * @type string
     */
    private $nameSpaceDelimiter = '\\';

    /**
     * @param string $name
     * @param bool|true $autoLoad
     *
     * @return bool
     */
    public function exists($name, $namespace = '\\', $autoLoad = true)
    {
        if (!$name) {
            return false;
        }

        // it's easier to just concat them and remove double \ afterwards
        $className = $namespace . $this->nameSpaceDelimiter . $name;

        $className = str_replace(
            $this->nameSpaceDelimiter.$this->nameSpaceDelimiter,
            $this->nameSpaceDelimiter,
            $className
        );

        return class_exists($className, $autoLoad);
    }


}