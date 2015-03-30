<?php
namespace chilimatic\lib\route;

use chilimatic\lib\cache\engine\Cache;
use chilimatic\lib\config\Config;
use chilimatic\lib\exception\RouteException;
use chilimatic\lib\route\routesystem\RouteSystemFactory;

/**
 * main routing system that loads & maps and saves 
 * contains a list of possible routes that can be called edited
 * 
 * 
 * possible are 
 * -> stdclass
 * -> array numeric
 * -> array assoc
 * -> lambda function
 * -> function call
 * -> static content
 * 
 * chilimatic framework routing
 * 
 * examples
 * \chilimatic\route\Route::register('/test/(:num)', array('job', 'load'), '/');
 * \chilimatic\route\Route::register('/user/add/(:num)', array('object' => 'user', 'method' => 'add', 'namespace' => '\\user\\', 'param' => array(true, false)));
 * \chilimatic\route\Route::register('/test/(:char)', array('object' => 'user', 'method' => 'add', 'namespace' => '\\user\\', 'param' => array(true, false)));
 * \chilimatic\route\Route::register('/mytest/(:array)[|]',  function($num) { foreach($num as $val) {  echo $val . ': this is a test'; }});
 * 
 * 
 * @author j
 */
class Router
{
    /**
     * @var string
     */
    const DEFAULT_ROUTING_TYPE = 'Node';

    /**
     * routing error code
     * 
     * @var int
     */
    const ROUTING_ERROR = 20;

    /**
     * list of set delimiters
     * 
     * @var array
     */
    private $_delimiter_list = array(
        Map::DEFAULT_URL_DELIMITER
    );
    
    /**
     * Caching Object
     * 
     * @var object
     */
    private $_cache = null;


    /**
     * url parts
     * 
     * @var array
     */
    public $urlPart = array();


    /**
     * @var
     */
    private $routeSystem;


    /**
     * singelton contructor
     *
     * @throws RouteException
     * @throws \Exception
     */
    public function __construct($type) {
        $this->routeSystem = RouteSystemFactory::make($type, $this->__getPath());
    }



    /**
     * get a property
     * 
     * @param string $property
     * 
     * @return mixed
     */
    public function get( $property )
    {
        if ( !property_exists($this, $property) ) return false;
        
        return $this->$property;
    }


    /**
     * returns a clean path
     * 
     * @param string $path
     * 
     * @return boolean|string|array
     */
    public function getCleanPath( $path )
    {
        // if there is no path it's not needed to try to get a clean one
        if ( empty($path) ) return false;

        // this delimiter is eventually going to be overwritten
        $delimiter = Map::DEFAULT_URL_DELIMITER;

        // loop through the delimiter list to find and "optional" setting
        foreach ( $this->_delimiter_list as $delimiter )
        {
            if ( mb_strpos($path, $delimiter) === false ) continue;
            // one hit means break and keep the delimiter
            break;
        }

        // remove the first slash for safety reasons [delimitor mapping] based on the web-server Rewrite
        if ( mb_strpos($path, $delimiter) === 0 )
        {
            $path = mb_substr($path, 1);
        }

        // if the last character is a delimiter remove it as well
        if ( mb_strpos($path, $delimiter) == mb_strlen($path) - 1 )
        {
            $path = mb_substr($path, 0, -1);
        }

        //remove the get parameter so it's clean
        if (($ppos = mb_strpos($path, '?')) && $ppos > 0) {
            $path = mb_substr($path, 0, $ppos);
        }

        unset($ppos);
        
        // check if there is even a need for further checks
        if ( mb_strpos($path, $delimiter) === false )
        {
            // set the root and the path
            $pathParts = array(
                                $delimiter,
                                $path
            );
            return $pathParts;
        }

        // if theres a deeper path it's time to walk through it and clean the empty parts etc
        $pathParts = explode($delimiter, $path);

        // walk through the array and remove the empty entries
        for ($i = 0, $c = count($pathParts); $i < $c; $i++ )
        {
            if ( empty($pathParts[$i]) ) unset($pathParts[$i]);
        }

        // path parts
        sort($pathParts);
        // prepend the default delimiter
        array_unshift($pathParts, $delimiter);

        // path parts
        return $pathParts;
    }


    /**
     * if no path has been specified get a fallback path
     *
     * @return array|string
     */
    private function __getPath()
    {

        if ( !empty($this->path) ) return $this->path;

        /**
         * check if the path is empty otherwise
         * set it with the server variable and if there's no server variable set it as the default delimiter [/] the root
         */
        $path = !empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;

        if ( empty($path) || $path == '/' ) $path = Map::DEFAULT_URL_DELIMITER;
        // get the clean path
        $this->urlPart = static::getCleanPath($path);


        return $path;
    }


    /**
     * the real routing should happen here
     *
     * @param mixed $path
     *
     * @return null
     *
     * @throws RouteException
     */
    public function getRoute( $path = null )
    {

        if ( empty($path)) {
            $path = $this->__getPath();;
        }

       return $this->routeSystem->getRoute($path);
    }




    /**
     * Saves the Routing list properly
     */
    public function save()
    {
        $this->_cache = Cache::getInstance(Config::get('cache_type'));
    }
}
?>