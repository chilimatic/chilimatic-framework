<?php
namespace chilimatic\lib\database\sql\connection;
/**
 *
 * @author j
 * Date: 9/20/15
 * Time: 8:20 PM
 *
 * File: ISQLConnectionSettings.php
 */

interface ISQLConnectionSettings {

    /**
     * @param $host
     * @param $username
     * @param $password
     * @param null $database
     * @param array $settingList
     */
    public function __construct($host, $username, $password, $database = null, $settingList = []);

    /**
     * @param $host
     * @param $username
     * @param $password
     * @param null $database
     * @param array $settingList
     *
     * @return mixed
     */
    public function setConnectionParam($host, $username, $password, $database = null, $settingList = []);


    /**
     * @return \Generator
     */
    public function getParameterGenerator();
}