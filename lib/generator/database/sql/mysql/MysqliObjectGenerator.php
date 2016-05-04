<?php
use chilimatic\lib\Interfaces\IFlyWeightGenerator;


/**
 *
 * @author j
 * Date: 9/29/15
 * Time: 10:11 PM
 *
 * File: MysqliObjectGenerator.php
 */

class MysqliObjectGenerator implements IFlyWeightGenerator
{
    /**
     * @var mixed
     */
    private $resource;

    /**
     * @var mixed
     */
    private $command;

    /**
     * @var mixed
     */
    private $exitCode = self::DEFAULT_EXIT;

    /**
     * @param mysqli_result $resource
     * @param mixed $command
     * @param mixed $exitCode
     */
    public function __construct($resource, $command = null, $exitCode = null)
    {
        $this->resource = $resource;

        if ($command) {
            $this->command = $command;
        }

        if ($exitCode !== null) {
            $this->exitCode = $exitCode;
        }

    }

    public function generate()
    {
        $cmd = yield;
        if ($cmd == $this->exitCode) {
            return null;
        }

        yield $this->resource->fetch_object();
    }

    public function __invoke()
    {
        return $this->generate();
    }
}