<?php
namespace chilimatic\lib\file;

use chilimatic\lib\config\Config;
use chilimatic\lib\exception\FileException;

/**
 * Class File_Text
 *
 * @package chilimatic\file
 */
Class Text extends File
{

    public function cat()
    {
        try {
            if (empty($this->file) && empty($this->fp)) {
                throw new FileException("No File has been given", Config::get('file_error'), Config::get('error_lvl_low'), __FILE__, __LINE__);
            }
        }
        catch (FileException $e) {
            throw $e;
        }

        return true;
    } 
}
