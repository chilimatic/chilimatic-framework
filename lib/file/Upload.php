<?php
namespace chilimatic\lib\file;

use chilimatic\lib\config\Config;
use chilimatic\lib\exception\FileException;

/**
 * Class File_Upload
 *
 * @package chilimatic\file
 */
class Upload extends File
{


    /**
     * temporary upload location
     *
     * @var string
     */
    public $u_tmp_name = null;


    /**
     * filename
     *
     * @var string
     */
    public $u_name = null;


    /**
     * upload size [byte]
     *
     * @var int
     */
    public $u_size = null;


    /**
     * mimetype of the uploaded file
     *
     * @var string
     */
    public $u_mime_type = null;


    /**
     * upload error
     *
     * @var null
     */
    public $u_error = null;


    /**
     * constructor calls upload method
     *
     * @param $file array
     *
     * @throws FileException
     */
    public function __construct( $file = null )
    {

        if ( !is_array($file) || empty($file) ) return;
        
        $this->upload($file);
    }


    /**
     * current upload process
     *
     * @param $file array
     *
     * @return bool
     * @throws FileException
     */
    public function upload( $file = null )
    {

        if ( empty($file) || !is_array($file) ) return false;
        
        $this->u_tmp_name = $file['tmp_name'];
        $this->u_name = $file['name'];
        $this->u_size = $file['size'];
        $this->u_mime_type = $file['type'];
        $this->u_error = isset($file['error']) ? $file['error'] : null;
        
        if ( $this->u_error )
        {
            throw new FileException("File couldn't be uploaded reason " . FileException::$upload_errors[$this->u_error], Config::get('file_error'), Config::get('error_lvl_low'), __FILE__, __LINE__);
        }
        
        $this->_get_file_extension();
        
        return true;
    }



    /**
     * gets the suffix for the file that has been uploaded
     *
     * @return bool
     */
    private function _get_file_extension()
    {

        if ( empty($this->u_mime_type) ) return false;
        
        $tmp = explode('/', $this->u_mime_type);
        $this->file_extension = array_pop($tmp);
        
        unset($tmp);
        
        return true;
    }


    /**
     * wrapper for changing the upload_tmp_dir in the php ini
     *
     * @param $path string
     *
     * @return bool
     */
    public function change_upload_path( $path )
    {

        if ( getenv('upload_tmp_dir') == $path ) return true;
        putenv("upload_tmp_dir=$path");
        return true;
    }


    /**
     * Saves [copies] the file to the specific folder / directory
     *
     * @param $path     string
     * @param string $file_name
     * @param bool $delete_source
     *
     * @throws \chilimatic\lib\exception\FileException
     * @throws \Exception
     * @internal param string $filename
     *
     * @return bool
     */
    public function save( $path , $file_name = '' , $delete_source = false )
    {

        try
        {
            if ( empty($path) )
            {
                throw new FileException("No path has been given : $path", Config::get('file_error'), Config::get('error_lvl_low'), __FILE__, __LINE__);
            }
            
            $save_name = "$path/" . (empty($file_name) ? $this->u_name : $file_name);
            $save_name = trim($save_name);
            $save_name = (strpos($save_name, '.') === false) ? trim($save_name) . "." . trim($this->file_extension) : trim($save_name);
            
            if ( !copy($this->u_tmp_name, $save_name) )
            {
                throw new FileException("Copy operation wasn't possible: $this->u_tmp_name, $save_name", Config::get('file_error'), Config::get('error_lvl_low'), __FILE__, __LINE__);
            }
            
            if ( $delete_source === true )
            {
                // delete the temporary file
                unlink($this->u_tmp_name);
            }
            
            return $this->open($save_name);
        
        }
        catch ( FileException $e )
        {
            throw $e;
        }
    }
}
