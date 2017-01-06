<?php

/*
**************************************************************************************************************************
** CORAL Usage Statistics Module
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

ini_set('max_execution_time', 1000); 
ini_set("default_socket_timeout", 120);
ini_set('memory_limit', '256M');


// Useful directory constants, ending with |/|.
define('BASE_DIR', dirname(__FILE__) . '/');
define('ADMIN_DIR', BASE_DIR . 'admin/');
define('CLASSES_DIR', ADMIN_DIR . 'classes/');

// Automatically load undefined classes from subdirectories of |CLASSES_DIR|.
function __autoload( $className ) {
	if (file_exists(CLASSES_DIR) && is_readable(CLASSES_DIR) && is_dir(CLASSES_DIR)) {
		$directory = dir(CLASSES_DIR);

		// Iterate over the files and directories in |CLASSES_DIR|.
		while (false !== ($entry = $directory->read())) {
			$path = CLASSES_DIR . $entry;

			// Look only at subdirectories
			if (is_dir($path)) {
				$filename = $path . '/' . $className . '.php';
				if (file_exists($filename) && is_readable($filename) && is_file($filename)) {
					// Could probably safely use |require()| here, since |__autoload()| is only called when a class isn't loaded.
					require_once($filename);
				}
			}
		}
		$directory->close();
	}
}

// Add lcfirst() for PHP < 5.3.0
if (false === function_exists('lcfirst')) {
	function lcfirst($string) {
		return strtolower(substr($string, 0, 1)) . substr($string, 1);
	}
}


//fix default timezone for PHP > 5.3
if(function_exists("date_default_timezone_set") and function_exists("date_default_timezone_get")){
  if (@date_default_timezone_get()){
	   date_default_timezone_set(@date_default_timezone_get());
  }else{
      date_default_timezone_set('UTC');  
  }
  
}





function format_date($mysqlDate) {

	//see http://php.net/manual/en/function.date.php for options

	//there is a dependence on strtotime recognizing date format for date inputs
	//thus, european format (d-m-Y) must use dashes rather than slashes

	//upper case Y = four digit year
	//lower case y = two digit year
	//make sure digit years matches for both directory.php and common.js

	//SUGGESTED: "m/d/Y" or "d-m-Y"

  if ($mysqlDate != ""){
   if ((date("Hi", strtotime($mysqlDate)) > 0)){
    return date("m/d/Y h:i a", strtotime($mysqlDate));
   }else{
    return date("m/d/Y", strtotime($mysqlDate)); 
   }
	 
  }else{
    return "";
  }

}

//Watched function to catch the strings being passed into resource_sidemenu for translation
function watchString($string) {
  return $string;
}

function usage_sidemenu($selected_link = '') {
  global $user;
  $links = array(
    'imports' => 'seahorseicon',
    'titles' => 'acquisitions',
    'statistics' => 'xls',
    'logins' => 'key',
    'sushi' => 'arrow_sides',
  );
  
  foreach ($links as $key => $icon) {
    $name = ucfirst($key);
    if ($selected_link == $key) {
      $class = 'sidemenuselected';
      $image = "images/".$icon;
      $icon_id = "icon_$key";
    } else {
      $class = 'sidemenuunselected';
      $image = "images/".$icon."_bw";
      $icon_id = "";
    }
    if ($key == 'imports') {
      $image .= '.png';
    } else {
      $image .= '.gif';
    }
    if ($key != 'accounts' || $user->accountTabIndicator == '1') {
    ?>
    <div class="<?php echo $class; ?>" style='position: relative; width: 105px'>
    	<span class='icon' id='<?php echo $icon_id; ?>'><img src='<?php echo $image; ?>'></span><span class='link'><a href='javascript:void(0)' class='show<?php echo $name; ?>'><?php echo _($key); ?></a></span>
    </div>
    <?php
    }
  }
}

function debug($value) {
  echo '<pre>'.print_r($value, true).'</pre>';
}

// Include file of language codes
include_once 'LangCodes.php';
$lang_name = new LangCodes();

// Verify the language of the browser
global $http_lang;
if(isset($_COOKIE["lang"])){
    $http_lang = $_COOKIE["lang"];
}else{        
    $codeL = substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,5);
    $http_lang = $lang_name->getLanguage($codeL);
    if($http_lang == "")
      $http_lang = "en_US";
}
putenv("LC_ALL=$http_lang");
setlocale(LC_ALL, $http_lang.".utf8");
bindtextdomain("messages", dirname(__FILE__) . "/locale");
textdomain("messages");

?>