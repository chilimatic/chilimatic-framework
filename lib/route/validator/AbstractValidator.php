<?php
namespace chilimatic\lib\route\validator;

use chilimatic\lib\Interfaces\IFlyWeightValidator;

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
     * @param mixed $value
     *
     * @return mixed
     */
    abstract function validate($value);


    /**
     * @param mixed $value
     *
     * @return mixed
     */
    abstract function __invoke($value);
}