<?php
/**
 *
 * @author j
 * Date: 12/23/14
 * Time: 2:32 PM
 *
 * File: abstractquerybuilder.class.php
 */
namespace chilimatic\lib\database\sql\querybuilder;

use chilimatic\lib\cache\engine\CacheInterface;
use chilimatic\lib\database\AbstractDatabase;
use chilimatic\lib\database\sql\orm\AbstractModel;
use chilimatic\lib\parser\annotation\ORMParser;

/**
 * Class AbstractQueryBuilder
 *
 * @package chilimatic\lib\database\orm
 */
abstract class AbstractQueryBuilder implements IQueryBuilder
{
    /**
     * @var string
     */
    const TABLE_DATA_INDEX = 'tableData';

    /**
     * @var string
     */
    const RELATION_INDEX = 'relationList';


    /**
     * @var ORMParser
     */
    protected $parser;

    /**
     * @var \PDO
     */
    protected $db;


    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var array
     */
    protected $modelDataCache;

    /**
     * @var \chilimatic\lib\database\sql\querybuilder\meta\AbstractSQLTableData
     */
    protected $tableData;

    /**
     * @var $string
     */
    protected $position;

    /**
     * @var \chilimatic\lib\interfaces\IFlyWeightTransformer
     */
    protected $paramTransformer;

    /**
     * constructor
     *
     * @param CacheInterface $cache
     * @param AbstractDatabase $db
     */
    public function __construct(CacheInterface $cache = null, AbstractDatabase $db)
    {
        $this->parser = new ORMParser();
        $this->cache  = $cache;
    }

    /**
     * @param \ReflectionClass $reflection
     *
     * @return mixed|string
     */
    public function parseTableName(\ReflectionClass $reflection)
    {
        $hd = $this->parser->parse($reflection->getDocComment());

        if (!empty($hd[0])) {
            return $hd[1];
        }

        $table = substr($reflection->getName(), strlen($reflection->getNamespaceName()));

        return strtolower(str_replace('\\', '', $table));
    }

    /**
     * @param AbstractModel $model
     *
     * @return array
     */
    public function fetchCacheData(AbstractModel $model)
    {
        $this->position = get_class($model);
        if (!isset($this->modelDataCache[$this->position])) {
            $this->modelDataCache[$this->position] = $this->prepareCacheData($model);
        }

        return $this->modelDataCache[$this->position];
    }

    /**
     * @param AbstractModel $model
     *
     * @return array
     */
    public function prepareCacheData(AbstractModel $model)
    {
        /**
         * @todo change MySQLTableData to a usecase based one
         */
        $tableData  = clone $this->tableData;
        $reflection = new \ReflectionClass($model);
        $tableData->setTableName($this->parseTableName($reflection));

        return [
            'tableData'    => $tableData,
            'reflection'   => new \ReflectionClass($model),
            'relationList' => $this->extractRelations($reflection),
        ];
    }


    /**
     * @param \ReflectionClass $reflection
     *
     * @return bool|\SplFixedArray
     */
    public function extractRelations(\ReflectionClass $reflection)
    {
        $relation     = new \SplFixedArray();
        $propertyList = $reflection->getDefaultProperties();

        if ($this->cache && $res = $this->cache->get(md5(json_encode($propertyList)))) {
            return $res;
        }

        foreach ($propertyList as $name => $value) {
            $d = $this->parser->parse($reflection->getProperty($name)->getDocComment());
            if (!$d) {
                continue;
            }
            $relation->setSize($relation->getSize() + 1);
            $relation[$relation->count() - 1] = $d;
        }

        if ($this->cache) {
            $this->cache->set(md5(json_encode($propertyList)), $relation, 300);
        }

        return $relation;
    }

    /**
     * @return ORMParser
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * @param ORMParser $parser
     *
     * @return $this
     */
    public function setParser($parser)
    {
        $this->parser = $parser;

        return $this;
    }

    /**
     * @return \PDO
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * @param AbstractDatabase $db
     *
     * @return $this
     */
    public function setDb(AbstractDatabase $db)
    {
        $this->db = $db;

        return $this;
    }
}