<?php
namespace chilimatic\lib\database\connection;
/**
 *
 * @author j
 * Date: 9/21/15
 * Time: 10:05 PM
 *
 * File: IDatabaseConnectionAdapter.php
 */



interface IDatabaseConnectionAdapter {

    /**
     * @param IDatabaseConnectionSettings $connectionSettings
     */
    public function __construct(IDatabaseConnectionSettings $connectionSettings);

    /**
     * @return mixed
     */
    public function initResource();

    /**
     * @return mixed
     */
    public function getResource();

    /**
     * @param $resource
     *
     * @return mixed
     */
    public function setResource($resource);

    /**
     * @return mixed
     */
    public function getResult();

    /**
     * @return mixed
     */
    public function setResult($result);
}