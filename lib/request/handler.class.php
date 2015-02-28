<?php
/**
 * Created by JetBrains PhpStorm.
 * User: j
 * Date: 19.10.13
 * Time: 00:18
 *
 * this basic Request object is just a Wrapper for the
 * superglobals and that a specific behaviour can be defined for a specific
 * Request
 *
 * it's a Singelton Factory Class
 *
 */
namespace chilimatic\lib\request;


/**
 * Class Handler
 * @package chilimatic\lib\request
 */
class Handler extends \stdClass implements RequestInterface {

    /**
     * Post object
     *
     * @var \chilimatic\lib\request\Post
     */
    public $post;

    /**
     * Get object
     *
     * @var \chilimatic\lib\request\Get
     */
    public $get;

    /**
     * File object
     *
     * @var \chilimatic\lib\request\File
     */
    public $file;

    /**
     * CLI object ($argv)
     *
     *
     * @var \chilimatic\lib\request\Cli
     */
    public $cli;

    /**
     * Request object
     *
     * @var \chilimatic\lib\request\Request|null
     */
    public $request;

    /**
     * @var
     */
    public $raw;
    /**
     * singelton instance
     *
     * @var \chilimatic\lib\request\Handler
     */
    public static $instance;


    /**
     * server path
     *
     * @var null
     */
    public $path;

    /**
     * constructor adds
     * all the standard request variables
     */
    private function __construct()
    {
        if (($data = file_get_contents('php://input'))) {
            $this->raw = Raw::getInstance($this->parseRawData($data));
        }

        if (!empty($_GET))
        {
            $this->get = Get::getInstance($_GET);
            // remove the $_GET so no one is tempted!
            unset ($_GET);
        }

        if (!empty($_POST))
        {
            $this->post = Post::getInstance($_POST);
            // remove the $_POST so no one is tempted
            unset ($_POST);
        }

        if (!empty($_FILES))
        {
            $this->file = File::getInstance($_FILES);
            // remove the $_FILES so no one is tempted
            unset ($_FILES);
        }

        if (!empty($argv))
        {
            $this->cli = CLI::getInstance($argv);
        }

        if (!empty($_REQUEST))
        {
            $this->request = Request::getInstance($_REQUEST);
            unset($_REQUEST);
        }
    }

    /**
     * @param $data
     *
     * @return mixed
     */
    private function parseRawData($data) {
        switch (true) {
            case strpos($data, '{') == 0:
                return json_decode($data, true);
                break;
            default:
                return $data;
                break;
        }
    }


    private function __clone() {

    }

    /**
     * singelton constructor
     *
     * @param array|string $param
     *
     * @return Handler|mixed $instance
     */
    public static function getInstance(array $param = array())
    {
        if (!(self::$instance instanceof \chilimatic\lib\request\Handler))
        {
            // construct the object
            self::$instance = new self();
        }

        if ($param) return self::$instance->$param;


        // return the object
        return self::$instance;
    }

    /**
     * @return Cli
     */
    public function getCli()
    {
        return $this->cli;
    }

    /**
     * @param Cli $cli
     *
     * @return $this
     */
    public function setCli(\chilimatic\lib\request\Cli $cli)
    {
        $this->cli = $cli;
        return $this;
    }

    /**
     * @return \chilimatic\lib\request\File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param \chilimatic\lib\request\File $file
     *
     * @return $this
     */
    public function setFile(\chilimatic\lib\request\File $file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * @return \chilimatic\lib\request\Get
     */
    public function getGet()
    {
        return $this->get;
    }

    /**
     * @param \chilimatic\lib\request\Get $get
     *
     * @return $this
     */
    public function setGet(\chilimatic\lib\request\Get $get)
    {
        $this->get = $get;
        return $this;
    }

    /**
     * @return \chilimatic\lib\request\Post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @param \chilimatic\lib\request\Post $post
     *
     * @return $this
     */
    public function setPost(\chilimatic\lib\request\Post $post)
    {
        $this->post = $post;
        return $this;
    }

    /**
     * @return Request|null
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request|null $request
     *
     * @return $this
     */
    public function setRequest(\chilimatic\lib\request\Request $request)
    {
        $this->request = $request;
        return $this;
    }


    /**
     * @return string
     */
    public function parsePath() {
        $this->path = !empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';;
    }

    /**
     * @return string|null
     */
    public function getPath() {
        if ($this->path != '') {
            return $this->path;
        }
        $this->parsePath();

        return $this->path;
    }

    /**
     * @return mixed
     */
    public function getRaw()
    {
        return $this->raw;
    }

    /**
     * @param mixed $raw
     *
     * @return $this
     */
    public function setRaw($raw)
    {
        $this->raw = $raw;

        return $this;
    }


    /**
     * magic method
     *
     * @return string
     */
    public function __to_string()
    {
        return __CLASS__;
    }


}