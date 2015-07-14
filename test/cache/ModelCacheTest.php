<?php

/**
 *
 * @author j
 * Date: 3/20/15
 * Time: 5:23 PM
 *
 * File: ModelCache.php
 */
class ModelCacheTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function storeModelInCache()
    {
        $model      = new \test\testdata\DummyModel();
        $modelCache = new \chilimatic\lib\cache\handler\ModelCache();
        $modelCache->set($model, ['id' => 12]);
        $model = $modelCache->get(new \test\testdata\DummyModel(), ['id' => 12]);

        //$this->assertInstanceOf('\chilimatic\lib\orm\AbstractModel', $model);

    }

}