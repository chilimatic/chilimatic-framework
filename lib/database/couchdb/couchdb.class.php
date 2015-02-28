<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 20.01.14
 * Time: 10:59
 */

namespace chilimatic\lib\database;

/**
 * Class CouchDBConn
 */
use chilimatic\lib\http\HTTP_Protocol;
use chilimatic\lib\http\HTTP_Socket;

/**
 * Class CouchDB
 *
 * @package chilimatic\lib\database
 */
Class CouchDB
{

    /**
     * default port based
     * on the documentation
     *
     * @var int
     */
    const DEFAULT_PORT = 5984;

    /**
     * connection data object
     *
     * @var \chilimatic\lib\http\HTTP_Socket
     */
    public $connection = null;


    /**
     * last Couchdb Query result
     *
     * @var \stdClass
     */
    public $last_result = null;

    /**
     * databas name
     *
     * @var string
     */
    public $database = '';

    /**
     * constructor creates the stream
     */
    public function __construct($param = null)
    {
        if (!$param) return;
        $this->init($param);
    }

    /**
     * initializes the socket
     *
     * @param \stdClass $param
     *
     * @return bool
     */
    public function init($param)
    {
        if (empty($param) || empty($param->host)) return false;

        if (!isset($param->port)) {
            $param->port = self::DEFAULT_PORT;
        }

        $this->connection = new HTTP_Socket(
            $param
        );

        return true;
    }

    /**
     * selects the database
     *
     * @param $database
     *
     * @return bool
     */
    public function select_db($database)
    {
        if (empty($database) || !$this->connection->is_connected) return false;

        if (!in_array($database, $this->getDbList())) return false;
        $this->database = $database;

        return true;
    }

    /**
     * gets all dbs as an array
     *
     * @return string
     */
    public function getDbList()
    {
        $this->last_result = $this->connection->send(HTTP_Protocol::GET, '/_all_dbs');

        return $this->last_result;
    }

    /**
     * get the stats of a specific database
     *
     * @param $database
     *
     * @return bool|string
     */
    public function getDbStats($database)
    {
        if (empty($database)) return false;
        $this->last_result = $this->connection->send(HTTP_Protocol::GET, "/$database");

        return $this->last_result;
    }

    /**
     * creates a database
     *
     * @param $database
     *
     * @return bool|string
     */
    public function createDb($database)
    {
        if (empty($database)) return false;
        $this->last_result = $this->connection->send(HTTP_Protocol::PUT, "/$database");

        return $this->last_result;
    }

    /**
     * deletes a database
     *
     * @param $database
     *
     * @return bool|string
     */
    public function deleteDb($database)
    {
        if (empty($database)) return false;
        $this->last_result = $this->connection->send(HTTP_Protocol::DELETE, "/$database");

        return $this->last_result;
    }

    /**
     * Database
     *
     * @param $database
     *
     * @return bool|string
     */
    public function getDbChanges($database)
    {
        if (empty($database)) return false;
        $this->last_result = $this->connection->send(HTTP_Protocol::GET, "/$database/_changes");

        return $this->last_result;
    }


    /**
     * get a specific database based on a
     * specific document id
     *
     * @param $database
     * @param $doc_id
     *
     * @return bool|string
     */
    public function getDocument($database, $doc_id)
    {
        if (empty($doc_id)) return false;
        $database = urlencode($database);
        $doc_id   = urlencode($doc_id);

        // set the last result
        $this->last_result = $this->connection->send(HTTP_Protocol::GET, "/$database/$doc_id");

        return $this->last_result;
    }

    /**
     * set document
     *
     * @param $database
     * @param $document
     *
     * @return bool
     */
    public function setDocument($database, $document)
    {
        if (empty($database)) return false;
        if (!is_string($document) || !@json_decode($document)) {
            $document = json_encode($document);
        }

        $this->last_result = $this->connection->send(HTTP_Protocol::PUT, "/$database/$document");

        return true;
    }
}