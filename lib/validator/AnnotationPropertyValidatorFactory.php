<?php

namespace chilimatic\lib\validator;

use chilimatic\lib\interfaces\IFlyWeightParser;
use chilimatic\lib\transformer\string\AnnotationValidatorClassName;
use chilimatic\lib\transformer\string\AnnotationValidatorPrependNameSpace;

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
     * @var array
     */
    private $missingValidators;

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
     * @return \SplObjectStorage
     */
    public function make(\ReflectionProperty $reflectionParameter)
    {
        if (!$this->parser) {
            throw new \RuntimeException("Missing the parser Parser!");
        }

        $result = $this->parser->parse(
            $reflectionParameter->getDocComment()
        );


        $validatorStorage = new \SplObjectStorage();
        if (!$result) {
            return $validatorStorage;
        }

        $this->missingValidators = [];
        $classNameTransformer = new AnnotationValidatorClassName();
        $namespaceTransformer = new AnnotationValidatorPrependNameSpace();


        foreach ($result as $valdiatorClassName) {
            // check if the annotation is with the full namespace already otherwise put it relative
            $className = $namespaceTransformer(
                $classNameTransformer(
                    $valdiatorClassName
                ),
                [
                    AnnotationValidatorPrependNameSpace::NAMESPACE_OPTION_INDEX => __NAMESPACE__
                ]
            );

            if (class_exists($className, true)) {
                $validatorStorage->attach(new $className());
            } else {
                $this->missingValidators[] = $className;
            }
        }

        if ($this->missingValidators) {
            throw new \RuntimeException("There is one or more Validators Missing: " . implode(',', $this->missingValidators));
        }

        return $validatorStorage;
    }

}