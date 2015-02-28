<?php

namespace chilimatic\lib\cache\engine;

use chilimatic\lib\base\Error;
use \chilimatic\lib\exception\Exception_Cache;

/**
 * Class Cache_APC
 * @package chilimatic\cache\engine
 */
class APC implements CacheInterface {

    /**
     * cache trait to reduce code duplication
     */
    use CacheTrait;

    /**
     *
     * @link http://php.net/manual/en/apc.constants.php
     *      
     * @var int
     */
    const APC_LIST_ACTIVE = 1;
    
    /**
     *
     * @link http://php.net/manual/en/apc.constants.php
     *      
     * @var int
     */
    const APC_LIST_DELETED = 2;
    
    /**
     *
     * @link http://php.net/manual/en/apc.constants.php
     *      
     * @var int
     */
    const APC_BIN_VERIFY_MD5 = 1;
    
    /**
     *
     * @link http://php.net/manual/en/apc.constants.php
     *      
     * @var int
     */
    const APC_BIN_VERIFY_CRC32 = 2;


    /**
     * @param \stdClass $param
     * @throws \chilimatic\lib\exception\Exception_Cache
     */
    public function __construct(\stdClass $param = null) {
        
        /**
         * check if the chache does exists
         */
        if (! function_exists ( 'apc_cache_info' )) {
            throw new Exception_Cache ( 'APC Cache not installed', Cache::ERROR_CACHE_MISSING, Error::SEVERITY_CRIT, __FILE__, __LINE__ );
        }

        // if we can establish the connection
        $this->setConnected(true);
        
        // Get the Cache Listing
        
        if ($this->exists ( 'cacheListing' ) == false) {
            apc_add ( 'cacheListing', array () );
            $this->cacheListing = array ();
        } else {
            $this->cacheListing = apc_fetch ( 'cacheListing' );
        }
        
        // check sum for the saving process
        $this->md5Sum = md5 ( json_encode ( $this->cacheListing ) );
    }
    
    /**
     * Save the cacheListing as an apc listing
     *
     * Although there is the possibility to list the cache otherwise
     *
     * @return boolean
     */
    public function saveCacheListing() {
        if (md5 ( json_encode( $this->cacheListing ) ) === $this->md5Sum)
        {
            return false;
        }

        return apc_add ( 'cacheListing', $this->cacheListing );
    }
    
    /**
     * a listing of all cached entries which have been
     * inserted through this wrapper
     *
     * @return boolean
     */
    public function listCache() {
        $new_list = array ();
        
        foreach ( $this->cacheListing as $key => $val ) {
            $new_list [$key] = new \stdClass ();
            
            foreach ( $val as $skey => $sval ) {
                $new_list [$key]->$skey = $sval;
            }
        }
        
        $this->list = $new_list;
        
        return true;
    }

    /**
     * checks if a key exists
     *
     * @param $key
     * @internal param bool|\string[] $keys A string, or an array of strings, that contain keys.
     *  A string, or an array of strings, that contain keys.
     *
     * @return bool string[]
     */
    public function exists($key) {
        return apc_exists ( $key );
    }
    
    /**
     * Returns a binary dump of the given files and user variables from the APC cache
     *
     * A NULL for files or user_vars signals a dump of every entry, while array() will dump nothing.
     *
     * @link http://php.net/manual/en/function.apc-bin-dump.php
     * @param string[]|null $files
     *            The files. Passing in NULL signals a dump of every entry, while passing in array() will dump nothing.
     * @param string[]|null $user_vars
     *            The user vars. Passing in NULL signals a dump of every entry, while passing in array() will dump nothing.
     *            
     * @return string bool null binary dump of the given files and user variables from the APC cache, false if APC is not enabled, or NULL if an unknown error is encountered.
     */
    public function bin_dump($files = null, $user_vars = null) {
        return apc_bin_dump ( $files, $user_vars );
    }
    
    /**
     * Output a binary dump of the given files and user variables from the APC cache to the named file
     *
     * @link http://php.net/manual/en/function.apc-bin-dumpfile.php
     *      
     * @param string[]|null $files
     *            The file names being dumped.
     * @param string[]|null $user_vars
     *            The user variables being dumped.
     * @param string $filename
     *            The filename where the dump is being saved.
     * @param int $flags
     *            Flags passed to the filename stream. See the file_put_contents() documentation for details.
     * @param resource $context
     *            The context passed to the filename stream. See the file_put_contents() documentation for details.
     *            
     * @return int bool number of bytes written to the file, otherwise false if APC
     *         is not enabled, filename is an invalid file name, filename can't be opened,
     *         the file dump can't be completed (e.g., the hard drive is out of disk space),
     *         or an unknown error was encountered.
     */
    public function bin_dumpfile($files, $user_vars, $filename, $flags = 0, $context = null) {
        return apc_bin_dumpfile ( $files, $user_vars, $filename, $flags, $context );
    }
    
    /**
     * Load the given binary dump into the APC file/user cache
     *
     * @link http://php.net/manual/en/function.apc-bin-load.php
     * @param string $data
     *            The binary dump being loaded, likely from apc_bin_dump().
     * @param int $flags
     *            Either APC_BIN_VERIFY_CRC32, APC_BIN_VERIFY_MD5, or both.
     * @return bool Returns true if the binary dump data was loaded with success, otherwise false is returned.
     *         false is returned if APC is not enabled, or if the data is not a valid APC binary dump (e.g., unexpected size).
     */
    public function bin_load($data, $flags = 0) {
        return apc_bin_load ( $data, $flags );
    }
    
    /**
     * Load the given binary dump from the named file into the APC file/user cache
     *
     * @link http://php.net/manual/en/function.apc-bin-loadfile.php
     *      
     * @param string $filename
     *            The file name containing the dump, likely from apc_bin_dumpfile().
     * @param resource $context
     *            The files context.
     * @param int $flags
     *            Either APC_BIN_VERIFY_CRC32, APC_BIN_VERIFY_MD5, or both.
     *            
     * @return bool Returns true on success, otherwise false Reasons it may return false include APC
     *         is not enabled, filename is an invalid file name or empty, filename can't be opened,
     *         the file dump can't be completed, or if the data is not a valid APC binary dump (e.g., unexpected size).
     */
    function apc_bin_loadfile($filename, $context = null, $flags = 0) {
        return apc_bin_loadfile ( $filename, $context, $flags );
    }
    
    /**
     * returns the shared memory info
     *
     * @link http://www.php.net/manual/de/function.apc-sma-info.php
     *      
     * @param bool $limited            
     * @return array bool
     */
    public function sma_info($limited = false) {
        return apc_sma_info ( $limited );
    }
    
    /**
     * Info wrapper
     *
     * @param string $type            
     * @param bool $limited            
     * @return array bool
     */
    public function info($type = '', $limited = false) {
        return apc_cache_info ( $type, $limited );
    }
    
    /**
     * standard apc method
     *
     * @param
     *            $key
     * @param mixed $value            
     * @param int $expiration            
     *
     * @return bool
     */
    public function store($key, $value = null, $expiration = 0) {
        return $this->set ( $key, $value, ($expiration ? $expiration : NULL) );
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
    public function set($key, $value = null, $expiration = 0) {        
        if (apc_store ( $key, $value, ($expiration ? $expiration : NULL) )) {
            $expiration = (empty ( $expiration )) ? 0 : $expiration;
            
            // Prepare Listing
            $newListing = array (
                    'key' => ( string ) $key,
                    'expiration' => ( int ) $expiration,
                    'updated' => ( string ) date ( 'Y-m-d H:i:s' ) 
            );
            
            if (isset ( $this->cacheListing [$key] )) {
                $this->cacheListing [$key] = array_merge ( $this->cacheListing [$key], $newListing );
            } else {
                $newListing ['created'] = $newListing ['updated'];
                $this->cacheListing [$key] = $newListing;
            }
            
            $this->saveCacheListing ();
            
            return true;
        }
        
        return false;
    }
    
    /**
     * A wrapper since APC standard functions are called that
     * way and it should be possible to use this as a stand alone class
     *
     * @param
     *            $key
     *            
     * @return mixed
     */
    public function fetch($key) {
        return $this->get ( $key );
    }

    /**
     * Stores a file in the bytecode cache, bypassing all filters
     *
     * @param string $file_name
     * @param bool|null $atomic
     *
     * @return bool
     */
    public function compile_file($file_name = '', $atomic = false) {
        if (empty ( $file_name ))
            return false;
        
        return apc_compile_file ( $file_name, $atomic );
    }
    
    /**
     * get method
     *
     * @param $key string            
     *
     * @return mixed
     */
    public function get($key = null) {
        if (isset ( $this->cacheListing [$key] )) {
            $val = apc_fetch ( $key, $success );
            return $success ? $val : $success;
        }
        
        return false;
    }

    /**
     * deletes a file or multiple files
     *
     * @param null $key
     * @internal param \chilimatic\cache\APCIterator|string|\string[] $keys
     *
     * @return bool|\string[]
     */
    public function delete_file($key = null) {
        return apc_delete_file ( $key );
    }
    
    /**
     * flush the whole cache
     *
     * @param $delay integer
     *            [not functional just for the interface]
     *            delay in seconds
     * @return boolean
     */
    public function flush($delay = 0) {
        return apc_clear_cache ();
    }
    
    /**
     * add method calls set function
     *
     * @param $key string            
     * @param $value mixed            
     * @param $expiration int            
     *
     * @return boolean
     */
    public function add($key, $value, $expiration = NULL) {
        return $this->set ( $key, $value, $expiration );
    }
    
    /**
     * delete method
     *
     * @param $key string            
     * @param $time int
     *            [no functionality]
     * @return boolean
     */
    public function delete($key, $time = 0) {
        if (apc_delete ( $key )) {
            unset ( $this->cacheListing [$key] );
            $this->saveCacheListing ();
            return true;
        }
        
        return false;
    }

    public function getStatus(){
        return apc_cache_info();
    }
    
    /**
     * delete cached values based on an input array
     *
     * @param array $key_array
     * @return bool
     */
    public function deleteFromList($key_array = array()) {
        if (empty ( $key_array ))
            return false;
        
        foreach ( $key_array as $key_del ) {
            if (empty ( $this->cacheListing ))
            {
                break;
            }

            foreach ( $this->cacheListing as $key ) {
                if (strpos ( strtolower ( $key ), strtolower ( $key_del ) !== false ))
                {
                    $this->delete ( $key );
                }
            }
        }
        
        return true;
    }
}