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
 *
 * @package chilimatic\lib\request
 */
class Handler extends \stdClass implements RequestInterface
{

    /**
     * Post object
     *
     * @var Post
     */
    private $post;

    /**
     * Get object
     *
     * @var Get
     */
    private $get;

    /**
     * File object
     *
     * @var File
     */
    private $file;

    /**
     * CLI object ($argv)
     *
     *
     * @var Cli
     */
    private $cli;

    /**
     * Request object
     *
     * @var Request|null
     */
    private $request;

    /**
     * @var
     */
    private $raw;
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
        // singelton constructor
    }

    /**
     * @param $data
     *
     * @return mixed
     */
    private function parseRawData($data)
    {
        switch (true) {
            case strpos($data, '{') === 0:
                return json_decode($data, true);
                break;
            default:
                return [$data];
                break;
        }
    }


    private function __clone()
    {

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
        if (!(self::$instance instanceof Handler)) {
            // construct the object
            self::$instance = new self();
        }

        if ($param)  {
            return self::$instance->{$param};
        }


        // return the object
        return self::$instance;
    }

    /**
     * @return Cli
     */
    public function getCli()
    {
        if (!$this->cli === null && isset($GLOBALS['argv'])) {
            $this->cli = CLI::getInstance($GLOBALS['argv']);
        } else {
            $this->cli = CLI::getInstance();
        }

        return $this->cli;
    }

    /**
     * @param Cli $cli
     *
     * @return $this
     */
    public function setCli(Cli $cli)
    {
        $this->cli = $cli;

        return $this;
    }

    /**
     * @return File
     */
    public function getFile()
    {
        if ($this->file === null) {
            $this->file = File::getInstance((array)$_FILES);
            // remove the $_FILES so no one is tempted
            unset ($_FILES);
        }

        return $this->file;
    }

    /**
     * @param File $file
     *
     * @return $this
     */
    public function setFile(File $file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return Get
     */
    public function getGet()
    {
        if ($this->get === null) {
            $this->get = Get::getInstance((array)$_GET);
            // remove the $_GET so no one is tempted!
            unset ($_GET);
        }

        return $this->get;
    }

    /**
     * @param Get $get
     *
     * @return $this
     */
    public function setGet(Get $get)
    {
        $this->get = $get;

        return $this;
    }

    /**
     * @return Post
     */
    public function getPost()
    {
        if ($this->post === null) {
            $this->post = Post::getInstance((array)$_POST);
            // remove the $_POST so no one is tempted
            unset ($_POST);
        }

        return $this->post;
    }

    /**
     * @param Post $post
     *
     * @return $this
     */
    public function setPost(Post $post)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * @return Request|null
     */
    public function getRequest()
    {
        if ($this->request === null) {
            $this->request = Request::getInstance((array)$_REQUEST);
            unset($_REQUEST);
        }

        return $this->request;
    }

    /**
     * @param Request|null $request
     *
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }


    /**
     * @return string
     */
    public function parsePath()
    {
        $this->path = !empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';;
    }

    /**
     * @return string|null
     */
    public function getPath()
    {
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
        if ($this->raw === null && ($data = file_get_contents('php://input'))) {
            $this->raw = Raw::getInstance((array)$this->parseRawData($data));
        } else {
            $this->raw = Raw::getInstance();
        }

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