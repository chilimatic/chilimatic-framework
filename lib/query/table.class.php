<?php

class Query_Table
{


    /**
     * fields to query
     *
     * @var array
     */
    private $field_list = array();


    /**
     * name of a table
     *
     * @var string
     */
    private $name = null;


    /**
     * name of the prefix
     *
     * @var string
     */
    private $prefix = null;


    /**
     * name of the schema
     *
     * @var string
     */
    private $schema = null;


    /**
     * constructor
     *
     * @param string $name
     * @param string $prefix
     * @param string $schema
     */
    public function __construct($name = null, $prefix = null, $schema = null)
    {

        $this->name = $name;
        $this->name = $prefix;
        $this->name = $schema;
    }


    /**
     * standard getter
     *
     * @param string $property
     * @param mixed $value
     *
     * @return boolean|self
     */
    public function __set($property, $value)
    {

        if (!property_exists(__CLASS__, $property)) return false;

        $this->$property = $value;

        // for chaining
        return $this;
    }


    /**
     * standard set
     *
     * @param string $property
     *
     * @return mixed
     */
    public function __get($property)
    {

        if (!property_exists(__CLASS__, $property)) return false;

        return $this->$property;
    }
}