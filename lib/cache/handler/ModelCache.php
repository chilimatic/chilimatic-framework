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

use chilimatic\lib\cache\handler\storage\ModelStorage;
use chilimatic\lib\database\sql\orm\AbstractModel;

class ModelCache
{
    /**
     * @var ModelStorage
     */
    private $modelStorage;

    public function __construct()
    {
        $this->modelStorage = new \SplObjectStorage();
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
     * removes the maybe existing object from the storage
     *
     * @param AbstractModel $model
     *
     * @return void
     */
    public function remove(AbstractModel $model) {
        $this->modelStorage->detach($model);
    }

    /**
     * @param AbstractModel $model
     * @param null $param
     *
     * @return AbstractModel|null
     */
    public function get(AbstractModel $model, $param = null)
    {
        $this->modelStorage->rewind();

        foreach ($this->modelStorage as $storedModel) {
            if ($storedModel === $model) {
                return $model;
            }
        }

        return null;
    }

}