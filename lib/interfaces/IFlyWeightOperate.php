<?php
namespace chilimatic\lib\interfaces;

/**
 *
 * @author j
 * Date: 10/27/15
 * Time: 12:21 PM
 *
 * File: IFlyweightOperate.php
 */
interface IFlyWeightOperate
{

    /**
     * @param mixed $input1
     * @param mixed $input2
     *
     * @return mixed
     */
    public function operate($input1, $input2);

    /**
     * @param mixed $input1
     * @param mixed $input2
     *
     * @return mixed
     */
    public function __invoke($input1, $input2);
}