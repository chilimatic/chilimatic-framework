<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 02.11.13
 * Time: 12:36
 */

namespace chilimatic\lib\config;

use chilimatic\lib\exception\ConfigException;

/**
 * Class Config_Ini
 * @package chilimatic\lib\config
 */
class Ini extends AbstractConfig
{

    /**
     * name of the config file
     *
     * @var string
     */
    public $configFile = '';

    /**
     * scanner mode
     *
     * @var int
     */
    public $scannerMode = null;

    /**
     * process sections
     *
     * @var bool
     */
    public $processSections = null;

    /**
     * @param null $param
     *
     * @throws ConfigException
     * @throws \Exception
     *
     * @return void
     */
    public function load( $param = null ){
        try
        {
            if ( empty($param['file']) ) {
                throw new ConfigException(_('No config file was give please, the parameter '. $param, 0, 1, __METHOD__, __LINE__));
            }
            $this->configFile = (string) $param['file'];

            if ( isset($param['process-sections']) ) {
                $this->processSections = (bool) $param['process-sections'];
            }
            if ( isset($param['scanner-mode']) ) {
                $this->scannerMode = (int) $param['scanner-mode'];
            }

            $data = parse_ini_file($this->configFile, $this->processSections, $this->scannerMode);

            $this->mainNode = new Node(null, IConfig::MAIN_NODE_KEY);
            foreach ($data as $key => $group)
            {
                if( !is_array($group) )
                {
                    $newNode = new Node($this->mainNode, $key, $group);
                    $this->mainNode->addChild($newNode);
                    continue;
                }

                $newNode = new Node($this->mainNode, $key, $key);

                foreach ($group as $name => $value)
                {
                    $childNode = new Node($newNode, $name, $value);
                    $newNode->addChild($childNode);
                }
                $this->mainNode->addChild($newNode);
            }
        }
        catch (ConfigException $e)
        {
            throw $e;
        }
    }

    /**
     * deletes a config node
     *
     * @param string $id
     * @return mixed
     */
    public function delete($id = ""){
        //@todo think of implementation
    }

    /**
     * saves the specified config
     *
     * @param Node $node
     * @internal param $array ;
     *
     * @return mixed
     */
    function saveConfig(Node $node = null){

    }
}