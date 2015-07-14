<?php
namespace chilimatic\lib\file;

use chilimatic\lib\config\Config;
use chilimatic\lib\exception\FileException;
use chilimatic\lib\Tool\Tool;

/**
 * Class File_Directory
 *
 * @package chilimatic\file
 */
class Directory
{


    /**
     * list of files / directory within the directory
     *
     * @var array
     */
    public $list = array();


    /**
     * dir handle
     *
     * @var resource
     */
    public $dh = null;


    /**
     * directory path
     *
     * @var string
     */
    public $dir = '';


    /**
     * list position
     *
     * @var int
     */
    public $position = 0;


    /**
     * recursive through all subdirectories
     *
     * @var bool
     */
    public $recursive = false;


    /**
     * group as directories or files
     *
     * @var boolean
     */
    public $grouped = false;


    /**
     * excluded directories pattern
     *
     * @var array
     */
    public $exclude_pattern = array();


    /**
     * constructor
     *
     * @param null $dir
     */
    public function __construct($dir = null)
    {

        $this->exclude_pattern = (array)Config::get('exclude_directory');

        if (empty($dir)) return;

        $this->open($dir);
    }


    /**
     * open file
     *
     * @param null $dir
     * @param bool $recursive
     * @param bool $grouped
     *
     * @return bool
     * @throws \chilimatic\exception\FileException
     * @throws \Exception
     */
    public function open($dir = null, $recursive = false, $grouped = false)
    {

        try {

            if (empty($dir)) {
                throw new FileException("The given path is empty", Config::get('file_error'), Config::get('error_lvl_low'), __FILE__, __LINE__);
            }

            if (!is_dir($dir)) {
                throw new FileException("The given path is not a directory", Config::get('file_error'), Config::get('error_lvl_low'), __FILE__, __LINE__);
            }

            if (!is_readable($dir)) {
                throw new FileException("The given path is not readable", Config::get('file_error'), Config::get('error_lvl_low'), __FILE__, __LINE__);
            }

            $this->dir = (string)$dir;

            $this->dh = opendir($this->dir);

            $this->recursive = $recursive;

            $this->grouped = $grouped;

            while (($file = readdir($this->dh)) !== false) {
                if ($file == '.' || $file == '..' || in_array($file, $this->exclude_pattern)) continue;

                if (is_file("$this->dir/$file")) {
                    $f = new File();
                    $f->open(Tool::clean_up_path("$this->dir/$file"));
                    if ($grouped === true) {
                        $this->list['file']["$this->dir/$file"] = $f;
                    } else {
                        $this->list["$this->dir/$file"] = $f;
                    }
                    unset($f);
                }

                if (is_dir("$this->dir/$file")) {
                    $d = new Directory();
                    $d->open(Tool::clean_up_path("$this->dir/$file"), $this->recursive);

                    if ($grouped === true) {
                        $this->list['directory']["$this->dir/$file"] = $d;
                    } else {
                        $this->list["$this->dir/$file"] = $d;
                    }

                    $this->list['directory'] = $d;
                    unset($d);
                }
            }

        } catch (FileException $e) {
            throw $e;
        }

        return true;
    }
}
