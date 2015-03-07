<?php

namespace chilimatic\lib\cache\engine;

use \chilimatic\lib\exception\Exception_Cache;
use \chilimatic\lib\interfaces\ISingelton;

/**
 * Class Cache
 * @package chilimatic\cache
 */
class Cache implements ISingelton
{

    /**
     * cache object
     *
     * @var object
     */
    public $cache = null;
    
    /**
     * login credentials if needed
     *
     * @var array
     */
    public $credentials = null;

    /**
     * checks if the cache is connected to a pool otherwise -> error
     *
     * @var bool
     */
    public $connected = false;
    
    /**
     * the name of the cache [memcache/memcached/redis/filebased/apc.....]
     *
     * @var string
     */
    private $cacheName = null;

    /**
     * singelton instance
     *
     * @var Cache
     */
    public static $instance = null;


    /**
     * Constructor sets credentials for the Caching
     *
     * @param null $name
     * @param array $credentials
     *
     * @throws \chilimatic\lib\exception\Exception_Cache|\Exception
     */
    private function __construct( $name = null, $credentials = array() )
    {
        try {
            $this->cache = CacheFactory::make($name, $credentials);
            $this->cacheName = get_class($this->cache);
            $this->connected = $this->cache->isConnected();
            $this->credentials = $credentials;
        } catch ( Exception_Cache $e ) {
            throw $e;
        }
    }

    /**
     * singelton constructor
     *
     * @param \stdClass $param
     * @return Cache
     */
    public static function getInstance( \stdClass $param = null)
    {
        if ( !self::$instance instanceof Cache ) {
            $type = $param->type;
            $credentials = (property_exists($param, 'credentials')) ? $param->credentials : null;

            self::$instance = new Cache($type, $credentials);
        }

        return self::$instance;
    }



    /**
     * set wrapper for caching
     *
     * @param string $key
     * @param mixed $value
     * @param int $expiration
     *
     * @return \chilimatic\lib\cache\engine\Cache|null $instance
     */
    public static function set( $key, $value = null, $expiration = NULL )
    {
        if (!self::$instance) {
            return null;
        }

        self::$instance->cache->set( $key, $value, $expiration);

        return self::$instance;
    }


    /**
     * get wrapper for caching
     *
     * @param string $key
     *
     * @return bool
     */
    public static function get( $key )
    {
        if (!self::$instance) return null;

        return self::$instance->cache->get( $key );
    }


    /**
     * gets the cache object
     *
     * @return object
     */
    public static function getCache()
    {
        if (!self::$instance) return null;
        return self::$instance->cache;
    }


    /**
     * gets the cache info
     *
     * @return object
     */
    public static function getStatus()
    {
        if (!self::$instance) return null;
        return self::$instance->cache->getStatus();
    }

    /**
     * @return boolean
     */
    public function isConnected()
    {
        return $this->connected;
    }

    /**
     * @return array
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * @param array $credentials
     */
    public function setCredentials($credentials)
    {
        $this->credentials = $credentials;
    }


}
    