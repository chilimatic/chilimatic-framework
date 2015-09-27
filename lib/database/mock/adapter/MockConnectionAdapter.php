<?php
namespace chilimatic\lib\database\mock\adapter;
use chilimatic\lib\database\connection\IDatabaseConnectionAdapter;
use chilimatic\lib\database\connection\IDatabaseConnectionSettings;

/**
 *
 * @author j
 * Date: 9/21/15
 * Time: 11:56 PM
 *
 * File: MockConnectionAdapter.php
 */


/**
 * Class MockConnectionAdapter
 *
 * @package chilimatic\lib\database\mock\adapter\mock
 */
class MockConnectionAdapter implements IDatabaseConnectionAdapter {
    /**
     * @var IDatabaseConnectionSettings
     */
    private $connectionSettings;

    /**
     * @var null
     */
    private $resource;

    public function __construct(IDatabaseConnectionSettings $connectionSettings)
    {
        $this->setConnectionSettings($connectionSettings);
    }

    public function initResource()
    {
        $this->resource = true;
    }

    /**
     * @return IDatabaseConnectionSettings
     */
    public function getConnectionSettings()
    {
        return $this->connectionSettings;
    }

    /**
     * @param IDatabaseConnectionSettings $connectionSettings
     *
     * @return $this
     */
    public function setConnectionSettings(IDatabaseConnectionSettings $connectionSettings)
    {
        $this->connectionSettings = $connectionSettings;

        return $this;
    }

    /**
     * @return null
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @param null $resource
     *
     * @return $this
     */
    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * @return array
     */
    public function getResult()
    {
        return [];
    }

    /**
     * @param $result
     *
     * @return $this
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }


}
