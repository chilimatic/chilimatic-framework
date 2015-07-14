<?php
/**
 *
 * @author j
 * Date: 12/22/14
 * Time: 3:40 PM
 *
 * File: querybuilder.php
 */

namespace chilimatic\lib\database\orm\querybuilder;
use chilimatic\lib\cache\engine\CacheInterface;
use chilimatic\lib\database\AbstractDatabase;
use chilimatic\lib\database\orm\AbstractModel;
use chilimatic\lib\database\orm\querybuilder\meta\MySQLTableData;
use chilimatic\lib\database\orm\querybuilder\strategy\MySQLDeleteStrategy;
use chilimatic\lib\database\orm\querybuilder\strategy\MySQLInsertStrategy;
use chilimatic\lib\database\orm\querybuilder\strategy\MySQLSelectStrategy;
use chilimatic\lib\database\orm\querybuilder\strategy\MySQLUpdateStrategy;

/**
 * Class MysqlQueryBuilder
 *
 * @package chilimatic\lib\database\orm
 */
class MysqlQueryBuilder extends AbstractQueryBuilder {

    /**
     * trait for the annotation checks
     */
    use ConsistencyTrait;

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
        if (isset($cacheData[self::RELATION_INDEX])) {
            $this->checkRelations($cacheData[self::RELATION_INDEX]);
        }

        /**
         * select Strategy
         */
        $strategy = new MySQLSelectStrategy(
            $cacheData[self::TABLE_DATA_INDEX],
            array_keys($param)
        );

        return $strategy->generateSQLStatement();
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
     * @param AbstractModel $model
     * @param MySQLTableData $tableData
     *
     * @return array
     */
    public function prepareModelData(AbstractModel $model, MySQLTableData $tableData)
    {
        $data = $columData = [];
        $keyList = $tableData->getPrimaryKey();

        foreach ($tableData->getColumnNames() as $column)
        {
            $reflection = new \ReflectionClass($model);
            $reflectedProperty = $reflection->getProperty($column);
            $reflectedProperty->setAccessible(true);

            $columData = [
                'value' => $reflectedProperty->getValue($model),
                'name' => $column
            ];

            if (in_array($column, $keyList)) {
                array_merge($columData, ['KEY' => true]);
            }
            $data[] = $columData;
        }

        return $data;
    }

    /**
     * @param AbstractModel $model
     *
     * @return mixed
     */
    public function generateInsertForModel(AbstractModel $model)
    {
        $cacheData = $this->fetchCacheData($model);

        $strategy = new MySQLInsertStrategy(
            $cacheData[self::TABLE_DATA_INDEX],
            $this->prepareModelData($model, $cacheData[self::TABLE_DATA_INDEX])
        );

        return [$strategy->generateSQLStatement(), $strategy->getModelData()];
    }

    /**
     * @param AbstractModel $model
     *
     * @return mixed
     */
    public function generateUpdateForModel(AbstractModel $model, $diff = null)
    {
        $cacheData = $this->fetchCacheData($model);
        $strategy = new MySQLUpdateStrategy(
            $cacheData[self::TABLE_DATA_INDEX],
            $this->prepareModelData($model, $cacheData[self::TABLE_DATA_INDEX])
        );

        return [$strategy->generateSQLStatement(), $strategy->getModelData()];
    }

    /**
     * @param AbstractModel $model
     *
     * @return mixed
     */
    public function generateDeleteForModel(AbstractModel $model)
    {
        $cacheData = $this->fetchCacheData($model);

        $strategy = new MySQLDeleteStrategy(
            $cacheData[self::TABLE_DATA_INDEX],
            $this->prepareModelData($model, $cacheData[self::TABLE_DATA_INDEX])
        );

        return [$strategy->generateSQLStatement(), $strategy->getModelData()];
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