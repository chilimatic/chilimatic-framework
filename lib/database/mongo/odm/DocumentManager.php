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


namespace chilimatic\lib\database\mongo\odm;
use chilimatic\lib\cache\handler\ModelCache;
use chilimatic\lib\database\ErrorLogTrait;
use chilimatic\lib\parser\annotation\AnnotationOdmParser;


/**
 * Class EntityManager
 *
 * @package chilimatic\lib\database\odm
 */
class DocumentManager
{
    /**
     * the different indexes of the mongoCollection return array
     */
    const RESULT_CONNECTION_ID_INDEX = 'connectionid';
    const RESULT_ERROR_INDEX         = 'err';
    const RESULT_OK_INDEX            = 'ok';
    const N_INDEX                    = 'n';

    /**
     * @trait
     */
    use ErrorLogTrait;

    /**
     * @var string
     */
    const setterPrefix = 'set';


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
     * @var
     */
    private $parser;

    /**
     * @var []
     */
    private $annotationCache = [];


    /**
     * @var []
     */
    private $modelPrototypeStorage;

    /**
     * @var []
     */
    private $lastResult;


    /**
     * DocumentManager constructor.
     *
     * @param \MongoClient $connection
     * @param AnnotationOdmParser $parser
     */
    public function __construct(\MongoClient $connection, AnnotationOdmParser $parser)
    {
        $this->connection           = $connection;
        $this->modelCache           = new ModelCache();
        $this->parser               = $parser;
        $this->modelTemplateStorage = [];
    }

    /**
     * @param string $modelName
     *
     * @return []
     */
    private function getModelConfiguration($modelName)
    {
        if (!isset($this->annotationCache[$modelName])) {
            $reflection = new \ReflectionClass($modelName);
            $comment    = $reflection->getDocComment();
            $this->annotationCache[$modelName] = $this->parser->parse($comment);
        }

        $configuration = $this->annotationCache[$modelName];
        return $configuration;
    }

    /**
     * @param $configuration
     *
     * @return \MongoCollection|null
     */
    private function getCollectionBy($configuration)
    {
        if (!$configuration) {
            return null;
        }

        $db = $this->connection->selectDB($configuration['db']);
        return $db->{$configuration['collection']};
    }

    /**
     * @param string $modelName
     * @param $param
     *
     * @return \SplObjectStorage
     */
    public function findBy($modelName, $param = [])
    {
        if (!class_exists($modelName, true)) {
            return new \SplObjectStorage();
        }

        $configuration  = $this->getModelConfiguration($modelName);
        $collection     = $this->getCollectionBy($configuration);

        $resultSet  = $collection->find($param);
        $result     = new \SplObjectStorage();
        // clone is much faster than new
        if (!$this->modelPrototypeStorage[$modelName]) {
            $this->modelTemplateStorage[$modelName] = new $modelName();
        }

        foreach ($resultSet as $data) {
            $nModel = $this->fillModel(
                clone $this->modelTemplateStorage[$modelName],
                $data
            );
            $result->attach($nModel);
        }

        return $result;
    }

    /**
     * @param AbstractModel $model
     * @param array $data
     *
     * @return AbstractModel
     */
    private function fillModel(AbstractModel $model, array $data)
    {
        if (!$data) {
            return $model;
        }

        $reflection = new \ReflectionClass($model);
        foreach($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            $property->setValue($model, $data[$property->getName()]);
            $property->setAccessible(false);
        }

        return $model;
    }

    /**
     * @param string $modelName
     * @param $param
     *
     * @return AbstractModel|null
     */
    public function findOneBy($modelName, $param = [])
    {
        if (!class_exists($modelName, true)) {
            return null;
        }

        $collection = $this->getCollectionBy(
            $this->getModelConfiguration($modelName)
        );
        $result = $collection->findOne($param);

        if (!$result) {
            return null;
        }

        // clone is much faster than new [speed over memory!]
        if (!$this->modelPrototypeStorage[$modelName]) {
            $this->modelTemplateStorage[$modelName] = new $modelName();
        }

        return $this->fillModel($this->modelTemplateStorage[$modelName], $result);
    }

    /**
     * @param AbstractModel $model
     *
     * @return array|bool
     */
    public function delete(AbstractModel $model)
    {
        $collection = $this->getCollectionBy(
            $this->getModelConfiguration(get_class($model))
        );

        $this->setLastResult(
            $collection->remove($this->getModelDataAsArray($model))
        );

        return (bool) ($this->lastResult) ? $this->lastResult[self::RESULT_OK_INDEX] : false;
    }

    /**
     * @param AbstractModel $model
     *
     * @return bool
     */
    public function persist(AbstractModel $model)
    {
        $collection = $this->getCollectionBy(
            $this->getModelConfiguration(get_class($model))
        );

        $this->setLastResult(
            $collection->insert($this->getModelDataAsArray($model))
        );

        return (bool) ($this->lastResult) ? $this->lastResult[self::RESULT_OK_INDEX] : false;
    }

    /**
     * @param AbstractModel $model
     *
     * @return array
     */
    public function getModelDataAsArray(AbstractModel $model)
    {
        $reflectionClass = new \ReflectionClass($model);
        $set = [];

        foreach ($reflectionClass->getProperties() as $property) {
            $property->setAccessible(true);
            $set[$property->getName()] = $property->getValue($model);
        }

        return $set;
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

    /**
     * @return array|null
     */
    public function getLastResult()
    {
        return $this->lastResult;
    }

    /**
     * @param array $lastResult
     *
     * @return $this
     */
    public function setLastResult(array $lastResult = null)
    {
        $this->lastResult = $lastResult;

        return $this;
    }
}