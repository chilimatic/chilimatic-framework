<?php
namespace chilimatic\lib\cache\engine;

/**
 * Interface Cache_Interface
 * @package chilimatic\cache
 */
Interface CacheInterface
{
    /**
     * @var int
     */
    const NO_DATA_TO_SAVE = 1;

    /**
     * Constructor in every cache abstractor should get the
     * parameters as an array so the usual parameter problems wont be there
     *
     * @param array|\stdClass $param
     *
     * @return \chilimatic\lib\cache\engine\CacheInterface
     */
    public function __construct(\stdClass $param = null );


    /**
     * Cache listing Save method [so an overview can be generated]
     * 
     * @return boolean
     */
    public function saveCacheListing();


    /**
     * Gets a cache entry by key
     * 
     * @param string $key
     * 
     * @return boolean
     */
    public function get( $key = null );


    /**
     * Sets a cache entry by key
     *
     * @param string $key
     * @param mixed $value
     * @param int $expiration
     *
     * @internal param mixed $ttl
     *
     * @return boolean
     */
    public function set( $key , $value = null , $expiration = 0 );


    /**
     * deletes the whole cache
     * 
     * @return void
     */
    public function flush();


    /**
     * delete a cache entry
     *
     * @param string $key
     * @param int $time
     * @return
     * @internal param int $ttl
     */
    public function delete( $key , $time = 0 );


    /**
     * cleans up the cache list
     * 
     * @param array $key_array
     */
    public function deleteFromList( $key_array = array() );

    /**
     * gets the status of the cache
     *
     * @return mixed
     */
    public function getStatus();
} 