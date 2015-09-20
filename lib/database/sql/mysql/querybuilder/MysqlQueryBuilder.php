<?php
/**
 *
 * @author j
 * Date: 12/22/14
 * Time: 3:40 PM
 *
 * File: querybuilder.php
 */

namespace chilimatic\lib\database\sql\orm\querybuilder;

use chilimatic\lib\cache\engine\CacheInterface;
use chilimatic\lib\database\AbstractDatabase;
use chilimatic\lib\database\sql\orm\AbstractModel;
use chilimatic\lib\database\sql\mysql\querybuilder\meta\MySQLTableData;
use chilimatic\lib\database\sql\mysql\querybuilder\strategy\MySQLDeleteStrategy;
use chilimatic\lib\database\sql\mysql\querybuilder\strategy\MySQLInsertStrategy;
use chilimatic\lib\database\sql\mysql\querybuilder\strategy\MySQLSelectStrategy;
use chilimatic\lib\database\sql\mysql\querybuilder\strategy\MySQLUpdateStrategy;
use chilimatic\lib\database\sql\querybuilder\AbstractQueryBuilder;
use chilimatic\lib\database\sql\querybuilder\meta\AbstractSQLTableData;
use chilimatic\lib\transformer\string\DynamicSQLParameter;

/**
 * Class MysqlQueryBuilder
 *
 * @package chilimatic\lib\database\orm
 */
class MysqlQueryBuilder extends AbstractQueryBuilder
{

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
    public function __construct(CacheInterface $cache = null, AbstractDatabase $db = null)
    {
        $this->relation       = new \SplFixedArray();
        $this->modelDataCache = [];
        /**
         * @todo DI for the future
         */
        $this->tableData = new MySQLTableData($db);
        $this->paramTransformer = new DynamicSQLParameter();

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

        $strategy->setTransformer($this->paramTransformer);

        return $strategy->generateSQLStatement();
    }

    /**
     * @param AbstractModel $model
     * @param MySQLTableData $tableData
     *
     * @return array
     */
    public function prepareModelData(AbstractModel $model, MySQLTableData $tableData)
    {
        $data    = $columData = [];
        $keyList = $tableData->getPrimaryKey();

        foreach ($tableData->getColumnNames() as $column) {
            $reflection        = new \ReflectionClass($model);
            $reflectedProperty = $reflection->getProperty($column);
            $reflectedProperty->setAccessible(true);

            $columData = [
                'value' => $reflectedProperty->getValue($model),
                'name'  => $column
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
            $this->prepareModelData(
                $model,
                $cacheData[self::TABLE_DATA_INDEX]
            )
        );

        $strategy->setTransformer($this->paramTransformer);


        return [
            $strategy->generateSQLStatement(),
            $this->prepareModelDataForStatement($strategy->getModelData())
        ];
    }

    /**
     * @param $modelData
     *
     * @return array
     */
    public function prepareModelDataForStatement($modelData)
    {
        $newModelData = [];
        foreach ($modelData as $column) {
            $newModelData[] = [$this->paramTransformer->transform($column['name']), $column['value']];
        }

        return $newModelData;
    }


    /**
     * @param AbstractModel $model
     *
     * @return mixed
     */
    public function generateUpdateForModel(AbstractModel $model, $diff = null)
    {
        $cacheData = $this->fetchCacheData($model);
        $strategy  = new MySQLUpdateStrategy(
            $cacheData[self::TABLE_DATA_INDEX],
            $this->prepareModelData(
                $model,
                $cacheData[self::TABLE_DATA_INDEX]
            )
        );

        $strategy->setTransformer($this->paramTransformer);

        return [
            $strategy->generateSQLStatement(),
            $strategy->getModelData()
        ];
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
            $this->prepareModelData(
                $model,
                $cacheData[self::TABLE_DATA_INDEX]
            )
        );

        $strategy->setTransformer($this->paramTransformer);

        return [
            $strategy->generateSQLStatement(),
            $strategy->getModelData()
        ];
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
    public function setRelation(\SPLFixedArray $relation)
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

    /**
     * @return array
     */
    public function getModelDataCache()
    {
        return $this->modelDataCache;
    }

    /**
     * @param array $modelDataCache
     *
     * @return $this
     */
    public function setModelDataCache($modelDataCache)
    {
        $this->modelDataCache = $modelDataCache;

        return $this;
    }

    /**
     * @return AbstractSQLTableData
     */
    public function getTableData()
    {
        return $this->tableData;
    }

    /**
     * @param AbstractSQLTableData $tableData
     *
     * @return $this
     */
    public function setTableData(AbstractSQLTableData $tableData)
    {
        $this->tableData = $tableData;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     *
     * @return $this
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return \chilimatic\lib\interfaces\IFlyWeightTransformer
     */
    public function getParamTransformer()
    {
        return $this->paramTransformer;
    }

    /**
     * @param \chilimatic\lib\interfaces\IFlyWeightTransformer $paramTransformer
     *
     * @return $this
     */
    public function setParamTransformer(\chilimatic\lib\interfaces\IFlyWeightTransformer $paramTransformer)
    {
        $this->paramTransformer = $paramTransformer;

        return $this;
    }

}