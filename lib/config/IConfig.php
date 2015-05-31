<?php
/**
 * Created by JetBrains PhpStorm.
 * User: J
 * Date: 26.10.13
 * Time: 17:54
 * Interface to normalize different types of configs
 * so the user can switch the medium the config is saved with
 * but still has the same standard functionality available
 * within the system
 */

namespace chilimatic\lib\config;

/**
 * Interface Config_Interface
 * @package chilimatic\lib\config
 */
Interface IConfig {

    /**
     * root node key
     *
     * @var string
     */
    const MAIN_NODE_KEY = '*';

    /**
     * default placeholder for hierachy
     *
     * -> since windows cant use the *
     * it's "@" you can change it if you are
     * sure no one is working with windows
     *
     * @var string
     */
    const HIERACHY_PLACEHOLDER = '*';

    /**
     * commandline variable to identify the
     * correct host for the scripts
     *
     * @var string
     */
    const CLI_HOST_VARIABLE = 'host';

    /**
     * commandline delimiter that indicates an variable
     * assignment
     *
     * @var string
     */
    const CLI_COMMAND_DELIMITER = '=';


    /**
     * config delimiter maybe you don't like
     * the ".' delimiter for the config and wanna
     * glue the structure by coma or exclamation marks
     *
     * @var string
     */
    const CONFIG_DELIMITER = '.';

    /**
     * constructor
     *
     * @param mixed $param
     */
    public function __construct($param = null);

    /**
     * loads the config based on the type / source
     *
     * @return mixed
     */
    public function load();

    /**
     * deletes a config node
     *
     * @param string $key
     *
     * @return bool
     */
    public function delete($key = '');

    /**
     * gets a specific parameter
     *
     * @param $key
     * @return mixed
     */
    public function get($key);


    /**
     * gets a specific parameter
     *
     * @param $id
     * @return mixed
     */
    public function getbyId($id);

    /**
     * gets a specific parameter
     *
     * @param $id
     * @param $val
     *
     * @return mixed
     */
    public function setById($id, $val);

    /**
     * sets a specific parameter
     *
     * @param $key
     * @param $val
     * @return mixed
     */
    public function set($key, $val);

    /**
     * saves the specified config
     *
     * @param Node $node
     * @return mixed
     */
    public function saveConfig(Node $node = null);
}