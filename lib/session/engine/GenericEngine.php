<?php
/**
 * Created by JetBrains PhpStorm.
 * User: j
 * Date: 25.10.13
 * Time: 17:04
 * To change this template use File | Settings | File Templates.
 */
namespace chilimatic\lib\session\engine;

abstract class GenericEngine implements SessionEngineInterface, \SplSubject
{
    /**
     * implements the methods for the observer Subject without crowding
     * this class
     */
    use \chilimatic\lib\traits\ObserverSubject;

    /**
     * specific session key if we use a cache storage
     *
     * @var string
     */
    protected $sessionKey = "sess.";

    /**
     * Session lifetime
     * defines the lifetime before a session gets invalid
     * default 1 hour
     *
     * @var int
     */
    protected $sessionLifeTime = SessionEngineInterface::SESSION_LIFETIME;


    /**
     * session data
     *
     * @var array
     */
    public $sessionData = [];


    /**
     * session id
     *
     * @var string
     */
    protected $sessionId;

    /**
     * constructor to bind the session handler
     * to the current class
     *
     * @param [] $config
     */
    public function __construct($config = [])
    {

        // init script
        $this->initTrait();
        if (!$this->init($config)) return;


        // Read the maxlifetime setting from PHP
        $this->sessionLifeTime = get_cfg_var("session.gc_maxlifetime");
        // Register this object as the session handler
        session_set_save_handler(array(
            &$this,
            "session_open"
        ), array(
            &$this,
            "session_close"
        ), array(
            &$this,
            "session_read"
        ), array(
            &$this,
            "session_write"
        ), array(
            &$this,
            "session_destroy"
        ), array(
            &$this,
            "session_gc"
        ));

        // override config [optional]
        $this->sessionLifeTime = isset($config['session_lifetime']) ? $config['session_lifetime'] : $this->sessionLifeTime;
        $this->sessionKey      = isset($config['session_key']) ? $config['session_key'] : $this->sessionKey;
        // start the session
        session_start();
    }

    /**
     * init method to add tables or other needed behaviour
     *
     * @param array $config
     *
     * @return mixed
     */
    abstract public function init($config = []);

    /**
     * reads a specific session
     *
     * @param string $sessionId
     *
     * @return mixed
     */
    abstract public function session_read($sessionId);

    /**
     * writes a specific session
     *
     * @param string $sessionId
     * @param mixed $sessionData
     *
     * @return mixed
     */
    abstract public function session_write($sessionId, $sessionData);

    /**
     * opens a specific session
     *
     * @param string $savePath
     * @param string $sessionName
     *
     * @return mixed
     */
    public function session_open($savePath, $sessionName)
    {
        // Don't need to do anything. Just return TRUE.
        return true;
    }

    /**
     * session garbage collector
     *
     * @return mixed
     */
    public function session_gc()
    {
        return true;
    }

    /**
     * destroys the session
     *
     * @param $sessionId
     *
     * @return mixed
     */
    abstract function session_destroy($sessionId);

    /**
     * close the session
     *
     * @return mixed
     */
    public function session_close()
    {
        // return true atm there is nothing specific needed
        return true;
    }

    /**
     * call for the garbage collector
     */
    public function __destruct()
    {
        // notify the observer so it can write the session data
        // before it's destroyed
        $this->notify();
    }

    /**
     * @return array
     */
    public function getSessionData()
    {
        return $this->sessionData;
    }

    /**
     * @param array $sessionData
     *
     * @return $this
     */
    public function setSessionData(array $sessionData)
    {
        $this->sessionData = $sessionData;
        $_SESSION          = $this->sessionData;

        return $this;
    }
}