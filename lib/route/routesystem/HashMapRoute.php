<?php

namespace chilimatic\lib\route\routesystem;

use chilimatic\lib\exception\RouteException;
use chilimatic\lib\route\Map;

class HashMapRoute extends AbstractRoute
{
    /**
     * @var array
     */
    private $routeMap = [];

    /**
     * @param array $urlParts
     * @return Map|mixed|null
     */
    public function getRoute(array $urlParts = [])
    {
        if (count($this->routeMap) === 0) {
            $this->setDefaultRoute();
        }


        $path = implode($urlParts);
        if (isset($this->routeMap[$path])) {
            return $this->routeMap[$path];
        } elseif (($map = $this->getStandardRoute($urlParts))) {
            return $map;
        }

        return $this->routeMap[self::PATH_ROOT];
    }

    /**
     * @param string $uri
     * @param string $callback
     * @param string $delimiter
     * @throws RouteException
     */
    public function addRoute($uri, $callback, $delimiter = Map::DEFAULT_URL_DELIMITER)
    {
        try {
            /**
             * if the uri is empty throw an exception
             */
            if (empty($uri)) {
                throw new RouteException(sprintf(_('There is no Route entered %s'), $uri));
            }

            // class for mapping
            $map = new Map($uri, $callback, $delimiter);
            $this->routeMap[$uri] = $map;
        } catch (RouteException $e) {
            throw $e;
        }
    }

    /**
     * adds the default route to the map
     */
    private function setDefaultRoute() {
        $this->routeMap[self::PATH_ROOT] = $this->getDefaultRoute();
    }

}