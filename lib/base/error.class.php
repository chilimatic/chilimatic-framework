<?php

namespace chilimatic\lib\base;

/**
 * Class Error
 * @package chilimatic\lib\base
 */
class Error
{


    /**
     * Severity Low [ignore / log]
     *
     * @var int
     */
    const SEVERITY_LOW = 0;


    /**
     * Severity [log]
     *
     * @var int
     */
    const SEVERITY_MED = 1;


    /**
     * Severity [log/Mail]
     *
     * @var int
     */
    const SEVERITY_HIGH = 2;


    /**
     * Severity [log/Hold System]
     *
     * @var int
     */
    const SEVERITY_CRIT = 3;
}