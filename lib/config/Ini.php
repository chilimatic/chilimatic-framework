<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 02.11.13
 * Time: 12:36
 */

namespace chilimatic\lib\config;

use chilimatic\lib\exception\Exception_Config;

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
     * loads the config based on the type / source
     *
     * @param mixed $param
     *
     * @throws \chilimatic\lib\exception\Exception_Config
     * @throws \Exception
     * @return mixed
     */
    public function load( $param = null ){
        try
        {
            if ( empty($param['file']) ) {
                throw new Exception_Config(_('No config file was give please, the parameter '. $param, 0, 1, __METHOD__, __LINE__));
            }
            $this->config_file = (string) $param['file'];

            if ( isset($param['process-sections']) ) {
                $this->process_sections = (bool) $param['process-sections'];
            }
            if ( isset($param['scanner-mode']) ) {
                $this->scanner_mode = (int) $param['scanner-mode'];
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
        catch (Exception_Config $e)
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