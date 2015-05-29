<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 28.10.14
 * Time: 09:08
 */

namespace chilimatic\lib\view;

class Json implements MinimalInterface
{
    /**
     * <p>
     * this is the HTTP header command so the client knows it's a json
     * he's receiving
     * </p>
     *
     * @var string
     */
    const CONTENT_TYPE_JSON = 'Content-Type: application/json';


    /**
     * @generic view trait as template
     */
    use ViewTrait;

    /**
     * (non-PHPdoc)
     *
     * @see      View_Generic_Interface::render()
     * @return mixed|void
     * @internal param bool $fetch
     */
    public function render() {
        $this->initRender();
        return json_encode($this->getAll(), JSON_NUMERIC_CHECK);
    }


    public function initRender(){
        header(self::CONTENT_TYPE_JSON);
    }

    /**
     * magic setter for overload -> will automatically added to the
     * engineVar variables
     *
     * @param $key
     * @param $val
     */
    public function __set($key, $val) {
        $this->set($key, $val);
    }

    /**
     * @param $key
     *
     * @return mixed|null
     */
    public function __get($key) {
        return $this->get($key);
    }
}