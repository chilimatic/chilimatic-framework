<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 31.05.15
 * Time: 15:43
 */

class ConfigFactory_Test extends PHPUnit_Framework_TestCase {


    /**
     * @before
     */
    public function createEmptyConfigFiles() {
        touch(__DIR__ .'/*.cfg');
        touch(__DIR__ .'/*.test.cfg');
        touch(__DIR__ . '/test.ini');
    }

    /**
     * @after
     */
    public function removeEmptyConfigFiles() {
        unlink(__DIR__ .'/*.cfg');
        unlink(__DIR__ .'/*.test.cfg');
        unlink(__DIR__ .'/test.ini');
    }

    /**
     * @test
     */
    public function getFileConfig()
    {
        $c = \chilimatic\lib\config\ConfigFactory::make(
            'File',
            [
                \chilimatic\lib\config\File::CONFIG_PATH_INDEX => __DIR__
            ]
        );

        $this->assertInstanceOf('\chilimatic\lib\config\File', $c);
    }

    /**
     * @test
     */
    public function getIniConfig()
    {
        $c = \chilimatic\lib\config\ConfigFactory::make(
            'Ini',
            [
                'file' => __DIR__ . '/test.ini'
            ]
        );

        $this->assertInstanceOf('\chilimatic\lib\config\Ini', $c);
    }

    /**
     * @test
     *
     * @expectedException \LogicException
     * @expectedExceptionMessage The Config Type has to be specified ... $type is empty
     */
    public function catchLogicExceptionNoType() {
        \chilimatic\lib\config\ConfigFactory::make(
            null,
            [
                \chilimatic\lib\config\File::CONFIG_PATH_INDEX => __DIR__
            ]
        );
    }

    /**
     * @test
     *
     * @expectedException \LogicException
     * @expectedExceptionMessage The Config class has to be implemented and accessible ... chilimatic\lib\config\Asfdasfd is not found
     */
    public function catchLogicExceptionClassDoesNotExist() {
        \chilimatic\lib\config\ConfigFactory::make(
            'asfdasfd',
            [
                \chilimatic\lib\config\File::CONFIG_PATH_INDEX => __DIR__
            ]
        );
    }
}