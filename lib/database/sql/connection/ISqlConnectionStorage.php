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
     * @param string $adapterName
     *
     * @return mixed
     */
    public function addConnectionBySetting(AbstractSqlConnectionSettings $connectionSettings, $adapterName = '');

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