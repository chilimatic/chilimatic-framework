<?php
namespace chilimatic\lib\route\map;

use chilimatic\lib\exception\RouteException;
use chilimatic\lib\route\parser\RouteMethodAnnotaionParser;

/**
 * Class MapObject
 *
 * @package chilimatic\lib\route\map
 */
class MapObject extends Generic
{

    const NAMESPACEDELIMITER = '\\';

    /**
     * @var string
     */
    const SETTER_PREFIX = 'set';

    /**
     * object / class to be called
     *
     * @var string
     */
    public $class;

    /**
     * method of the object to be called
     *
     * @var string
     */
    public $method;

    /**
     * @var null
     */
    public $param;

    /**
     * @var \ReflectionClass
     */
    public $reflection;


    /**
     * @return mixed|void
     * @throws RouteException
     */
    public function init()
    {

        // numeric array
        if (!empty($this->config) && is_object($this->config)) {
            $this->class      = (string)$this->config->object;
            $this->method     = (string)$this->config->method;
            $this->param      = !empty($this->config->param) ? $this->config->param : false;
            $this->reflection = new \ReflectionClass($this->class);
        } elseif (!empty($this->config[0])) {
            $this->class      = (string)$this->config [0];
            $this->method     = (string)!empty($this->config[1]) ? $this->config[1] : '';
            $this->param      = !empty($this->config[2]) ? $this->config[2] : false;
            $this->reflection = new \ReflectionClass($this->class);
        } elseif (!empty($this->config['object'])) {
            $this->class      = (string)$this->config['object'];
            $this->method     = (string)!empty($this->config['method']) ? $this->config['method'] : '';
            $this->param      = !empty($this->config['param']) ? $this->config['param'] : false;
            $this->reflection = new \ReflectionClass($this->class);
        } // invalid
        else {
            throw new RouteException(sprintf(_('The Callback was not proper defined %s'), print_r($this->config, true)));
        }
    }

    /**
     * executes route
     *
     * @param null $param
     *
     * @return mixed
     */
    public function call($param = null)
    {
        // get the class within the correct namespace
        // initiate object
        $object = $this->prepareObject(new $this->class());

        $return    = new \SplFixedArray(2);
        $return[1] = $object;

        if (!empty($this->method)) {
            if (!empty($param) && !empty($this->param)) {
                $return[0] = $object->{$this->method}(array_merge((array)$this->param, (array)$param));

                return $return;
            }
            $return[0] = $object->{$this->method}();

            return $return;
        }

        return $return;
    }

    /**
     * @param $object
     *
     * @return mixed
     */
    public function prepareObject($object)
    {
        $tokenList = $this->getMethodTokenList();

        for ($i = 0, $c = count($tokenList); $i < $c; $i++) {
            if ($this->reflection->hasMethod(self::SETTER_PREFIX . ucfirst($tokenList[$i]['property']))) {
                $m = self::SETTER_PREFIX . ucfirst($tokenList[$i]['property']);
                if ($tokenList[$i]['type'] == RouteMethodAnnotaionParser::TYPE_CLASS) {
                    $value = new $tokenList[$i]['value']();
                } else {
                    $value = $tokenList[$i]['value'];
                }
                $object->$m($value);
            }
        }

        return $object;
    }


    /**
     * @return mixed
     */
    public function getMethodTokenList()
    {
        $reflectionMethod = $this->reflection->getMethod($this->method);
        $doc              = $reflectionMethod->getDocComment();

        if (!$doc || !$this->parser) {
            return [];
        }

        return $this->parser->parse($doc);
    }

}