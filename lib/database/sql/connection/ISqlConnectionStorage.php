<?php
namespace chilimatic\lib\database\sql\connection;

/**
 *
 * @author j
 * Date: 9/21/15
 * Time: 12:12 AM
 *
 * File: ISqlConnectionStorage.php
 */

interface ISqlConnectionStorage
{

    public function __construct();

    /**
     * @param AbstractSqlConnection $connection
     *
     * @return mixed
     */
    public function addConnection(AbstractSqlConnection $connection);

    /**
     * @param AbstractSqlConnectionSettings $connectionSettings
     *
     * @return mixed
     */
    public function addConnectionBySetting(AbstractSqlConnectionSettings $connectionSettings);

    /**
     * @param $host
     * @param $username
     * @param $password
     * @param string $database
     * @param int $port
     * @param array $settingList
     *
     * @return mixed
     */
    public function addConnectionByParameters($host, $username, $password, $database = null, $port = null, $settingList = []);

    /**
     * @param AbstractSqlConnection $connection
     *
     * @return bool
     */
    public function findConnection(AbstractSqlConnection $connection);


    /**
     * @param AbstractSqlConnection $connection
     *
     * @return true|null
     */
    public function removeConnection(AbstractSqlConnection $connection);
}