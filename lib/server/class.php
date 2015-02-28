<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 05.04.14
 * Time: 16:35
 */

class Server
{

    /**
     * port on which the main socket is listening
     *
     * @var int
     */
    const LISTENING_PORT = 8090;

    /**
     * main IP address to start listening
     *
     * @var string
     */
    const SERVER_IP = "127.0.0.1";

    /**
     * socket for the default server port 80
     *
     * @var null
     */
    private $_socket_http = null;

    /**
     * we ignore this one since I
     * don't wanna play around with SSL
     *
     * @var null
     */
    private $_socket_https = null;

    /**
     * maximum amount of clients
     *
     *
     * @var int
     */
    private $max_clients = 10;

    /**
     * client array
     *
     * @var array
     */
    private $client = [];

    /**
     * request queue
     *
     * @var array
     */
    public $request = array();

    /**
     * @var null
     */
    public $error = null;

    /**
     * read array
     *
     * @var null
     */
    private $read = null;

    /**
     * write array
     *
     * @var null
     */
    private $write = null;

    /**
     * expect array
     *
     * @var null
     */
    private $expect = null;

    /**
     * list of allowed hosts
     *
     * @var array
     */
    private $_host_list = array();

    /**
     * amount of conections
     *
     * @var int
     */
    private $ready = 0;


    /**
     * constructor
     *
     * @param array $host_list
     */
    public function __construct($host_list)
    {
        if (!is_array($host_list)) {
            foreach ($host_list as $host) {
                if (!$host instanceof Host) continue;
                $this->_host_list["{$host->name}:{$host->port}"] = $host;
            }
        }
    }

    /**
     * adds a host to the hostlist
     *
     * @param Host $host
     * @return $this
     */
    public function addHost(Host $host)
    {
        if (empty($host)) return $this;

        $this->_host_list["{$host->name}:{$host->port}"] = $host;

        return $this;
    }

    /**
     * adds the socket to listening sockets
     *
     * @param $socket
     *
     * @return array
     */
    public function checkSocket($socket)
    {
        $read = [$socket];
        // loop through the sockets
        if (count($this->client) > 0) {
            for ($i = 0; $i < $this->max_clients; $i++) {
                if (isset($this->client[$i]) && $this->client[$i]['sock'] != null)
                {
                    $read[$i + 1] = $this->client[$i]['sock'];
                }
            }
        }
        return $read;
    }

    /**
     * checks if there are clients
     * accessing the specific port of the socket
     *
     * @return bool
     */
    public function checkClient()
    {
        /* if a new connection is being made add it to the client array */
        if (in_array($this->_socket_http, $this->read))
        {
            for ($i = 0; $i < $this->max_clients; $i++) {
                if (isset($this->client[$i]) && $this->client[$i]['sock'] == null) {
                    $this->client[$i]['sock'] = @socket_accept($this->_socket_http);
                    break;
                } elseif ($i == $this->max_clients - 1) {
                    //die ("too many clients $i");
                } else {
                    $this->client[] = ['sock' => @socket_accept($this->_socket_http)];
                    break;
                }
            }

            if (--$this->ready <= 0)  {
                return false;
            }
        } // end if in_array

        return true;
    }

    /**
     * handler for Client writing
     *
     * @return bool
     */
    public function writeSocket($socket, $message)
    {


        return true;
    }

    /**
     * gets error and appends them to the error array
     *
     * @return void
     */
    public function getError()
    {
        for ($i = 0, $c = count($this->read); $i < $c; $i++) {
            if (!$this->read[$i]) continue;
            $this->error[] = socket_last_error($this->read[$i]['sock']);
        }
    }


    /**
     * now we open an endless loop
     *
     * open connections to our localhost and let them iterate
     *
     * @return void
     */
    public function start()
    {
        $this->_socket_http = socket_create(AF_INET, SOCK_STREAM, IPPROTO_IP);
        if (!$this->_socket_http)  {
            throw new Exception("Unable to create socket\n");
        }

        if (!socket_bind($this->_socket_http, self::SERVER_IP, self::LISTENING_PORT))  {
            throw new Exception('Could not bind address and port');
        }

        // Start listening for connections
        if (!socket_listen($this->_socket_http)) {
            throw new Exception('Could not listen to main socket!');
        }

        while (true)
        {
            // Setup clients listen socket for reading
            $this->read = $this->checkSocket($this->_socket_http);

            // Set up a blocking call to socket_select()
            $this->ready = socket_select($this->read, $this->write, $this->expect, null);

            if ($this->ready === false) {
                $this->getError();
            }

            // if there a no clients we don't need to write
            if (!$this->checkClient() || empty($this->client)) {
                continue;
            }

            // init the Execute phase
            $this->execute();

        } // end while

        // Close the master sockets
        $this->close($this->_socket_http);
    }

    /**
     * here is where we handle the input and the
     * types of input -> read and writes from the stream
     *
     * @return bool
     */
    public function execute()
    {
        // loop through the open client connections
        foreach ($this->client as $key => $client) {
            // if the client connection doesn exist in the current read iteration
            if (!in_array($client['sock'], $this->read))
            {
                // Close the socket
                $this->close($client['sock']);
                // flag for garbage collection
                unset($this->client[$key]);
            }

            // get the input from the client
            $input = socket_read($client['sock'], 1024);


            // Zero length string meaning disconnected
            if ($input == null) { unset($this->client[$key]); }
            $n = trim($input);

            switch (true) {
                // kill switch for telnet
                case $n === 'exit':
                    $this->close($client['sock']);
                    break;

                default:
                    // here is where it get interesting we will need a Handler who responds
                    socket_write($client['sock'], $this->getResponse($input));
                    break;
            }
        }
    }

    /**
     * @param $input
     */
    public function getResponse($input) {
        $handler = null;
        switch (true) {
            case (strpos('HTTP', $input) !== false) :
                $handler = new HTTP_Handler($input);
                break;
        }

        return ($handler !== null) ? $handler->response() : '';
    }

    /**
     * there should be only one place where sockets
     * are closed!
     *
     * @param $socket
     * @return bool
     */
    public function close($socket)
    {
        if (get_resource_type($socket) !== 'Socket') {
            return false;
        }

        @socket_close($socket);
        return true;
    }
}


interface SocketHandlerInterface {
    /**
     * constructor
     *
     * @param $string
     */
    public function __construct($string);

    /**
     * output function
     *
     * @return mixed
     */
    public function response();
}

class HTTP_Handler implements SocketHandlerInterface{

    /**
     * @var string
     */
    public $request_string = '';

    /**
     * response
     *
     * @var string
     */
    public $response = '';

    /**
     * constructor
     *
     * @param $request_string
     *
     * @internal param string $input
     */
    public function __construct($request_string) {
        $this->request_string = $request_string;
    }

    /**
     * here should server checks ->
     * file exists and other things happen
     * the correct header will be sent
     * and so on and so on!
     *
     * @return mixed|string
     */
    protected function execute(){
        return $this->request_string;
    }

    /**
     * this just for the output and
     * maybe some other magic ;)
     *
     * @return mixed|string
     */
    public function response() {
        $this->response = $this->execute();

        return $this->response;
    }
}


class Host
{

    /**
     * host user
     *
     * @var string
     */
    CONST WWW_USER = 'www-data';

    /**
     * Host group
     *
     * @var string
     */
    CONST WWW_GROUP = 'www-data';

    /**
     * mandatory fields
     *
     * @var array
     */
    private $mandatory_properties = array('name', 'port', 'document_root');

    /**
     * name of the host
     *
     * @var string
     */
    private $name = '';

    /**
     * port it's listening to
     *
     * @var int
     */
    protected $port = 80;

    /**
     * document root
     *
     * @var null
     */
    protected $document_root = null;


    /**
     * constructor for the host
     *
     * @param string $name
     * @param array $setting
     */
    public function __construct($name, $setting)
    {
        $this->name = $name;
        $this->init($setting);
        $this->_check_mandatory();
    }

    /**
     * check for mandatory fields
     *
     * @throws Exception
     */
    private function _check_mandatory()
    {
        foreach($this->mandatory_properties as $property) {
            if (!$this->$property) {
                throw new Exception('Missing Property: ' . $property);
            }
        }
    }

    /**
     * initializes the server variables
     *
     * @param array $setting
     * @return $this
     */
    public function init($setting = array())
    {
        if (!is_array($setting) || count($setting) == 0) return $this;
        foreach ($setting as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = (string)$value;
            }
        }

        return $this;
    }

    /**
     * you can read all properties but you cannot change
     * then from the outside
     *
     * @param $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->$key;
    }
}