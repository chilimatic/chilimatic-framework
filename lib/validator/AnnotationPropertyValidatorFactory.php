<?php

namespace chilimatic\lib\validator;

use chilimatic\lib\interfaces\IFlyWeightParser;
use chilimatic\lib\parser\annotation\AnnotationValidatorParser;
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
class AnnotationPropertyValidatorFactory
{
    /**
     * @var string
     */
    const INDEX_RESULT = 'result';

    /**
     * @var IFlyWeightParser
     */
    private $parser;

    /**
     * so we can check even if we catch the exception
     *
     * @var array
     */
    private $missingValidators;

    /**
     * @var AnnotationValidatorClassName
     */
    private $classNameTransformer;

    /**
     * @var AnnotationValidatorPrependNameSpace
     */
    private $namespaceTransformer;

    /**
     * @var []
     */
    private $validatorTemplates;


    /**
     * @param IFlyWeightParser $parser
     */
    public function __construct(IFlyWeightParser $parser)
    {
        $this->parser = $parser;
        $this->classNameTransformer = new AnnotationValidatorClassName();
        $this->namespaceTransformer = new AnnotationValidatorPrependNameSpace();
    }

    /**
     * returns null if there is no result from the parser
     *
     * @param \ReflectionProperty $reflectionParameter
     *
     * @return array
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
            return [];
        }

        $validatorSet = [];
        $this->missingValidators = [];

        foreach ($result as $validatorToken) {
            // check if the annotation is with the full namespace already otherwise put it relative
            $className = $this->namespaceTransformer->transform(
                $this->classNameTransformer->transform(
                    $validatorToken[AnnotationValidatorParser::INDEX_INTERFACE]
                ),
                [
                    AnnotationValidatorPrependNameSpace::NAMESPACE_OPTION_INDEX => __NAMESPACE__
                ]
            );
            if (class_exists($className, true)) {
                // keep the validators in the runtime -> they are stateless and can be used as a reference
                if (empty($this->validatorTemplates[$className])) {
                    $this->validatorTemplates[$className] = new $className();
                }

                $validatorSet[] = [
                    self::INDEX_RESULT                             => null,
                    AnnotationValidatorParser::INDEX_MANDATORY     => $validatorToken[AnnotationValidatorParser::INDEX_MANDATORY],
                    AnnotationValidatorParser::INDEX_OPERATOR      => $validatorToken[AnnotationValidatorParser::INDEX_OPERATOR],
                    AnnotationValidatorParser::INDEX_INTERFACE     => $this->validatorTemplates[$className],
                    AnnotationValidatorParser::INDEX_EXPECTED      => $validatorToken[AnnotationValidatorParser::INDEX_EXPECTED],
                ];
            } else {
                $this->missingValidators[] = $className;
            }
        }

        if ($this->missingValidators) {
            throw new \RuntimeException("There is one or more Validators Missing: " . implode(',', $this->missingValidators));
        }

        return $validatorSet;
    }
}