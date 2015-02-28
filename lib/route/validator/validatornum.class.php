<?php

namespace chilimatic\lib\route\validator;

/**
 * Class ValidatorNum
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
class ValidatorNum extends AbstractValidator
{


    /**
     * (non-PHPdoc)
     *
     * @see \Route\Route_AbstractValidator::validate()
     */
    public function __invoke( $value )
    {
        // generic pattern match if it's numeric [float/int/double]
        if ( !preg_match( '/^\d{0,}[.,]?\d*$/', (string) $value ) ) return false;
        
        $this->value = $this->get_typecast( $value );
        
        return true;
    }

    /**
     * sets the typecast
     * 
     * @param mixed $value
     * @return number|boolean
     */
    public function get_typecast( $value )
    {

        switch ( 1 )
        {
            case preg_match( '/^\d$/', $value ) :
                return (int) $value;
                break;
            case preg_match( '/^\d{0,}[.,]?\d*$/', $value ) :
                return (float) $value;
                break;
            default :
                return (float) $value;
                break;
        }
    }
}