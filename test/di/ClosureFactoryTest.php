<?php

/**
 * Created by PhpStorm.
 * User: j
 * Date: 25.11.14
 * Time: 22:14
 */
class ClosureFactory_Test extends PHPUnit_Framework_TestCase
{
    /**
     * @before
     */
    public function destroyDI()
    {
        \chilimatic\lib\di\ClosureFactory::destroyInstance();
    }

    /**
     * @test
     */
    public function closureFactorySingeltonInstance()
    {
        $this->assertInstanceOf('\chilimatic\lib\di\ClosureFactory', \chilimatic\lib\di\ClosureFactory::getInstance());
    }

    /**
     * @test
     */
    public function setServiceClosure()
    {
        $di = \chilimatic\lib\di\ClosureFactory::getInstance();
        $di->set('my-test', function () use ($di) {
            return 'my-test';
        });

        $this->assertEquals('my-test', $di->get('my-test'));
    }

    /**
     * @test
     */
    public function loadClosureSetFromFileViaConstructor()
    {
        $di = \chilimatic\lib\di\ClosureFactory::getInstance(
            __DIR__ . '/../testdata/test-service-list.php'
        );

        $testArray = ['test1', 'test2'];

        $this->assertEquals($testArray, $di->get('my-test', $testArray));
    }

    /**
     * @test
     */
    public function loadClosureSetByArrayViaConstructor()
    {
        $di = \chilimatic\lib\di\ClosureFactory::getInstance(null, [
                'my-test' => function ($setting) {
                    return $setting;
                }
            ]
        );

        $testArray = ['test1', 'test2'];

        $this->assertEquals($testArray, $di->get('my-test', $testArray));
    }

    /**
     * @test
     */
    public function loadClosureSetByArray()
    {
        $di = \chilimatic\lib\di\ClosureFactory::getInstance();
        $di->setServiceList([
            'my-test' => function ($setting) {
                return $setting;
            }
        ]);

        $testArray = ['test1', 'test2'];

        $this->assertEquals($testArray, $di->get('my-test', $testArray));

    }

    /**
     * @test
     */
    public function loadClosureSetByFile()
    {
        $di = \chilimatic\lib\di\ClosureFactory::getInstance();
        $di->loadServiceFromFile(__DIR__ . '/../testdata/test-service-list.php');

        $testArray = ['test1', 'test2'];

        $this->assertEquals($testArray, $di->get('my-test', $testArray));
    }

    /**
     * @test
     */
    public function overrideClosureBySet()
    {
        $di = \chilimatic\lib\di\ClosureFactory::getInstance();
        $di->loadServiceFromFile(__DIR__ . '/../testdata/test-service-list.php');

        $di->set('my-test', function ($setting) {
            return array_pop($setting);
        });
        $testArray = ['test1', 'test2'];

        $this->assertEquals('test2', $di->get('my-test', $testArray));
    }

    /**
     * @test
     *
     * @expectedException BadFunctionCallException
     * @expectedExceptionMessage my-test closure is missing
     */
    public function tryToGetNonExistingClosure()
    {
        $di = \chilimatic\lib\di\ClosureFactory::getInstance();
        $di->get('my-test');
    }

    /**
     * @test
     *
     * @expectedException BadFunctionCallException
     * @expectedExceptionMessage my-test closure is missing
     */
    public function removeClosure()
    {
        $di = \chilimatic\lib\di\ClosureFactory::getInstance();
        $di->set('my-test', function () {
        });
        $di->remove('my-test');
        $di->get('my-test');
    }

    /**
     * @test
     */
    public function getClosure()
    {
        $di = \chilimatic\lib\di\ClosureFactory::getInstance();
        $di->set('my-test', function () {
        });

        $this->assertInstanceOf('\Closure', $di->getClosure('my-test'));
    }

    /**
     * @test
     *
     * @expectedException BadFunctionCallException
     * @expectedExceptionMessage my-test closure is missing
     */
    public function destroyInstance()
    {
        $di = \chilimatic\lib\di\ClosureFactory::getInstance();
        $di->set('my-test', function () {
        });

        \chilimatic\lib\di\ClosureFactory::destroyInstance();

        $di = \chilimatic\lib\di\ClosureFactory::getInstance();
        $di->set('my-test2', function () {
        });

        $di->get('my-test');
    }

    /**
     * @test
     */
    public function checkIfClosureExists()
    {
        $di = \chilimatic\lib\di\ClosureFactory::getInstance();
        $di->set('my-test', function () {
        });

        $this->assertEquals(true, $di->exists('my-test'));
    }


    /**
     * @test
     */
    public function checkIfPseudoSingeltonIsWorking()
    {
        $di = \chilimatic\lib\di\ClosureFactory::getInstance();
        $di->set('my-test', function () {
            return new \stdClass();
        });

        $asSingelton  = $di->get('my-test', [], true);
        $asSingelton2 = $di->get('my-test', [], true);

        $this->assertEquals(true, $asSingelton === $asSingelton2);
    }
}