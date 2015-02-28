<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 05.02.15
 * Time: 22:32
 */

namespace chilimatic\lib\error;

use chilimatic\lib\exception\Exception_Admin;

class Handler {

    /**
     * @var string
     */
    private $message;

    /**
     * @var \chilimatic\lib\log\client\AbstractClient
     */
    private $client;

    public function __construct(\chilimatic\lib\log\client\AbstractClient $client) {
        if (!$client) {
            throw new \chilimatic\lib\exception\Dependency('No Client was declared');
        }
        $this->client = $client;
    }

    /**
     * @return mixed
     */
    public function getClient() {
        return $this->client;
    }
}