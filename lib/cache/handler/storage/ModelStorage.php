<?php
/**
 *
 * @author j
 * Date: 3/20/15
 * Time: 4:44 PM
 *
 * File: Model.php
 */
namespace chilimatic\lib\cache\handler\storage;
use chilimatic\lib\database\sql\orm\AbstractModel;

class ModelStorage implements \Countable, \Iterator, \Serializable
{
    /**
     * @var array
     */
    private $storage = [];

    /**
     * @var string
     */
    private $currentId;

    /**
     * @param AbstractModel $model
     *
     * @return string
     */
    public function generateHash(AbstractModel $model)
    {
        return md5(serialize($model));
    }

    /**
     * @param AbstractModel $model
     *
     * @return bool
     */
    public function contains(AbstractModel $model)
    {
        foreach ($this->storage as $key => $storedModel) {
            if ($model === $storedModel->getModel()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param AbstractModel $model
     * @param null $param
     */
    public function addData(AbstractModel $model, $param = null)
    {
        foreach ($this->storage as $key => $storedModel) {
            if ($model === $storedModel->getModel()) {
                $storedModel->addData($param);

                return;
            }
        }
    }

    /**
     * @param AbstractModel $model
     * @param null|mixed $param
     */
    public function attach(AbstractModel $model, $param = null)
    {
        if ($this->contains($model)) {
            return;
        }
        $this->currentId                 = $this->generateHash($model);
        $this->storage[$this->currentId] = new ModelStorageDecorator($model, $param);
    }

    /**
     * @param string $key
     *
     * @return void
     */
    public function deleteByKey($key)
    {
        if (isset($this->storage[$key])) {
            unset($this->storage[$key]);
        }
    }

    /**
     * @param AbstractModel $model
     *
     * @return void
     */
    public function detach(AbstractModel $model)
    {
        foreach ($this->storage as $key => $storedModel) {
            if ($model === $storedModel) {
                unset($this->storage[$key]);
            }
        }
    }

    /**
     * @param string $modelName
     * @param array $param
     *
     * @return null
     */
    public function findByParam($modelName, $param)
    {
        /**
         * @var ModelStorageDecorator $storedModel
         */
        foreach ($this->storage as $key => $storedModel) {
            if ($modelName != get_class($storedModel)) {
                continue;
            }

            if ($this->containsParam($storedModel, $param)) {
                return $storedModel->getModel();
            }
        }

        return null;
    }

    /**
     * @param ModelStorageDecorator $storedModel
     * @param array $param
     *
     * @return null|AbstractModel
     */
    public function containsParam(ModelStorageDecorator $storedModel, $param)
    {
        if (!$storedModel || !$param) {
            return false;
        }
        $reflection = $storedModel->getReflection();

        foreach ($param as $propertyName => $propertyValue) {
            if (!($p = $reflection->getProperty($propertyName))) {
                return false;
            }

            if ($p->isPublic()) {
                if ($propertyValue != $storedModel->$propertyName) {
                    return false;
                }
            }

            if ($p->isPrivate() || $p->isProtected()) {
                $p->setAccessible(true);
                if ($propertyValue != $storedModel->$propertyName) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param AbstractModel $model
     * @param null $param
     *
     * @return null|AbstractModel
     */
    public function get(AbstractModel $model, $param = null)
    {
        if ($this->contains($model)) {
            return $this->storage[$this->generateHash($model, $param)]->getModel();
        }

        return null;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     *
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->storage[$this->currentId];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     *
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {

    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     *
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->currentId;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     *
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     *       Returns true on success or false on failure.
     */
    public function valid()
    {
        return isset($this->storage[$this->currentId]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     *
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $keyList         = array_keys($this->storage);
        $this->currentId = array_shift($keyList);
    }


    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     *
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return $this;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     *
     * @link http://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized <p>
     *                           The string representation of the object.
     *                           </p>
     *
     * @return void
     */
    public function unserialize($serialized)
    {
        // TODO: Implement unserialize() method.
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     *
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     *       </p>
     *       <p>
     *       The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->storage);
    }


}