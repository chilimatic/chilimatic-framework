<?php
namespace chilimatic\lib\view;

/**
 * Class Generic
 * @package chilimatic\lib\view
 */
abstract class Generic implements ViewInterface
{
    /**
     * trait for inheritance
     */
    use ViewTrait;

    /**
     * Engine (like Smarty and so on)
     *
     * @var Object
     */
    protected $_engine = null;


    /**
     * sets the engine vars to the current engine
     * needs to run before the engine really is accessed!
     *
     * @return boolean
     */
    public function initEngine()
    {

        if ( empty($this->setting) ) return false;
        
        foreach ( $this->setting as $key => $value )
        {
            if ( empty($key) || !property_exists($this->_engine, $key) ) continue;
            
            $this->_engine->$key = $this->setting->$key;
        }
        
        return true;
    }

    /**
     * @return mixed
     */
    abstract public function initRender();


    /**
     * (non-PHPdoc)
     * fetch set if the template file should be displayed directly
     * or just be rendered and returned
     *
     *
     * @see View_Generic_Interface::render()
     * @param string $templateFile
     */
    abstract public function render($templateFile = '');


    /**
     * (non-PHPdoc)
     *
     * @see View_Generic_Interface::__destruct()
     */
    public function __destruct()
    {

        return;
    }
}
?>