<?php

namespace chilimatic\lib\cache\engine;

/**
 * Class CacheMemcached
 * @package chilimatic\cache
 */
class Memcached extends \Memcached implements CacheInterface
{
    /**
     * cache trait to reduce code duplication
     */
    use CacheTrait;

    /**
     * @var int
     */
    const DEFAULT_PORT = 11211;

    /**
     * construct wrapper
     *
     * @param \stdClass $param
     *
     * @return \chilimatic\lib\cache\engine\Memcached
     */
    public function __construct($param = null )
    {

        parent::__construct( ( isset($param->persistent_id) ) ? $param->persistent_id : NULL, isset($param->callback) ? $param->callback : null );
        
        if ( !empty( $param->server_list ) )
        {
            $server = $param->server_list;
            if ( count( $server ) == 1 ) {
            	$server = array_pop($server);
                $this->setConnected(
                    parent::addServer(
                        $server->host,
                        isset($server->port) ? $server->port : self::DEFAULT_PORT,
                        isset($server->weight) ? $server->weight : null )
                );
            } else {
                $this->setConnected(parent::addServers( $server ));
            }
        }
        
        // Get the Cache Listing
        $this->cacheListing = parent::get( 'cacheListing' );
        
        if ( $this->cacheListing === false )
        {
            parent::add( 'cacheListing', array() );
            $this->cacheListing = array();
        }
        
        // check sum for the saving process
        $this->md5Sum = md5( json_encode( $this->cacheListing ) );
    }
    
    /**
     * Save the cacheListing to memcached
     *
     * @return boolean
     */
    public function saveCacheListing()
    {

        if ( md5( json_encode( $this->cacheListing ) ) === $this->md5Sum ) return false;
        
        return parent::set( 'cacheListing', $this->cacheListing );
    }
    
    /**
     * a listing of all cached entries which have been
     * inserted through this wrapper
     *
     * @return boolean
     */
    public function listCache()
    {

        $newlist = array();
        
        foreach ( $this->cacheListing as $key => $val )
        {
            $newlist [$key] = new \stdClass();
            
            foreach ( $val as $skey => $sval )
            {
                $newlist [$key]->$skey = $sval;
            }
        }
        
        $this->list = $newlist;
        
        return true;
    }
    
    
    /**
     * returns the current status 
     */
    public function getStatus()
    {

        $info_array = array();
        $info_array ['status'] = parent::getStats();
        $info_array ['version'] = parent::getVersion();
        $info_array ['server_list'] = parent::getServerList();
        
        return $info_array;
    }
    
    /**
     * set method
     *
     * @param $key string            
     * @param $value mixed            
     * @param $expiration int            
     *
     * @return boolean
     */
    public function set( $key, $value = null, $expiration = 0 )
    {

        if ( parent::set( $key, $value, ( $expiration ? $expiration : NULL ) ) )
        {
            $expiration = ( empty( $expiration ) ) ? 0 : $expiration;
            
            // Prepare Listing
            $newListing = array(
                    'key' => (string) $key,
                    'expiration' => (int) $expiration,
                    'updated' => (string) date( 'Y-m-d H:i:s' ) );
            
            if ( isset( $this->cacheListing [$key] ) )
            {
                $this->cacheListing [$key] = array_merge( $this->cacheListing [$key], $newListing );
            }
            else
            {
                $newListing ['created'] = $newListing ['updated'];
                $this->cacheListing [$key] = $newListing;
            }
            
            $this->saveCacheListing();
            
            return true;
        }
        
        return false;
    }
    
    /**
     * get method
     *
     * @param $key string            
     * @param $cache_cb callable
     *            [optional]
     * @param $cas_token float
     *            [optional]
     *            
     * @return mixed
     */
    public function get( $key = null, $cache_cb = NULL, &$cas_token = NULL )
    {

        if ( isset( $this->cacheListing [$key] ) )
        {
            return parent::get( $key, $cache_cb, $cas_token );
        }
        
        return false;
    }
    
    /**
     * flush the whole cache
     *
     * @param $delay integer
     *            delay in seconds
     *            
     * @return boolean
     */
    public function flush( $delay = 0 )
    {

        if ( parent::flush( (int) $delay ) )
        {
            $this->cacheListing = array();
            return true;
        }
        
        return false;
    }
    
    /**
     * add method
     *
     * @param $key string            
     * @param $value mixed            
     * @param $expiration int            
     *
     * @return boolean
     */
    public function add( $key, $value, $expiration = NULL )
    {

        return $this->set( $key, $value, $expiration );
    }
    
    /**
     * delete method
     *
     * @param $key string            
     * @param $time int            
     * @return boolean
     */
    public function delete( $key, $time = 0 )
    {

        if ( parent::delete( $key, ( $time ? $time : NULL ) ) )
        {
            unset( $this->cacheListing [$key] );
            $this->saveCacheListing();
            return true;
        }
        
        return false;
    }
    
    /**
     * delete method multiserver pools
     *
     * @param $server_key string            
     * @param $key string            
     * @param $time int            
     *
     * @return boolean
     */
    public function deleteByKey( $server_key, $key, $time = 0 )
    {

        if ( parent::deleteByKey( $server_key, $key, ( $time ? $time : NULL ) ) )
        {
            unset( $this->cacheListing [$key] );
            $this->saveCacheListing();
            return true;
        }
        
        return false;
    }
    
    /**
     * delete memcached values based on an input array
     *
     *
     * @param array $key_array
     * @return bool
     */
    public function deleteFromList( $key_array = array() )
    {

        if ( empty( $key_array ) ) return false;
        
        foreach ( $key_array as $key_del )
        {
            if ( empty( $this->cacheListing ) ) break;
            foreach ( $this->cacheListing as $key )
            {
                if ( mb_strpos(mb_strtolower($key), mb_strtolower($key_del)) !== false  ) $this->delete( $key );
            }
        }
        
        return true;
    }


    /**
     *
     */
    public function __destruct()
    {
        // optional
    }
}
