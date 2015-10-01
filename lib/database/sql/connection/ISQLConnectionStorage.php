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

interface ISQLConnectionStorage
{

    public function __construct();

    /**
     * @param AbstractSQLConnection $connection
     *
     * @return mixed
     */
    public function addConnection(AbstractSQLConnection $connection);

    /**
     * @param AbstractSQLConnectionSettings $connectionSettings
     * @param string $adapterName
     *
     * @return mixed
     */
    public function addConnectionBySetting(AbstractSQLConnectionSettings $connectionSettings, $adapterName = '');

    /**
     * @param AbstractSQLConnection $connection
     *
     * @return bool
     */
    public function findConnection(AbstractSQLConnection $connection);


    /**
     * @param AbstractSQLConnection $connection
     *
     * @return true|null
     */
    public function removeConnection(AbstractSQLConnection $connection);
}