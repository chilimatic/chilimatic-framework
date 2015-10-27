<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 05.02.15
 * Time: 22:32
 */

namespace chilimatic\lib\error;

use chilimatic\lib\exception\DependencyException;
use chilimatic\lib\log\client\AbstractClient;

class Handler
{

    /**
     * @var AbstractClient
     */
    private $client;

    /**
     * @param AbstractClient $client
     *
     * @throws DependencyException
     */
    public function __construct(AbstractClient $client)
    {
        if (!$client) {
            throw new DependencyException('No Client was declared');
        }
        $this->client = $client;
    }

    /**
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }
}