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


namespace chilimatic\lib\database\orm;

use chilimatic\lib\database\AbstractDatabase;

/**
 * Class EntityManager
 *
 * @package chilimatic\lib\database\ORM
 */
class EntityManager {

    /**
     * @var string
     */
    const setterPrefix = 'set';

    /**
     * @var AbstractDatabase
     */
    public $db;

    /**
     * @var \chilimatic\lib\database\ORM\AbstractQueryBuilder
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
     * @var
     */
    private $modeCache;


    /**
     * @param AbstractDatabase $db
     * @param AbstractQueryBuilder $queryBuilder
     */
    public function __construct(AbstractDatabase $db, AbstractQueryBuilder $queryBuilder = null)
    {
        $this->db = $db;
        $this->queryBuilder = $queryBuilder;
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
         * @var \PDOStatement $stmt
         */
        $stmt = $this->db->prepare($query);
        $c = 0;
        foreach ($param as $value) {
            $c++;
            $n = "key{$c}";
            $$n = $value;
            $stmt->bindParam($c, $$n);
        }

        return $stmt;
    }

    /**
     * @param \PDOStatement $stmt
     * @param $model
     *
     * @return AbstractModel|EntityObjectStorage
     */
    public function executeQuery($model,\PDOStatement $stmt)
    {
        if ($stmt->execute()) {
            if ( $stmt->rowCount() > 1) {
                return $this->getList($model, $stmt);
            }
            return $this->hydrate($model, $stmt->fetchObject());
        }
        return $model;
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
        while($row = $stmt->fetchObject()) {
            $container->attach($this->hydrate(clone $model, $row));
        }

        return $container;
    }

    /**
     * @param AbstractModel $model
     * @param $param
     *
     * @return AbstractModel
     */
    public function findBy(AbstractModel $model, $param = [])
    {
        if ($this->useCache && $this->modelCache) {
            return $this->modelCache->fetchFromCache($model, $param);
        }

        $query = $this->queryBuilder->generateForModel($model, $param);
        $res = $this->executeQuery($model, $this->prepare($query, $param));

        if ($this->useCache && $this->modelCache) {
            $this->modelCache->storeInCache($model, $param, $res);
        }

        return $res;
    }




    /**
     * @param AbstractModel $model
     * @param $param
     *
     * @return AbstractModel
     */
    public function findOneBy(AbstractModel $model, $param = [])
    {
        $query = $this->queryBuilder->generateForModel($model, $param);
        $result = $this->executeQuery($model, $this->prepare($query, $param));

        if ($result instanceof AbstractModel) {
            return $result;
        }

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

        $p = (array) get_object_vars($row);

        foreach ($p as $property => $value) {
            $property = str_replace('_', '', $property);
            $m = self::setterPrefix. ucfirst($property);
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
        if (!$this->queryBuilder->getRelation()) {
            return $model;
        }

        foreach ($this->queryBuilder->getRelation() as $relation) {
            $injectionModel = new $relation[1]();
            if (strpos($relation[1], '\\') !== false) {
                $tmp = explode('\\', $relation[1]);
                $property = array_pop($tmp);
            } else {
                $property = $relation[1];
            }
            $m = self::setterPrefix . $property;

            /**
             * @todo implement dynamic mappings from doc head
             */
            if (!$this->lazyLoading) {
                $this->queryBuilder->generateForModel($injectionModel, ['id' => $relation[0]]);
            }

            $model->$m($injectionModel);
        }

        return $model;
    }


    /**
     *
     */
    public function getCustomQuery(){

    }

    /**
     * @param AbstractQueryBuilder $queryBuilder
     *
     * @return $this
     */
    public function setQueryBuilder(\chilimatic\lib\database\ORM\AbstractQueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
        return $this;
    }

    /**
     * @return \chilimatic\lib\database\ORM\AbstractQueryBuilder
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
        $this->lazyLoading = (bool) $lazyLoading;
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
        $this->useCache = (bool) $useCache;

        return $this;
    }

}