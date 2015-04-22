<?php
/**
 *
 * @author j
 * Date: 3/30/15
 * Time: 10:14 PM
 *
 * File: MySQLTableData.php
 */
namespace chilimatic\lib\database\orm\querybuilder\meta;

use chilimatic\lib\database\AbstractDatabase;

/**
 * Class TableData
 *
 * @package chilimatic\lib\database\orm
 */
class MySQLTableData
{
    /**
     * @var string
     */
    private $tableName;

    /**
     * @var array
     */
    private $columnNames;

    /**
     * @var array
     */
    private $columnNamesWithPrefix;

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
     * @var array
     */
    private $primaryKey = [];


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
    public function getPrimaryKey()
    {
        if ($this->primaryKey) {
            return $this->primaryKey;
        }


        foreach ($this->getColumnData() as $data) {
            if (!empty($data['Key']) && $data['Key'] == 'PRI') {
                $this->primaryKey[] = $data['Field'];
            }
        }

        return $this->primaryKey;
    }

    /**
     * @param $columName
     *
     * @return null
     */
    public function getColumnNameWithPrefix($columName)
    {
        if (!isset($this->columnNamesWithPrefix[$columName])) {
            return null;
        }

        return $this->columnNamesWithPrefix[$columName];
    }


    /**
     * @return array
     */
    public function getColumnNamesWithPrefix()
    {
        if (empty($this->columnNamesWithPrefix)) {
            $this->fetchLazy();
        }

        return $this->columnNamesWithPrefix;
    }

    /**
     * fills in the column data
     */
    private function fetchLazy()
    {
        if ($this->columnData && $this->columnNamesWithPrefix && $this->columnNames){
            return;
        }
        $this->columnNames = [];
        $this->columnNamesWithPrefix = [];
        foreach ($this->getColumnData() as $value) {
            $this->columnNamesWithPrefix[$value['Field']] = "`$this->prefix`.`{$value['Field']}`";
            $this->columnNames[] = $value['Field'];
        }
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

    public function getTableNameWithPrefix() {
        return "$this->tableName `$this->prefix`";
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
    public function getColumnNames()
    {
        if (!$this->columnNames) {
            $this->getColumnData();
            $this->fetchLazy();
        }

        return $this->columnNames;
    }

    /**
     * @param array $columnNames
     *
     * @return $this
     */
    public function setColumnsNames($columnNames)
    {
        $this->columnNames = $columnNames;

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