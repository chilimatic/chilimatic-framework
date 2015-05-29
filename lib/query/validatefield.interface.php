<?php
namespace query;
Interface Query_ValidateField
{
    const MYSQL = 'mysql';

    /**
     * public constructor
     */
    public function __construct();


    /**
     * returns the string field name
     */
    public function init();


    /**
     * validates the content of a field 
     * based on the field type and value
     * 
     * @return bool
     */
    public function validate();
}