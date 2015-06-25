<?php

namespace chilimatic\lib\database;

use \chilimatic\lib\exception\DatabaseException as DatabaseException;

/**
 * Class MySQLClone
 *
 * @package chilimatic\lib\database
 */
class MySQLClone extends Mysql
{


    /**
     * forces rebuild the script to
     * drop the tables first
     *
     * @var boolean
     */
    public $force_rebuild = false;


    /**
     * defines if the database needs to be created
     * or not
     *
     * @var boolean
     */
    public $clone_exists = false;


    /**
     * name clone
     *
     * @var string
     */
    public $clone_dbname = '';


    /**
     * name of the database to be cloned
     *
     * @var string
     */
    public $main_dbname = '';


    /**
     * list of all datases on the server
     *
     * @var array
     */
    protected $database_list = null;


    /**
     * list of all tables within the main database
     *
     * @var string
     */
    protected $table_list = null;


    /**
     * starts the cloning process
     *
     * @param $main_db    string
     * @param $clone_db   string
     * @param $table_list array|string
     *
     * @throws \chilimatic\exception\DatabaseException
     * @throws \Exception
     * @return boolean
     */
    public function init( $main_db , $clone_db , $table_list = null )
    {

        try
        {
            if ( empty($main_db) && empty($clone_db) ) return false;
            
            if ( !$this->select_db((string) $main_db) )
            {
                throw new DatabaseException('Could not select Main Database ' . $main_db);
            }
            
            $this->clone_dbname = $clone_db;
            $this->main_dbname = $main_db;
            
            $sql = "SHOW DATABASES";
            
            if ( ($this->database_list = $this->fetch_simple_list($this->query((string) $sql))) == false )
            {
                throw new DatabaseException('No Databases on the Main Server');
            }
            
            $this->clone_exists = (!in_array($clone_db, $this->database_list)) ? false : true;
            
            if ( empty($table_list) )
            {
                $sql = "SHOW TABLES";
                $this->table_list = $this->fetch_simple_list($this->query($sql));
            }
            elseif ( !is_array($table_list) )
            {
                $this->table_list = array(
                                        $table_list
                );
            }
            else
            {
                $this->table_list = $table_list;
            }
            
            if ( $this->clone_exists === false && !$this->clone_database($main_db, $clone_db) )
            {
                throw new DatabaseException('Couldnt replicate database');
            }
            
            foreach ( $this->table_list as $table_name )
            {
                if ( $this->clone_exists )
                {
                    $sql = ($this->force_rebuild) ? "DROP TABLE `{$clone_db}`.`{$table_name}`" : "TRUNCATE `{$clone_db}`.`{$table_name}`";
                    $this->query($sql);
                }
                $sql = "CREATE TABLE `{$clone_db}`.`{$table_name}` LIKE `{$main_db}`.`{$table_name}`";
                if ( !$this->query($sql) ) continue;
                $sql = "INSERT INTO `{$clone_db}`.`{$table_name}` SELECT * FROM `{$main_db}`.`{$table_name}`";
                if ( !$this->query($sql) ) continue;
            }
        
        }
        catch ( DatabaseException $e )
        {
            throw $e;
        }

        return true;
    }


    /**
     * clones the specific database
     *
     * @param $main_db  string
     * @param $clone_db string
     *
     * @return bool
     * @throws DatabaseException
     * @throws \Exception
     */
    public function clone_database( $main_db = null , $clone_db = null )
    {

        if ( empty($this->db) )
        {
            throw new DatabaseException('Clone or Main database not connected');
        }
        
        if ( empty($main_db) || empty($clone_db) )
        {
            throw new DatabaseException('clone names havent been set');
        }
        
        $sql = "SHOW CREATE DATABASE `$main_db`";
        $result = $this->fetch_assoc($this->query($sql));
        
        $create_sql = str_replace($main_db, $clone_db, $result['Create Database']);
        
        if ( empty($create_sql) )
        {
            throw new DatabaseException('Create database statement is empty!');
        }
        
        return $this->query($create_sql);
    }
}
