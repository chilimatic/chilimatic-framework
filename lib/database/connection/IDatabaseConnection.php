<?php
namespace chilimatic\lib\database\connection;
use chilimatic\lib\exception\DatabaseException;

/**
 * Interface IDatabaseConnection
 *
 * @package chilimatic\lib\database\connection
 */
interface IDatabaseConnection
{

    /**
     * binary values for connection roles
     */
    const CONNECTION_ROLE_MASTER = 0b001;
    const CONNECTION_ROLE_SLAVE = 0b010;

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
     * @param string $adapterName
     */
     public function __construct(IDatabaseConnectionSettings $connectionSettings, $adapterName = '');

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
     * @throws DatabaseException
     *
     * @return mixed
     */
    public function prepareAndInitilizeAdapter(IDatabaseConnectionSettings $connectionSettings, $adapterName);


    /**
     * tries to reconnect to the database if the connection is lost
     *
     * @return mixed
     */
    public function reconnect();
}