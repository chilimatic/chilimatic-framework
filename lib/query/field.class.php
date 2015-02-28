<?php
namespace query;
class Query_Field
{


    /**
     * field name
     *
     * @var string
     */
    private $name = null;


    /**
     * field value this can be even an array
     *
     * @var mixed
     */
    private $value = null;


    /**
     * condition is optional default will be and
     * 
     * @var string
     */
    private $condition = 'AND';

	/**
	 * list of functions within mysql as a field position
	 * 
	 * @var array
	 */
    private $function_list = array('count', 'max');
    
    
    private $function = '';
    
    
    /**
     * constructor
     */
    public function __construct()
    {
        // start init process
    }


    /**
     * magic setter
     *
     * @param string $property
     * @param mixed $value
     *
     * @return boolean
     */
    public function __set( $property , $value )
    {

        if ( !property_exists(get_called_class(), $property) ) return false;
        $this->$property = $value;
        
        return true;
    }


    /**
     * magic getter
     *
     * @param string $property
     *
     * @return mixed
     */
    public function __get( $property )
    {

        if ( !property_exists(get_called_class(), $property) ) return false;
        
        return $this->$property;
    }


    /**
     * returns a string of the field
     * 
     * if the value has been set it's a where field so it returns the condition
     * 
     * @return string
     */
    public function tostring()
    {

        if ( empty($this->value) ) {
           
           return (empty($this->function)) ? "$this->function(`$this->name`)" : " `$this->name`";
        }
        
        $str = strtoupper($this->condition) . " `$this->name`";
        
        if ( is_array($this->value) )
        {
            $str .= " IN (" . implode("','", $this->value) . "')";
        }
        else
        {
            $str .= " = '$this->value'";
        }
        return $str;
    }
}