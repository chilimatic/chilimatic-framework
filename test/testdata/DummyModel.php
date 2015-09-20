<?php
/**
 *
 * @author j
 * Date: 3/20/15
 * Time: 5:58 PM
 *
 * File: DummyModel.php
 */

namespace test\testdata;

use chilimatic\lib\database\sql\orm\AbstractModel;

class DummyModel extends AbstractModel
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return [
            'id'   => $this->id,
            'name' => $this->name
        ];
    }


}