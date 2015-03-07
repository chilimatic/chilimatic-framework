<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 03.03.15
 * Time: 18:19
 *
 * autoloader for the testings since composer fucks up the whole thing
 *
 */
use chilimatic\lib\config\Config as Config;

define('INCLUDE_ROOT', realpath(__DIR__ . '../'));

/**
 * @param $file
 * @return bool
 */
function rq_file($file)
{
    if (!file_exists($file) || !is_readable($file)) return false;
    require_once $file;
    return true;
}


/**
 * Autoloader for Libs view
 *
 * @param string $class_name
 * @return void
 */
function main_loader( $class_name )
{
    echo $class_name;
    if ( empty($class_name) ) return;

    echo str_replace('\\', '/', $class_name);
    // convert to lowercase
    $path = strtolower($class_name);

    $folder_name = '';
    if ( strpos($path, '\\') !== false )
    {
        $root = explode('\\', $path);
        array_shift($root);
        $class_name = array_pop($root);
        $folder_name = implode('/', $root);
        unset($root);
    }

    $file_name = $class_name . '.php';
    unset($part);


    $base_path[] = (string) INCLUDE_ROOT . (string) "/$folder_name/$file_name";

    unset($class_name, $file_name, $folder_name);

    while ($class = array_pop($base_path)) {
        if ( rq_file($class) ) break;
    }

    return;
}

spl_autoload_register('main_loader');
