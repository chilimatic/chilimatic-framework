<?php
use chilimatic\lib\cache\handler\storage\ModelStorage;
use chilimatic\lib\cache\handler\storage\ModelStorageDecorator;
use test\testdata\DummyModel;

/**
 *
 * @author j
 * Date: 3/22/15
 * Time: 11:14 PM
 *
 * File: ModelStorageTest.php
 */

class ModelStorageTest extends PHPUnit_Framework_TestCase
{
    protected $storage;

    /**
     * @before
     */
    public function createANewStorage() {
        $this->storage = new ModelStorage();
    }

    /**
     * @test
     */
    public function storeAndRetriveOneModel() {
        $model = new DummyModel();
        $this->storage->attach($model, null);
        $retModel = $this->storage->get($model);
        $this->assertEquals($model, $retModel);
    }

    public function storeAndFindAModelByParam(){
        $model = new DummyModel();
        $model->setId(12);

        $model2 = new DummyModel();
        $model->setId(12);

        $this->storage->attach($model);
        $this->storage->attach($model2);
        $this->storage->attach($model);


    }




}