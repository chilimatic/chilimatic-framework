<?php

/**
 * generic generated class based on the table
 */
class Database_Server extends Base
{
    /**
     * @var Object
     */
    public $db = null;



    /**
     * Extra: [Not null] 
     * @var varchar(45)
     */
    public $db_server_host = '';



    /** 
     * @var varchar(200)
     */
    public $db_server_host_name = '';



    /**
     * Extra: auto_increment[Not null] 
     * @var int(10) unsigned [PRIMARY KEY]
     */
    public $db_server_id = 0;



    /**
     * Extra: [Not null] 
     * @var varchar(45)
     */
    public $db_server_password = '';



    /** 
     * @var int(10) unsigned
     */
    public $db_server_port = 0;



    /**
     * Extra: [Not null] 
     * @var varchar(100)
     */
    public $db_server_user = '';



    /**
     * @var array
     */
    public $mandatory_field = array('db_server_id','db_server_host','db_server_user','db_server_password');


                
                 
    /**
     * constructor gets the standard DB model
     *
     * @param object $db
     * @param $db_server_id
     *
     * @return void
     */
    public function __construct($db, $db_server_id = null)
    {
        if (empty($db))
        {
            return false;
        }
        
        $this->db = $db;
        
        $this->db_server_id = $db_server_id;

                        
        $this->load();
    }
                    
                    
    /**
     * returns you a fix composed select statement with all the fields
     *
     * @param bool $count
     *
     * @return string
     */
    private function _load_sql()
    {
        return  'SELECT `ds`.`db_server_id`, `ds`.`db_server_host_name`, `ds`.`db_server_host`, `ds`.`db_server_user`, `ds`.`db_server_password`, `ds`.`db_server_port` FROM';
    }
                    
                    
    /**
     * loads based on the given parameter or on the id 1 specific entry
     * 
     * @param array $param
     * 
     * @return bool
     */
    public function load($param = null)
    {
        if (((empty($this->db_server_id)) && empty($param)) || empty($this->db))
        {
            return false;
        }                     
        
        $w_sql = " WHERE ";
        foreach ($param as $key => $val)
        {
            if (property_exists($this, $key))
            {
                // default add
                if (!empty($w_sql))
                {
                    $w_sql .= " AND ";
                }
                // optional can be extended for specific needs
                switch(true)
                {
                    default:
                        $w_sql .= "`$key` = '$val'";
                        break;
                }
            }
        }
    
        $sql = $this->_load_sql() . $w_sql;
        
        if (empty($param))
        {
            $sql = $this->_load_sql() . " `db_server` ds WHERE `db_server_id` = $this->db_server_id ";
        }
        
        if (($res = $this->db->query($sql)) === false)
        {
            return false;
        }
    
        $row = $this->db->fetch_object($res);
        $this->db->free($res);
        
        return $this->load_row($row);
    }
                    
    /**
     * load_row is a generic loading function based on the row
     *
     * @param object $row
     *
     * @return bool
     */
    private function load_row($row)
    {
        if (empty($row))
        {
            return false;
        }
        
        $this->db_server_id = (int) $row->db_server_id;
        $this->db_server_host_name = (string) $row->db_server_host_name;
        $this->db_server_host = (string) $row->db_server_host;
        $this->db_server_user = (string) $row->db_server_user;
        $this->db_server_password = (string) $row->db_server_password;
        $this->db_server_port = (int) $row->db_server_port;

        
        return true;
    }
                    
                    
    /**
     * generic save method
     *
     * @return bool
     */    
    public function save()
    {
        if (empty($this->db_server_id))
        {
            $new = true;
        }
        
        // switch between create or update statement
        if ($new)
        {
            $sql = "INSERT INTO db_server SET ";
        }
        else
        {
            $sql = "UPDATE db_server SET ";
        }
        
        // check if the fields that are mandatory for creation have been set
        if ($new)
        {
            foreach ($this->mandatory_field as $field)
            {
                try
                {
                    if ($this->$field === null || $this->$field === false) 
                    {
                        throw new Exception('the field $field hasnt been filled correctly');
                    }
                }
                catch (Exception $e)
                {
                    $this->error = $e->getMsg();
                }
            }
        }
        
        if (!empty($this->error)) return false;
        
        $sql .= (!empty($this->db_server_id) ? "`db_server_id`= '" . addslashes(stripslashes($this->db_server_id)) . "', "  : '');
        $sql .= (!empty($this->db_server_host_name) ? "`db_server_host_name`= '" . addslashes(stripslashes($this->db_server_host_name)) . "', "  : '');
        $sql .= (!empty($this->db_server_host) ? "`db_server_host`= '" . addslashes(stripslashes($this->db_server_host)) . "', "  : '');
        $sql .= (!empty($this->db_server_user) ? "`db_server_user`= '" . addslashes(stripslashes($this->db_server_user)) . "', "  : '');
        $sql .= (!empty($this->db_server_password) ? "`db_server_password`= '" . addslashes(stripslashes($this->db_server_password)) . "', "  : '');
        $sql .= (!empty($this->db_server_port) ? "`db_server_port`= '" . addslashes(stripslashes($this->db_server_port)) . "'"  : '');

        
        return $this->db->query($sql);
    }
}

?>