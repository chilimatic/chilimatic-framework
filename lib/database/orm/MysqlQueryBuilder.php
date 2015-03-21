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
use chilimatic\lib\parser\ORMParser;

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
     * @var \ReflectionClass
     */
    private $reflection;

    /**
     * @var array
     */
    private $propertyList;

    /**
     * @var \SPLFixedArray
     */
    private $relation;

    /**
     * @var
     */
    private $table;

    /**
     * @var
     */
    private $model;

    /**
     * @var \chilimatic\lib\cache\engine\shmop
     */
    private $cache;

    /**
     * @var array
     */
    private $param;

    /**
     * @var ORMParser
     */
    private $parser;

    /**
     * init cache connection
     *
     * @param CacheInterface $cache
     */
    public function __construct(CacheInterface $cache = null)
    {
        $this->parser = new ORMParser();
        $this->relation = new \SplFixedArray();
    }

    /**
     * @param AbstractModel $model
     * @param array $param
     *
     * @return string
     */
    public function generateSelectForModel(AbstractModel $model, $param)
    {
        $this->reflection = new \ReflectionClass($model);
        $this->model = $model;
        $this->param = $param;
        $this->extractRelations();
        $this->checkRelations();

        return $this->_generateSelect();
    }

    /**
     *
     */
    public function getTableName()
    {
        $hd = $this->parser->parse($this->reflection->getDocComment());

        if (!empty($hd[0])) {
            $this->$hd[0] = $hd[1];
            return;
        } else {
            $table = substr($this->reflection->getName(), strlen($this->reflection->getNamespaceName()));
            $this->table = strtolower(str_replace('\\', '', $table));
        }
    }

    /**
     * @return string
     */
    public function generateCondition() {
        $str = ' WHERE ';
        foreach ($this->param as $key => $value) {
            if ($value) {
                $str .= " $key = ? AND";
            } else {
                $str .= " $key AND";
            }
        }

        return substr($str, 0, -3);
    }

    /**
     * @return string
     */
    public function _generateSelect(){
        return "SELECT " . ($this->propertyList ? implode(',', $this->propertyList) : '*') . " FROM $this->table ". $this->generateCondition();
    }

    /**
     * @return bool
     * @throws \ErrorException
     */
    public function checkRelations()
    {
        if (!$this->relation) return true;
        $this->relation->rewind();
        while($this->relation->valid() && $entry = $this->relation->current()) {
            if (!class_exists($entry[1])) {
                throw new \ErrorException($entry[1]. ' Relations Class does not exist!');
            }
            $this->relation->next();
        }
        return true;
    }

    /**
     * parses the doc header for relations
     *
     * @return bool
     */
    public function extractRelations()
    {
        $this->relation = new \SplFixedArray();
        $this->getTableName();

        $this->propertyList = $this->reflection->getDefaultProperties();

        if ($this->cache && $res = $this->cache->get(md5(json_encode($this->propertyList)))) {
            $this->relation = $res;
            return true;
        }

        foreach ($this->propertyList as $name => $value) {
            $d = $this->parser->parse($this->reflection->getProperty($name)->getDocComment());
            if ( !$d ) {
                continue;
            }
            $this->relation->setSize($this->relation->getSize() + 1);
            $this->relation[$this->relation->count()-1] = $d;
        }

        if ($this->cache){
            $this->cache->set(md5(json_encode($this->propertyList)), $this->relation, 300);
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
        // TODO: Implement generateInsertForModel() method.
    }


    /**
     * @param AbstractModel $model
     *
     * @return mixed
     */
    public function generateUpdateForModel(AbstractModel $model)
    {
        // TODO: Implement generateUpdateForModel() method.
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
     * @return \ReflectionClass
     */
    public function getReflection()
    {
        return $this->reflection;
    }

    /**
     * @param \ReflectionClass $reflection
     *
     * @return $this
     */
    public function setReflection($reflection)
    {
        $this->reflection = $reflection;

        return $this;
    }

    /**
     * @return array
     */
    public function getPropertyList()
    {
        return $this->propertyList;
    }

    /**
     * @param array $propertyList
     *
     * @return $this
     */
    public function setPropertyList($propertyList)
    {
        $this->propertyList = $propertyList;

        return $this;
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
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param mixed $table
     *
     * @return $this
     */
    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param mixed $model
     *
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;

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
    public function getParam()
    {
        return $this->param;
    }

    /**
     * @param array $param
     *
     * @return $this
     */
    public function setParam($param)
    {
        $this->param = $param;

        return $this;
    }


}