<?php
/**
 * Created by JetBrains PhpStorm.
 * User: j
 * Date: 19.10.13
 * Time: 11:04
 * Generic Request Object
 */

namespace chilimatic\lib\request;

use chilimatic\lib\interfaces\ISingeltonArray;
use chilimatic\lib\tool\TypeCast;
use \JsonSerializable;

/**
 * Class Generic
 *
 * @package chilimatic\lib\request
 */
abstract class Generic extends \stdClass implements JsonSerializable, ISingeltonArray
{

    /**
     * the default delimiter
     *
     * @var string
     */
    const DEFAULT_DELIMITER = '&';

    /**
     * the common assignment operator
     *
     * @var string
     */
    const ASSIGNMENT_OPERATOR = '=';

    /**
     * the default character for
     * an HTTP Request that indicates a following parameter list
     *
     * @var string
     */
    const REQUEST_DELIMITER = '?';

    /**
     * singelton instance
     *
     * @var Post|Get|File|CLI
     */
    public static $instance = null;

    /**
     * @var array
     */
    private $param = [];

    /**
     * initializes the generic request object
     *
     * @param array $param
     *
     * @return \chilimatic\lib\request\Generic
     */
    protected function __construct(array $param = null)
    {
        $this->param = $param;
    }


    /**
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function add($key, $value)
    {
        if (!$key) return $this;

        $this->param[$key] = $value;

        return $this;
    }

    /**
     * adds a array as normal properties
     *
     * @param array $param
     *
     * @return bool
     */
    public function addArray(array $param = null)
    {
        $this->param = array_merge($this->param, $param);

        return $this;
    }

    /**
     * @return array
     */
    public function getAllParam()
    {
        return $this->param;
    }

    /**
     * @param $property
     * @param null $type
     *
     * @return null
     */
    public function get($property, $type = null)
    {
        if (!isset($this->param[$property])) {
            return null;
        }

        if (isset($type)) {
            $method = TypeCast::METHODPREFIX . $type;

            return TypeCast::$method($property);
        }

        return $this->param[$property];
    }


    /**
     * a request object is a pure data object
     *
     * there should be no open streams or other other
     * special wrapper object inside of this !
     * as soon this changes, for whatever hideous reasons
     *
     * you need to change this functions in the CHILD OBJECTS
     * please note that the name "generic" was used for a reason ! ;)
     *
     * @return mixed
     */
    public function jsonSerialize()
    {
        return $this->param;
    }
}