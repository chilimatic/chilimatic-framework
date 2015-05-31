<?php
namespace chilimatic\lib\command;

/**
 * Class Command
 *
 * @package chilimatic\lib\command
 */
class Command
{
    /**
     * pipes the output
     *
     * @var string
     */
    CONST DESC_PIPE = "pipe";

    /**
     * file output
     *
     * @var string
     */
    CONST DESC_FILE = "file";

    /**
     * read like file
     *
     * @var string
     */
    CONST READ = "r";

    /**
     * write option
     *
     * @var string
     */
    CONST WRITE = "w";

    /**
     * append option
     *
     * @var string
     */
    CONST APPEND = "a";

    /**
     * stdin index
     *
     * @var int
     */
    CONST STDIN = 0;

    /**
     * stdout index
     *
     * @var int
     */
    CONST STDOUT = 1;

    /**
     * stderr index
     *
     * @var int
     */
    CONST STDERR = 2;

    /**
     * command that's passed to the shell
     *
     * @var string
     */
    public $cmd = '';


    /**
     * general descriptor of the system output handling
     *
     * STDIN is piped to read
     * STDOUT should be written
     * STDERR should be written
     *
     * those are just the default settings
     *
     * @var array
     */
    public $descriptorspec = array(
                                    self::STDIN => array(self::DESC_PIPE, self::READ),
                                    self::STDOUT => array(self::DESC_PIPE, self::WRITE),
                                    self::STDERR => array(self::DESC_PIPE, self::WRITE)
                                    );

    /**
     * pipes are REFERENCE given to the function
     * the handling is defined by descriptorspec
     *
     * @var array
     */
    public $pipes = array();

    /**
     * change working directory [important for relative path execution]
     * has to be a string and the absolute position!
     *
     * default null
     * @var string
     */
    public $cwd = null;

    /**
     * list of enviroment variables that should be used! [like setting a different X Server]
     *
     * default null
     *
     * @var array
     */
    public $env = null;

    /**
     * list of options
     *
     * suppress_errors (true/false) [only windows] supress error messages that are caused by the called function
     * bypass_shell (true/false) [only windows] don't use the "cmd.exe"
     * context stream_context
     *
     * @var array
     */
    public $other_options = null;


    /**
     * constructor
     *
     * @param string $cmd
     * @param array $descriptorspec
     * @param array $pipes
     * @param string $cwd
     * @param array $env
     * @param array $other_option
     */
    public function __construct($cmd, $descriptorspec = array(), $pipes, $cwd = '', $env = array(), $other_option = array() )
    {
        $this->cmd = $cmd;
        if ( count($this->descriptorspec) > 0)
        {
            $this->descriptorspec = array_merge((array) $this->descriptorspec,(array) $descriptorspec);
        }

        $this->pipes = $pipes;
        $this->cwd = $cwd;
        $this->env = $env;
        $this->other_options = $other_option;
        $this->init();
    }

    public function init()
    {

    }

    public function exec(){

    }
}
