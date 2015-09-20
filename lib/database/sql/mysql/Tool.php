<?php

namespace chilimatic\lib\database\sql\mysql;

/**
 * Class Tool
 *
 * @package chilimatic\lib\database
 */
class Tool
{
    /**
     * generic clean up from db_quoteStr() clone
     *
     * @param string $string
     *
     * @return string
     */
    static public function db_sanitize($string = '')
    {
        function mres($string = '')
        {
            $search  = array("\\", "\x00", "\n", "\r", "'", '"', "\x1a");
            $replace = array("\\\\", "\\0", "\\n", "\\r", "\'", '\"', "\\Z");

            return str_replace($search, $replace, $string);
        }

        if (empty($string)) return '';

        // remove only double empty single quotes
        $string = (string)preg_replace("/[']{2}/", "'", $string);
        $string = (string)str_replace("\\n", "\n", $string);
        $string = (string)str_replace("\\r", "\r", $string);
        $string = (string)str_replace("\\\\", "\\", $string);
        $string = (string)mres($string);

        return $string;
    }


    /**
     * aes mysql decryption (from php.net)
     * http://php.net/manual/de/ref.mcrypt.php
     *
     * @param string $val
     * @param string $ky
     *
     * @return null|string
     */
    static public function mysql_aes_decrypt($val, $ky)
    {

        if (empty($ky) && empty($val)) return null;

        $key = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";
        for ($a = 0; $a < strlen($ky); $a++) {
            $key[$a % 16] = chr(ord($key[$a % 16]) ^ ord($ky[$a]));
        }
        $mode = MCRYPT_MODE_ECB;
        $enc  = MCRYPT_RIJNDAEL_128;
        $dec  = @mcrypt_decrypt($enc, $key, $val, $mode, @mcrypt_create_iv(@mcrypt_get_iv_size($enc, $mode), MCRYPT_DEV_URANDOM));

        return rtrim($dec, ((ord(substr($dec, strlen($dec) - 1, 1)) >= 0 && ord(substr($dec, strlen($dec) - 1, 1)) <= 16) ? chr(ord(substr($dec, strlen($dec) - 1, 1))) : null));
    }

    /**
     * aes mysql encryption
     *
     * @param string $val
     * @param string $ky
     *
     * @return null|string
     */
    static public function mysql_aes_encrypt($val, $ky)
    {

        if (empty($ky) && empty($val)) return null;

        $key = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";
        for ($a = 0; $a < strlen($ky); $a++) {
            $key[$a % 16] = chr(ord($key[$a % 16]) ^ ord($ky[$a]));
        }
        $mode = MCRYPT_MODE_ECB;
        $enc  = MCRYPT_RIJNDAEL_128;
        $val  = str_pad($val, (16 * (floor(strlen($val) / 16) + (strlen($val) % 16 == 0 ? 2 : 1))), chr(16 - (strlen($val) % 16)));

        return mcrypt_encrypt($enc, $key, $val, $mode, mcrypt_create_iv(mcrypt_get_iv_size($enc, $mode), MCRYPT_DEV_URANDOM));
    }

    /**
     * mysql statement wrapper for requesting a encypted specific field value
     *
     * @param $type
     * @param $field
     *
     * @return string
     */
    static public function mysql_encrypt_wrapper($type, $field)
    {
        if (empty($type)) return $field;

        switch (strtoupper($type)) {
            case 'SHA512':
                return "SHA2($field, 512)";
                break;
            case 'MD5':
                return "MD5($field)";
                break;
            case 'SHA1':
                return "SHA1($field)";
                break;
        }

        return $field;
    }
}