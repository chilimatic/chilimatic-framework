<?php
namespace chilimatic\lib\database\sql\orm;

class EntityObjectStorage extends \SplObjectStorage implements \JsonSerializable
{
    /**
     * @var \ReflectionClass
     */
    private $reflection;

    /**
     * keep them cached
     *
     * @var []
     */
    private $columMap;

    /**
     * @param string $columName
     *
     * @return array
     */
    public function getAsArray($columName = null)
    {
        if ($columName) {
            $this->rewind();
            $obj = $this->current();
            if ($obj) {
                if (!$this->reflection) {
                    $this->reflection = new \ReflectionClass($this->current());
                }

                if ($this->columMap === null) {
                    $this->columMap = [];
                    foreach ($this->reflection->getProperties() as $property) {
                        $this->columMap[$property->getName()] = $property;
                    }
                }
            }
        }


        $this->rewind();
        $arr = [];
        while ($this->valid()) {
            if ($columName && !empty($this->columMap[$columName])) {
                $obj = $this->current();
                /**
                 * @var \ReflectionProperty $test
                 */
                $this->columMap[$columName]->setAccessible(true);
                $arr[] = $this->columMap[$columName]->getValue($obj);
            } else {
                $arr[] = $this->current();
            }

            $this->next();
        }

        return $arr;
    }

    public function jsonSerialize()
    {
        return $this->getAsArray();
    }
}