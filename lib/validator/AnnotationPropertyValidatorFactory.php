<?php

namespace chilimatic\lib\validator;

use chilimatic\lib\interfaces\IFlyWeightParser;

/**
 *
 * @author j
 * Date: 9/27/15
 * Time: 3:52 PM
 *
 * File: AnnotationValidatorFactory.php
 */
class AnnotationPropertyValidatorFactory {

    /**
     * @var IFlyWeightParser
     */
    private $parser;

    /**
     * @param IFlyWeightParser $parser
     */
    public function __construct(IFlyWeightParser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * returns null if there is no result from the parser
     *
     * @param \ReflectionProperty $reflectionParameter
     *
     * @return \SplObjectStorage|null
     */
    public function make(\ReflectionProperty $reflectionParameter)
    {
        if (!$this->parser) {
            throw new \RuntimeException("Missing the parser Parser!");
        }

        $result = $this->parser->parse(
            $reflectionParameter->getDocComment()
        );

        if (!$result) {
            return null;
        }

        $validatorStorage = new \SplObjectStorage();
        $missingValidators = [];
        foreach ($result as $valdiatorClassName) {
            // check if the annotation is with the full namespace already otherwise put it relative
            if (strpos($valdiatorClassName, __NAMESPACE__) === false) {
                echo $valdiatorClassName;
                $className = __NAMESPACE__ . '\\' . $valdiatorClassName;
            } else {
                $className = $valdiatorClassName;
            }

            if (class_exists($className, true)) {
                $validatorStorage->attach(new $className());
            } else {
                $missingValidators[] = $className;
            }
        }

        if ($missingValidators) {
            throw new \RuntimeException("There is one or more Validators Missing: " . implode(',', $missingValidators));
        }

        return $validatorStorage;
    }

}