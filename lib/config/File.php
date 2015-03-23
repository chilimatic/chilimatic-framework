<?php
/**
 * Created by JetBrains PhpStorm.
 * User: j
 * Date: 26.10.13
 * Time: 17:53
 *
 */

namespace chilimatic\lib\config;

use chilimatic\lib\exception\Exception_Config;


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
     * default fallback path
     *
     * @var string
     */
    CONST INCLUDE_PATTERN = '/lib';

    /**
     * default pattern for the path only the config within shop_includes shall
     * be used
     *
     * @var string
     */
    CONST PATH_PATTERN = '/app/config';

    /**
     * extension for config files
     *
     * @var string
     */
    CONST FILE_EXTENSION = 'cfg';



    /**
     * constructor
     *
     * @param array $param
     */
    public function __construct($param = array())
    {
        // set the main node on which all other nodes should be appended
        $this->main_node = new Node(null, self::MAIN_NODE_KEY, null);

        // add custom parameters
        if (is_array($param) && count($param)) {
            // set the given parameters
            foreach ($param as $key => $value)
            {
                $node = new Node($this->main_node, $key, $value, self::INIT_PARAMETER);
                $this->main_node->addChild($node);
            }
        }

        // get the path of the config if the path has not been set
        if ( !$this->get(self::CONFIG_PATH_INDEX) ) {
            $this->_getConfigPath();
        }

        $this->_initHostId();

        $this->load();
    }


    /**
     * gets the current host id for the machine
     */
    private function _initHostId() {
        // if an apache is running use the http host of it
        if ( !empty( $_SERVER ['HTTP_HOST'] ) )
        {
            $this->main_node->addChild(new Node($this->main_node, 'host_id', $_SERVER ['HTTP_HOST']));
        }
        // else check if there are console parameters
        else
        {
            // split them via spaces
            foreach ( $GLOBALS ['argv'] as $param )
            {
                if (strpos($param, IConfig::CLI_COMMAND_DELIMITER) === false) continue;
                // split the input into a key value pair
                $inp = ( array ) explode( IConfig::CLI_COMMAND_DELIMITER , $param );
                if ( strtolower( trim( $inp [0] ) ) == IConfig::CLI_HOST_VARIABLE )
                {
                    $this->main_node->addChild(new Node($this->main_node, 'host_id', ( string ) trim( $inp [1] )));
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

        if ( empty( $_SERVER ) && empty( $GLOBALS ['argv'] ) )
        {
            return array();
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
            $this->main_node->addChild(new Node($this->main_node, 'host_id', ( string ) substr( $host_id, 0, $pos )));
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

        $config_path = $this->get(self::CONFIG_PATH_INDEX);
        // we don't need to rebuild this standard strings all the time
        $config_del = self::HIERACHY_PLACEHOLDER . ( string ) self::CONFIG_DELIMITER;
        $extension = self::CONFIG_DELIMITER . self::FILE_EXTENSION;

        // the first config is the current host id + .cfg
        $self = (string)  $config_path . '/' . (string) implode(self::CONFIG_DELIMITER , $id_part_list) . (string) $extension;

        do
        {
            // shift the first position of the array
            array_shift( $id_part_list );

            // if the file exists add it to the "to be parsed list"
            if ( file_exists( $self ) && !in_array($self, $_config_set) ) {
                $_config_set [] = (string) $self;
            }

            $file_name = (string) (count($id_part_list) > 0  ? implode( self::CONFIG_DELIMITER , $id_part_list ) .(string) $extension : self::FILE_EXTENSION ) ;
            $self = (string) $this->get(self::CONFIG_PATH_INDEX) . '/' . ( string ) $config_del . $file_name ;
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
        uasort( $_config_set, function ( $a, $b )
        {
            // include to the normal namespace

            if ( substr_count( $a, self::CONFIG_DELIMITER ) == substr_count( $a, self::CONFIG_DELIMITER ) )
            {
                if ( strpos( $a, self::HIERACHY_PLACEHOLDER ) == true && strpos( $b, self::HIERACHY_PLACEHOLDER ) == false ) {
                    return -1;
                }
                elseif ( strpos( $a, self::HIERACHY_PLACEHOLDER ) == false && strpos( $b, self::HIERACHY_PLACEHOLDER ) == true ) {
                    return 1;
                }

                return 0;
            }
            return (substr_count( $a, self::CONFIG_DELIMITER ) > substr_count( $b, self::CONFIG_DELIMITER ) ? -1 : 1);
        } );

        return $_config_set;
    }




    /**
     * loads the config settings
     *
     * @throws Exception_Config
     *
     * @return bool
     */
    public function load()
    {
        // check if the config path has been set
        if ( !$this->get(self::CONFIG_PATH_INDEX) ) return false;

        // if there already has been a config set it means it already
        // has been loaded so why bother retrying ! this is not a dynamic language !
        if ( count((array) $this->get('config_set')) > 0 ) return true;
        $config_path = $this->get(self::CONFIG_PATH_INDEX);
        $config_set = $this->get('config_set');

        // if the config set already exists don't parse it
        if ( empty($config_set) && !($config_set = $this->_getConfigSet()))  {
            // set default config set for the default execution
            $config_set = array(
                "{$config_path}" . (string) self::HIERACHY_PLACEHOLDER . (string) self::CONFIG_DELIMITER . ( string ) self::FILE_EXTENSION
            );
            $this->set('config_set', $config_set);
        }

        /**
         * create the total config parameter array and merge it recursive
         */
        try
        {
            if ( empty( $config_set ) || !is_readable( $config_set [0] ) )
            {
                throw new Exception_Config( "No default config file declared {$config_path}/" . self::HIERACHY_PLACEHOLDER . (string) self::CONFIG_DELIMITER . ( string ) self::FILE_EXTENSION );
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

                $Node = new Node($this->main_node, $key , $config, 'self');
                // add the config node
                $this->main_node->addChild($Node);

                unset($key);
                $configParser->parse($this->getConfigFileContent($config), $Node);
            }

        } catch (Exception_Config $e) {
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
     * self recursive function call to match the path pattern to
     * the current list of possible directories based on the execution path
     *
     * @param string $n path_part
     * @param array $tmp path_array
     *
     * @return boolean|string
     */
    protected function _extractSubPath( $n, $tmp )
    {

        if ( empty( $tmp ) || empty( $n ) ) return false;

        if ( strpos( $n, $tmp [0] ) === false ) return false;

        if ( count( $tmp ) == 1 && is_dir( "$n/{$tmp[0]}" ) )
        {
            return "$n/{$tmp[0]}";
        }
        elseif ( is_dir( "$n/{$tmp[0]}" ) )
        {
            return $this->_extractSubPath( "$n/{$tmp[0]}", $tmp );
        }

        return false;
    }



    /**
     * extracts the config path from the execution path
     *
     * @return boolean string Ambigous
     */
    protected function _getConfigPath()
    {
        /**
         * if the shop_includes path can be found from the current directory
         */
        if ( ($cut = strpos( __DIR__, self::INCLUDE_PATTERN )) !== false )
        {
            $this->main_node
                ->addChild(new Node(
                    $this->main_node,
                        self::CONFIG_PATH_INDEX,
                    (substr( __DIR__, 0, $cut ) . self::PATH_PATTERN))
                );

            return true;
        }

        // get the current path of execution
        $execution_path = getcwd();

        // get the class constant
        $tmp = self::PATH_PATTERN;

        // if there is no pattern match just quit
        if ( empty( $tmp ) ) return false;

        // if we're on the docroot lets see if it's in the current path
        if ( file_exists( "$execution_path/" . self::PATH_PATTERN ) )
        {
            $config_path = new Node($this->main_node, self::CONFIG_PATH_INDEX, ( string ) "$execution_path/" . self::PATH_PATTERN);
            $this->main_node->addChild($config_path);
            return true;
        }

        // check if it's a multiple path otherwise convert it to an array anyway
        if ( strstr( $tmp, '/' ) !== false )
        {
            $tmp_path = (array) explode( '/', $tmp );
        }
        else
        {
            $tmp_path = array(
                $tmp
            );
        }

        // this is the last resort go downwards till it hits / for the config
        $directory_depth = ( array ) explode( '/', $execution_path );
        $count = ( int ) count( $directory_depth );

        for( $i = 0; $i < $count; $i++ )
        {
            $current_dir = ( string ) array_shift( $directory_depth );
            $parent_dir = ( string ) "/" . implode( '/', $directory_depth );

            // open dir handle and lets try to find the directory
            $dh = @opendir( "$parent_dir/$current_dir" );
            if ( !$dh ) continue;

            // start the loop based on the directory
            while ( ($n = ( string ) readdir( $dh )) !== false )
            {
                // check if it's an directory
                if ( !is_dir( $n ) ) continue;

                // start self recursion on the path pattern
                if ( ($path = $this->_extractSubPath( $execution_path . '/' . $n, $tmp_path )) !== false )
                {
                    // if there's a hit for the config path
                    $config_path = new Node($this->main_node, self::CONFIG_PATH_INDEX, ( string ) $path);
                    $this->main_node->addChild($config_path);
                    return true;
                }
            }
        }

        return false;
    }

    public function saveConfig(Node $node = null)
    {
        if (!empty($node)) return $this->saveNode($node);

        return true;
    }

    public function saveNode(){
        return true;
    }
}