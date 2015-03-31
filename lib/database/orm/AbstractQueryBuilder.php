<?php
/**
 *
 * @author j
 * Date: 12/23/14
 * Time: 2:32 PM
 *
 * File: abstractquerybuilder.class.php
 */
namespace chilimatic\lib\database\orm;
use chilimatic\lib\cache\engine\CacheInterface;
use chilimatic\lib\database\AbstractDatabase;
use chilimatic\lib\parser\ORMParser;

/**
 * Class AbstractQueryBuilder
 *
 * @package chilimatic\lib\database\orm
 */
abstract class AbstractQueryBuilder
{
    /**
     * @var ORMParser
     */
    protected $parser;

    /**
     * @var \PDO
     */
    protected $db;

    /**
     * @var array
     */
    protected $tableData;


    /**
     * @var array
     */
    protected $param;


    /**
     * constructor
     *
     * @param CacheInterface $cache
     * @param AbstractDatabase $db
     */
    public function __construct(CacheInterface $cache = null, AbstractDatabase $db) {
        $this->parser = new ORMParser();
        $this->tableData = new TableData($db);
    }

    /**
     * @param AbstractModel $model
     * @param array $param
     *
     * @return mixed
     */
    abstract public function generateSelectForModel(AbstractModel $model, $param);

    /**
     * @param AbstractModel $model
     *
     * @return mixed
     */
    abstract public function generateInsertForModel(AbstractModel $model);

    /**
     * @param AbstractModel $model
     *
     * @return mixed
     */
    abstract public function generateUpdateForModel(AbstractModel $model);

    /**
     * @param AbstractModel $model
     *
     * @return mixed
     */
    abstract public function generateDeleteForModel(AbstractModel $model);

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
        $this->tableData->setDb($db);
        return $this;
    }

    /**
     * @return array
     */
    public function getTableData()
    {
        return $this->tableData;
    }

    /**
     * @param array $tableData
     *
     * @return $this
     */
    public function setTableData($tableData)
    {
        $this->tableData = $tableData;

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