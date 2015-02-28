<?php
namespace chilimatic\lib\generator;
use chilimatic\lib\config\Config;


/**
 * Class generator Mysql
 *
 * @property mixed _l_table_name
 * @author j
 */
class Cgenerator_Mysql extends CGenerator
{

    private $namespace = "chilimatic\\db\\";
	
	/**
	 * mandatory field list for validation
	 *
	 * @var array
	 */
	public $mandatory_fields = '';
	
	
	/**
	 * SQL where statement for the update -> primary keys
	 *
	 * @var string
	 */
	public $primary_key_where_statement = '';
	
	
	/**
	 * if condition all primary keys have to be set
	 *
	 * @var string
	 */
	public $primary_key_if_statement = '';
	
	
	/**
	 * array assignment -> default key assignment for the list array
	 *
	 * @var string
	 */
	public $primary_key_assign_statement = '';
	
	
	/**
	 * primary key method statements
	 *
	 * @var string
	 */
	public $primary_key_method_statement = '';
	
	/**
	 * where statement
	 *
	 * @var string
	 */
	public $where_statement = '';
	
	
	/**
	 * constructor
	 *
	 * @param $db object
	 * @param $table_name string
	 */
	public function __construct( $db , $table_name = '' )
	{
	
		if ( empty($db) ) return;
		$this->db = $db;
		parent::__construct($db, $table_name);
		if ( !empty($table_name) )
		{
			$this->scan_table($table_name);
		}
		
		$this->tpl_path = Config::get('document_root') . Config::get('template_path'). '/cgenerator/mysqlclass.tpl';
	}
	
	
	/**
	 * fetches a numeric table list
	 *
	 * @param $table_name string
	 *
	 * @return boolean
	 */
	public function scan_table( $table_name = '' )
	{
	
		if ( empty($table_name) ) return false;
	
		$this->_l_table_name = strtolower($table_name);
		$this->table_name = $table_name;
		$this->tpl_replacements['table_name'] = $table_name;
		// get a more detailed description of the table
		$sql = "SHOW FULL COLUMNS FROM `$table_name`";
	   
		$res = $this->db->query($sql);
		$this->table_model = $this->db->fetch_object_list($res);
		
		return true;
	}


    /**
     * get type value parses the mysql type and returns
     *
     * @param $type string
     * @param bool|string $type_cast string
     *
     * @return string
     */
	public function get_type_value( $type = null , $type_cast = false )
	{
	
		if ( empty($type) ) return "null";
	
		if ( strpos(strtolower($type), 'int') !== false )
		{
			return ($type_cast) ? '(int)' : 0;
		}
	
		if ( strpos(strtolower($type), 'float') !== false )
		{
			return ($type_cast) ? '(float)' : 0;
		}
	
		if ( strpos(strtolower($type), 'char') !== false )
		{
			return ($type_cast) ? '(string)' : "''";
		}
	
		if ( strpos(strtolower($type), 'enum') !== false )
		{
			return ($type_cast) ? '(string)' : "''";
		}
	
		if ( strpos(strtolower($type), 'dec') !== false )
		{
			return ($type_cast) ? '(float)' : 0;
		}
	
		if ( strpos(strtolower($type), 'text') !== false )
		{
			return ($type_cast) ? '(string)' : "''";
		}
	
		if ( strpos(strtolower($type), 'bit') !== false )
		{
			return ($type_cast) ? '(string)' : "''";
		}
	
		if ( strpos(strtolower($type), 'bool') !== false )
		{
			return ($type_cast) ? '(boolean)' : "false";
		}
	
		if ( strpos(strtolower($type), 'datetime') !== false )
		{
			return ($type_cast) ? '(string)' : "'0000-00-00 00:00:00'";
		}
	
		if ( strpos(strtolower($type), 'date') !== false )
		{
			return ($type_cast) ? '(string)' : "'0000-00-00'";
		}
	
		if ( strpos(strtolower($type), 'year') !== false )
		{
			return ($type_cast) ? '(string)' : "'0000'";
		}
	
		if ( strpos(strtolower($type), 'time') !== false )
		{
			return ($type_cast) ? '(int)' : 0;
		}
	
		if ( strpos(strtolower($type), 'double') )
		{
			return ($type_cast) ? '(double)' : 0;
		}
	
		return "null";
	}

    /**
     * generates the primary key statement
     *
     * @return bool
     */
    public function generate_primary_key_statement()
	{
	
		if ( empty($this->primary_key) ) return false;
	
		if ( count($this->primary_key) == 1 )
		{
			$this->tpl_replacements['primary_key_assign_statement'] = "'{$this->primary_key[0]}'";
			$this->tpl_replacements['primary_key_if_statement'] = 'empty($this->' . (string) strtolower((string) $this->primary_key[0]) . ')';
			$this->tpl_replacements['primary_key_where_statement'] = ' `' . (string) $this->primary_key[0] . '` = \'{$this->' . (string) strtolower((string) $this->primary_key[0]) . '}\'';
			$this->tpl_replacements['primary_key_method_statement'] = '$' . strtolower((string) $this->primary_key[0]) . " = null";
			return true;
		}
	
		$this->tpl_replacements['primary_key_assign_statement'] = 'array(';
		foreach ( $this->primary_key as $p_key )
		{
			$this->tpl_replacements['primary_key_assign_statement'] .= "'$p_key',";
			$this->tpl_replacements['primary_key_if_statement'] .= ' || empty($this->' . (string) strtolower((string) $p_key) . ')';
			$this->tpl_replacements['primary_key_where_statement'] .= ' AND `' . (string) $p_key . '` = \'{$this->' . (string) strtolower((string) $p_key) . '}\'';
			$this->tpl_replacements['primary_key_method_statement'] .= '$' . (string) strtolower((string) $p_key) . " = null,";
		}
		$this->tpl_replacements['primary_key_if_statement'] = substr($this->primary_key_if_statement, 4);
		$this->tpl_replacements['primary_key_assign_statement'] = substr($this->primary_key_assign_statement, 0, -1) . ')';
		$this->tpl_replacements['primary_key_method_statement'] = substr($this->primary_key_method_statement, 0, -1);
		$this->tpl_replacements['primary_key_where_statement'] = substr($this->primary_key_where_statement, 5);
	
		return true;
	}
	
	
	/**
	 * initializes the whole object generating process
	 *
	 * @return boolean
	 */
	public function init()
	{
		// the table model is an object list
		if ( empty($this->table_model) )
		{
			return false;
		}
	
		// get a short tag based on the table name
		if ( !$this->get_table_short_tag() ) return false;
	
		$this->tpl_replacements['where_statement'] = '';
		$this->tpl_replacements['load_row_list'] = '';
		$this->tpl_replacements['trait_list'] = "\n\nuse " . $this->namespace ."Database;\n\n" ;
		$this->tpl_replacements['update_insert_list'] = '';
		
		$i = 0;
		$count = count($this->table_model);
		// try to fit all possibilities of generated code in here
		// otherwise it would be iterated much more often
		foreach ( $this->table_model as $column )
		{
			// to reduce the use of the strtolower function ;) just do it once
			$property_name = strtolower($column->Field);
	
			// get a list of properties
			$property_list_unsorted[(string) $property_name] = (string) "\t/**" . ($column->Comment ? self::STD_INTEND_PROPERTIES . " * comment: $column->Comment" : '');
			$property_list_unsorted[(string) $property_name] .= (string) ($column->Extra || strtolower($column->Null) == 'no' ? self::STD_INTEND_PROPERTIES . " * Extra: $column->Extra[Not null]" : '');
			$property_list_unsorted[(string) $property_name] .= (string) " " . self::STD_INTEND_PROPERTIES . " * @var $column->Type" . ($column->Key == 'PRI' ? " [PRIMARY KEY]" : '') . self::STD_INTEND_PROPERTIES . " */" . self::STD_INTEND_PROPERTIES . "public $" . $property_name . " = " . $this->get_type_value((($column->Null == 'NO' && !$this->with_null) ? $column->Type : '')) . ";\n\n";
	
			if ( $column->Key == 'PRI' )
			{
				
				$this->primary_key[] = $column->Field;
				if ( !empty($this->where_statement) )
				{
					$this->tpl_replacements['where_statement'] .= (string) ' AND ';
				}
				// generate generic where statement based on the primary keys
				$this->tpl_replacements['where_statement'] .= (string) "`$column->Field` = '\$this->" . $property_name . "'";
				$this->tpl_replacements['constructor_key_list'] = '';
				$this->tpl_replacements['constructor_key_list'] .= (string) "\t\t" . '$this->' . $property_name . ' = $' . $property_name . ";\n";
	
			}
	
			if ( $column->Null == 'NO' )
			{
				$this->tpl_replacements['mandatory_fields'][] = (string) $property_name;
			}
			
			// mysql list for the save function
			$this->tpl_replacements['update_insert_list'] .= (string) "\t\t\$sql .= (\$this->$property_name !== " . (($column->Null == 'NO' && !$this->with_null) ? $this->get_type_value($column->Type) : 'null') . ") ? \"";
			$this->tpl_replacements['update_insert_list'] .= (string) "`$column->Field`= '\" . Database_Tool::db_sanitize(stripslashes(\$this->$property_name)) . \"'";
			$this->tpl_replacements['update_insert_list'] .= (string) ($count != $i + 1 ? ', "' : '"') . " : '';\n";
	
			// get multi keys for assignment
			if ( $column->Key == 'MUL' )
			{
				$this->key_list = (string) $column->Field;
			}
			// get multi keys for assignment
			if ( $column->Key == 'UNI' )
			{
				$this->key_list = (string) $column->Field;
			}
	
			// assign the SQL field list
			$this->sql_field_list[] = (string) "`$this->table_short_tag`.`$column->Field`";

			// generates the load row property assignment list
			$this->tpl_replacements['load_row_list'].= (string) "\t\t\$this->" . strtolower($column->Field) . " = " . (($column->Null == 'NO' && !$this->with_null) ? $this->get_type_value($column->Type, true) : '') . " isset(\$row->" . $column->Field . ") ? \$row->" . $column->Field . " : " . (($column->Null == 'NO' && !$this->with_null) ? $this->get_type_value($column->Type) : 'null') .";\n";
			$i = $i + 1;
		}
		// remove the last coma
		// add the db to the listing of the class properties
		$property_list_unsorted['db'] = (string) "\t/**" . self::STD_INTEND_PROPERTIES . " * Database object" . self::STD_INTEND_PROPERTIES . "  * @var Object" . self::STD_INTEND_PROPERTIES . " */" . self::STD_INTEND_PROPERTIES . "public \$db = null;\n\n";
		$property_list_unsorted['mandatory'] = (string) "\t/**" . self::STD_INTEND_PROPERTIES . " * list of mandatory fields " . self::STD_INTEND_PROPERTIES . "  * @var array" . self::STD_INTEND_PROPERTIES . " */" . self::STD_INTEND_PROPERTIES . "public \$mandatory_field = array('" . implode("','", $this->tpl_replacements['mandatory_fields']) . "');\n\n";
		// sort alpabetical based on the key
		ksort($property_list_unsorted);
		$this->tpl_replacements['property_list'] = implode("\n\n", $property_list_unsorted);
		unset($property_list_unsorted);
	
		$this->generate_primary_key_statement();
	
		// implode the field list
		$this->tpl_replacements['sql_field_list'] = implode(', ', $this->sql_field_list);
		
		$this->tpl_replacements['constructor_param_list'] = '$' . strtolower(implode("'\n\t\t* @param ', $", $this->primary_key));
		return true;
	}
	
}