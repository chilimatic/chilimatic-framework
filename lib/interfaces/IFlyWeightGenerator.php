<?php
namespace chilimatic\lib\interfaces;


/**
 *
 * @author j
 * Date: 9/29/15
 * Time: 10:08 PM
 *
 * File: IFlyWeightGenerator.php
 */
interface IFlyWeightGenerator
{
    /**
     * default exit code if none has been set
     *
     * @var string
     */
    const DEFAULT_EXIT = 'exit';

    /**
     * @param mixed $resource
     * @param string $command
     * @param mixed $exitCode // defines a exit sequence
     */
    public function __construct($resource, $command, $exitCode = null);

    /**
     * @return mixed
     */
    public function generate();

    /**
     * @return mixed
     */
    public function __invoke();
}