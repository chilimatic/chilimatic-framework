<?php

namespace chilimatic\lib\cache\engine;


use chilimatic\lib\base\Error;
use chilimatic\lib\cache\engine\shmop\Entry;
use chilimatic\lib\exception\CacheException;
use chilimatic\lib\traits\RandomDataGenerator;

/**
 * Class Shmop
 *
 * @package chilimatic\lib\cache\engine
 */
class Shmop implements CacheInterface
{
    /**
     * random number generator
     */
    use RandomDataGenerator;

    /**
     * cache trait to reduce code duplication
     */
    use CacheTrait;

    /**
     * missing cache error
     *
     * @var int
     */
    const ERROR_CACHE_MISSING = 2;

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
     * time to live is unlimited
     *
     * @var int
     */
    const TTL_UNLIMITED = 0;

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
     * constructor
     */
    public function __construct(\stdClass $param = null)
    {
        $this->indexMode = self::CREATE_MOD;
        $this->init();
    }

    /**
     * initializes the cache
     *
     * @throws CacheException
     *
     * @return bool
     */
    public function init()
    {
        try
        {

            $this->readIndexList();
            // load the cache into the memory for faster access
            if ( !empty( $this->cacheListing ) ) {
                $this->loadCache();
            }
            $this->setConnected(true);

        }
        catch (CacheException $e)
        {
            throw $e;
        }

        return true;
    }

    public function readIndexList()
    {
        if ( !function_exists( 'shmop_open' ) )
        {
            throw new CacheException( _('Shared memory functions are not available'), self::ERROR_CACHE_MISSING, Error::SEVERITY_CRIT, __FILE__, __LINE__ );
        }

        $id = shmop_open( self::INDEX_LIST, $this->indexMode, self::DEFAULT_PERMISSIONS, self::INDEX_SIZE );
        $tmp = shmop_read( $id, self::DEFAULT_OFFSET, self::INDEX_SIZE );
        $cacheListing = strlen(trim($tmp)) > 1 ? unserialize( $tmp ) : array();

        $this->cacheListing = (!is_array($cacheListing)) ? array() : $cacheListing;
    }



    /**
     * load all the items into the current memory
     * for faster access
     *
     * @return bool
     */
    protected function loadCache()
    {

        if ( empty( $this->cacheListing ) ) return false;
        
        foreach ( $this->cacheListing as $key => $entry )
        {
            if (!$entry) {
                continue;
            }


            if (!($entry instanceof Entry))
            {
                /** @var $entry Entry */
                unset($entry);
                continue;
            }

            /** @var $entry Entry */
            if (!$entry->getId() )
            {
                unset( $this->cacheListing [$key] );
                continue;
            }
            
            // if there is no value or the time to live is exceeded
            // unset the index and if necessary allocated the memoryblock
            if ( !$entry->getData() || ($entry->getTtl() !== self::TTL_UNLIMITED && $entry->getTtl() < time()) )
            {
                if ($entry instanceof Entry) {
                    $entry->delete();
                }
                
                unset( $this->cacheListing [$key] );
                continue;
            }
            
            $this->list [$key] = $entry->getData();
        }
        
        return true;
    }



    /**
     * (non-PHPdoc)
     *
     * @see \chilimatic\lib\cache\engine\Cache_Interface::save_cacheListing()
     */
    public function saveCacheListing()
    {
        $s = serialize( $this->cacheListing );
        $id = shmop_open(self::INDEX_LIST, self::WRITE_MOD, self::DEFAULT_PERMISSIONS, strlen($s));
        return shmop_write( $id, $s,self::DEFAULT_OFFSET);
    }



    /**
     * Gets a cache entry by key
     *
     * @param string $key
     *
     * @return Entry|null
     */
    public function get( $key = null )
    {
        if ( !isset( $this->list[$key] ) ) return null;
        $this->currentIdentifier = $this->cacheListing[$key]->getId();
        return $this->list[$key];
    }


    /**
     * Sets a cache entry by key
     *
     * @param string $key
     * @param mixed $value
     * @param int $expiration
     *
     * @return bool
     */
    public function set( $key, $value = null, $expiration = 0 )
    {
        if (empty($key)) return false;

        if( isset($this->list[$key]) )
        {
            if (!$this->list[$key] == $value) return true;

            $this->list[$key] = $value;
            $this->cacheListing[$key]->data = $value;
            $this->cacheListing[$key]->save();
            return true;
        }

        $this->list[$key] = $value;
        $entry = new Entry();

        $entry->setKeyName($key);
        $entry->setKey($this->getRandomInt());

        $entry->setPermission(self::DEFAULT_PERMISSIONS);
        $entry->setMode(self::CREATE_MOD);
        $entry->setSize((int) self::DEFAULT_SIZE);
        $entry->setOffset((int) self::DEFAULT_OFFSET);
        $entry->setStart((int) 0);
        $entry->setCount((int) 0);
        $entry->setTtl((int) $expiration);
        $entry->setData($value);
        
        $entry->save();
        $this->cacheListing[$key] = $entry;

        return true;
    }



    /**
     * deletes the whole cache
     *
     * @return bool
     */
    public function flush()
    {
        if ( !empty( $this->cacheListing ) ) {
            foreach ( $this->cacheListing as $entry )
            {
                /** @var $entry Entry */
                if ($entry instanceof Entry){
                    $entry->delete();
                }
            }

            $this->cacheListing = array();
        }
        

        @shmop_delete($this->indexIdentifier);
        return true;
    }



    /**
     * delete a cache entry
     *
     * @param string $key
     * @param int $time [only used because the interface is defined this way has no application]
     *
     * @return bool
     */
    public function delete( $key, $time = 0 )
    {
        if (empty($key) || empty($this->cacheListing[$key])) return true;

        /** @var $entry Entry */
        $entry = $this->cacheListing[$key];
        $entry->delete();
        unset($this->cacheListing[$key]);

        return true;
    }

    /**
     * @todo generic status implementation
     * @return array|mixed
     */
    public function getStatus(){
        return [];
    }

    /**
     * cleans up the cache list
     *
     * @param array $key_array
     *
     * @return bool
     */
    public function deleteFromList( $key_array = array() )
    {
        if ( empty( $key_array ) ) return true;
        
        foreach ( $key_array as $key )
        {
            if (!isset($this->cacheListing[$key])) continue;
            /** @var $entry Entry */
            $entry = $this->cacheListing[$key];
            $entry->delete();
            unset( $this->cacheListing [$key] );
        }
        
        return true;
    }



    /**
     * close the current write pipes
     */
    public function __destruct()
    {
        $this->saveCacheListing();
        // close the open shmop ids
        @shmop_close( $this->indexIdentifier );
    }

}
