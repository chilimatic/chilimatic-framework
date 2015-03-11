<?php
/**
 * User: j
 * Date: 11.02.14
 * Time: 10:52
 */

namespace chilimatic\lib\route\validator;
namespace chilimatic\lib\interfaces;
/**
 * Interface ValidatorInterface
 *
 * @package chilimatic\lib\route\validator
 */
interface IFlyWeightValidator {

    /**
     * abstract validation
     *
     * @param mixed $value
     */
    function __invoke( $value );
}