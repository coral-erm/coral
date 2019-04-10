<?php

/*
**************************************************************************************************************************
** CORAL Common Directory
**
** Copyright (c) 2010 University of Notre Dame
**
** This file is part of CORAL.
**
** CORAL is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
**
** CORAL is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License along with CORAL.  If not, see <http://www.gnu.org/licenses/>.
**
**************************************************************************************************************************
*/

// Increase memory due to large sized reports
ini_set('max_execution_time', 1000);
ini_set("default_socket_timeout", 1000);
ini_set('memory_limit', '256M');

define('ADMIN_DIR', BASE_DIR . 'admin/');
define('CLASSES_DIR', ADMIN_DIR . 'classes/');
define('INTERFACES_DIR', ADMIN_DIR . 'interfaces/');

// Automatically load undefined classes from subdirectories of |CLASSES_DIR|.
spl_autoload_register( function ($className) {
	$directories = array(CLASSES_DIR,INTERFACES_DIR);
	foreach ($directories as $currentDirectory) {
		if (file_exists($currentDirectory) && is_readable($currentDirectory) && is_dir($currentDirectory)) {
			$directory = dir($currentDirectory);
			// Iterate over the files and directories in |CLASSES_DIR|.
			while (false !== ($entry = $directory->read())) {
				$path = $currentDirectory . $entry;
				// Look only at subdirectories
				if (is_dir($path)) {
					$filename = $path . '/' . $className . '.php';
					if (file_exists($filename) && is_readable($filename) && is_file($filename)) {
						// Could probably safely use |require()| here, since |__autoload()| is only called when a class isn't loaded.
						require_once($filename);
						break;
					}
				}
			}
			$directory->close();
		}
	}
});


// Add lcfirst() for PHP < 5.3.0
if (false === function_exists('lcfirst')) {
	function lcfirst($string) {
		return strtolower(substr($string, 0, 1)) . substr($string, 1);
	}
}

//fix default timezone for PHP > 5.3
if(function_exists("date_default_timezone_set") and function_exists("date_default_timezone_get")){
	@date_default_timezone_set(@date_default_timezone_get());
}

function return_date_format() {
    $config = new Configuration();
    $config_date_format = $config->settings->date_format;
    if (isset($config_date_format) && $config_date_format != '') {
        $date_format = $config_date_format;
    } else {
        $date_format = "%m/%d/%Y";
    }
    return $date_format;
}

function return_sql_locale() {
    $config = new Configuration();
    $config_sql_locale = $config->database->sql_locale;
    if (isset($config_sql_locale) && $config_sql_locale != '') {
        $sql_locale = "'$config_sql_locale'";
    } else {
        // If not set, setting to null will use MySQL/MariaDB default
        $sql_locale = "NULL";
    }
    return $sql_locale;
}

function return_sql_decimals() {
    $config = new Configuration();
    $config_sql_decimals = $config->database->sql_decimals;
    if (isset($config_sql_decimals) && $config_sql_decimals != '') {
        $sql_decimals = $config_sql_decimals;
    } else {
        $sql_decimals = 2;
    }
    return $sql_decimals;
}


function format_date($mysqlDate) {

	//see http://php.net/manual/en/function.date.php for options

	//upper case Y = four digit year
	//lower case y = two digit year
	//make sure digit years matches for both directory.php and common.js

	//SUGGESTED: "m/d/Y" or "d-m-Y"
    return strftime(return_date_format(), strtotime($mysqlDate));

}

function debug($value) {
  echo '<pre>'.print_r($value, true).'</pre>';
}

// Include file of language codes
include_once BASE_DIR . '/../LangCodes.php';
$lang_name = new LangCodes();

// Verify the language of the browser
$GLOBALS['http_lang'] = NULL;

if(isset($_COOKIE["lang"])) {
	$GLOBALS['http_lang'] = $_COOKIE["lang"];
} else {
	$codeL = str_replace("-","_",substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,5));
	$GLOBALS['http_lang'] = $lang_name->getLanguage($codeL);

	if($GLOBALS['http_lang'] == "") {
		$GLOBALS['http_lang'] = "en_US";
	}
}

putenv("LC_ALL={$GLOBALS['http_lang']}");
setlocale(LC_ALL, "{$GLOBALS['http_lang']}.utf8");
bindtextdomain("messages", BASE_DIR."locale");
textdomain("messages");

?>
