<?php
/**
 * little collection of useful functionality
 * Enter description here ...
 * @author j
 *
 */

namespace chilimatic\lib\Tool;

use chilimatic\lib\config\Config;
use chilimatic\lib\exception\Exception_InvalidArgument;

/**
 * Class Tool
 *
 * @package chilimatic\lib\Tool
 */
class Tool
{

    /**
     * get a nested key based array
     *
     * @param $array_map
     * @param $map_keys
     * @param $bind_value
     *
     * @internal param array $arr
     * @internal param array $keys
     * @internal param mixed $value
     *
     * @credits  the ideas was taken from stack overflow and slightly modified
     *
     * @return array
     */
    public static function generate_nested_array( $array_map , $map_keys , $bind_value )
    {

        $reference = &$array_map;
        
        while ( count($map_keys) > 0 )
        {
            // get next first key
            $new_map = array_shift($map_keys);
            
            // if $reference isn't an array already, make it one
            if ( !is_array($reference) )
            {
                $reference = array();
            }
            
            // moves the reference deeper
            $reference = &$reference[$new_map];
        }
        $reference = $bind_value;
        
        // returns the nested array
        return $array_map;
    }    

    /**
     * returns if an IP is valid
     *
     * @param string $ip            
     * @return boolean
     */
    public static function validate_ip( $ip = '' )
    {

        if ( empty($ip) ) return false;
        
        return (ip2long($ip) !== false) ? true : false;
    }





    /**
     * removes multiple slashes and backslashes
     *
     * @param $path string            
     * @return string
     */
    public static function clean_up_path( $path )
    {

        if ( empty($path) ) return '';
        if ( strpos($path, '\\') !== false )
        {
            return str_replace("\\\\", "\\", $path);
        }
        else
        {
            return str_replace("//", "/", $path);
        }
    }


    /**
     * trims the content of an array
     *
     * @param $array array            
     *
     * @return array
     */
    public static function trim_array( $array )
    {

        if ( empty($array) || !is_array($array) ) return $array;
        
        foreach ( $array as $key => $value )
        {
            $array[$key] = trim($value);
        }
        
        unset($key, $value);
        
        return $array;
    }


    /**
     * For an associative multi-dimensional array, pull out the one-dimensional
     * array along the $key column
     * Also works if the array consists of objects, not arrays
     *
     * @param $thing array|object
     * @param $key   string
     *
     * @return array|bool
     */
    public static function getSubArray( $thing , $key )
    {

        if ( is_object($thing) )
        {
            $array = get_object_vars($thing);
        }
        else
        {
            $array = $thing;
        }
        if ( !is_array($array) || empty($array) ) return false;
        
        $res = array();
        
        foreach ( $array as $idx => $subarray )
        {
            if ( is_object($subarray) )
            {
                if ( isset($subarray->$key) ) $res[$idx] = $subarray->$key;
            }
            else
            {
                if ( isset($subarray[$key]) ) $res[$idx] = $subarray[$key];
            }
        }
        
        return $res;
    }


    /**
     * Take a number an re-format the number to the target decimal separator
     *
     * @param $number                   float
     *
     * @internal param string $target_decimal_separator
     *
     * @return float
     */
    public static function php_number_format( $number )
    {

        preg_match('/[.,](\d+)$/', $number, $matches);
        
        $num_decimals = strlen($matches[1]);
        
        return preg_replace('/[.,]/', '', $number) / pow(10, $num_decimals);
    }


    /**
     * update a parameter in a query string with a new value
     *
     * @param $key          string
     * @param $value        string
     * @param $reverseorder bool
     * @param $qs           string
     *
     * @return mixed|string
     */
    public static function updateQS( $key , $value , $reverseorder = false , $qs = '' )
    {

        if ( empty($qs) )
        {
            $qs = $_SERVER['QUERY_STRING'];
        }
        if ( $reverseorder )
        {
            if ( preg_match("/ascdesc=ASC/", $qs) )
            {
                $qs = self::updateQS('ascdesc', 'DESC', 0, $qs);
            }
            else
            {
                $qs = self::updateQS('ascdesc', 'ASC', 0, $qs);
            }
        }
        if ( !$qs )
        {
            if ( $value )
            {
                return "$key=$value";
            }
            else
            {
                return "";
            }
        }
        if ( preg_match("/$key=/", $qs) )
        {
            if ( $value )
            {
                return preg_replace("/$key=[^&]+/", "$key=$value", $qs);
            }
            else
            {
                return preg_replace("/$key=[^&]?/", "", $qs);
            }
        }
        else
        {
            if ( $value )
            {
                return $qs . "&$key=$value";
            }
            else
            {
                return $qs;
            }
        }
    }


    /**
     * Array_Diff Implementation for multi-dimensional arrays
     * Can only handle 2 arrays
     *
     * @param $array1 array
     * @param $array2 array
     *
     * @return array
     */
    public static function array_diff_multi( $array1 , $array2 )
    {

        $res = array();
        foreach ( $array1 as $entry )
        {
            if ( !in_array($entry, $array2) )
            {
                $res[] = $entry;
            }
        }
        return $res;
    }


    /**
     * Sort an array by a key
     *
     * @param $array                                                        array
     * @param $key                                                          string
     * @param \chilimatic\Tool\SORT_ASC|\chilimatic\Tool\SORT_DESC|int $asc SORT_ASC|SORT_DESC
     *
     * @throws Exception_InvalidArgument
     */
    public static function usortByArrayKey( &$array , $key , $asc = SORT_ASC )
    {

        $sort_flags = array(
                            SORT_ASC, 
                            SORT_DESC
        );
        
        if ( !in_array($asc, $sort_flags) ) throw new Exception_InvalidArgument('sort flag only accepts SORT_ASC or SORT_DESC');
        
        $cmp = function ( array $a , array $b ) use($key , $asc , $sort_flags )
        {
            if ( !is_array($key) )
            { // just one key and sort direction
                if ( !isset($a[$key]) || !isset($b[$key]) )
                {
                    throw new \Exception('attempting to sort on non-existent keys');
                }
                if ( $a[$key] == $b[$key] ) return 0;
                return ($asc == SORT_ASC xor $a[$key] < $b[$key]) ? 1 : -1;
            }
            else
            { // using multiple keys for sort and sub-sort
                foreach ( $key as $sub_key => $sub_asc )
                {
                    // array can come as 'sort_key'=>SORT_ASC|SORT_DESC or just
                    // 'sort_key', so need to detect which
                    if ( !in_array($sub_asc, $sort_flags) )
                    {
                        $sub_key = $sub_asc;
                        $sub_asc = $asc;
                    }
                    // just like above, except 'continue' in place of return 0
                    if ( !isset($a[$sub_key]) || !isset($b[$sub_key]) )
                    {
                        throw new \Exception('attempting to sort on non-existent keys');
                    }
                    if ( $a[$sub_key] == $b[$sub_key] ) continue;
                    return ($sub_asc == SORT_ASC xor $a[$sub_key] < $b[$sub_key]) ? 1 : -1;
                }
                return 0;
            }
        };
        usort($array, $cmp);
    }


    /**
     * Check for a valid email
     *
     * @param $email string            
     * @return boolean
     */
    public static function checkValidEmail( $email )
    {

        if ( !preg_match("#^[_a-z0-9+-]+(\\.[_a-z0-9+-]+)*@[a-z0-9-]+(\\.[a-z0-9-]+)*(\\.[a-z]{2,6})$#", $email) )
        {
            return false;
        }
        return true;
    }


    /**
     * generates a bitly url
     *
     * @param string $url
     * @param string $login
     * @param string $appkey
     * @param string $format
     *
     * @return string
     */
    public static function make_bitly_url( $url , $login = '' , $appkey = 'R_f0b76fa0948d2cb9f223fb5387ad09ba' , $format = 'xml' )
    {
        // create the URL
        $bitly = 'http://api.bitly.com/v3/shorten?longUrl=' . urlencode($url) . '&login=' . urlencode($login) . '&apiKey=' . $appkey . '&format=' . $format;
        
        // get the url
        // could also use cURL here
        $response = file_get_contents($bitly);
        
        // parse depending on desired format
        if ( strtolower($format) == 'json' )
        {
            $json = @json_decode($response, true);
            return $json['results'][$url]['shortUrl'];
        }
        else // xml
        {
            $xml = simplexml_load_string($response);
            return Config::get('bitly_url') . $xml->data->hash;
        }
    }


    /**
     * Turn an array into an associative array assigned by the given key
     *
     * @param array $array
     * @param string $key
     * @param bool $group
     *            whether to group items into arrays (use if key is non-unique
     *            in the array)
     *
     * @return array
     */
    public static function assignByKey( $array , $key , $group = false )
    {

        if ( empty($array) ) return $array;
        
        $res = array();
        foreach ( $array as $subthing )
        {
            $idx = null;
            if ( is_object($subthing) && property_exists($subthing, $key) )
            {
                $idx = $subthing->$key;
            }
            elseif ( is_array($subthing) && isset($subthing[$key]) )
            {
                $idx = $subthing[$key];
            }
            if ( $idx !== null )
            {
                if ( !$group )
                {
                    $res[$idx] = $subthing;
                }
                else
                {
                    if ( !isset($res[$idx]) ) $res[$idx] = array();
                    $res[$idx][] = $subthing;
                }
            }
        }
        return $res;
    }


}