<?php
/**
 *
 * @author j
 * Date: 4/21/15
 * Time: 9:43 PM
 *
 * File: IQueryBuilder.php
 */
namespace chilimatic\lib\database\sql\querybuilder;

use chilimatic\lib\database\sql\orm\AbstractModel;

interface IQueryBuilder
{
    /**
     * @param AbstractModel $model
     * @param array $param
     *
     * @return mixed
     */
    public function generateSelectForModel(AbstractModel $model, $param);

    /**
     * @param AbstractModel $model
     *
     * @return mixed
     */
    public function generateInsertForModel(AbstractModel $model);

    /**
     * @param AbstractModel $model
     *
     * @return mixed
     */
    public function generateUpdateForModel(AbstractModel $model);

    /**
     * @param AbstractModel $model
     *
     * @return mixed
     */
    public function generateDeleteForModel(AbstractModel $model);
}