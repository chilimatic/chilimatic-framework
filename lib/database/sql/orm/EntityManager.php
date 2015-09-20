<?php
/**
 *
 * @author j
 * Date: 12/22/14
 * Time: 1:35 PM
 *
 * File: entitymanager.class.php
 *
 * This is a really reduced ORM system if you want something
 * sophistaced use something like Doctrine
 */


namespace chilimatic\lib\database\sql\orm;
use chilimatic\lib\cache\handler\ModelCache;
use chilimatic\lib\database\AbstractDatabase;
use chilimatic\lib\database\ErrorLogTrait;
use chilimatic\lib\database\sql\orm\querybuilder\AbstractQueryBuilder;
use chilimatic\lib\log\ILog;


/**
 * Class EntityManager
 *
 * @package chilimatic\lib\database\ORM
 */
class EntityManager
{
    /**
     * @trait
     */
    use ErrorLogTrait;

    /**
     * @var string
     */
    const setterPrefix = 'set';

    /**
     * @var AbstractDatabase
     */
    public $db;

    /**
     * @var \chilimatic\lib\database\sql\orm\querybuilder\AbstractQueryBuilder
     */
    public $queryBuilder;

    /**
     * parameter if the objects within an object should be loaded
     *
     * @var bool
     */
    private $lazyLoading = true;

    /**
     * parameter for caching
     *
     * @var bool
     */
    private $useCache = true;


    /**
     * cache to store already queried data within
     * -> the models will be filled by it but not stored within it
     *
     * @var ModelCache
     */
    private $modelCache;


    /**
     * @param AbstractDatabase $db
     * @param AbstractQueryBuilder $queryBuilder
     */
    public function __construct(AbstractDatabase $db, AbstractQueryBuilder $queryBuilder = null)
    {
        $this->db           = $db;
        $this->queryBuilder = $queryBuilder;
        $this->modelCache   = new ModelCache();

        if ($this->queryBuilder) {
            $this->queryBuilder->setDb($db);
        }
    }

    /**
     * @param string $query
     * @param array $param
     *
     * @return \PDOStatement
     */
    protected function prepare($query, $param)
    {
        /**
         * @var \PdoStatement $stmt
         */
        $stmt = $this->db->prepare($query);

        foreach ($param as $set) {
            if (isset($set['value'])) {
                $value = &$set['value'];
                $key   = $set['name'];
            } else {
                $value = &$set[1];
                $key   = $set[0];
            }
            $stmt->bindParam($key, $value);
        }

        return $stmt;
    }

    /**
     * @param \PDOStatement $stmt
     * @param $model
     *
     * @return EntityObjectStorage
     */
    public function executeQuery($model, \PDOStatement $stmt)
    {
        $objectStorage = new EntityObjectStorage();
        if ($stmt->execute()) {
            if ($stmt->rowCount() > 1) {
                return $this->getList($model, $stmt);
            }
            $objectStorage->attach($this->hydrate($model, $stmt->fetchObject()));

        } else {
            $this->log(ILog::T_ERROR, implode(',', $stmt->errorInfo()));
            $objectStorage->attach($model);
        }

        return $objectStorage;
    }


    /**
     * @param $stmt
     * @param $model
     *
     * @return EntityObjectStorage
     */
    public function getList($model, $stmt)
    {
        $container = new EntityObjectStorage();
        /**
         * @var $stmt \PDOStatement
         */
        while ($row = $stmt->fetchObject()) {
            $container->attach($this->hydrate(clone $model, $row));
        }

        return $container;
    }

    /**
     * this method is so the user can add the param list like this [ 'id' => 1, 'name' => 'bla' ]
     * the other strategies are built different because of the update statement
     *
     * @param array $param
     *
     * @return array
     */
    public function prepareParam(array $param = null)
    {
        if (!$param) {
            return [];
        }
        $ret = [];

        foreach ($param as $key => $value) {
            $ret[] = [':' . md5($key), $value];
        }

        return $ret;
    }

    /**
     * @param AbstractModel $model
     * @param $param
     *
     * @return EntityObjectStorage
     */
    public function findBy(AbstractModel $model, $param = [])
    {
        if ($this->useCache && $this->modelCache) {
            if ($ret = $this->modelCache->get($model, $param)) {
                return $ret;
            }
        }

        $query  = $this->queryBuilder->generateSelectForModel($model, $param);
        $result = $this->executeQuery(
            $model,
            $this->prepare(
                $query,
                $this->prepareParam($param)
            )
        );

        if ($this->useCache && $this->modelCache) {
            $this->modelCache->set($model, $param);
        }

        return $result;
    }

    /**
     * @param AbstractModel $model
     * @param $param
     *
     * @return AbstractModel
     */
    public function findOneBy(AbstractModel $model, $param = [])
    {

        $result = $this->findBy($model, $param);
        $result->rewind();

        return $result->current();
    }

    /**
     * @param AbstractModel $model
     * @param mixed $row
     *
     * @return AbstractModel
     */
    public function hydrate(AbstractModel $model, $row)
    {
        if (!$row) {
            return $model;
        }

        $p = (array)get_object_vars($row);

        foreach ($p as $property => $value) {
            $property = str_replace('_', '', $property);
            $m        = self::setterPrefix . ucfirst($property);
            if (method_exists($model, $m)) {
                $model->$m($value);
            }
        }

        $this->hydrateRelations($model);

        return $model;
    }

    /**
     * @param AbstractModel $model
     *
     * @return AbstractModel
     */
    public function hydrateRelations(AbstractModel $model)
    {
        if (!$this->queryBuilder->fetchCacheData($model)) {
            return $model;
        }

        foreach ($this->queryBuilder->fetchCacheData($model)['relationList'] as $relation) {
            $injectionModel = new $relation[1]();
            if (strpos($relation[1], '\\') !== false) {
                $tmp      = explode('\\', $relation[1]);
                $property = array_pop($tmp);
            } else {
                $property = $relation[1];
            }
            $m = self::setterPrefix . $property;

            /**
             * @todo implement dynamic mappings from doc head
             */
            if (!$this->lazyLoading) {
                $this->queryBuilder->generateSelectForModel($injectionModel, ['id' => $relation[0]]);
            }

            $model->$m($injectionModel);
        }

        return $model;
    }

    public function delete(AbstractModel $model)
    {

        $data = $this->queryBuilder->generateDeleteForModel($model);

        if ($this->modelCache->get($model)) {
            $this->modelCache->remove($model);
        }
        
        $stmt = $this->prepare($data[0], $data[1]);
        return $stmt->execute();
    }


    /**
     * @param AbstractModel $model
     *
     * @return bool
     */
    public function persist(AbstractModel $model)
    {
        // create a query based on if the model exists or not (update or insert)
        if ($this->modelCache->get($model)) {
            $data = $this->queryBuilder->generateUpdateForModel($model);
        } else {
            $data = $this->queryBuilder->generateInsertForModel($model);
        }

        // add to the modelCache
        $this->modelCache->set($model);

        $stmt = $this->prepare($data[0], $data[1]);

        return $stmt->execute();
    }

    /**
     * @param AbstractQueryBuilder $queryBuilder
     *
     * @return $this
     */
    public function setQueryBuilder(AbstractQueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;

        return $this;
    }

    /**
     * @return \chilimatic\lib\database\ORM\querybuilder\AbstractQueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }

    /**
     * @param $lazyLoading
     *
     * @return $this
     */
    public function setLazyLoading($lazyLoading)
    {
        $this->lazyLoading = (bool)$lazyLoading;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getLazyLoading()
    {
        return $this->lazyLoading;
    }

    /**
     * @param $db
     *
     * @return $this
     */
    public function setDb($db)
    {
        $this->db = $db;

        return $this;
    }

    /**
     * @return \chilimatic\lib\database\AbstractDatabase
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * @return boolean
     */
    public function isUseCache()
    {
        return $this->useCache;
    }

    /**
     * @param boolean $useCache
     *
     * @return $this
     */
    public function setUseCache($useCache)
    {
        $this->useCache = (bool)$useCache;

        return $this;
    }

}