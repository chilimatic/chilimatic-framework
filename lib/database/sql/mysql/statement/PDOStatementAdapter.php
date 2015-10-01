<?php
namespace chilimatic\lib\database\sql\mysql\statement;


/**
 *
 * @author j
 * Date: 9/29/15
 * Time: 9:31 PM
 *
 * File: PDOStatementAdapter.php
 */

class PDOStatementAdapter extends AbstractMySQLStatement
{
    /**
     * @var \PDOStatement
     */
    private $statement;

    /**
     * @var []
     */
    private $result;

    /**
     * @var \mysqli_stmt
     */
    private $mState;


    /**
     * @return int
     */
    public function rowCount()
    {
        return $this->getDbAdapter()->rowCount();
    }

    /**
     * @return int
     */
    public function columnCount()
    {
        return $this->getDbAdapter()->columnCount();
    }

    /**
     * @return bool
     */
    public function execute()
    {
        return (bool) $this->getStatement()->execute();
    }

    public function setOptions(array $options = [])
    {
        foreach ($options as $name => $option) {

        }
    }

    public function fetchColumn($postion)
    {
        // todo check for difference between libs
    }

    public function getAffectedRows()
    {
        return $this->getStatement()->rowCount();
    }

    public function fetchObject()
    {
        return $this->statement->fetchObject();
    }

    public function fetchAll($resultType)
    {
        return $this->statement->fetchAll();
    }

    public function getAsGenerator($resultType) {

        switch ($resultType) {
            case self::RETURN_TYPE_OBJECT:

                return;
                break;
            case self::RETURN_TYPE_ASSOC:
                break;
            case self::RETURN_TYPE_NUM:
                break;
            case self::RETURN_TYPE_BOTH:
                break;

        }

    }

    public function getInsertId()
    {
        // TODO: Implement getInsertId() method.
    }

    /**
     * @return \PDOStatement
     */
    public function getStatement()
    {
        return $this->statement;
    }

    /**
     * @param \PDOStatement $statement
     *
     * @return $this
     */
    public function setStatement($statement)
    {
        $this->statement = $statement;

        return $this;
    }
}