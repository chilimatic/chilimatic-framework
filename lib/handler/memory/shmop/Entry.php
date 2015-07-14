<?php

namespace chilimatic\lib\handler\memory\Shmop;

use chilimatic\lib\exception\MemoryException;
use chilimatic\lib\handler\memory\Shmop;

/**
 * Class Entry
 *
 * @package chilimatic\lib\cache\engine\shmop
 */
class Entry
{

    /**
     * shmop shared memory reference missing
     *
     * @var int
     */
    const ERR_SHMOP_MISSING = 1;

    /**
     * shmop not opened
     *
     * @var int
     */
    const ERR_SHMOP_NOT_OPEN = 2;

    /**
     * read error
     *
     * @var int
     */
    const ERR_SHMOP_READ = 3;

    /**
     *
     * shmop can't write
     *
     * @var int
     */
    const ERR_SHMOP_WRITE = 4;

    /**
     * human readable name for the cache
     *
     * @var string
     */
    protected $keyName = '';

    /**
     * key (random key -> memory)
     *
     * @var int
     */
    protected $key;

    /**
     * permission
     *
     * @var int
     */
    protected $permission;

    /**
     * mode
     *
     * @var string
     */
    protected $mode;

    /**
     * size
     *
     * @var int
     */
    protected $size;

    /**
     * offset
     *
     * @var int
     */
    protected $offset = 0;

    /**
     * start
     *
     * @var int
     */
    protected $start = 0;

    /**
     * count
     *
     * @var int
     */
    protected $count = 0;

    /**
     * time to live
     *
     * @var int
     */
    protected $ttl = 0;

    /**
     * current identifier id
     *
     * @var int
     */
    protected $id = 0;

    /**
     * current data to be saved
     *
     * @var mixed
     */
    protected $data = null;


    /**
     * Opens a shared memory block
     *
     *
     *
     *
     * @param int $id
     * @param string $mode
     * @param int $permission
     * @param int $size
     */
    public function __construct($id = null, $mode = null, $permission = null, $size = null)
    {
        try {
            if (!function_exists('shmop_open')) {
                throw new MemoryException(__METHOD__ . ' - shmop_open does not exist please install the module or change the caching module', self::ERR_SHMOP_MISSING, 10, __FILE__, __LINE__);
            }

            $this->id         = (int)$id;
            $this->mode       = $mode;
            $this->permission = $permission;
            $this->size       = $size;

            if ($this->id) {
                $this->load();
            }
        } catch (MemoryException $e) {
            throw $e;
        }
    }


    /**
     * saves the data of an entry
     *
     * @return boolean
     */
    public function save()
    {

        if ($this->id) {
            $this->id = shmop_open($this->key, $this->mode, $this->permission, $this->size);
        }

        @shmop_write($this->id, serialize($this->data), $this->offset);

        return true;
    }


    /**
     * @return bool
     * @throws MemoryException
     */
    public function load()
    {
        try {
            if (empty($this->key)) {
                throw new MemoryException(__METHOD__ . ' - no key exists', self::ERR_SHMOP_NOT_OPEN, 1, __FILE__, __LINE__);
            }

            if (empty($this->permission)) {
                throw new MemoryException(__METHOD__ . ' - no permission set', self::ERR_SHMOP_NOT_OPEN, 1, __FILE__, __LINE__);
            }

            // switch to read
            if (!($this->id = shmop_open($this->key, Shmop::READ_MOD, $this->permission, $this->size))) {
                throw new MemoryException(__METHOD__ . ' - shmop_open not working', self::ERR_SHMOP_NOT_OPEN, 1, __FILE__, __LINE__);
            }

            $data = @shmop_read($this->id, $this->start, $this->count);

            $this->data = $data;
        } catch (MemoryException $e) {
            throw $e;
        }


        return true;
    }

    /**
     * deletes the entry
     *
     * @return boolean
     */
    public function delete()
    {
        if (empty($this->id)) {
            $this->id = shmop_open($this->key, $this->mode, $this->permission, $this->size);
        }
        @shmop_delete($this->id);
        @shmop_close($this->id);

        return true;

    }

    /**
     * @return string
     */
    public function getKeyName()
    {
        return $this->keyName;
    }

    /**
     * @param string $keyName
     *
     * @return $this
     */
    public function setKeyName($keyName)
    {
        $this->keyName = $keyName;

        return $this;
    }

    /**
     * @return int
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param int $key
     *
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = (int)$key;

        return $this;
    }

    /**
     * @return int
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * @param int $permission
     *
     * @return $this
     */
    public function setPermission($permission)
    {
        $this->permission = $permission;

        return $this;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     *
     * @return $this
     */
    public function setMode($mode)
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param int $size
     *
     * @return $this
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     *
     * @return $this
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @return int
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param int $start
     *
     * @return $this
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param int $count
     *
     * @return $this
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * @return int
     */
    public function getTtl()
    {
        return $this->ttl;
    }

    /**
     * @param int $ttl
     *
     * @return $this
     */
    public function setTtl($ttl)
    {
        $this->ttl = $ttl;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        if (is_string($this->data) && strpos(trim($this->data), 'a:') == 0) {
            return unserialize($this->data);
        }

        return $this->data;
    }

    /**
     * @param mixed $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }


    /**
     * destruct
     */
    public function __destruct()
    {
        @shmop_close($this->id);
    }

}
