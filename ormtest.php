<?php
/**
 *
 * @author j
 * Date: 3/17/15
 * Time: 8:17 PM
 *
 * File: ormtest.php
 */

require_once ('vendor/autoload.php');

$dispatcher = \chilimatic\lib\di\ClosureFactory::getInstance(
    realpath('./lib/general/config/default-service-collection.php')
);
chilimatic\lib\config\Config::getInstance();
$dispatcher->set('entity-manager', function(){
        $mysqlStorage = new \chilimatic\lib\database\mysql\MysqlConnectionStorage();
        $mysqlStorage->addConnection(
            'localhost',
            'root',
            '$karpunk1',
            'chilimatic',
            null
        );

        $em = new \chilimatic\lib\database\orm\EntityManager(
            new \chilimatic\lib\database\mysql\Mysql($mysqlStorage->getConnection(0))
        );
        return $em->setQueryBuilder(\chilimatic\lib\di\ClosureFactory::getInstance()->get('query-builder'));
});


/**
 * Class User
 *
 * @ORM table=chilimatic.user;
 * @package chilimatic\app\model
 */
class User extends \chilimatic\lib\database\orm\AbstractModel{

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $group_id;

    /**
     * @var Group
     */
    protected $group;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var bool
     */
    protected $active;

    /**
     * @var bool
     */
    protected $deleted;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $updated;

    /**
     * @var string
     */
    protected $created;

    /**
     * this is the mapping part of the entity system
     * so I don't have to parse the classes
     *
     * the reason why it's a json is to save memory if we got 1000 arrays it's a lot of mem in php
     * @ORM group_id=Group;
     *
     * @var string
     */
    protected $fieldMapping;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getGroupId()
    {
        return $this->group_id;
    }

    /**
     * @param int $group_id
     *
     * @return $this
     */
    public function setGroupId($group_id)
    {
        $this->group_id = $group_id;

        return $this;
    }

    /**
     * @return Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param Group $group
     *
     * @return $this
     */
    public function setGroup($group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     *
     * @return $this
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param boolean $deleted
     *
     * @return $this
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param string $updated
     *
     * @return $this
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * @return string
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param string $created
     *
     * @return $this
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'active' => $this->active,
            'group_id' => $this->group_id,
            'deleted' => $this->deleted,
            'created' => $this->created,
            'updated' => $this->updated
        ];
    }
}

/**
 * Class Group
 *
 * @ORM table=chilimatic.group;
 *
 * @package chilimatic\app\model
*/
class Group extends \chilimatic\lib\database\orm\AbstractModel
{

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $active;

    /**
     * @var bool
     */
    protected $deleted;

    /**
     * @var string
     */
    protected $updated;

    /**
     * @var string
     */
    protected $created;

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
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     *
     * @return $this
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param mixed $deleted
     *
     * @return $this
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param mixed $updated
     *
     * @return $this
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     *
     * @return $this
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'active' => $this->active,
            'deleted' => $this->deleted,
            'created' => $this->created,
            'updated' => $this->updated
        ];
    }
}

/**
 * @var \chilimatic\lib\database\orm\EntityManager $em
 */
$em = $dispatcher->get('entity-manager');

/**
 * @var User $user
 */
$user = $em->findBy(new User(), ['id' => 1]);
$user->setEmail('test@test.de');
$em->persist($user);
var_dump($user);