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
define('INCLUDE_ROOT', realpath(__DIR__.'/../'));
/**
 * @param $file
<<<<<<< HEAD
 * @return bool
=======
>>>>>>> 336a13ce09982d8ab14d6fadbb1d8dd97927b344
 */
function rq_file($file)
{
    if (!file_exists($file) || !is_readable($file)) return false;
    require_once $file;
<<<<<<< HEAD
    return true;
=======
>>>>>>> 336a13ce09982d8ab14d6fadbb1d8dd97927b344
}


/**
 * Autoloader for Libs view
 *
 * @param string $class_name
 * @return void
 */
function main_loader( $class_name )
{
    if ( empty($class_name) ) return;

    $class_name = str_replace('\\', '/', $class_name);
    $class_name = preg_replace('|chilimatic/|', '/', $class_name);
    $file_name = $class_name . '.php';

<<<<<<< HEAD


    unset($part);

    $base_path[] = realpath(INCLUDE_ROOT . "/$file_name");
    unset($class_name, $file_name, $folder_name);

    while ($class = array_pop($base_path)) {
        if ( rq_file($class) ) break;
    }

    return;
=======
    unset($part);

    $base_path = realpath(INCLUDE_ROOT . "/$file_name");
    unset($class_name, $file_name, $folder_name);

    rq_file($base_path);
>>>>>>> 336a13ce09982d8ab14d6fadbb1d8dd97927b344
}

spl_autoload_register('main_loader');
