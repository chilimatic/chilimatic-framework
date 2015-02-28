<?php

namespace chilimatic\lib\base;

use \chilimatic\lib\cache\engine\Cache;
use \chilimatic\lib\config\Config;

/**
 * Class Model
 * @package chilimatic\lib\base
 */
class Model implements \JsonSerializable
{
	
	/**
	 * use cache setting
	 * 
	 * @var bool
	 */
	protected $use_cache = true;
	
    /**
     * cache Class
     * 
     * @var Cache
     */
    protected $cache = null;
    
    
    /**
     * list of properties to be remove during the json encode process
     * 
     * @var array
     */
    private $json_encode_remove = array();


    /**
     * @var null
     */
    public $error = null;

	
    /**
     * constructor
     */
    public function __construct()
    {
    	$this->__init_cache();
    	$this->__init_json_encode();
    }
    
    /**
     * initializes the cache
     */
    public function __init_cache()
    {
    	if ($this->use_cache === true)
    	{
            $param = new \stdClass();
            $param->type = Config::get( 'cache_type' );
            $param->credential = Config::get('cache_settings');
    		$this->cache = Cache::getInstance($param);
    	}
    	
    	return true;
    }
    
    /**
     * gets the variables who are needed to be removed 
     * during the json encode process
     * 
     * @return bool
     */
    public function __init_json_encode()
    {
    	
    	if ( ($this->json_encode_remove = Config::get( 'json_encode_remove' )) == '')
    	{
    		$this->json_encode_remove = array();
    	}
    	
    	return true;
    }
	
	/**
     * jsonSerialize to null all resources
     * 
     * @return object with the nulled properties
     */
	 public function jsonSerialize(){
    	$property_list = get_class_vars(get_called_class());
    	
    	foreach ($property_list as $property => $value)
    	{
    		if (is_resource($value) || in_array($property, $this->json_encode_remove))
    		{
    			$this->$property = null;
    		} 
    			
    	}
    	return $this;
    }  
    
    
    /**
     * magic set
     *
     * @param string $property            
     * @param mixed $value            
     * @return boolean
     */
    public function __set( $property , $value )
    {

        if ( !property_exists($this, $property) ) return false;
        
        $this->$property = $value;
        return true;
    }


    /**
     * magic get
     *
     * @param string $property            
     * @return boolean mixed
     */
    public function __get( $property )
    {

        if ( !property_exists($this, $property) ) return false;
        
        return $this->$property;
    }


    /**
     * destructor
     */
    public function __destruct()
    {
        //
    }
}
?>