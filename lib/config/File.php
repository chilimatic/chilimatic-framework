<?php
/**
 * Created by JetBrains PhpStorm.
 * User: j
 * Date: 26.10.13
 * Time: 17:53
 *
 */

namespace chilimatic\lib\config;

use chilimatic\lib\exception\ConfigException;

/**
 * Class self
 * @package chilimatic\lib\config
 */
class File extends AbstractConfig
{
    /**
     * @var string
     */
    const CONFIG_PATH_INDEX = 'config_path';

    /**
     * extension for config files
     *
     * @var string
     */
    CONST FILE_EXTENSION = 'cfg';


    /**
     * @var string
     */
    private $config_path;


    /**
     * @param array $param
     *
     * @throws ConfigException
     * @throws \Exception
     */
    public function __construct($param = array())
    {
        // set the main node on which all other nodes should be appended
        $this->mainNode = new Node(null, self::MAIN_NODE_KEY, null);

        // add custom parameters
        if (is_array($param) && count($param)) {
            // set the given parameters
            foreach ($param as $key => $value)
            {
                $node = new Node($this->mainNode, $key, $value, self::INIT_PARAMETER);
                $this->mainNode->addChild($node);
            }
        }

        // get the path of the config if the path has not been set
        if ( !($this->config_path = $this->get(self::CONFIG_PATH_INDEX)) ) {
            throw new ConfigException('no path for configfiles has been set');
        }

        $this->_initHostId();

        $this->load($param);
    }


    /**
     * gets the current host id for the machine
     */
    private function _initHostId() {

        if ($this->get('host_id')) {
            return;
        }

        // if an apache is running use the http host of it
        if ( !empty( $_SERVER ['HTTP_HOST'] ) )
        {
            $this->mainNode->addChild(new Node($this->mainNode, 'host_id', $_SERVER ['HTTP_HOST']));
        }
        // else check if there are console parameters
        else
        {
            // split them via spaces
            foreach ( $GLOBALS ['argv'] as $param )
            {
                if (strpos($param, IConfig::CLI_COMMAND_DELIMITER ) === false) continue;
                // split the input into a key value pair
                $inp = (array) explode( IConfig::CLI_COMMAND_DELIMITER , $param );
                if ( strtolower(trim($inp[0])) == IConfig::CLI_HOST_VARIABLE )
                {
                    $this->mainNode->addChild(new Node($this->mainNode, 'host_id', (string) trim($inp[1])));
                    break;
                }
            }
            unset( $inp, $param );
        }
    }

    /**
     * gets the needed config files based on the
     * url or parameters given by the console
     *
     * @return array
     */
    protected function _getConfigSet()
    {

        if (empty($_SERVER) && empty($GLOBALS['argv'])) {
            return [];
        }

        // default config for all of them
        $_config_set = array();
        $host_id = $this->get('host_id');

        /**
         * if there's a specific port remove the port
         *
         * @todo keep in mind that maybe someone needs a port specific behaviour for his app
         */
        if ( ($pos = strpos( (string) $host_id, ':' )) !== false )
        {
            $this->mainNode->addChild(new Node($this->mainNode, 'host_id', ( string ) substr( $host_id, 0, $pos )));
        }


        // split up the server host_id to an array
        $id_part_list = (array) explode( self::CONFIG_DELIMITER , $host_id );
        if (count($id_part_list) < 3) {
            array_unshift($id_part_list, self::HIERACHY_PLACEHOLDER);
        }

        // add an extra iteration so there is a specific config for a subdomain
        // and a generic one for all subdomains in this toplevel domain
        $count = (int) count( $id_part_list ) + 1;
        $i = 0;


        // we don't need to rebuild this standard strings all the time
        $config_del = self::HIERACHY_PLACEHOLDER . ( string ) self::CONFIG_DELIMITER;
        $extension = self::CONFIG_DELIMITER . self::FILE_EXTENSION;

        // the first config is the current host id + .cfg
        $self = (string)  $this->config_path . '/' . (string) implode(self::CONFIG_DELIMITER , $id_part_list) . (string) $extension;

        do
        {
            // shift the first position of the array
            array_shift( $id_part_list );

            // if the file exists add it to the "to be parsed list"
            if ( file_exists( $self ) && !in_array($self, $_config_set) ) {
                $_config_set [] = (string) $self;
            }

            $file_name = (string) (count($id_part_list) > 0  ? implode(self::CONFIG_DELIMITER , $id_part_list ) .(string) $extension : self::FILE_EXTENSION) ;
            $self = (string) $this->config_path . '/' . (string) $config_del . $file_name ;
            ++$i;
        } while ( $i < $count );

        /**
         * Config sort algorithm
         *
         * lambda function for sorting
         *
         * @param $a string
         * @param $b string
         *
         * @return int
         */
        uasort($_config_set, function($a, $b)
        {
            // include to the normal namespace

            if (substr_count($a, self::CONFIG_DELIMITER) == substr_count($a, self::CONFIG_DELIMITER))
            {
                if (strpos($a, self::HIERACHY_PLACEHOLDER) == true && strpos($b, self::HIERACHY_PLACEHOLDER) == false) {
                    return -1;
                }
                elseif (strpos($a, self::HIERACHY_PLACEHOLDER) == false && strpos($b, self::HIERACHY_PLACEHOLDER) == true) {
                    return 1;
                }

                return 0;
            }
            return (substr_count($a, self::CONFIG_DELIMITER) > substr_count($b, self::CONFIG_DELIMITER) ? -1 : 1);
        } );

        return $_config_set;
    }




    /**
     * loads the config settings
     *
     * @throws ConfigException
     *
     * @return bool
     */
    public function load($param = null)
    {
        // if there already has been a config set it means it already
        // has been loaded so why bother retrying ! this is not a dynamic language !
        $config_set = $this->get('config_set');
        if (count((array) $config_set) > 0) return true;

        // if the config set already exists don't parse it
        if (empty($config_set) && !($config_set = $this->_getConfigSet()))  {
            // set default config set for the default execution
            $config_set = [
                realpath("{$this->config_path}/" . (string) self::HIERACHY_PLACEHOLDER . (string) self::CONFIG_DELIMITER . (string) self::FILE_EXTENSION)
            ];
            $this->set('config_set', $config_set);
        }

        /**
         * create the total config parameter array and merge it recursive
         */
        try
        {
            if (empty($config_set) || !is_readable($config_set[0]) )
            {
                throw new ConfigException("No default config file declared {$this->config_path}/" . self::HIERACHY_PLACEHOLDER . (string) self::CONFIG_DELIMITER . (string) self::FILE_EXTENSION);
            }

            $configParser = new \chilimatic\lib\config\configfile\Parser();
            // first insert point
            $Node = null;
            foreach ($config_set as $config)
            {
                /**
                 * get the key for the config node
                 */
                $key = explode('/', $config);
                $key = substr(array_pop($key), 0, -4);

                $Node = new Node($this->mainNode, $key , $config, 'self');
                // add the config node
                $this->mainNode->addChild($Node);

                unset($key);
                $configParser->parse($this->getConfigFileContent($config), $Node);
            }

        } catch (ConfigException $e) {
            throw $e;
        }

        return true;
    }

    /**
     * reads the specific config file
     *
     * @param $self
     * @return array|string
     */
    private function getConfigFileContent($self)
    {
        // if empty just skip it
        if (!filesize( $self )) return array();

        // read the file handler
        $config = (string) file_get_contents($self);

        // check for linebreaks
        if (strpos( $config, "\n" ) === false) {
            $config = array(
                $config
            );
        } else {
            $config = (array) explode("\n", $config);
        }

        return $config;
    }

    /**
     * @param Node $node
     *
     * @return bool
     */
    public function saveConfig(Node $node = null)
    {
        if (!empty($node)) return $this->saveNode($node);

        return true;
    }

    /**
     * @return bool
     */
    public function saveNode(){
        return true;
    }
}