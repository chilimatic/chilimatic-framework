<?php
/**
 * Created by JetBrains PhpStorm.
 * User: j
 * Date: 25.10.13
 * Time: 17:05
 * To change this template use File | Settings | File Templates.
 */

namespace chilimatic\lib\session\engine;

Interface SessionEngineInterface {

    /**
     * default sesion life time in seconds
     *
     * @var int
     */
    const SESSION_LIFETIME = 3600;


    /**
     * constructor to bind the session handler
     *
     * @param array $config
     */
    public function __construct($config = []);

    /**
     * init method to add tables or other needed behaviour
     *
     * @param array $config
     * @return mixed
     */
    public function init($config = []);

    /**
     * reads a specific session
     *
     * @param $session_id
     * @return mixed
     */
    public function session_read( $session_id );

    /**
     * writes a specific session
     *
     * @param $session_id
     * @param $session_data
     * @return mixed
     */
    public function session_write( $session_id , $session_data );

    /**
     * opens a specific session
     *
     * @param $save_path
     * @param $session_name
     * @return mixed
     */
    public function session_open( $save_path , $session_name );

    /**
     * session garbage collector
     *
     * @return mixed
     */
    public function session_gc();

    /**
     * destroys the session
     *
     * @param $session_id
     * @return mixed
     */
    public function session_destroy( $session_id );

    /**
     * close the session
     *
     * @return mixed
     */
    public function session_close();

    /**
     * call for the garbage collector
     */
    public function __destruct();
}

