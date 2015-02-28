<?php

namespace chilimatic\lib\database\mysql;

/**
 * Class Detail
 *
 * @package chilimatic\lib\database\mysql
 */
class MysqlDetail
{
    /**
     * mysql object
     * @var object
     */
    private $db = null;

    /**
     * character set
     *
     * @var string
     */
    public $character_set_database = '';

    /**
     * constructor
     *
     * @param Mysql $db
     */
    public function __construct(Mysql $db )
    {

        if ( empty($db) ) return;
        
        $this->db = $db;
        $this->_fill();
    }


    /**
     * method that gets all the mysql database 
     * settings an fills them into properties of 
     * this object
     * 
     */
    private function _fill()
    {

        if ( empty($this->db) ) return false;
        
        $sql = (string) "SHOW VARIABLES";
        $res = $this->db->query((string) $sql);
        
        if ( !$res ) return false;
        
        $data = $this->db->fetch_assoc_list($res);
        if ( !empty($data) )
        {
            foreach ( $data as $variable )
            {
                $this->$variable[(string) 'Variable_name'] = (string) $variable['Value'];
            }
            $this->db->free($res);
        }
        
        // clear all the unecessary variables and the endles recursion of the db
        unset($this->db, $data, $variable);
        return true;
    }
}