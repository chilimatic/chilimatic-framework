<?php
/**
 *
 * @author j
 * Date: 12/23/14
 * Time: 2:32 PM
 *
 * File: abstractquerybuilder.class.php
 */
namespace chilimatic\lib\database\orm;

/**
 * Class AbstractQueryBuilder
 *
 * @package chilimatic\lib\database\orm
 */
abstract class AbstractQueryBuilder
{
    /**
     *
     */
    abstract public function __construct();

    /**
     * @param AbstractModel $model
     * @param array $param
     *
     * @return mixed
     */
    abstract public function generateSelectForModel(AbstractModel $model, $param);

    /**
     * @param AbstractModel $model
     *
     * @return mixed
     */
    abstract public function generateInsertForModel(AbstractModel $model);

    /**
     * @param AbstractModel $model
     *
     * @return mixed
     */
    abstract public function generateUpdateForModel(AbstractModel $model);

    /**
     * @param AbstractModel $model
     *
     * @return mixed
     */
    abstract public function generateDeleteForModel(AbstractModel $model);
}