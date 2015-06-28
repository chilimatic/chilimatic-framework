<?php
namespace chilimatic\lib\route;

use chilimatic\lib\cache\engine\Cache;
use chilimatic\lib\config\Config;
use chilimatic\lib\exception\RouteException;
use chilimatic\lib\interfaces\IFlyWeightParser;
use chilimatic\lib\route\parser\UrlParser;
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
class Router implements IRouter
{
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
    protected $urlPart = array();

    /**
     * @var
     */
    private $routeSystem;

    /**
     * @var
     */
    private $urlParser;


    /**
     * @param string
     *
     * @throws RouteException
     * @throws \Exception
     */
    public function __construct($type)
    {
        $this->urlParser   = new UrlParser();
        $this->routeSystem = RouteSystemFactory::make($type, $this->__getPath());
    }

    /**
     * if no path has been specified get a fallback path
     *
     * @return array|string
     */
    private function __getPath()
    {

        if (!empty($this->path)) return $this->path;

        /**
         * check if the path is empty otherwise
         * set it with the server variable and if there's no server variable set it as the default delimiter [/] the root
         */
        $path = !empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;

        if (empty($path) || $path == '/') $path = Map::DEFAULT_URL_DELIMITER;
        // get the clean path
        $this->urlPart = $this->urlParser->parse($path);


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
    public function getRoute($path = null)
    {

        if (empty($path)) {
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

    /**
     * @return mixed
     */
    public function getUrlParser()
    {
        return $this->urlParser;
    }

    /**
     * @param mixed $urlParser
     *
     * @return $this
     */
    public function setUrlParser(IFlyWeightParser $urlParser)
    {
        $this->urlParser = $urlParser;

        return $this;
    }


}