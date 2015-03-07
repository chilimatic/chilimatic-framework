<?php
/**
 *
 * @author j
 * Date: 3/7/15
 * Time: 1:38 PM
 *
 * File: lastNode.php
 */
namespace chilimatic\lib\node\filter;

/**
 * returns the "deepest nested Node"
 *
 * Class LastNode
 *
 * @package chilimatic\lib\node\filter
 */
class LastNode extends AbstractFilter
{
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

        $idValue = 0;
        $returnNode = null;
        foreach($param as $node) {
            if (strlen($node->getId()) > $idValue) {
                $idValue = $node->getId();
                $returnNode = $node;
            }
        }

        return $returnNode;
    }
}