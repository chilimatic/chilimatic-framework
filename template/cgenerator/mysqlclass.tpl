<?php

#NAMESPACE#
#IMPORTS#

/**
 * generic generated class based on the table
 *
 */
#CLASS_DEFINITION#
{
	#TRAIT_LIST#

#PROPERTY_LIST#
				
				 
	/**
	 * constructor gets the standard DB model
	 *
	 * @param #CONSTRUCTOR_PARAM_LIST#
	 *
	 * @return void
	 */
	public function __construct( #PRIMARY_KEY_METHOD_STATEMENT# )
	{
		
#CONSTRUCTOR_KEY_LIST#
						
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
		return (string) 'SELECT #SQL_FIELD_LIST# FROM';
	}

	
	
	/**
	 * generic delete method
	 * 
	 * @return boolean
	 */
	public function delete()
	{
		if (#PRIMARY_KEY_IF_STATEMENT#) return false;
		
		$sql = "DELETE FROM `#TABLE_NAME#` WHERE #PRIMARY_KEY_WHERE_STATEMENT#";
        $this->__init_database();
        
        return $this->db->query($sql);
	}
					
	/**
	 * loads based on the given parameter or on the id 1 specific entry
	 * 
	 * @param array $param
	 * 
	 * @return bool
	 */
	public function load( $param = null )
	{
		if (((#PRIMARY_KEY_IF_STATEMENT#) && empty($param)))
		{
			return false;
		}					 
		
		$w_sql = '';
		// fallback gets everything 
		if ( !empty($param) )
		{
			foreach ( $param as $key => $val )
			{
				if ( property_exists($this, strtolower($key)) )
				{
					// optional can be extended for specific needs
					switch( true )
					{
						default:
							$w_sql .= (string) " AND `$key` = '$val'";
							break;
					}
				}
			}
		}
		
		// replace the first 4 signs " AND" in the where sql statement
        if ( !empty($w_sql) )
        {
            $w_sql = (string) ' WHERE ' . (string) substr($w_sql, 4);
        }
	
		$sql = $this->_load_sql() . "`#TABLE_NAME#` #TABLE_SHORT_TAG#" . $w_sql;
		
		if ( empty($param) )
		{
			$sql = (string) $this->_load_sql() . " `#TABLE_NAME#` #TABLE_SHORT_TAG# WHERE #WHERE_STATEMENT# ";
		}
		
		// only initilize on loading
		$this->__init_database();
		
		if ( ($res = $this->db->query($sql)) === false )
		{
			return false;
		}
	
		$row = $this->db->fetch_object($res);
		$this->db->free($res);
		
		if( empty($row) ) return false;
		
		return $this->load_row((object) $row);
	}
					
	/**
	 * load_row is a generic loading function based on the row
	 *
	 * @param object $row
	 *
	 * @return bool
	 */
	public function load_row( $row )
	{
		if ( empty($row) )
		{
			return false;
		}
		
#LOAD_ROW_LIST#
		
		return true;
	}
										
	/**
	 * generic save method
	 *
	 * @return bool
	 */	
	public function save($force = false)
	{
		$new = false; 
		$w_sql = '';
		if (#PRIMARY_KEY_IF_STATEMENT# || $force === true)
		{
			$new = true;
		}
		
		// switch between create or update statement
		if ( $new )
		{
			$sql = (string) "INSERT INTO `#TABLE_NAME#` SET ";
		}
		else
		{
			$sql = (string) "UPDATE `#TABLE_NAME#` SET ";
			$w_sql = (string) " WHERE #PRIMARY_KEY_WHERE_STATEMENT#";
		}
		
		// check if the fields that are mandatory for creation have been set
		if ( !$new && !empty($this->mandatory_field ) )
		{
			foreach ( $this->mandatory_field as $field )
			{
				try
				{
					if ( $this->$field === null || $this->$field === false ) 
					{
						throw new Exception((string) 'the field $field hasnt been filled correctly');
					}
				}
				catch ( Exception $e )
				{
					$this->error = $e->getMessage();
				}
			}
		}
		
		if ( !empty($this->error) ) return false;
		
#UPDATE_INSERT_LIST#
		
        $sql = trim($sql,', ');
        
		$sql = "$sql $w_sql";
		
		// init database
		$this->__init_database();
		
		return $this->db->query($sql);
	}
	
		
	
	/**
	 * magic sleep method for serializing
	 * 
	 * @return array -> all the variables that should be unset during the serializing process
	 */
	public function __sleep(){
		return array('db', 'cache');
	}
	
	
	
	/**
	 * magic wake up function deserialize 
	 * could be used to reinitialize resources 
	 */
	public function __wake()
	{
		// some code
	}
	
	
	/**
	 * destructor  
	 */
	public function __destruct()
	{
		// some code
	}
}

#NAMESPACE#
#IMPORTS#

/**
 * generic listing Class so that class listes can be 
 * fetched more easiely 
 */
#CLASS_DEFINITION_LIST#
{

	#TRAIT_LIST#

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
    * count of elements
    *
    * @var int
    */
    public $count = 0;
	
	/**
	 * generic constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	
	/**
	 * loads the list with objects
	 *
	 * @param array $object_list
	 *
	 * @return boolean
	 */
	public function create_list( $object_list = null )
	{
		if ( empty($object_list) ) return false;
		
		$count = count($object_list);
		foreach ($object_list as $key => $object)
		{
			$obj = new #CLASS_NAME#();
			$obj->load_row($object);
			$this->list[$key] = $obj;
			unset($obj);
		}
		
		return true;
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
		return (string) ( empty($count) ) ?  'SELECT #SQL_FIELD_LIST# FROM' : 'SELECT COUNT(*) AS `count` FROM';
	}

	
	/**
	 * load method 
	 * @param array $param
	 *
	 * @return boolean
	 */
	public function load( $param = null )
	{
		// parameters for the basic options
		$limit = 100; // standard max amount
		$count = false;
		$order = null;
        $order_sort = null;
        $w_sql = null;
        $assign_by = null;
        $o_sql = null;
        $l_sql = null;
        
        /*
         * Generate a parameter key for the cache
         */
        $cache_key = __CLASS__ . '_' . md5(serialize($param));
        
        if( $this->use_cache === true && $this->cache && ( $res = $this->cache->get($cache_key) ) ) return $this->create_list($res);
        
		// fallback gets everything 
		if ( !empty($param) )
		{
			// foreach loop for the parameters
			foreach ( $param as $key => $val )
			{
				switch ( $key )
				{
					// optional switchcase where you can
					// add as many options you need
					case 'limit':
						$$key = $val;
						break;
					case 'count':
						$$key = $val;
						break;
					case 'order':
						$$key = $val;
						break;
					case 'order_sort':
						$$key = $val;
						break;
					case 'assign_by':
						$$key = $val;
						break;
					default:
						$$key = $val;
						if ( is_array($val) )
                        {
                            $w_sql .= " AND $key in (";
                            foreach ($val as $subval)
                            {
                            	$w_sql .=  "'$subval',";
                        	}
                        	$w_sql = substr($w_sql, 0, -1) . ')';
                        }
                        else 
                        {
                            $w_sql .= " AND $key = '$val'";
                        }
						break;
				}
			}
		}
					
		$sql = (string) $this->_load_sql($count) . " `#TABLE_NAME#` `#TABLE_SHORT_TAG#`";

		// order by
		if ( !empty($order) )
		{
			$o_sql = (string) "ORDER BY $order " . ($order_sort ? "$order_sort" : "ASC");
		}
		
		// check if the limit is empty if not set it 
		// this can be a string as well as an integer
		if ( !empty($limit) && !$count )
		{
			$l_sql = (string) " LIMIT $limit";
		}
		
		// replace the first 4 signs " AND" in the where sql statement
		if ( !empty($w_sql) )
		{
			$w_sql = (string) ' WHERE ' . (string) substr($w_sql, 4);
		}
		
		// create correct sql
		$sql = "$sql $w_sql $o_sql $l_sql";
		// init database
        $this->__init_database();
        
		$res = $this->db->query($sql);
		if ( empty($res) )
		{
			return false;
		}
		
		$assign_by = ( empty($assign_by) ) ? #PRIMARY_KEY_ASSIGN_STATEMENT# : $assign_by;
        
        
        
		if ( !$count )
		{
        	$object_list = (array) $this->db->fetch_object_list($res, (!empty($assign_by) ? $assign_by : null));
        }
        else
        {
        	$this->count = (int) $this->db->fetch_string($res, (!empty($assign_by) ? $assign_by : null));
        }
        
        $this->db->free($res);
        
        if ( !empty($object_list) && $this->cache && $this->use_cache )
        {
        	$this->cache->set($cache_key, $object_list);
        }
        
        return (!$count) ? $this->create_list($object_list) : ($count !== false);
    }
}
?>