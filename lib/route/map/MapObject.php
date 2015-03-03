<?php
namespace chilimatic\lib\route\map;

use chilimatic\lib\exception\Exception_Route;

/**
 * Class MapObject
 * @package chilimatic\lib\route\map
 */
class MapObject extends Generic
{

    const NAMESPACEDELIMITER = '\\';

    /**
     * object / class to be called
     *
     * @var string
     */
    public $class = null;

    /**
     * method of the object to be called
     *
     * @var string
     */
    public $method = null;

    /**
     * @var null
     */
    public $param = null;


    /**
     * @return mixed|void
     * @throws Exception_Route
     */
    public function init()
    {
        // numeric array
        if (!empty($this->config) && is_object($this->config) ) {
            $this->class = (string) $this->config->object;
            $this->method = (string) $this->config->method;
            $this->param = !empty($this->config->param) ? $this->config->param : false;
        }
        elseif ( !empty($this->config[0]) ) {
            $this->class = (string) $this->config [0];
            $this->method = (string) !empty($this->config[1]) ? $this->config[1] : '';
            $this->param = !empty($this->config[2]) ? $this->config[2] : false;
        }
        elseif (!empty($this->config['object'])) {
            $this->class = (string) $this->config['object'];
            $this->method = (string) !empty( $this->config['method']) ? $this->config['method'] : '';
            $this->param = !empty( $this->config['param']) ? $this->config['param'] : false;
        }
        // invalid
        else {
            throw new Exception_Route( sprintf( _( 'The Callback was not propper defined %s' ), print_r( $this->config, true ) ) );
        }
    }

    /**
     * executes route
     *
     * @param null $param
     * @return mixed
     */
    public function call($param = null) {
        // get the class within the correct namespace
        // initiate object
        $object = new $this->class();
        $return = new \SplFixedArray(2);
        $return[1] = $object;
        if (!empty($this->method)) {
            if (!empty($param) && !empty($this->param)) {
                $return[0] = $object->{$this->method}(array_merge((array) $this->param, (array) $param));
                return $return;
            }

            $return[0] = $object->{$this->method}();
            return $return;
        }

        $return[1] = $object;
        return $return;
    }
}