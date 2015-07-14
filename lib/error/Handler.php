<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 05.02.15
 * Time: 22:32
 */

namespace chilimatic\lib\error;

class Handler
{

    /**
     * @var \chilimatic\lib\log\client\AbstractClient
     */
    private $client;

    /**
     * @param \chilimatic\lib\log\client\AbstractClient $client
     */
    public function __construct(\chilimatic\lib\log\client\AbstractClient $client)
    {
        if (!$client) {
            throw new \chilimatic\lib\exception\Dependency('No Client was declared');
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