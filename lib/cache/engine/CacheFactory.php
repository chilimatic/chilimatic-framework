<?php
/**
 *
 * @author j
 * Date: 12/23/14
 * Time: 5:06 PM
 *
 * File: cachefactory.class.php
 */

namespace chilimatic\lib\cache\engine;
use chilimatic\lib\base\Error;
use chilimatic\lib\exception\Exception_Cache;

/**
 * Class CacheFactory
 *
 * @package chilimatic\lib\cache
 */
class CacheFactory
{
    /**
     * Default caching type
     *
     * @var string
     */
    const CACHE_DEFAULT_TYPE = 'File';

    /**
     * Error Number
     *
     * @var string
     */
    const ERROR_CACHE_MISSING = 1;

    /**
     * Init like always enables to reset the class
     *
     * @param $name
     * @param array $credentials
     *
     * @throws \chilimatic\lib\exception\Exception_Cache|\Exception
     *
     * @return mixed
     */
    public static function make( $name, $credentials = array() )
    {
        if (!$name && !$credentials) {
            return null;
        }

        try
        {
            $cacheName = ( empty( $name ) ) ? self::CACHE_DEFAULT_TYPE : $name;
            $c = '\\'.__NAMESPACE__.'\\'.$cacheName;

            // check if the class exists or can be loaded
            if ( !class_exists( $c ) ) {
                throw new Exception_Cache( _( 'The Cache is not implemented or not installed:' ).$c, self::ERROR_CACHE_MISSING, Error::SEVERITY_CRIT, __FILE__, __LINE__ );
            }

            /**
             * @var CacheTrait $cache
             */
            $cache = new $c( $credentials );

            if ($cache->isConnected() == false ) {
                throw new Exception_Cache( _( 'The Cache could not establish connection:' ).$c, self::ERROR_CACHE_MISSING, Error::SEVERITY_CRIT, __FILE__, __LINE__ );
            }

        } catch ( Exception_Cache $e )
        {
            throw $e;
        }

        return $cache;
    }
}