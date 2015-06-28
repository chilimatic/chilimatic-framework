<?php

namespace chilimatic\lib\http\response;

/**
 * Class Response
 *
 * @package chilimatic\request
 */
class Response implements IResponse, \JsonSerializable
{
    /**
     * @var []
     */
    protected $header;

    /**
     * @var Status
     */
    protected $status;

    /**
     * @var Content
     */
    protected $content;


    public function __construct()
    {
        $this->status  = new Status();
        $this->content = new Content();
    }

    public function addHeader($header)
    {
        $this->header[] = $header;
    }

    /**
     * @return mixed
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param mixed $header
     *
     * @return $this
     */
    public function setHeader($header)
    {
        $this->header = $header;

        return $this;
    }

    /**
     * @param int $code
     * @param string $message
     */
    public function setStatusCode($code, $message)
    {
        $this->status->setCode($code, $message);
    }


    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param Content $content
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     *
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *       which is a value of any type other than a resource.
     */
    function jsonSerialize()
    {
        return [
            'content' => $this->content
        ];
    }


}