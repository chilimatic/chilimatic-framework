<?php
namespace chilimatic\lib\traits;

use chilimatic\lib\config\Config;
use chilimatic\lib\database\CouchDB;
use chilimatic\lib\database\Mongo;
use chilimatic\lib\database\mysql\Mysql;
use chilimatic\lib\database\mysql\MysqlConnection;
use chilimatic\lib\exception\DatabaseException;

/**
 * Generic trait to initialize the database object if it's needed
 * 
 * @author j
 */

trait Database
{
    /**
     * default database type
     *
     * @var string
     */
    private $_default_database_type = 'mysql';

    /**
     * current database type
     *
     * @var string
     */
    private $database_type = '';

    /**
     * Database Object
     *
     * @var Mysql
     */
    public $db = null;


    /**
     * initializes the database Object if necessary
     *
     *
     * @param null $param
     * @throws \chilimatic\lib\exception\DatabaseException|\Exception
     * @return boolean
     */
    protected function __init_database($param = null)
    {

        if ($this->_default_database_type == '')
        {
            $this->_default_database_type = Config::get('default_database_type');
        }

        if ( isset($param['type']) && is_string($param['type']) ) {
            $this->database_type = $param['type'];
        } else {
            $this->database_type = $this->_default_database_type;
        }


        switch(true) {
            case ( $this->db instanceof Mysql ): return true;
            case ( $this->db instanceof CouchDB ): return true;
            case ( $this->db instanceof Mongo ): return true;
        }

        
        try
        {
            switch($this->database_type){
                case 'mysql':
                    $master_param = new MysqlConnection([
                        'host' => (string) Config::get('db_host'),
                        'username' => (string) Config::get('db_user'),
                        'password' => (string) Config::get('db_pass'),
                        'database' => (string) Config::get('db_name'),
                        'port' => (string) Config::get('db_port')
                    ]);

                    $this->db = new Mysql($master_param);
                    break;
                case 'couchdb':
                    $this->db = new CouchDB();
                    break;
                case 'mongo':
                    break;
            }

        }
        catch ( DatabaseException $e )
        {
            throw $e;
        }
        return true;
    }
    
    
    /**
     * destroys the current database object
     *
     *
     * @return boolean
     */
    protected function __clean_database(){
        $this->db = null;
        return true;
    }
}