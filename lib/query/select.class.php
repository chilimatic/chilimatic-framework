<?php
namespace query;

class Query_Select{
    
    public $field_list = null;
    
    public function __construct($field_list = null)
    {
        if (is_array($field_list))
        {
            $this->field_list = new Query_FieldList($field_list);
        }
        elseif ($field_list instanceof Query_FieldList)
        {
            $this->field_list = $field_list;
        }
    }
    
    
    public function add($field_list)
    {
        $this->field_list->add($field_list);
    }
    
    public function tostring()
    {
        $str = '';
        $str = "SELECT ".$this->field_list->tostring()." FROM ";
        
        return $str;
    }
}