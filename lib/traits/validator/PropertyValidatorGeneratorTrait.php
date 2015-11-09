<?php
namespace chilimatic\lib\traits\validator;
use chilimatic\lib\interfaces\IFlyWeightValidator;
use chilimatic\lib\interpreter\operator\InterpreterOperatorFactory;
use chilimatic\lib\parser\annotation\AnnotationValidatorParser;
use chilimatic\lib\validator\AnnotationPropertyValidatorFactory;

/**
 *
 * @author j
 * Date: 11/9/15
 * Time: 4:18 PM
 *
 * File: PropertyValidatorGeneratorTrait.php
 */
Trait PropertyValidatorGeneratorTrait
{
    /**
     * @var AnnotationPropertyValidatorFactory
     */
    private $validatorFactory;

    /**
     * @var InterpreterOperatorFactory
     */
    private $operatorFactory;


    /**
     * @param string $operator
     * @param bool $result_old
     * @param bool $result_new
     *
     * @return mixed
     */
    private function translator($operator, $result_old, $result_new)
    {
        if (!$this->operatorFactory) {
            $this->operatorFactory = new InterpreterOperatorFactory();
        }

        switch ($operator) {
            case '&':
                return $this->operatorFactory->make('binary\InterpreterBinaryAnd', null)->operate($result_old, $result_new);
                break;
            case '|':
                return $this->operatorFactory->make('binary\InterpreterBinaryOr', null)->operate($result_old, $result_new);
                break;
            case '^':
                return $this->operatorFactory->make('binary\InterpreterBinaryXOr', null)->operate($result_old, $result_new);
                break;
        }

        return false;
    }

    /**
     * iterates over all properties, including the parent ones till the super class is reached
     * and checks for the validator annotation if it finds it a set will be generated of all properties
     * with validators and the interface will be executed
     *
     * after that it will combine the sets and if one set is false it will return false
     *
     * @return bool
     */
    public function validateProperties()
    {
        if (!$this->validatorFactory) {
            $this->validatorFactory = new AnnotationPropertyValidatorFactory(
                new AnnotationValidatorParser()
            );
        }

        $resultSet = [];
        /**
         * @var $property \ReflectionProperty
         */
        foreach ($this->getPropertyReflectionGenerator() as $property) {
            $validatorSetList = $this->validatorFactory->make($property);
            if (!$validatorSetList) {
                continue;
            }

            foreach ($validatorSetList as &$validatorSet) {
                // set it accessible
                $property->setAccessible(true);
                // if the field is not mandatory and null the result will be true by default
                // we could just leave it out but I don't wanna confuse the developer
                if ($validatorSet[AnnotationValidatorParser::INDEX_MANDATORY] == false && $property->getValue($this) === null) {
                    $validatorSet[AnnotationPropertyValidatorFactory::INDEX_RESULT] = true;
                } else {
                    $validatorSet[AnnotationPropertyValidatorFactory::INDEX_RESULT] =
                        $validatorSet[AnnotationValidatorParser::INDEX_INTERFACE]($property->getValue($this));
                }
            }
            $resultSet[] = $validatorSetList;
        }
        // remove the garbage
        unset($validatorSet, $property, $validatorFactory, $validatorSetList);


        $c = count($resultSet);
        $setBool = true;
        for ($i = 0; $i < $c; $i++) {
            $c2 = count($resultSet[$i]);
            for ($x = 0; $x < $c2; $x++) {
                // there are no groups implemented so I assume that use the AND operator
                $setBool &= $this->translator(
                    $resultSet[$i][$x][AnnotationPropertyValidatorFactory::INDEX_RESULT],
                    $resultSet[$i][$x][AnnotationValidatorParser::INDEX_EXPECTED],
                    $resultSet[$i][$x][AnnotationValidatorParser::INDEX_OPERATOR]
                );
            }
            if ($setBool == false) {
                return false;
            }
        }

        return true;
    }


    /**
     * @param \ReflectionClass $reflection
     *
     * @return \ReflectionProperty[]
     */
    private function getReflectionPropertiesRecursive(\ReflectionClass $reflection)
    {
        $properties = $reflection->getProperties();

        if ($reflection->getParentClass()) {
            $properties = array_merge(
                $properties,
                $this->getReflectionPropertiesRecursive($reflection->getParentClass())
            );
        }

        return $properties;
    }


    /**
     * returns the reflection Properties as Traversable
     *
     * @return \Generator
     */
    private function getPropertyReflectionGenerator()
    {
        static $reflection, $propertyList;

        if (!$reflection) {
            $reflection = new \ReflectionClass($this);
            $propertyList = $this->getReflectionPropertiesRecursive($reflection);
        }

        foreach ($propertyList as $property) {
            yield $property;
        }
    }
}