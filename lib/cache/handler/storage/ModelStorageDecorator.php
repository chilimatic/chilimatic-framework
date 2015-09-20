<?php
/**
 *
 * @author j
 * Date: 3/21/15
 * Time: 8:42 PM
 *
 * File: ModelStorageDecorator.php
 */

namespace chilimatic\lib\cache\handler\storage;

use chilimatic\lib\database\sql\orm\AbstractModel;

/**
 * Class ModelStorageDecorator
 *
 * @package chilimatic\lib\cache\handler\storage
 */
class ModelStorageDecorator
{

    /**
     * @var AbstractModel
     */
    private $model;

    /**
     * @var array
     */
    private $data;

    /**
     * @lazy_loaded
     * @var \ReflectionClass
     */
    private $reflection;

    /**
     * @param AbstractModel $model
     * @param $data
     */
    public function __construct(AbstractModel $model, $data)
    {
        $this->model = $model;
        $this->data  = $data;
    }

    /**
     * @param $data
     */
    public function addData($data)
    {
        $this->data = array_merge($this->data, $data);
    }

    /**
     * @return AbstractModel
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param AbstractModel $model
     *
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return \ReflectionClass
     */
    public function getReflection()
    {
        if (!$this->reflection) {
            $this->reflection = new \ReflectionClass($this->model);
        }

        return $this->reflection;
    }

    /**
     * @param \Reflection $reflection
     *
     * @return $this
     */
    public function setReflection($reflection)
    {
        $this->reflection = $reflection;

        return $this;
    }


}