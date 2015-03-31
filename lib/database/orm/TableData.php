<?php
/**
 *
 * @author j
 * Date: 3/30/15
 * Time: 10:14 PM
 *
 * File: TableData.php
 */
namespace chilimatic\lib\database\orm;
use chilimatic\lib\database\AbstractDatabase;

/**
 * Class TableData
 *
 * @package chilimatic\lib\database\orm
 */
class TableData
{
    /**
     * @var string
     */
    private $tableName;

    /**
     * @var array
     */
    private $columnsNames;

    /**
     * @var array
     */
    private $columnsNamesWithPrefix;

    /**
     * @var array
     */
    private $columnData;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var AbstractDatabase
     */
    private $db;

    /**
     * @param AbstractDatabase $db
     */
    public function __construct(AbstractDatabase $db) {
        $this->db = $db;
    }

    /**
     * @param string $tableName
     *
     * @return string
     */
    private function generatePrefix($tableName) {
        return substr(md5($tableName), 0, 4);
    }


    private function fetchTableMetaData()
    {
        if (!$this->db || !$this->tableName) {
            return;
        }

        /**
         * @var \PDOStatement $stmt
         */
        $stmt = $this->db->getDb()->query('desc ' . $this->tableName);
        $this->columnData = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    /**
     * @return array
     */
    public function getColumnsNamesWithPrefix()
    {
        if (!empty($this->columnsNamesWithPrefix)) {
            return $this->columnsNamesWithPrefix;
        }

        foreach ($this->getColumnData() as $value) {
            $this->columnsNamesWithPrefix[] = "`$this->prefix`.`{$value['Field']}`";
        }

        return $this->columnsNamesWithPrefix;
    }

    /**
     * @return AbstractDatabase
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


    /**
     * @return mixed
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param mixed $tableName
     *
     * @return $this
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
        $this->setPrefix($this->generatePrefix($tableName));
        return $this;
    }


    /**
     * @return array
     */
    public function getColumnsNames()
    {
        return $this->columnsNames;
    }

    /**
     * @param array $columnsNames
     *
     * @return $this
     */
    public function setColumnsNames($columnsNames)
    {
        $this->columnsNames = $columnsNames;

        return $this;
    }

    /**
     * @return array
     */
    public function getColumnData()
    {
        if (!$this->columnData) {
            $this->fetchTableMetaData();
        }

        return $this->columnData;
    }

    /**
     * @param array $columnData
     *
     * @return $this
     */
    public function setColumnData($columnData)
    {
        $this->columnData = $columnData;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param mixed $prefix
     *
     * @return $this
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }


}