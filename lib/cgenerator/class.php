<?php
/**
 *
 * @author j
 * 
 * 
 */

namespace chilimatic\lib\generator;
use chilimatic\lib\file\File;


/**
 * Class generator
 * 
 * @author j
 */
abstract class CGenerator
{


    /**
     * for code formating reasons
     *
     * @var int
     */
    CONST SPACE_AMOUNT = 4;


    /**
     * for the property listing
     *
     * @var string
     */
    CONST STD_INTEND_PROPERTIES = "\n\t";


    /**
     * path to the template file
     * 
     * @var string
     */
    public $tpl_path = '';
    
    /**
     * mysql numeric object list
     *
     * @var array
     */
    public $table_model = null;


    /**
     * mysql table name
     *
     * @var string
     */
    public $table_name = '';


    /**
     * switch all propertie default values
     * to null
     *
     * @var boolean
     */
    public $with_null = false;


    /**
     * db object
     *
     * @var object
     */
    public $db = null;


    /**
     * numeric list of keys
     *
     * @var array
     */
    public $key_list = null;


    /**
     * numeric list of primary keys
     *
     * @var array
     */
    public $primary_key = null;


    /**
     * string with all properties for class generation
     *
     * @var string
     */
    public $property_list = '';


    /**
     * SQL field list of all properties
     *
     * @var string
     */
    public $sql_field_list = '';


    /**
     * generated short tag for the SQL
     *
     * @var string
     */
    public $table_short_tag = '';


    /**
     * property string with typecast
     *
     * @var string
     */
    public $load_row_list = '';


    /**
     * list for assignment of the primary key in the constructor
     * for the loading process
     *
     * @var string
     */
    public $constructor_parameter = '';


    /**
     * the output in the end
     *
     * @var string
     */
    public $generated_code = '';


    /**
     * classname for the generation of the code
     *
     * @var string
     */
    public $class_name = '';


    /**
     * class extends
     *
     * @var string
     */
    public $extend_class = 'Base';
    
    
    /**
     * is_final [final class]
     * 
     * @var bool
     */
    public $is_final = false;
    
    
    /**
     * list of parameter for the constructor
     * 
     * @var string
     */
    public $constructor_param_list = '';

    /**
     * is_abstract [abstract class]
     *
     * @var bool
     */
    public $is_abstract = false;
    
    /**
     * interface names which are implemented
     *
     * @var string
     */
    public $implement_interface = '';
    
    /**
     * array with replacement values
     * #KEY# will be replaced with $value
     * @var array
     */
	public $tpl_replacements = array();
    
    
    /**
     * constructor
     *
     * @param $db object           
     * @param $table_name string           
     */
    public function __construct( $db , $table_name = '' )
    {
		$this->db = $db;
		$this->table_name = $table_name;
    }



    /**
     * major class generation body needs to be redone
     * i am sure but for the beta this will work
     *
     * @return boolean
     */
    public function generate_class()
    {


        /**
         * generates the spaces 
         * 
         * @param int $amount
         * @return string
         */
        function gen_spaces( $amount )
        {

            $spaces = '';
            for ( $i = 0 ; $i < $amount ; $i++ )
            {
                $spaces .= ' ';
            }
            return $spaces;
        }
        
        if ( empty($this->table_model) )
        {
            return false;
        }
        // general initiation of the object properties based on the
        // $this->table_model
        $this->init();

        $this->generate_class_definition();
        if ( strpos($this->class_name, '_') !== false )
        {
            $tmp = explode('_', $this->class_name);
            foreach ( $tmp as $key => $part )
            {
                $tmp[$key] = ucfirst(strtolower($part));
            }
            $this->class_name = implode('_', $tmp);
        }
        $this->tpl_replacements['class_name'] = $this->class_name;
        $file = new File();
        $file->open($this->tpl_path);     

        $this->generated_code =  $file->read();
        
        foreach ($this->tpl_replacements as $pattern => $value )
        {
        	if (!is_string($value)) continue;
        	
        	$pattern = '#' . strtoupper($pattern) . '#'; 
        	$this->generated_code = str_replace($pattern, $value, $this->generated_code);
        }

        $this->generated_code = str_replace("\t", gen_spaces(self::SPACE_AMOUNT), $this->generated_code);
        return true;
    }
    
    abstract public function init();


    /**
     * generates the class definiton
     * 
     * @return boolean
     */
    public function generate_class_definition()
    {
        $this->tpl_replacements['class_definition'] = '';
        $this->tpl_replacements['class_definition_list'] = '';
        if ($this->is_final == true)
        {
            $this->tpl_replacements['class_definition'] .= 'final ';
        }

        if ($this->is_abstract == true)
        {
            $this->tpl_replacements['class_definition'] .= 'abstract ';
        }

        $this->tpl_replacements['class_definition'] .= 'class ' . ucfirst(strtolower($this->class_name));
        $this->tpl_replacements['class_definition_list'] .= 'class ' . ucfirst(strtolower($this->class_name)) . 'List';

        if (empty($this->extend_class) && empty($this->implement_interface)) return true;

        if (!empty($this->extend_class))
        {
            $this->tpl_replacements['class_definition'] .= ' extends ' . ucfirst(strtolower($this->extend_class));
            $this->tpl_replacements['class_definition_list'] .= ' extends ' . ucfirst(strtolower($this->extend_class)) . 'List';
        }

        if (!empty($this->implement_interface))
        {
            $this->tpl_replacements['class_definition'] .= ' implements ' . implode(',', $this->implement_interface);
            $this->tpl_replacements['class_definition_list'] .= ' implements ' . implode('List,', $this->implement_interface) . 'List';
        }

        return true;
    }


    /**
     * get table short tag parses based on the table name the first
     * character or based on the underscores for example
     * PX_GALLERY -> pg
     * CUSTOMER -> c
     *
     * @return boolean
     */
    public function get_table_short_tag()
    {

        if ( empty($this->table_name) ) return false;
        
        $tmp = null;
        
        if ( strpos($this->table_name, '_') !== false )
        {
            $tmp = explode('_', $this->table_name);
        }
        
        if ( !empty($tmp) )
        {
            foreach ( $tmp as $val )
            {
                $this->table_short_tag .= strtolower(substr($val, 0, 1));
            }
        }
        else
        {
            $this->table_short_tag .= strtolower(substr($this->table_name, 0, 1));
        }
        
        $this->tpl_replacements['table_short_tag'] =  $this->table_short_tag;
        return true;
    }


    
}
?>