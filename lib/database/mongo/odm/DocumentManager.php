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
     * DocumentManager constructor.
     *
     * @param \MongoClient $connection
     * @param AnnotationOdmParser $parser
     */
    public function __construct(\MongoClient $connection, AnnotationOdmParser $parser)
    {
        $this->connection   = $connection;
        $this->modelCache   = new ModelCache();
        $this->parser       = $parser;
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
            $comment = $reflection->getDocComment();
            $this->annotationCache[$modelName] = $this->parser->parse($comment);
        }

        $configuration = $this->annotationCache[$modelName];

        return $configuration;
    }

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

        $configuration = $this->getModelConfiguration($modelName);
        $collection = $this->getCollectionBy($configuration);

        $resultSet = $collection->find($param);
        $result = new \SplObjectStorage();
        // clone is much faster than new
        $tpl = new $modelName();
        foreach ($resultSet as $data) {
            $nModel = $this->fillModel(clone $tpl, $data);
            $result->attach($nModel);
        }

        return $result;
    }

    private function fillModel(AbstractModel $model, array $data) {
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
     * @return AbstractModel
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

        return $this->fillModel(new $modelName(), $result);
    }

    /**
     * @param AbstractModel $model
     */
    public function delete(AbstractModel $model)
    {
        if ($this->modelCache->get($model)) {
            $this->modelCache->remove($model);
        }


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

        return $collection->insert($this->getModelData($model));;
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

}