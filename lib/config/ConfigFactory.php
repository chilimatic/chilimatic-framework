<?php
/**
 *
 * @author j
 * Date: 5/12/15
 * Time: 4:53 PM
 *
 * File: ConfigFactory.php
 */

namespace chilimatic\lib\config;

<<<<<<< HEAD
=======
/**
 * Class ConfigFactory
 * @package chilimatic\lib\config
 */
>>>>>>> 336a13ce09982d8ab14d6fadbb1d8dd97927b344
class ConfigFactory
{

    /**
     * Factory for creating Config objects
     *
     * the type specifies the Config Type like 'ini' or 'file' or another one implemented lateron
     *
     * @param string $type
     * @param array $param
     *
     * @throws \LogicException
     *
     * @return AbstractConfig
     */
    public static function make($type, $param = null)
    {
        if (!$type) {
<<<<<<< HEAD
            throw new \LogicException("The Config Type has to be specified ... $type is empty");
=======
            throw new \LogicException("The Config Type has to be specified ... \$type is empty");
>>>>>>> 336a13ce09982d8ab14d6fadbb1d8dd97927b344
        }

        $className = (string) __NAMESPACE__ . '\\' . (string) ucfirst($type);

        if (!class_exists( $className , true)) {
<<<<<<< HEAD
            throw new \LogicException("The Config Type has to be specified ... $className is empty");
=======
            throw new \LogicException("The Config class has to be implemented and accessible ... $className is not found");
>>>>>>> 336a13ce09982d8ab14d6fadbb1d8dd97927b344
        }

        return new $className($param);
    }
}