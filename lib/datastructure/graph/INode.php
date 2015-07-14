<?php
namespace chilimatic\lib\datastructure\graph;

use chilimatic\lib\datastructure\graph\filter\AbstractFilter;

/**
 *
 * @author j
 * Date: 4/10/15
 * Time: 6:31 PM
 *
 * File: INode.php
 */
interface INode
{

    /**
     * @param INode $parentNode
     * @param $key
     * @param $data
     * @param string $comment
     */
    public function __construct(INode $parentNode = null, $key, $data, $comment = '');

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getById($id);

    /**
     * @param mixed $key
     *
     * @param filter\AbstractFilter $filter
     *
     * @return mixed
     */
    public function getByKey($key, AbstractFilter $filter = null);

    /**
     * @param mixed $key
     *
     * @return mixed
     */
    public function getLastByKey($key);
}