<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 22.10.14
 * Time: 20:02
 */

namespace chilimatic\lib\handler;
use chilimatic\lib\di\DIFactory;

/**
 * Class HTTPHandler
 * @package chilimatic\lib\handler
 */
class HTTPHandler extends GenericHandler
{
    /**
     * @var string
     */
    const FRAMEWORK_NAMESPACE = 'chilimatic';

    /**
     *
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * @return mixed|null
     *
     * @todo return should change from array to object storage and iterate
     */
    public function getContent()
    {
        if (!$this->route) {
            return null;
        }
        $return = $this->route->call();
        $this->setView($return[1]->getView());

        $this->getView()->setConfigVariable('templatePath', $this->getDefaultTemplate(get_class($return[1])));
        return $this->getView()->render();
    }

    /**
     * @param $className
     *
     * @return string
     */
    public function getDefaultTemplate($className){
        return INCLUDE_ROOT .
            strtolower(
                str_replace('\\', '/',
                    str_replace(
                        self::FRAMEWORK_NAMESPACE, '', str_replace(
                            'controller', 'view', $className
                        )
                    )
                )
            );
    }

    /**
     *
     */
    public function init()
    {

    }

}