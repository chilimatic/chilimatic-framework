<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 28.10.14
 * Time: 11:44
 */

namespace chilimatic\lib\cache\engine;

/**
 * Class CacheTrait
 *
 * @package chilimatic\lib\cache
 */
trait CacheTrait
{

    /**
     * storage for key flushing, if needed
     *
     * @var array
     */
    protected $cacheListing = array();

    /**
     * md5 sum of the cache_list from
     * the contructor
     *
     * @var string
     */
    protected $md5Sum = null;

    /**
     * connected boolean
     *
     * @var bool
     */
    protected $connected = false;

    /**
     * list array of all entries
     *
     * @var array
     */
    protected $list = array();

    /**
     * @return bool
     */
    public function isConnected()
    {
        return (bool)$this->connected;
    }

    /**
     * @param bool $connected
     */
    protected function setConnected($connected = true)
    {
        $this->connected = (bool)$connected;
    }

}