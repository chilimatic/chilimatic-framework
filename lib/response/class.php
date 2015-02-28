<?php

namespace chilimatic\lib\response;


use stdClass;

/**
 * Class Response
 *
 * @package chilimatic\request
 */
class Response
{

    /**
     * suffix to identify callback functions
     *
     * @var string
     */
    const CALLBACK_PREFIX = 'cb_';

    /**
     * prefix to identify callback functions
     *
     * @var string
     */
    const CALLBACK_SUFFIX = '_cb';

    /**
     * respond as a json string
     *
     * @var int
     */
    const RESPONSE_JSON = 0;

    /**
     * respond as a serialized string
     *
     * @var int
     */
    const RESPONSE_SERIALIZE = 1;

    /**
     * respond as a plain string
     *
     * @var int
     */
    const RESPONSE_PLAIN = 2;

    /**
     * type of response
     *
     * @var int
     */
    public $type = null;

    /**
     * complete response Data
     *
     * @var stdClass
     */
    public $html = null;

    /**
     * message
     *
     * @var string
     */
    public $msg = null;

    /**
     * error data
     *
     * @var \stdClass
     */
    public $error = null;

    /**
     * json string
     *
     * @var string
     */
    public $json = null;

    /**
     * serialized data
     *
     * @var string
     */
    public $serialized = null;

    /**
     * callback function / method
     *
     * @var mixed
     */
    public $callback = null;

    /**
     * function call
     *
     * @var null
     */
    public $call = null;

    /**
     * for html to decide if it should replace or append the data
     *
     * @var boolean
     */
    public $append = false;

    /**
     * extra data like javascript files
     * that can be added
     *
     * @var array
     */
    public $data = array();



    /**
     * constructor
     *
     * @param string $type
     */
    public function __construct( $type = null )
    {

        if ( empty( $type ) ) $this->type = self::RESPONSE_PLAIN;
        
        $this->type = $type;
    }



    /**
     * gets the wanted response
     *
     * @return string
     */
    public function get_response()
    {

        switch ( $this->type )
        {
            case self::RESPONSE_JSON :
                return json_encode( $this );
                break;
            case self::RESPONSE_PLAIN :
                return $this->html;
                break;
            case self::RESPONSE_SERIALIZE :
                return serialize( $this );
                break;
        }

        return '';
    }

}

?>