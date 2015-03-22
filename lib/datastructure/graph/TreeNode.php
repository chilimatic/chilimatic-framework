<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 18.10.14
 * Time: 17:24
 */


namespace chilimatic\lib\datastructure\graph;

class TreeNode extends Node {

    /**
     * @var
     */
    protected $treePath;

    /**
     * @var int
     */
    protected $maxDepth = 0;

    /**
     * @var int
     */
    protected $depth = 0;

    /**
     * @param int $depth
     * @return null
     */
    private function searchTree($depth = 0)
    {
        $this->depth = $depth;
        foreach ($this->getChildren()->getList() as $node)
        {
            if ($depth > count($this->treePath)) {
                break;
            }

            if ($this->treePath[$depth] != $node->getKey()) {
                continue;
            }

            /**
             * @var $node TreeNode
             */
            if (count($this->treePath) > $depth && $this->allowedToDiveDeeper($depth)) {
                $node->setTreepath($this->treePath);
                return $node->searchTree(++$depth);
            }
        }

        return $this;
    }

    /**
     * @param $depth
     * @return bool
     */
    protected function allowedToDiveDeeper($depth) {
        if ($this->maxDepth == 0) {
            return true;
        }

        return $this->maxDepth >= $depth;
    }

    /**
     * @param $key
     * @param $data
     * @param string $delimiter
     *
     * @return TreeNode|null
     */
    public function appendToBranch($key, $data, $delimiter = self::DEFAULT_KEY_DELIMITER) {
        $node = $this->findTreeBranch($key, $delimiter);

        /**
         * check if a whole tree structure is missing
         * build it if necessary
         */
        if ($this->getAmountMissingTreeNodes() > 1) {
            return $this->createTree($this->getMissingKeysByDepth(), $node);
        }

        /**
         * create just one missing node to a fully established tree with branches
         */
        $key = array_pop(explode((string) $delimiter, trim((string) $key, (string) $delimiter)));
        $newNode = new self($node, $key , $data, $delimiter);
        $node->addChild($newNode);

        return null;
    }

    /**
     * returns the missing keys for the TreeNodes to be built
     *
     * @return array
     */
    public function getMissingKeysByDepth() {
        return array_slice(
            $this->getTreePath(),
            $this->getAmountMissingTreeNodes() * -1,
            $this->getAmountMissingTreeNodes()
        );
    }

    /**
     * recursive way to build tree structure
     *
     * @param array $keyParts
     * @param TreeNode $rootNode
     * @return TreeNode
     */
    protected function createTree(&$keyParts, TreeNode $rootNode) {
        if (!count($keyParts)) return $rootNode;

        $newKey = array_shift($keyParts);
        $newNode = new self($rootNode, $newKey , null, $rootNode->getKeyDelimiter());
        $rootNode->addChild($newNode);

        return $this->createTree($keyParts, $newNode);
    }


    /**
     * @param $key
     * @param string $delimiter
     *
     * @return $this|null
     */
    public function findTreeBranch($key, $delimiter = self::DEFAULT_KEY_DELIMITER) {
        $this->resetTreeSearch();
        $this->treePath = explode($delimiter, trim($key, $delimiter));
        if (($node = $this->searchTree()) != null) {
            return $node;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getAmountMissingTreeNodes() {
        return count($this->treePath) - ($this->depth+1);
    }

    /**
     * resets the treePath
     */
    protected function resetTreeSearch(){
        $this->setTreePath([]);
    }


    /**
     * @return mixed
     */
    public function getTreePath()
    {
        return $this->treePath;
    }

    /**
     * @param mixed $treePath
     *
     * @return $this
     */
    public function setTreePath($treePath)
    {
        $this->treePath = $treePath;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxDepth()
    {
        return $this->maxDepth;
    }

    /**
     * @param int $maxDepth
     *
     * @return $this
     */
    public function setMaxDepth($maxDepth)
    {
        $this->maxDepth = $maxDepth;
        return $this;
    }

    /**
     * @return string
     */
    public function getKeyDelimiter()
    {
        return $this->keyDelimiter;
    }

    /**
     * @param string $keyDelimiter
     *
     * @return $this
     */
    public function setKeyDelimiter($keyDelimiter)
    {
        $this->keyDelimiter = $keyDelimiter;
        return $this;
    }
}