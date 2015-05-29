<?php
namespace chilimatic\lib\route;
use chilimatic\lib\exception\RouteException;

/**
 *
 * @author j
 * Date: 4/25/15
 * Time: 8:46 PM
 *
 * File: IRouter.php
 */

interface IRouter {

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
     * @param string
     *
     * @throws RouteException
     * @throws \Exception
     */
    public function __construct($type);


}