<?php
namespace chilimatic\lib\route\validator;

/**
 * Class ValidatorChar
 *
 * Simple validation Class can be extended
 * 
 * class is based on the pattern (:num) which will be resolved to this classname
 * therefore new classes can be added and new types of validations defined with such short snipplets
 * 
 * @author j 
 *
 * @package chilimatic\lib\route\validator
 */
class ValidatorChar extends AbstractValidator
{


    /**
     * (non-PHPdoc)
     * @see \Route\Route_AbstractValidator::validate()
     */
    public function __invoke( $value )
    {

        if ( preg_match('/^\w{0,}$/', $value) && !preg_match('/^\d{0,}[.,]?\d*$/', $value) ) return true;
        
        return false;
    }
}