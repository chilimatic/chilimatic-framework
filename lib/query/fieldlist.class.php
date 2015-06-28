<?php
namespace query;

class Query_FieldList
{

    /**
     * array that contains field objects
     *
     * @var array
     */
    public $field_list = array();

    public function __construct($field_list = null)
    {
        if (empty($field_list)) return;

        $this->add($field_list);
    }

    /**
     * adds fields based on an array
     *
     * @param array $field_list
     *
     * @return boolean
     */
    public function add($field_list)
    {
        if (!is_array($field_list)) return false;

        foreach ($field_list as $value) {
            $field = new Query_Field();
            if (count($value) == 1) {
                $field->name              = $value;
                $this->field_list[$value] = $field;
                continue;
            }

            $field->name     = $value['name'];
            $field->function = $value['function'];

        }

        return true;
    }


    /**
     * getter
     *
     * @param string $field
     */
    public function __get($field)
    {
        return $this->field_list[$field];
    }

    /**
     * deletes a field
     *
     * @param strings $field
     *
     * @return boolean
     */
    public function delete($field)
    {
        unset($this->field_list[$field]);

        return true;
    }

    /**
     * returns a string
     *
     * @return string
     */
    public function __toString()
    {

        if (empty($this->field_list)) return '*';

        $str = '';
        foreach ($this->field_list as $field) {
            $str .= "`$field->name`,";
        }

        return substr($str, 0, -1);
    }
}