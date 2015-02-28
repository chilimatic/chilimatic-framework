<?php


/**
 * generic listing Class so that class listes can be 
 * fetched more easiely 
 */
class Database_ServerList extends BaseList
{
    /**
     * list of objects
     */
    public $list = array();
    
    /**
     * database object
     * @var object
     */
    public $db = null;
    
    /**
     * generic constructor
     */
    public function __construct($db = null)
    {
        if (empty($db)) return false;
        $this->db = $db;
    }
    
    /**
     * returns you a fix composed select part of a statement with all the fields
     *
     * @param bool $count
     *
     * @return string
     */
    private function _load_sql($count = false)
    {
        return (empty($count)) ? 'SELECT `ds`.`db_server_id`, `ds`.`db_server_host_name`, `ds`.`db_server_host`, `ds`.`db_server_user`, `ds`.`db_server_password`, `ds`.`db_server_port` FROM' : 'SELECT COUNT(*) FROM';
    }
    
    public function load($param = null)
    {
        // parameters for the basic options
        $limit = 100; // standard max amount
        $count = false;
                

        // fallback gets everything 
        if (!empty($param))
        {
            // foreach loop for the parameters
            foreach ($param as $key => $val)
            {
                switch ($key)
                {
                    // optional switchcase where you can
                    // add as many options you need
                    case 'limit':
                        $$key = $val;
                        break;
                    case 'count':
                        $$key = $val;
                        break;
                    default:
                        $$key = $val;
                        $w_sql = " AND ${$key} = '$val'";
                        break; 
                }
            }
        }
                    
        $sql = $this->_load_sql($count) . " `db_server` ds";

        // check if the limit is empty if not set it 
        // this can be a string as well as an integer
        if (!empty($limit))
        {
            $l_sql = (string) " LIMIT $limit";
        }
        
        // replace the first 4 signs " AND" in the where sql statement
        if (!empty($w_sql))
        {
            $w_sql = (string) substr($w_sql, 4); 
        }
        
        $res = $this->db->query($sql);
        if (empty($res))
        {
            return false;
        }
        
        $assign_by =  'db_server_id';
        
        $this->list = $this->db->fetch_object_list($res, (!empty($assign_by) ? $assign_by : null));
        $this->db->free($res);
        return true;
    }
}
?>