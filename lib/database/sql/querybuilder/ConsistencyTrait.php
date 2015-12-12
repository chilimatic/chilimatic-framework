<?php
namespace chilimatic\lib\database\sql\querybuilder;
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
        if (!$relationList) {
            return true;
        }

        foreach ($relationList as $entry) {
            if (!class_exists($entry['model'])) {
                throw new \ErrorException($entry['model'] . ' Relations Class does not exist!');
            }
        }

        return true;
    }


}