<?php

class Query
{

    protected $select = null;

    protected $field_list = null;

    protected $from = null;

    protected $inner_join = null;

    protected $outer_join = null;

    protected $left_join = null;

    protected $right_join = null;

    protected $cross_join = null;

    protected $where = null;

    protected $group_by = null;

    protected $having = null;

    protected $order_by = null;

    protected $limit = null;


    /**
     * Select [FIELD/FUNCTIONS] FROM [TABLENAME] ([table syn]) ([JOIN]) WHERE [FIELDS/CONDITIONS] [GROUPBY] [HAVING] [ORDERBY] [LIMIT]
     */


    public function __construct()
    {
        $this->select = new Query_Select();
        $this->from   = new Query_From();
        $this->where  = new Query_Where();
    }


}