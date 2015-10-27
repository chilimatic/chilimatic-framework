<?php
namespace chilimatic\lib\database\sql\connection;

use chilimatic\lib\database\connection\IDatabaseConnection;
use chilimatic\lib\database\connection\IDatabaseConnectionAdapter;
use chilimatic\lib\database\connection\IDatabaseConnectionSettings;
use chilimatic\lib\database\sql\mysql\connection\MySQLConnectionSettings;
use chilimatic\lib\exception\DatabaseException;
use chilimatic\lib\interfaces\IFlyWeightValidator;
use chilimatic\lib\interpreter\operator\InterpreterOperatorFactory;
use chilimatic\lib\parser\annotation\AnnotationValidatorParser;
use chilimatic\lib\validator\AnnotationPropertyValidatorFactory;

/**
 * Class AbstractSqlConnection
 *
 * @package chilimatic\lib\database\sql
 */
abstract class AbstractSQLConnection implements IDatabaseConnection, ISQLConnection {

    /**
     * if it's active (in use)
     *
     * @var bool
     */
    protected $active = false;

    /**
     * the socket
     *
     * @var bool
     */
    private $socket;

    /**
     * @var int
     */
    private $lastPing;

    /**
     * amount of current reconnects
     *
     * @var int
     */
    private $reconnectCount;

    /**
     * connection Role
     * @var int
     */
    private $connectionRole;

    /**
     * amount of max reconnects
     *
     * @var int
     */
    private $maxReconnects = self::MAX_DEFAULT_RECONNECTS;

    /**
     * current status of connection
     *
     * @var bool
     */
    private $connected = false;

    /**
     * the connection
     *
     * @var IDatabaseConnectionAdapter
     */
    private $dbAdapter;


    /**
     * @param IDatabaseConnectionSettings $connectionSettings
     * @param string $adapterName
     */
    public function __construct(IDatabaseConnectionSettings $connectionSettings, $adapterName = '') {
        // initializes the needed steps for the Connection
        $this->prepareAndInitializeAdapter($connectionSettings, $adapterName);
    }

    /**
     * @param IDatabaseConnectionSettings $connectionSettings
     * @param $adapterName
     *
     * @throws DatabaseException
     *
     * @return mixed
     */
    abstract public function prepareAndInitializeAdapter(IDatabaseConnectionSettings $connectionSettings, $adapterName);

    /**
     * a database connection needs certain parameters to work
     *
     * Host | Username | Password these are the minimum requirements which have to be checked
     * the secondary parameters like database and port need to be checked if they're set as well
     *
     * @return bool
     */
    public function connectionSettingsAreValid()
    {
        // if there is no adapter how can we initialize the correct validator to check it
        if (!$this->getDbAdapter()) {
            return false;
        }

        /**
         * @var MySQLConnectionSettings $connectionSettings
         */
        $connectionSettings = $this->getDbAdapter()->getConnectionSettings();
        if (!$connectionSettings) {
            return false;
        }

        $validatorFactory = new AnnotationPropertyValidatorFactory(
            new AnnotationValidatorParser()
        );


        $resultSet = [];
        /**
         * @var $property \ReflectionProperty
         */
        foreach ($connectionSettings->getParameterGenerator() as $property) {

            $validatorSetList = $validatorFactory->make($property);
            if (!$validatorSetList) {
                continue;
            }

            /**
             * @var IFlyweightValidator $validator
             */
            foreach ($validatorSetList as &$validatorSet) {
                $property->setAccessible(true); //

                // if the field is not mandatory and null it will be set as true
                if ($validatorSet[AnnotationValidatorParser::INDEX_MANDATORY] == false && $property->getValue($connectionSettings) === null) {
                    $validatorSet[AnnotationPropertyValidatorFactory::INDEX_RESULT] = true;
                } else {
                    $validatorSet[AnnotationPropertyValidatorFactory::INDEX_RESULT] =
                        $validatorSet[AnnotationValidatorParser::INDEX_INTERFACE]($property->getValue($connectionSettings));
                }
                $validatorSet['value'] = $property->getValue($connectionSettings);
                $validatorSet['name'] = $property->getName();
            }
            $resultSet[] = $validatorSetList;
        }
        unset($validatorSet, $property, $validatorFactory, $validatorSetList);


        $translator = function ($operator, $result_old, $result_new) {
            static $operatorFactory;

            if (!$operatorFactory) {
                $operatorFactory = new InterpreterOperatorFactory();
            }

            switch ($operator) {
                case '&':
                    return $operatorFactory->make('binary\InterpreterBinaryAnd', null)->operate($result_old, $result_new);
                    break;
                case '|':
                    return $operatorFactory->make('binary\InterpreterBinaryOr', null)->operate($result_old, $result_new);
                    break;
                case '^':
                    return $operatorFactory->make('binary\InterpreterBinaryXOr', null)->operate($result_old, $result_new);
                    break;
            }
        };

        $c = count($resultSet)-1;
        for ($i = 0; $i < $c; $i++) {
            $c2 = count($resultSet[$i]);
            $setValue = true;
            for ($x = 0; $x < $c2; $x++) {
                $result = $resultSet[$i][$x][AnnotationPropertyValidatorFactory::INDEX_RESULT];
                $expectation = $resultSet[$i][$x][AnnotationValidatorParser::INDEX_EXPECTED];
                $operator = $resultSet[$i][$x][AnnotationValidatorParser::INDEX_OPERATOR];

                $setValue &= $translator($operator, $result, $expectation);

            }
            if ($setValue == false) {
                return false;
            }
        }

        return true;
    }


    /**
     * @return mixed
     */
    abstract public function ping();


    /**
     * @return mixed
     */
    abstract public function reconnect();


    /**
     * @return boolean
     */
    public function isActive()
    {
        return (bool) $this->active;
    }

    /**
     * @param boolean $active
     *
     * @return $this
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isSocket()
    {
        return $this->socket;
    }

    /**
     * @param boolean $socket
     *
     * @return $this
     */
    public function setSocket($socket)
    {
        $this->socket = $socket;

        return $this;
    }

    /**
     * increments the reconnect counter
     */
    public function increaseReconnectCount() {
        $this->reconnectCount++;
    }

    /**
     * @return int
     */
    public function getLastPing()
    {
        return $this->lastPing;
    }

    /**
     * @param int $lastPing
     *
     * @return $this
     */
    public function setLastPing($lastPing)
    {
        $this->lastPing = $lastPing;

        return $this;
    }

    /**
     * @return int
     */
    public function getReconnectCount()
    {
        return $this->reconnectCount;
    }

    /**
     * @param int $reconnectCount
     *
     * @return $this
     */
    public function setReconnectCount($reconnectCount)
    {
        $this->reconnectCount = (int) $reconnectCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxReconnects()
    {
        return $this->maxReconnects;
    }

    /**
     * @param int $maxReconnects
     *
     * @return $this
     */
    public function setMaxReconnects($maxReconnects)
    {
        $this->maxReconnects = (int) $maxReconnects;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isConnected()
    {
        return $this->connected;
    }

    /**
     * @param boolean $connected
     *
     * @return $this
     */
    public function setConnected($connected)
    {
        $this->connected = $connected;

        return $this;
    }

    /**
     * @return AbstractSQLConnectionAdapter
     */
    public function getDbAdapter()
    {
        return $this->dbAdapter;
    }

    /**
     * @param IDatabaseConnectionAdapter $dbAdapter
     *
     * @return $this
     */
    public function setDbAdapter(IDatabaseConnectionAdapter $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;

        return $this;
    }

    /**
     * @return int
     */
    public function getConnectionRole()    {
        return $this->connectionRole;
    }

    /**
     * @param int $connectionRole
     *
     * @return $this
     */
    public function setConnectionRole($connectionRole)
    {
        $this->connectionRole = $connectionRole;

        return $this;
    }
}