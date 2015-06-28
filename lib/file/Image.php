<?php

namespace chilimatic\lib\file;

/**
 * Class File_Image
 *
 * @package chilimatic\file
 */
class Image extends File
{

    /**
     * Imagick object
     *
     * @var \Imagick
     */
    public $imagemagik = null;


    /**
     * constructor
     *
     * @param string $file_name
     *
     * @internal param string $filename
     *
     * @return bool
     */
    public function __contruct($file_name = '')
    {

        if (empty($file_name)) return;

        $this->open($file_name);

        $this->imagemagik = new \Imagick($file_name);
    }


    /**
     * (non-PHPdoc)
     *
     * @see File::open()
     *
     * @param string $file_name
     *
     * @return boolean
     */
    public function open($file_name = '')
    {

        if (!parent::open($file_name)) return false;
        $this->imagemagik = new \Imagick($file_name);

        return true;
    }


    /**
     * Wrapper for Imagick thumbnail
     *
     * @param int $width
     * @param int $height
     * @param boolean $bestfit
     * @param boolean $fill
     *
     * @throws \Exception
     * @throws \ImagickException
     * @return boolean
     */
    public function create_thumbnail($width = 200, $height = 200, $bestfit = false, $fill = false)
    {

        if (empty($this->imagemagik) || empty($this->filename)) return false;
        try {
            $this->imagemagik->thumbnailimage($width, $height, $bestfit, $fill);
        } catch (\ImagickException $e) {
            throw $e;
        }

        return true;
    }


    /**
     * saves the file and opens the fileinfo for this file
     *
     * @param string $path
     * @param string $filename
     *
     * @throws \Exception
     * @throws \ImagickException
     * @return boolean
     */
    public function saveThumb($path = '', $filename = '')
    {

        if (empty($this->filename)) return false;
        try {
            $save_name = (empty($path) ? $this->path : $path);
            $save_name = "$save_name/" . (empty($filename) ? 'th_' . $this->filename : $filename);
            $this->imagemagik->writeImage($save_name);
        } catch (\ImagickException $e) {
            throw $e;
        }

        return $this->open($save_name);
    }
}