<?php
/**
 * User: j
 * Date: 11.02.14
 * Time: 10:52
 */

namespace chilimatic\lib\route\validator;

/**
 * Interface ValidatorInterface
 *
 * @package chilimatic\lib\route\validator
 */
interface ValidatorInterface {

    /**
     * abstract validation
     *
     * @param mixed $value
     */
    function __invoke( $value );
}