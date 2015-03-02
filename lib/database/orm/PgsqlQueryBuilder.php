<?php
/**
 *
 * @author j
 * Date: 2/18/15
 * Time: 10:55 PM
 *
 * File: psqlquerybuilder.class.php
 */
namespace chilimatic\lib\database\orm;

class PgsqlQueryBuilder extends AbstractQueryBuilder {


    public function __construct() {

    }

    /**
     * @param AbstractModel $model
     * @param array $param
     *
     * @return mixed
     */
    public function generateForModel(AbstractModel $model, $param)
    {
        // TODO: Implement generateForModel() method.
    }


}