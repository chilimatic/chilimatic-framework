<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 06.02.15
 * Time: 00:21
 */

namespace chilimatic\lib\log\client;

class printOut extends AbstractClient {

    public function send()
    {
        foreach ($this->logMessages as $message) {
            echo $message['date'] . '-' . $message['message'] . '-' . print_r($message['data'], true);
        }
    }

}