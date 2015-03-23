<?php
/**
 *
 * @author j
 * Date: 3/11/15
 * Time: 9:07 PM
 *
 * File: FirstNode.php
 */
namespace chilimatic\lib\datastructure\graph\filter;
use chilimatic\lib\comperator\traits\StringValueBiggerThan;

/**
 * Class LastNode
 *
 * @package chilimatic\lib\datastructure\graph\filter
 */
class FirstNode extends AbstractFilter
{

    use StringValueBiggerThan;

    /**
     * @param \SplObjectStorage $param
     *
     * @return mixed
     */
    function filter($param = null)
    {
        if (!$param) {
            return null;
        }

        if ($param->count() === 1){
            return $param;
        }

        $idValue = $returnNode = null;
        $returnCollection = new \SplObjectStorage();
        foreach($param as $node) {
            if (!$idValue || $this->compare($idValue, $node->getId())) {
                $idValue = $node->getId();
                $returnNode = $node;
            }
        }

        $returnCollection->attach($returnNode);
        return $returnCollection;
    }
}