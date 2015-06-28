<?php
/**
 *
 * @author j
 * Date: 3/7/15
 * Time: 1:38 PM
 *
 * File: lastNode.php
 */
namespace chilimatic\lib\datastructure\graph\filter;

use chilimatic\lib\traits\comperator\StringValueBiggerThan;

/**
 * returns the "deepest nested Node"
 *
 * Class LastNode
 *
 * @package chilimatic\lib\datastructure\graph\filter
 */
class LastNode extends AbstractFilter
{
    use StringValueBiggerThan;

    /**
     * @param \SplObjectStorage $param
     *
     * @return \SplObjectStorage
     */
    function filter($param = null)
    {
        if (!$param) {
            return new \SplObjectStorage();
        }

        if ($param->count() === 1) {
            return $param;
        }

        $idValue          = $returnNode = null;
        $returnCollection = new \SplObjectStorage();
        foreach ($param as $node) {
            if (!$idValue || $this->compare($node->getId(), $idValue)) {
                $idValue    = $node->getId();
                $returnNode = $node;
            }
        }

        $returnCollection->attach($returnNode);

        return $returnCollection;
    }

}