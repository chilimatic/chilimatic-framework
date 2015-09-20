<?php
namespace chilimatic\lib\database\sql\orm\querybuilder;
/**
 *
 * @author j
 * Date: 4/21/15
 * Time: 9:57 PM
 *
 * File: ConsistencyTrait.php
 */

Trait ConsistencyTrait
{

    /**
     * @return bool
     * @throws \ErrorException
     */
    public function checkRelations($relationList)
    {
        if (!$relationList) return true;
        $relationList->rewind();
        foreach ($relationList as $entry) {
            if (!class_exists($entry[1])) {
                throw new \ErrorException($entry[1] . ' Relations Class does not exist!');
            }
        }

        return true;
    }


}