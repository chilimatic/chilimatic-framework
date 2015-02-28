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
class ConfigIni extends AbstractConfig{

    /**
     * name of the config file
     *
     * @var string
     */
    public $config_file = '';

    /**
     * scanner mode
     *
     * @var int
     */
    public $scanner_mode = null;

    /**
     * process sections
     *
     * @var bool
     */
    public $process_sections = null;

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

            if ( isset($param['process_sections']) ) {
                $this->process_sections = (bool) $param['process_sections'];
            }
            if ( isset($param['scanner_mode']) ) {
                $this->scanner_mode = (int) $param['scanner_mode'];
            }

            $data = parse_ini_file($this->config_file, $this->process_sections, $this->scanner_mode);

            $this->main_node = new ConfigNode(null, 'main_node');
            foreach ($data as $key => $group)
            {
                if( !is_array($group) )
                {
                    $main_node = new ConfigNode($this->main_node, $key, $group);
                    $this->main_node->addChild($main_node);
                    continue;
                }

                $main_node = new ConfigNode($this->main_node, $key, $key);

                foreach ($group as $name => $value)
                {
                    $node = new ConfigNode($main_node, $name, $value);
                    $main_node->addChild($node);
                }
                $this->main_node->addChild($main_node);
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
    public function delete($id = ''){
        //@todo think of implementation
    }

    /**
     * saves the specified config
     *
     * @param ConfigNode $node
     * @internal param $array ;
     *
     * @return mixed
     */
    function saveConfig(ConfigNode $node = null){

    }
}