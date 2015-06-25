<?php
namespace query;
/*
 * 
 * 
TINYINT
	A very small integer
	The signed range is –128 to 127. The unsigned range is 0 to 255.
SMALLINT
	A small integer
	The signed range is –32768 to 32767. The unsigned range is 0 to 65535
MEDIUMINT
	A medium-size integer
	The signed range is –8388608 to 8388607. The unsigned range is 0 to 16777215
INT or INTEGER
	A normal-size integer
	The signed range is –2147483648 to 2147483647. The unsigned range is 0 to 4294967295
BIGINT
	A large integer
	The signed range is –9223372036854775808 to 9223372036854775807. The unsigned range is 0 to 18446744073709551615
FLOAT
	A small (single-precision) floating-point number. Cannot be unsigned
	Ranges are –3.402823466E+38 to –1.175494351E-38, 0 and 1.175494351E-38 to 3.402823466E+38. If the number of Decimals is not set or <= 24 it is a single-precision floating point number
DOUBLE,
DOUBLE PRECISION,
REAL
	A normal-size (double-precision) floating-point number. Cannot be unsigned
	Ranges are -1.7976931348623157E+308 to -2.2250738585072014E-308, 0 and 2.2250738585072014E-308 to 1.7976931348623157E+308. If the number of Decimals is not set or 25 <= Decimals <= 53 stands for a double-precision floating point number
DECIMAL,
NUMERIC
	An unpacked floating-point number. Cannot be unsigned
	Behaves like a CHAR column: “unpacked” means the number is stored as a string, using one character for each digit of the value. The decimal point, and, for negative numbers, the ‘-‘ sign is not counted in Length. If Decimals is 0, values will have no decimal point or fractional part. The maximum range of DECIMAL values is the same as for DOUBLE, but the actual range for a given DECIMAL column may be constrained by the choice of Length and Decimals. If Decimals is left out it’s set to 0. If Length is left out it’s set to 10. Note that in MySQL 3.22 the Length includes the sign and the decimal point
DATE
	A date
	The supported range is ‘1000-01-01’ to ‘9999-12-31’. MySQL displays DATE values in ‘YYYY-MM-DD’ format
DATETIME
	A date and time combination
	The supported range is ‘1000-01-01 00:00:00’ to ‘9999-12-31 23:59:59’. MySQL displays DATETIME values in ‘YYYY-MM-DD HH:MM:SS’ format
TIMESTAMP
	A timestamp
	The range is ‘1970-01-01 00:00:00’ to sometime in the year 2037. MySQL displays TIMESTAMP values in YYYYMMDDHHMMSS, YYMMDDHHMMSS, YYYYMMDD or YYMMDD format, depending on whether M is 14 (or missing), 12, 8 or 6, but allows you to assign values to TIMESTAMP columns using either strings or numbers. A TIMESTAMP column is useful for recording the date and time of an INSERT or UPDATE operation because it is automatically set to the date and time of the most recent operation if you don’t give it a value yourself
TIME
	A time
	The range is ‘-838:59:59’ to ‘838:59:59’. MySQL displays TIME values in ‘HH:MM:SS’ format, but allows you to assign values to TIME columns using either strings or numbers
YEAR
	A year in 2- or 4- digit formats (default is 4-digit)
	The allowable values are 1901 to 2155, and 0000 in the 4 year format and 1970-2069 if you use the 2 digit format (70-69). MySQL displays YEAR values in YYYY format, but allows you to assign values to YEAR columns using either strings or numbers. (The YEAR type is new in MySQL 3.22.)
CHAR
	A fixed-length string that is always right-padded with spaces to the specified length when stored
	The range of Length is 1 to 255 characters. Trailing spaces are removed when the value is retrieved. CHAR values are sorted and compared in case-insensitive fashion according to the default character set unless the BINARY keyword is given
VARCHAR
	A variable-length string. Note: Trailing spaces are removed when the value is stored (this differs from the ANSI SQL specification)
	The range of Length is 1 to 255 characters. VARCHAR values are sorted and compared in case-insensitive fashion unless the BINARY keyword is given
TINYBLOB,
TINYTEXT
	
	A BLOB or TEXT column with a maximum length of 255 (2^8 - 1) characters
BLOB,
TEXT
	
	A BLOB or TEXT column with a maximum length of 65535 (2^16 - 1) characters
MEDIUMBLOB,
MEDIUMTEXT
	
	A BLOB or TEXT column with a maximum length of 16777215 (2^24 - 1) characters
LONGBLOB,
LONGTEXT
	
	A BLOB or TEXT column with a maximum length of 4294967295 (2^32 - 1) characters
ENUM
	An enumeration
	A string object that can have only one value, chosen from the list of values ‘value1’, ‘value2’, ..., or NULL. An ENUM can have a maximum of 65535 distinct values.
SET
	A set
	A string object that can have zero or more values, each of which must be chosen from the list of values ‘value1’, ‘value2’, ... A SET can have a maximum of 64 members

 */



class Query_MysqlVal extends Query_FieldVal
{


    /**
     * (non-PHPdoc)
     * @see \query\Query_ValidateField::__construct()
     */
    public function __construct()
    {
        parent::__construct();
        $this->init(Query_ValidateField::MYSQL);
    }


    /**
     * (non-PHPdoc)
     * @see \query\Query_FieldVal::init()
     */
    public function init( $db_type )
    {
        /*
        $this->field_typelist = new \stdClass();
        // tiny int
        $this->field_typelist->tinyint = new \stdClass();
        $this->field_typelist->tinyint->srange = array(–128, 127);
        $this->field_typelist->tinyint->urange = array(0, 255);
        $this->field_typelist->tinyint->unsigned = true;
        $this->field_typelist->tinyint->type = 'integer';
        // small int
        $this->field_typelist->smallint = new \stdClass();
        $this->field_typelist->smallint->srange = array(–32768, 32767);
        $this->field_typelist->smallint->urange = array(0, 65535);
        $this->field_typelist->smallint->unsigned = true;
        $this->field_typelist->smallint->type = 'integer';
        // medium int
        $this->field_typelist->mediumint = new \stdClass();
        $this->field_typelist->mediumint->srange = array(–8388608, 8388607);
        $this->field_typelist->mediumint->urange = array(0, 16777215);
        $this->field_typelist->mediumint->unsigned = true;
        $this->field_typelist->mediumint->type = 'integer';
        // int
        $this->field_typelist->int = new \stdClass();
        $this->field_typelist->int->srange = array(–2147483648, 2147483647);
        $this->field_typelist->int->urange = array(0, 4294967295);
        $this->field_typelist->int->unsigned = true;
        $this->field_typelist->int->type = 'integer';
        $this->field_typelist->integer = new \stdClass();
        $this->field_typelist->integer->srange = array(–2147483648, 2147483647);
        $this->field_typelist->integer->urange = array(0, 4294967295);
        $this->field_typelist->integer->unsigned = true;
        $this->field_typelist->integer->type = 'integer';
        // big int
        $this->field_typelist->bigint = new \stdClass();
        $this->field_typelist->bigint->srange = array(–9223372036854775808, 9223372036854775807);
        $this->field_typelist->bigint->urange = array(0, 18446744073709551615);
        $this->field_typelist->bigint->unsigned = true;
        $this->field_typelist->bigint->type = 'integer';
        // float
        $this->field_typelist->float = new \stdClass();
        $this->field_typelist->float->srange = array(38);
        $this->field_typelist->float->type = 'float';
        
        // doubles
        $this->field_typelist->double = new \stdClass();
        $this->field_typelist->double->srange = array(308);
        $this->field_typelist->double->urange = 0;
        $this->field_typelist->double->type = 'float';
        $this->field_typelist->double->unsigned = false;
        $this->field_typelist->double_precision = new \stdClass();
        $this->field_typelist->double_precision->srange = array(308);
        $this->field_typelist->double_precision->urange = null;
        $this->field_typelist->double_precision->type = 'float';
        $this->field_typelist->real = new \stdClass();
        $this->field_typelist->real->srange = array(308, 308);
        $this->field_typelist->real->urange = array(0, 0);
        $this->field_typelist->real->type = 'float';
        
        // decimal string stored number
        $this->field_typelist->decimal = new \stdClass();
        $this->field_typelist->decimal->srange = array(308, 308);
        $this->field_typelist->decimal->urange = array(0, 0);
        $this->field_typelist->decimal->type = 'float';
        $this->field_typelist->decimal->unsigned = false;
        $this->field_typelist->numeric = new \stdClass();
        $this->field_typelist->numeric->srange = array(10);
        $this->field_typelist->numeric->urange = array(10);
        $this->field_typelist->numeric->type = 'float';
        
        // date
        $this->field_typelist->date = new \stdClass();
        $this->field_typelist->date->srange = null;
        $this->field_typelist->date->urange = null;
        $this->field_typelist->date->length = 10;
        $this->field_typelist->date->type = 'date';
        $this->field_typelist->date->pattern = '/^(\d){4}-(\d){2}-(\d){2}$/';
        */
        
    }


    /**
     * (non-PHPdoc)
     * @see \query\Query_FieldVal::validate()
     */
    public function validate()
    {

    }


    /**
     * (non-PHPdoc)
     * @see \query\Query_FieldVal::tostring()
     */
    public function tostring()
    {

    }
}
