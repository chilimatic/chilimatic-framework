<<<<<<< HEAD
<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 25.11.13
 * Time: 18:24
 */

namespace chilimatic\lib\database;

/**
 * Class MySQLPool
 *
 * @package chilimatic\lib\database
 */
class MySQLPool
{

    protected $pool = null;

    private $position = 0;

    public function __construct($database = null){
        if (empty($database)) return;

        $this->add($database);
    }

    public function add($database)
    {
        if (empty($database)) return $this;

        if ( empty($this->pool) ) {
            $this->pool[] = $database;
            return $this;
        }

        $new = true;
        $class = get_class($database);

        foreach ($this->pool as $key => $pdb){
            if (get_class($pdb) !== $class) {
                break;
            }

            if ($database == $pdb) {
                $new = false;
                break;
            }
        }

        if ($new === true) {
            $this->pool[] = $database;
        }

        return $this;
    }



=======
<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 25.11.13
 * Time: 18:24
 */

namespace chilimatic\lib\database;

/**
 * Class MySQLPool
 *
 * @package chilimatic\lib\database
 */
class MySQLPool
{

    protected $pool = null;

    private $position = 0;

    public function __construct($database = null){
        if (empty($database)) return;

        $this->add($database);
    }

    public function add($database)
    {
        if (empty($database)) return $this;

        if ( empty($this->pool) ) {
            $this->pool[] = $database;
            return $this;
        }

        $new = true;
        $class = get_class($database);

        foreach ($this->pool as $key => $pdb){
            if (get_class($pdb) !== $class) {
                break;
            }

            if ($database == $pdb) {
                $new = false;
                break;
            }
        }

        if ($new === true) {
            $this->pool[] = $database;
        }

        return $this;
    }



>>>>>>> 336a13ce09982d8ab14d6fadbb1d8dd97927b344
}