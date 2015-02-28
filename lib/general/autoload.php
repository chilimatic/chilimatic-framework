<?php
/**
 * autoload script
 */
define('INCLUDE_ROOT', realpath(__DIR__ . '../../'));

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
    if ( empty($class_name) ) return;

    // convert to lowercase
    $class_name = strtolower($class_name);

    $folder_name = '';
    if ( strpos($class_name, '\\') !== false )
    {
        $root = explode('\\', $class_name);
        array_shift($root);
        $class_name = array_pop($root);
        $folder_name = implode('/', $root);
        unset($root);
    }

    switch (true) {
        case ( strpos($class_name, '_') !== false ):
            $part = explode('_', $class_name);
            $file_name = str_replace((string) array_shift($part) . '_', '', $class_name) . '.class.php';
            unset($part);
            break;
        default:
            $file_name = 'class.php';
            break;
    }

    $base_path[] = (string) INCLUDE_ROOT . (string) "/$folder_name/$file_name";
    $base_path[] = (string) INCLUDE_ROOT . (string) "/$folder_name/" . $class_name . '.class.php';
    unset($class_name, $file_name, $folder_name);

    while ($class = array_pop($base_path)) {
        if ( rq_file($class) ) break;
    }

    return;
}

spl_autoload_register('main_loader');
