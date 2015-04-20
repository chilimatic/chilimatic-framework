<?php
/**
 *
 * @author j
 * Date: 12/22/14
 * Time: 3:40 PM
 *
 * File: querybuilder.php
 */

namespace chilimatic\lib\database\orm;
use chilimatic\lib\cache\engine\CacheInterface;
use chilimatic\lib\cache\handler\ModelCache;
use chilimatic\lib\database\AbstractDatabase;


/**
 * Class MysqlQueryBuilder
 *
 * @package chilimatic\lib\database\orm
 */
class MysqlQueryBuilder extends AbstractQueryBuilder {

    /**
     * orm table mapping field
     */
    const TABLE_INDEX = 'table';

    /**
     * this is the property where the relations are
     * stored as a json object
     *
     * @var string
     */
    const RELATION_PROPERTY = "fieldMapping";


    /**
     * init cache connection
     *
     * @param CacheInterface $cache
     * @param AbstractDatabase $db
     */
    public function __construct(CacheInterface $cache = null, AbstractDatabase $db)
    {
        $this->relation = new \SplFixedArray();
        $this->modelDataCache = [];

        parent::__construct($cache, $db);
    }


    /**
     * @param AbstractModel $model
     * @param array $param
     *
     * @return string
     */
    public function generateSelectForModel(AbstractModel $model, $param)
    {
        $cacheData = $this->fetchCacheData($model);
        if (isset($cacheData['relation'])) {
            $this->checkRelations($cacheData['relation']);
        }
        return $this->_generateSelect($cacheData, $param);
    }

    /**
     * @return string
     */
    public function generateCondition($param)
    {
        if (empty($param)) {
            return '';
        }

        $str = ' WHERE ';
        foreach ($param as $key => $value) {
            if ($value) {
                $str .= " $key = ? AND";
            } else {
                $str .= " $key AND";
            }
        }

        return substr($str, 0, -3);
    }

    /**
     * @param TableData $tableData
     *
     * @return string
     */
    public function generateColumList(TableData $tableData)
    {
        return implode(',', $tableData->getColumnsNamesWithPrefix());
    }


    /**
     * @return string
     */
    public function _generateSelect(array $cacheData, $param = null){
        return implode(
            " ",
            [
                "SELECT",
                $this->generateColumList($cacheData['tableData']),
                "FROM",
                $cacheData['tableData']->getTableName(),
                $cacheData['tableData']->getPrefix(),
                $this->generateCondition($param)
            ]
        );
    }

    /**
     * @return bool
     * @throws \ErrorException
     */
    public function checkRelations($relationList)
    {
        if (!$relationList) return true;
        $relationList->rewind();
        foreach ($relationList as $entry) {
            if (!class_exists($entry[1])) {
                throw new \ErrorException($entry[1]. ' Relations Class does not exist!');
            }
        }
        return true;
    }

    /**
     * @param AbstractModel $model
     *
     * @return mixed
     */
    public function generateInsertForModel(AbstractModel $model)
    {
        $cacheData = $this->fetchCacheData($model);

        $query = implode(
            " ",
            [
                'INSERT INTO',
                $cacheData['tableData']->getTableName(),
            ]
        );

        $ret = $this->generateSetStatement($model, $cacheData);
        return [$query . $ret['query'], $ret['param']];
    }


    /**
     * @param AbstractModel $model
     *
     * @return mixed
     */
    public function generateUpdateForModel(AbstractModel $model, $diff = null)
    {
        $cacheData = $this->fetchCacheData($model);

        return $this->_generateUpdateForModel($model, $cacheData);
    }

    protected function _generateUpdateForModel(AbstractModel $model, $cacheData) {
        $query = implode(
            " ",
            [
                'UPDATE',
                $cacheData['tableData']->getTableName(),
            ]
        );

        $ret = $this->generateSetStatement($model, $cacheData, true);
        return [$query . $ret['query'], $ret['param']];
    }

    /**
     * @param AbstractModel $model
     * @param $cacheData
     *
     * @return array
     */
    public function generateSetStatement(AbstractModel $model, $cacheData, $isUpdate = false)
    {

        $query = ' SET ';
        $keyList = $cacheData['tableData']->getPrimaryKey();
        $whereStmt = [];
        $param = [];
        foreach ($cacheData['tableData']->getColumnNames() as $column)
        {
            $reflection = new \ReflectionClass($model);
            $reflectedProperty = $reflection->getProperty($column);
            $reflectedProperty->setAccessible(true);
            $value = $reflectedProperty->getValue($model);
            $param[] = [":$column", $value];

            $query .= " `$column`= :$column,";

            if (in_array($reflectedProperty->getName(), $keyList)) {
                $whereStmt[] = " `$column`= :$column";
            }
        }

        return  ['query' => substr($query,0, -1) . ($isUpdate ?  ' WHERE ' . implode( ' AND ', $whereStmt) : ''), 'param' => $param];
    }


    /**
     * @param AbstractModel $model
     *
     * @return mixed
     */
    public function generateDeleteForModel(AbstractModel $model)
    {
        // TODO: Implement generateDeleteForModel() method.
    }


    /**
     * @return \SPLFixedArray
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * @param \SPLFixedArray $relation
     *
     * @return $this
     */
    public function setRelation($relation)
    {
        $this->relation = $relation;

        return $this;
    }

    /**
     * @return \chilimatic\lib\cache\engine\CacheInterface
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param \chilimatic\lib\cache\engine\CacheInterface $cache
     *
     * @return $this
     */
    public function setCache(CacheInterface $cache)
    {
        $this->cache = $cache;

        return $this;
    }


}