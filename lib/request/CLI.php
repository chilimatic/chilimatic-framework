<?php
/**
 * Created by JetBrains PhpStorm.
 * User: J
 * Date: 19.10.13
 * Time: 11:32
 * CLI class is for commandline given parameters to be added to a normal
 * array
 */
namespace chilimatic\lib\request;

/**
 * Class CLI
 * @package chilimatic\lib\request
 */
class CLI extends Generic implements RequestInterface
{

    /**
     * the standard CLI delimiter is a space
     */
    const CLI_DELIMITER = ' ';

    /**
     * the first param of argv is always
     * the name of the script
     *
     * @var mixed|string
     */
    public $script_name = '';


    /**
     * constructor
     *
     * @param array $array
     */
    protected function __construct(array $array = null)
    {
        // removes the script name from the argv
        $this->script_name = array_shift($array);
        $this->_transform($array);
    }

    /**
     * singelton constructor
     *
     * @param array $array
     *
     * @return Request_Get
     */
    public static function getInstance(array $array = array())
    {
        if (!(self::$instance instanceof Request_Control))
        {
            // construct the object
            self::$instance = new Request_CLI($array);
        }

        // return the object
        return self::$instance;
    }

    /**
     * adapted "transform" method for the argv we
     * need a different approach ofc
     *
     * @param array $array
     *
     * @return void
     */
    public function _transform(array $array = null)
    {
        if (empty($array)) return;


        foreach ($array as $param)
        {
            if (strpos($param, '=') > 0)
            {
                $tmp = explode(Request_Generic::ASSIGNMENT_OPERATOR, $param);
                $this->$tmp[0] = $tmp[1];
            }
        }

        return;
    }

}