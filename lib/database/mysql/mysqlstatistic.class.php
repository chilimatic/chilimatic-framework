<?php
namespace chilimatic\lib\database;


use \chilimatic\lib\exception\Exception_Database;

/**
 * Class MysqlStatistic
 *
 * @package chilimatic\lib\database
 */
class MysqlStatistic extends Mysql
{


    /**
     * disk usage
     *
     * @var array
     */
    public $disk_usage = '';


    /**
     * amount of table
     *
     * @var int
     */
    public $table_amount = '';


    /**
     * size of table
     *
     * @var int
     */
    public $table_size = '';


    /**
     * db object
     *
     * @var object
     */
    public $db = null;

    /**
     * constructor
     * 
     * @param mysql $db
     */
    public function __construct( Mysql $db = null )
    {

        if ( empty($db) ) return;
        
        // assign database object
        $this->db = $db;
    
    }

    /**
     * get processlist
     * 
     * @throws Exception_Database
     * @return mixed:
     */
    public function get_processlist()
    {

        try
        {
            if ( empty($this->db) )
            {
                throw new Exception_Database(__METHOD__ . 'No Database Object has been given', Mysql::ERR_NO_CREDENTIALS, Mysql::SEVERITY_LOG, __FILE__, __LINE__);
            }
            
            $sql = (string) "SHOW FULL PROCESSLIST";
            $res = $this->db->query($sql);
            
            if ( empty($res) ) return array();
            
            return $this->db->fetch_object_list($res);
        }
        catch ( Exception_Database $e )
        {
            throw $e;
        }
    }

    /**
     * show table listing
     * 
     * @throws Exception_Database
     * 
     * @return array:
     */
    public function show_table_listing()
    {

        try
        {
            if ( empty($this->db) )
            {
                throw new Exception_Database(__METHOD__ . 'No Database Object has been given', Mysql::ERR_NO_CREDENTIALS, Mysql::SEVERITY_LOG, __FILE__, __LINE__);
            }
            
            $sql = (string) "SELECT * FROM `information_schema`.`tables`";
            $res = $this->db->query($sql);
            
            if ( empty($res) ) return array();
            
            return $this->db->fetch_object_list($res);
        
        }
        catch ( Exception_Database $e )
        {
            throw $e;
        }
    }
}
?>