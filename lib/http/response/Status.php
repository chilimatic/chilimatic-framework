<?php
namespace chilimatic\lib\http\response;

/**
 *
 * @author j
 * Date: 4/13/15
 * Time: 11:51 PM
 *
 * File: Status.php
 */

class Status
{

    /**
     * @var int
     */
    private $code;

    /**
     * @var string
     */
    private $message;

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param int $code
     *
     * @return $this
     */
    public function setCode($code, $message)
    {
        $this->message = $message;
        $this->code    = (int)$code;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "$this->code $this->message";
    }
}