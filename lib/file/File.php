<?php
/**
 * file class
 * @author j
 *
 */

namespace chilimatic\lib\file;

/**
 * Class File
 *
 * @package chilimatic\file
 */
class File
{


    /**
     * current file pointer
     *
     * @var resource
     */
    public $fp = false;


    /**
     * possible label for the file
     *
     * @var string
     */
    public $label = '';


    /**
     * file type
     *
     * @var string
     */
    public $type = '';


    /**
     * filename
     *
     * @var string
     */
    public $filename = '';


    /**
     * absolute filename
     *
     * @var string
     */
    public $name = '';


    /**
     * path to the file
     *
     * @var string
     */
    public $path = '';


    /**
     * the last time the file was modified
     * unixtime
     *
     * @var int
     */
    public $modified = '';


    /**
     * the time the file was created
     * unixtime
     *
     * @var int
     */
    public $created = '';


    /**
     * the numeric group of the file
     *
     * @var int
     */
    public $owner = '';


    /**
     * size of the file
     *
     * @var int
     */
    public $size = '';


    /**
     * if the file is readable
     *
     * @var bool
     */
    public $readable = false;


    /**
     * if the file is writeable
     *
     * @var bool
     */
    public $writeable = false;


    /**
     * the last time the file was accessed
     * unixtime
     *
     * @var int
     */
    public $accessed = '';


    /**
     * no file lock
     *
     * @var bool
     */
    public $file_lock = false;


    /**
     * the inode of the file
     *
     * @var int
     */
    public $f_inode = '';


    /**
     * gets the mime type of a file
     *
     * @var string
     */
    public $mime_type = '';


    /**
     * the absolute file path
     *
     * @var string
     */
    public $file = '';


    /**
     * current read / write mode
     *
     * @var string
     */
    private $_option = '';


    /**
     * file_extension
     * 
     * @var string
     */
    public $file_extension = null;

    /**
     * group settings
     *
     * @var int
     */
    public $group = null;


    /**
     * last changed
     *
     * @var int
     */
    public $changed = null;

    /**
     * permission settings
     *
     * @var int
     */
    public $permission = null;

    /**
     * constructor
     *
     * @param $filename string           
     *
     * @return bool
     */
    public function __contruct( $filename = '' )
    {

        if ( empty($filename) ) return;
        $this->open($filename);

    }


    /**
     * appends to a file based + create if wanted
     *
     * @param $content string           
     * @param $create bool           
     *
     * @return bool
     */
    public function append( $content , $create = false )
    {

        if ( $this->writeable !== true || empty($content) ) return false;
        
        if ( strpos($this->_option, 'a') === false ) 
        {
            // close open filepoint
            if ( !empty($this->fp) ) fclose($this->fp);
            // opens fp for writing with file lock
            $this->open_fp((is_bool($create) && $create === true ? 'a+' : 'a'));
        }
        
        if ( $this->file_lock !== true && !$this->lock(LOCK_EX) ) return false;
        
        // writes to file
        fputs($this->fp, $content, strlen($content));
        // releases lock
        $this->lock(LOCK_UN);
        
        return true;
    }


    /**
     * file lock
     *
     * @param int|string $mode string
     *
     * @return bool
     */
    public function lock( $mode = LOCK_SH )
    {

        if ( !is_resource($this->fp) ) return false;
        
        if ( flock($this->fp, $mode) === false ) return false;
        
        if ( $mode != LOCK_UN ) $this->file_lock = true;
        else $this->file_lock = false;
        
        return true;
    }


    /**
     * close ressource
     */
    public function close()
    {

        if ( !is_resource($this->fp) ) return false;
        return fclose($this->fp);
    }


    /**
     * changes file owner
     *
     * @param $owner int           
     *
     * @return bool
     */
    public function change_owner( $owner )
    {

        if ( empty($owner) || !is_int($owner) || $this->owner != getmyuid() ) return false;
        
        if ( !empty($this->fp) ) fclose($this->fp);
        
        if ( chown($this->path . $this->filename, $owner) )
        {
            return $this->open($this->path . $this->filename);
        }
        
        return false;
    }


    /**
     * changes the file user/group permissions
     *
     * @param $mode int           
     *
     * @return bool
     */
    public function change_permission( $mode )
    {

        if ( empty($mode) || !is_int($mode) ) return false;
        
        if ( chmod($this->path . $this->filename, $mode) )
        {
            return $this->open($this->path . $this->filename);
        }
        
        return false;
    }


    /**
     * changes the group
     *
     * @param $group int           
     *
     * @return bool
     */
    public function change_group( $group )
    {

        if ( empty($group) || !is_int($group) ) return false;
        
        if ( chgrp($this->path . $this->filename, $group) )
        {
            return $this->open($this->path . $this->filename);
        }
        
        return false;
    }


    /**
     * creates a file
     *
     * @param $file string
     *
     * @return bool
     */
    public function create_file( $file )
    {

        if ( empty($file) ) return false;
        
        return touch($file);
    }


    /**
     * extracts the extension of the current file
     */
    private function _extract_file_extension()
    {

        if ( empty($this->filename) ) return false;
        
        if ( !empty($this->mime_type) )
        {
        	$array = explode('/', $this->mime_type);       	
            $this->file_extension = array_pop($array);
        }
        else
        {
            if ( strpos($this->filename, '.') !== false )
            {
                $array = explode('/', $this->mime_type);       	
            	$this->file_extension = array_pop($array);
            }
            else
            {
                $this->file_extension = 'unknown';
            }
        }

        return true;
    }


    /**
     * gets the filename of the file
     */
    private function _extract_filename()
    {

        if ( empty($this->file) ) return false;
        
        $tmp_array = explode('/', $this->file);
        $count = (int) count($tmp_array);
        for ( $i = 0 ; $i < $count ; $i++ )
        {
            if ( $i + 1 == $count )
            {
                $this->filename = (string) $tmp_array[0];
            }
            array_shift($tmp_array);
        }
        unset($tmp_array);
        
        return true;
    }


    /**
     * gets the filename out of the entered path
     *
     * @return bool
     */
    private function _get_path()
    {

        if ( empty($this->file) && is_string($this->file) ) return false;
        
        if ( strpos($this->file, '/') !== false )
        {
            $path = explode('/', $this->file);
            array_pop($path);
            $this->path = (string) implode('/', $path) . '/';
        }
        elseif ( strpos('\\', $this->file) !== false )
        {
            $path = explode('\\', $this->file);
            array_pop($path);
            $this->path = (string) implode('\\', $path) . '\\';
        }
        else
        {
            $this->path = getcwd() . '/';
        }
        return true;
    }


    /**
     * gets all the information about the file
     *
     * @param $filename string           
     *
     * @return bool
     */
    public function open( $filename )
    {

        if ( !is_file($filename) ) return false;
        
        $this->file = @(string) $filename;
        $this->group = @(int) filegroup($filename);
        $this->owner = @(int) fileowner($filename);
        $this->size = @(int) filesize($filename);
        $this->type = @(string) filetype($filename);
        $this->accessed = @(int) fileatime($filename);
        $this->changed = @(int) filectime($filename);
        $this->modified = @(int) filemtime($filename);
        $this->permission = @(int) fileperms($filename);
        $this->f_inode = @ fileinode($filename);
        $this->writeable = @(bool) is_writable($filename);
        $this->readable = @(bool) is_readable($filename);
        $this->mime_type = @(string) mime_content_type($filename);
        $this->_get_path();
        $this->_extract_filename();
        $this->_extract_file_extension();
        
        return true;
    }


    /**
     * opens a filepointer
     *
     * @param $option string
     *
     * @return bool
     */
    function open_fp( $option = 'r' )
    {

        if ( empty($option) || !is_string($option) ) return false;
        
        switch ( true )
        {
            case (strpos($option, 'r') !== false) :
                $mode = LOCK_SH;
                break;
            case (strpos($option, 'a') !== false || strpos($option, 'w') !== false) :
                $mode = LOCK_EX;
                break;
            default :
                $mode = LOCK_EX;
                break;
        }
        $this->readable = true;
        
        if ( ($this->fp = fopen($this->file, $option)) !== false )
        {
            $this->lock($mode);
            // sets the fopen option based on it reduces
            // reopening of a file
            $this->_option = (string) $option;
            
            return true;
        }
        
        return false;
    }


    /**
     * read the current content of the file
     *
     * @return string
     */
    public function read()
    {

        // if file is readable
        if ( $this->readable !== true ) return false;
        
        if ( !empty($this->fp) && is_resource($this->fp) ) fclose($this->fp);
        
        $this->open_fp('r');
        
        if ( filesize($this->file) == 0 ) return false;
        $this->lock(LOCK_SH);
        
        $content = (string) fread($this->fp, ($this->size >= 0) ? $this->size : 1);
        
        $this->lock(LOCK_UN);
        
        return $content;
    }


    /**
     * writes to file + create if wanted
     *
     * @param $content string           
     * @param $create bool           
     *
     * @return bool
     */
    public function write( $content , $create = false )
    {

        if ( $this->writeable !== true || empty($content) ) return false;
        
        if ( strpos($this->_option, 'w') === false )
        {
            // close open filepointer
            if ( !empty($this->fp) ) fclose($this->fp);
            
            // open the file point with a lock
            $this->open_fp((is_bool($create) && $create === true ? 'w+' : 'w'));
        
        }
        
        // check for the lock
        if ( $this->file_lock !== true && !$this->lock(LOCK_EX) ) return false;
        
        // write to the file
        fputs($this->fp, $content, strlen($content));
        // release the lock
        $this->lock(LOCK_UN);
        
        return true;
    }


    /**
     * closes the open file resource
     */
    public function __destruct()
    {

        if ( !empty($this->fp) && is_resource($this->fp) ) fclose($this->fp);
        return true;
    }
}
?>