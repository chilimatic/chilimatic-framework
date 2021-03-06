<?php

namespace chilimatic\lib\base;

/**
 * Class Error
 *
 * @package chilimatic\lib\base
 */
class Error
{


    /**
     * Severity Low [ignore / log]
     *
     * @var int
     */
    const SEVERITY_LOW = 0b0000;


    /**
     * Severity [log]
     *
     * @var int
     */
    const SEVERITY_MED = 0b0001;


    /**
     * Severity [log/Mail]
     *
     * @var int
     */
    const SEVERITY_HIGH = 0b0010;


    /**
     * Severity [log/Hold System]
     *
     * @var int
     */
    const SEVERITY_CRIT = 0b0011;
}