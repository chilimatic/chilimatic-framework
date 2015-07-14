<?php

/**
 *
 * @author j
 * Date: 5/14/15
 * Time: 5:38 PM
 *
 * File: ShmopCacheTest.php
 */
class ShmopCacheTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function checkIfShmopImplementsTheCorrectInterface()
    {
        $this->assertInstanceOf('\chilimatic\lib\cache\engine\CacheInterface', new \chilimatic\lib\cache\engine\Shmop());
    }
}