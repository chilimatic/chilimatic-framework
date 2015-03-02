<?php
namespace chilimatic\lib\route\map;
use chilimatic\lib\exception\Exception_Route;
use chilimatic\lib\route\Map;

class StrategyFactory implements StaticMapFactory {

    /**
     * @param $type
     * @param $config
     * @return MapClosure|MapObject|MapFunction|mixed
     */
    public static function make($type, $config) {
        switch ($type) {
            case Map::TYPE_O:
                return new MapObject($config);
            break;
            case Map::TYPE_F:
                //return new MFunction($config);
                break;
            case Map::TYPE_LF:
                //return new LambdaFunction($config);
                break;
            default:
                throw new Exception_Route("Invalid Call type" . __METHOD__);
                break;
        }
    }
}