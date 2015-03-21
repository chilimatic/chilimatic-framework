<?php
/**
 *
 * @author j
 * Date: 3/20/15
 * Time: 4:39 PM
 *
 * File: Model.php
 */

namespace chilimatic\lib\cache\handler;

use chilimatic\lib\cache\engine\CacheInterface;
use chilimatic\lib\cache\handler\storage\ModelStorage;
use chilimatic\lib\database\orm\AbstractModel;

class ModelCache
{
    /**
     * @var ModelStorage
     */
    private $modelStorage;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @param CacheInterface $cache
     */
    public function __construct(CacheInterface $cache = null)
    {
        $this->cache = $cache;
        $this->modelStorage = new ModelStorage();
    }

    /**
     * @param AbstractModel $model
     * @param null $param
     */
    public function set(AbstractModel $model, $param = null)
    {
        if (!$this->modelStorage->contains($model)) {
            $this->modelStorage->attach($model);
        }
    }

    /**
     * @param AbstractModel $model
     * @param null $param
     */
    public function get(AbstractModel $model, $param = null) {
        $this->modelStorage->rewind();



    }

}