<?php
/**
 * Sub class for easy use
 * so that lazy bastards kann use the function of
 * the original class without to worry about the
 * ressources :)
 *
 * @author j
 *
 *
 */
namespace chilimatic\lib\database\sql\mysql;

/**
 * Class Simple
 *
 * @package chilimatic\lib\database
 */
final class Simple extends Mysql
{


    /**
     * constructor
     *
     * @param $master_param object
     * @param $slave_param  object
     * @param $persistent   bool
     */
    public function __construct($master_param, $slave_param = null, $persistent = false)
    {

        if (empty($master_param->host) || empty($master_param->username) || empty($master_param->password)) return;

        $this->persistent = $persistent;
        $this->connect($master_param->host, $master_param->username, $master_param->password, $master_param->dbname, false);

        if (!empty($slave_param)) {
            $this->connect($slave_param->host, $slave_param->username, $slave_param->password, true);
        }

        return;
    }


    /**
     * fetches an array with only 1 dimension
     * "SELECT <col_name> FROM <tablename>
     *
     * array($col_entry1,$col_entry2)
     *
     * @param $sql string
     *
     * @return array
     */
    public function fetch_simple_list($sql)
    {

        if (empty($sql)) return false;
        $result = array();
        $res    = $this->query($sql);

        while (($row = @mysql_fetch_array($res)) !== false) {
            $result[] = $row[0];
        }

        $this->free($res);

        return $result;
    }


    /**
     * fetches an associative array
     *
     * @param $sql string
     *
     * @return bool array
     */
    public function fetch_assoc($sql)
    {

        if (empty($sql)) return false;

        $res    = $this->query($sql);
        $result = @mysql_fetch_assoc($res);
        $this->free($res);

        return $result;
    }


    /**
     * simple wrapper for those lazy bastards who don't wanna
     * control their own ressources
     *
     * @param $sql       string
     * @param $assign_by string
     *
     * @return array bool
     */
    public function fetch_assoc_list($sql, $assign_by = null)
    {

        if (empty($sql)) return false;

        $result = array();

        // send the query
        $res = $this->query($sql);

        if (empty($res)) return false;

        while (($row = @mysql_fetch_assoc($res)) !== false) {
            if (!empty($assign_by)) {
                $result[$row[$assign_by]] = $row;
            } elseif (!empty($assign_by) && is_array($assign_by)) {
                $key = '';
                // for more complex keys
                foreach ($assign_by as $key_w) {
                    $key = "- $row[$key_w]";
                }
                // removes the first '- '
                $key          = substr($key, 2);
                $result[$key] = $row;
            } else {
                $result[] = $row;
            }
        }

        // free the ressource afterwards
        $this->free($res);

        return $result;
    }


    /**
     * fetches an object of the current row
     *
     * @param $sql string
     *
     * @return bool object
     */
    public function fetch_object($sql)
    {

        if (empty($sql)) return false;

        $res    = $this->query($sql);
        $result = @mysql_fetch_object($res);
        $this->free($res);

        return $result;
    }


    /**
     * fetches an object list of associative arrays
     * which can be assigned to a specific key from the row as well
     *
     * @param $sql       string
     * @param $assign_by string
     *
     * @return array bool
     */
    public function fetch_object_list($sql, $assign_by = null)
    {

        if (empty($sql)) return false;

        $result = array();

        $res = $this->query($sql);

        while (($row = @mysql_fetch_object($res)) !== false) {
            if (!empty($assign_by) && !is_array($assign_by)) {
                $result[$row->$assign_by] = $row;
            } elseif (!empty($assign_by) && is_array($assign_by)) {
                $key = '';
                // for more complex keys
                foreach ($assign_by as $key_w) {
                    $key = "- $row->$key_w";
                }
                // removes the first '- '
                $key          = substr($key, 2);
                $result[$key] = $row;
            } else {
                $result[] = $row;
            }
        }

        $this->free($res);

        return $result;
    }


    /**
     * fetches a string wrapper for
     * easy use
     *
     * @param $sql string
     *
     * @return string
     */
    public function fetch_string($sql)
    {

        if (empty($sql)) return false;

        $res = $this->query($sql);
        $row = @mysql_fetch_array($res);

        return (string)$row[0];
    }


    /**
     *
     * @see Database::__destruct()
     */
    function __destruct()
    {

        parent::__destruct();
    }
}