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
define('INCLUDE_ROOT', realpath(__DIR__ . '/../'));
/**
 * @param $file
 */
function rq_file($file)
{
    if (!file_exists($file) || !is_readable($file)) return;
    require_once $file;
}


/**
 * Autoloader for Libs view
 *
 * @param string $class_name
 *
 * @return void
 */
function main_loader($class_name)
{
    if (empty($class_name)) return;

    $class_name = str_replace('\\', '/', $class_name);
    $class_name = preg_replace('|chilimatic/|', '/', $class_name);
    $file_name  = $class_name . '.php';

    unset($part);

    $base_path = realpath(INCLUDE_ROOT . "/$file_name");
    unset($class_name, $file_name, $folder_name);

    rq_file($base_path);
}

spl_autoload_register('main_loader');
