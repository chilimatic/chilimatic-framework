<?php
/**
 * Created by PhpStorm.
 * User: J
 * Date: 10.01.14
 * Time: 14:28
 */

namespace chilimatic\lib\session\engine;

use chilimatic\lib\cache\engine\Cache as CacheManager;
use chilimatic\lib\config\Config;


/**
 * Class Session_Cache
 *
 * @package chilimatic\session
 *
 * Cache plugin for the Session that uses the
 * cache system
 */
class Cache extends GenericEngine
{

    /**
     * cache engine container
     *
     * @var null
     */
    private $engine = null;

    /**
     * init method to add tables or other needed behaviour
     *
     * @return mixed
     */
    public function init($config = [])
    {

        if (isset($config['session_cache'])) {
            $session_type = $config['session_cache'];
        } else {
            $session_type = $config['cache_type'];
        }

        /**
         * initialize the current Cache
         */
        $param              = new \stdClass();
        $param->type        = (string)$session_type;
        $param->credentials = (($s = Config::get('session_cache_settings') == '') ? Config::get('cache_settings') : $s);
        // write it to the cache
        $this->engine = CacheManager::getInstance($param);

        if (!$this->engine || !$this->engine->isConnected()) return false;

        return true;
    }

    /**
     * reads a specific session
     *
     * @param $sessionId
     *
     * @return mixed
     */
    public function session_read($sessionId)
    {
        // assign the current session id
        $this->sessionId   = (string)$sessionId;
        $this->sessionData = $this->engine->get($this->sessionKey . $sessionId);
        // if the sessionData is "false" set it to be an array (casting would create an array with a false entry)
        if (!$this->sessionData) {
            $this->sessionData = [];
        }

        // session data is set uncompressed
        return $this->sessionData;
    }

    /**
     * writes a specific session
     *
     * @param $sessionId
     * @param $sessionData
     *
     * @return mixed
     */
    public function session_write($sessionId, $sessionData)
    {
        $sessionData       = (!$sessionData || !is_array($sessionData)) ? [] : $sessionData;
        $this->sessionData = (!$this->sessionData) ? [] : $this->sessionData;

        $this->sessionData = array_merge($sessionData, $this->sessionData);
        $this->engine->set($this->sessionKey . $sessionId, $this->sessionData, $this->sessionLifeTime);

        return true;
    }


    /**
     * destroys the session
     *
     * @param $sessionId
     *
     * @return mixed
     */
    public function session_destroy($sessionId)
    {
        $this->engine->delete($this->sessionKey . $sessionId);
    }
}