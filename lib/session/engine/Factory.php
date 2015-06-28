<?php
/**
 *
 * @author j
 * Date: 2/14/15
 * Time: 12:10 PM
 *
 * File: factory.class.php
 */

namespace chilimatic\lib\session\engine;

use chilimatic\lib\exception\InvalidArgumentException;

class Factory
{
    /**
     * @param string $engineName
     * @param array $config
     *
     * @return GenericEngine|null
     * @throws InvalidArgumentException
     */
    public static function make($engineName, $config = null)
    {

        // namespace needed for dynamic loading ;) php is sometime pretty weird
        $session_name = (string)__NAMESPACE__ . (string)'\\' . (string)ucfirst($engineName);

        if (!class_exists($session_name, true)) {
            throw new InvalidArgumentException('Session Engine ' . $session_name . ' does not exist');
        }

        return new $session_name($config);
    }
}