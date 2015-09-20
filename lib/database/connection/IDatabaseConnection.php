<?php
namespace chilimatic\lib\database\connection;

/**
 * Interface IDatabaseConnection
 *
 * @package chilimatic\lib\database\connection
 */
interface IDatabaseConnection
{

    /**
     * amount of default reconnect tries
     *
     * @var int
     */
    const MAX_DEFAULT_RECONNECTS = 3;

    /**
     * <p>
     *  since different databases need to implement different Connections this is a rather
     *  generic interface
     * </p>
     *
     * @param IDatabaseConnectionSettings $connectionSettings
     */
     public function __construct(IDatabaseConnectionSettings $connectionSettings);

    /**
     * checks if the connectionSettings are valid
     *
     * @return bool
     */
    public function connectionSettingsAreValid();

    /**
     * prepares the different connection meta data so
     * the validator and other processes can be triggered
     *
     * @return mixed
     */
    public function prepareConnectionMetaData();


    /**
     * tries to reconnect to the database if the connection is lost
     *
     * @return mixed
     */
    public function reconnect();
}