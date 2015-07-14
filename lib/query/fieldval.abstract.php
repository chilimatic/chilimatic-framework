<?php
namespace query;
abstract class Query_FieldVal implements Query_ValidateField
{


    /**
     * list of posible field types for validation
     *
     * @var array
     */
    private $field_typelist = null;


    /**
     * db type important for the validation and the tostring method
     *
     * @var string
     */
    private $db_type = null;


    /**
     * constructor
     */
    abstract public function __construct();


    /**
     * abstract init for the different databases
     *
     */
    abstract public function init($db_type);


    /**
     * validator
     *
     */
    abstract public function validate();


    /**
     * returns the string
     */
    public abstract function __tostring();
}