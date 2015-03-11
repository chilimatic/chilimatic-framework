<?php
namespace chilimatic\lib\route\validator;
use chilimatic\lib\interfaces\IFlyWeightValidator;

/**
 * Class AbstractValidator
 *
 * @package chilimatic\lib\route\validator
 */
abstract class AbstractValidator implements IFlyWeightValidator
{


    /**
     * current value inserted through the url
     * 
     * 
     * @var string
     */
    public $value = null;


    /**
     * for more complex calles / lamdafunctions with array parameters through
     * the url a delimiter is needed
     * 
     * @var string
     */
    public $delimiter = null;


    /**
     * abstract validation
     * 
     * @param mixed $value
     */
    abstract function __invoke( $value );
}