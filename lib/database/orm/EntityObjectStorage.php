<?php
/**
 *
 * @author j
 * Date: 12/31/14
 * Time: 12:04 AM
 *
 * File: entityobjectstorage.class.php
 */

namespace chilimatic\lib\database\orm;

class EntityObjectStorage extends \SplObjectStorage implements \JsonSerializable
{

    public function jsonSerialize()
    {
        $this->rewind();
        $arr = [];

        while ($this->valid()) {
            $arr[] = $this->current();
            $this->next();
        }

        return $arr;
    }
}