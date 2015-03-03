<?php
/**
 *
 * @author j
 * Date: 12/22/14
 * Time: 3:45 PM
 *
 * File: DatabaseInterface.php
 */
namespace chilimatic\lib\database;

/**
 * Interface DatabaseInterface
 *
 * @package chilimatic\lib\database
 */
interface DatabaseInterface
{
    /**
     * @param string $query
     *
     * @return mixed
     */
    public function query($query);

    /**
     * @return mixed
     */
    public function lastQuery();

    /**
     * @param string $query
     *
     * @return mixed
     */
    public function execute($query);

    /**
     * @return mixed
     */
    public function prepare();
}