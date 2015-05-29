<?php

namespace chilimatic\lib\http\response;

/**
 *
 * @author j
 * Date: 4/13/15
 * Time: 11:48 PM
 *
 * File: Content.php
 */

class Content {
    /**
     * complete response Data
     *
     * @var string
     */
    public $html;

    /**
     * message
     *
     * @var string
     */
    public $msg;

    /**
     * error data
     *
     * @var \stdClass
     */
    public $error;

    /**
     * callback function / method
     *
     * @var mixed
     */
    public $callback;

    /**
     * function call
     *
     * @var null
     */
    public $call;

    /**
     * for html to decide if it should replace or append the data
     *
     * @var boolean
     */
    public $append = false;

    /**
     * @var bool
     */
    public $insertNode;

    /**
     * extra data like javascript files
     * that can be added
     *
     * @var array
     */
    public $data = [];

    /**
     * @var array
     */
    public $javascript;

    /**
     * @var array
     */
    public $css;

    /**
     * @var string
     */
    public $jsExecute;

    /**
     * @return string
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * @param string $html
     *
     * @return $this
     */
    public function setHtml($html)
    {
        $this->html = $html;

        return $this;
    }

    /**
     * @return string
     */
    public function getMsg()
    {
        return $this->msg;
    }

    /**
     * @param string $msg
     *
     * @return $this
     */
    public function setMsg($msg)
    {
        $this->msg = $msg;

        return $this;
    }

    /**
     * @return \stdClass
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string $error
     *
     * @return $this
     */
    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param string $callback
     *
     * @return $this
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * @return null
     */
    public function getCall()
    {
        return $this->call;
    }

    /**
     * @param string $call
     *
     * @return $this
     */
    public function setCall($call)
    {
        $this->call = $call;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isAppend()
    {
        return $this->append;
    }

    /**
     * @param boolean $append
     *
     * @return $this
     */
    public function setAppend($append)
    {
        $this->append = $append;

        return $this;
    }

    /**
     * @return string
     */
    public function isInsertNode()
    {
        return $this->insertNode;
    }

    /**
     * @param string $insertNode
     *
     * @return $this
     */
    public function setInsertNode($insertNode)
    {
        $this->insertNode = $insertNode;

        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return array
     */
    public function getJavascript()
    {
        return $this->javascript;
    }

    /**
     * @param array $javascript
     *
     * @return $this
     */
    public function setJavascript($javascript)
    {
        $this->javascript = $javascript;

        return $this;
    }

    /**
     * @return array
     */
    public function getCss()
    {
        return $this->css;
    }

    /**
     * @param array $css
     *
     * @return $this
     */
    public function setCss($css)
    {
        $this->css = $css;

        return $this;
    }

    /**
     * @return string
     */
    public function getJsExecute()
    {
        return $this->jsExecute;
    }

    /**
     * @param string $jsExecute
     *
     * @return $this
     */
    public function setJsExecute($jsExecute)
    {
        $this->jsExecute = $jsExecute;

        return $this;
    }
}