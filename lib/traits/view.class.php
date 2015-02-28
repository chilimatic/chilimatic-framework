<?php
namespace chilimatic\lib\traits;
/**
 * Generic trait to initialize the trait object if it's needed
 *
 * @author j
 */

use \chilimatic\lib\exception\Exception_View;

trait View
{
    /**
     * Default fallback view type
     *
     * @var string
     */
    private $default_view_engine = 'Html';

    /**
     * this class can be any type of view plugin
     * based on the view interface
     *
     * @var mixed
     */
    public $view = null;

    /**
     * initializes the database Object if necessary
     *
     *
     * @param string $engine
     * @throws \chilimatic\lib\exception\Exception_View|\Exception
     * @return boolean
     */
    protected function __init_view($engine = '')
    {

        $view = (string) __NAMESPACE__ . (string) (empty($engine) ? "\\View_{$this->default_view_engine}" : "\\View_{$engine}" );

        if ( $this->view instanceof $view ) return true;

        try
        {
            $this->view = new $view();
        }
        catch ( Exception_View $e )
        {
            throw $e;
        }
        return true;
    }


    /**
     * destroys the current view object
     *
     * @return boolean
     */
    protected function __clean_view()
    {
        $this->view = null;

        return true;
    }
}