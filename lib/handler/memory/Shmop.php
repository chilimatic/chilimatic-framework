<?php
namespace chilimatic\lib\handler\memory;
use chilimatic\lib\handler\memory\shmop\Entry;

/**
 *
 * @author j
 * Date: 5/14/15
 * Time: 5:48 PM
 *
 * File: Shmop.php
 */

class Shmop
{
    /**
     * write mod
     *
     * @var string
     */
    const WRITE_MOD = 'w';

    /**
     * read mod
     *
     * @var string
     */
    const READ_MOD = 'a';

    /**
     * create mod
     *
     * @var string
     */
    const CREATE_MOD = 'c';

    /**
     * default shared memory permission
     *
     * @var int
     */
    const DEFAULT_PERMISSIONS = 0664;

    /**
     * default offset for the shared memory
     *
     * @var int
     */
    const DEFAULT_OFFSET = 0;

    /**
     * the default memory block for the
     * list of all keys
     *
     * @var int
     */
    const INDEX_LIST = 2424;

    /**
     * size of the cache index list (2MB)
     * it's way to big but hey .
     *
     * ...
     *
     * @var int
     */
    const INDEX_SIZE = 2048;

    /**
     * Default size is 1 MB
     *
     * @var int
     */
    const DEFAULT_SIZE = 1024;

    /**
     * the current opened cache identifier
     *
     * @var int
     */
    private $currentIdentifier = null;

    /**
     * for easier read and write purposes the index
     * has its own pointer
     *
     * @var int
     */
    private $indexIdentifier = null;

    /**
     * mode for opening the index memory block
     *
     * @var string
     */
    private $indexMode = 'w';

    /**
     * @param $options
     */
    public function __construct($options) {
        $this->setOptions($options);
    }

    /**
     * @param $id
     * @param string $mode
     * @param int $permission
     * @param int $size
     *
     * @return Entry
     */
    public function open($id, $mode = self::READ_MOD, $permission = self::DEFAULT_PERMISSIONS, $size = self::DEFAULT_SIZE) {
        return new Entry($id, $mode, $permission, $size);
    }

    public function


}