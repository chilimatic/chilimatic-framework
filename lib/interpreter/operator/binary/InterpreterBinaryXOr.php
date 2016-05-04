<?php
namespace chilimatic\lib\interpreter\operator\binary;
use chilimatic\lib\Interfaces\IFlyWeightOperate;

/**
 *
 * @author j
 * Date: 10/27/15
 * Time: 12:25 PM
 *
 * File: InterpreterBinaryXOr.php
 */

class InterpreterBinaryXOr implements IFlyWeightOperate
{
    /**
     * @param $input1
     * @param $input2
     *
     * @return int
     */
    public function operate($input1, $input2)
    {
        return $input1 ^ $input2;
    }

    /**
     * @param mixed $input1
     * @param mixed $input2
     *
     * @return int
     */
    public function __invoke($input1, $input2)
    {
        return $this->operate($input1, $input2);
    }
}