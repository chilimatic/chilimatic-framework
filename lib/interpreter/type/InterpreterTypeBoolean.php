<?php

namespace chilimatic\lib\interpreter\type;
use chilimatic\lib\Interfaces\IFlyWeightExecute;

/**
 * Class InterpreterTypeBoolean
 *
 * @package chilimatic\lib\interpreter\type
 */
class InterpreterTypeBoolean implements IFlyWeightExecute
{
    /**
     * @var string
     */
    const PATTERN = "(true|false|0|1)";

    /**
     * @param string $string
     *
     * @return bool|null
     */
    public function execute($string)
    {
        if (!preg_match(self::PATTERN, $string)) {
            return null;
        }

        $string = strtolower($string);

        if ($string{0} == 't' || $string{0} == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $input
     *
     * @return bool|null
     */
    public function __invoke($input)
    {
        return $this->execute($input);
    }
}