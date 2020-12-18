<?php

require_once 'safe_file_rewrite.php';

if (!function_exists('write_php_ini')) {
    /**
     * Safely writes to a .ini file from an array
     *
     * @param string $file A path to a .ini file
     *
     * @param array $array The array to write as a .ini file
     *                         integer key of the column you wish to retrieve, or it
     *                         may be the string key name for an associative array.
     */
    function write_php_ini($file, $array)
    {
        $res = array();
        foreach($array as $key => $val)
        {
            if(is_array($val))
            {
                $res[] = "[$key]";
                foreach($val as $skey => $sval) $res[] = "$skey = ".(is_numeric($sval) ? $sval : '"'.addcslashes($sval, '"').'"');
            }
            else $res[] = "$key = ".(is_numeric($val) ? $val : '"'.addcslashes($val, '"').'"');
        }
        safe_file_rewrite($file, implode("\r\n", $res));
    }
}