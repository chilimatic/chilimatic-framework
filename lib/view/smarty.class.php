<?php
namespace chilimatic\lib\view;


/**
 * Class Smarty
 * @package chilimatic\lib\view
 */
final class Smarty extends Generic
{

    public function __construct()
    {

        parent::__construct();
        
        $this->_engine = new \Smarty();
    }


    /**
     * sets the engine vars to the current engine
     * needs to run before the engine really is accessed!
     *
     * @return boolean
     */
    public function initRender()
    {

        if ( empty($this->engine_var_list) ) return false;
        
        foreach ( $this->engine_var_list as $key => $value )
        {
            if ( empty($key) ) continue;
            
            $this->_engine->assign($key, $value);
        }
        
        return true;
    }

    /**
     * calls the rendering process
     * 
     * (non-PHPdoc)
     * @see \chilimatic\view\View_Generic::render()
     */
    public function render( $template_file = '')
    {
        $this->initEngine();
        $this->initRender();

        return $this->_engine->fetch($template_file);
    }
}