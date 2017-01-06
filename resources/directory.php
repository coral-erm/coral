<?php

/*
**************************************************************************************************************************
** CORAL Resources Module v. 1.0
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


// Useful directory constants, ending with |/|.
define('ADMIN_DIR', dirname(__FILE__) . '/admin/');
define('BASE_DIR', dirname(__FILE__) . '/');
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
	@date_default_timezone_set(@date_default_timezone_get());
}





//commonly used to convert price into integer for insert into database
function cost_to_integer($price) {

    $price = preg_replace("/[^0-9\.]/", "", $price);

	$decimal_place = strpos($price,".");

    if (strpos($price,".") > 0) {
		$cents = '.' . substr($price, $decimal_place+1, 2);
        $price = substr($price,0,$decimal_place);
    }else{
    	$cents = '.00';
    }

    $price = preg_replace("/[^0-9]/", "", $price);

    if (is_numeric($price . $cents)){
    	return ($price . $cents) * 100;
    }else{
    	return false;
    }
}

//commonly used to convert integer into a price for display
function integer_to_cost($price) {
	//we know this is an integer
	if ($price > 0){
    	return number_format(($price / 100),2,'.',',');
    }else{
    	return "";
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

	return date("m/d/Y", strtotime($mysqlDate));

}

function normalize_date($date) {
    if (($date == "0000-00-00") || ($date == "")){
        return "";
    }else{
        return format_date($date);
    }
}

function is_null_date($date) {
    return (!$date || $date == "0000-00-00" || $date == "");
}

function previous_year($year) {
    return preg_replace_callback(
        '/(19[0-9][0-9]|2[0-9][0-9][0-9])/',
        function ($matches) { return $matches[0]-1; },
        $year,
        1
    );
}

//Watched function to catch the strings being passed into resource_sidemenu for translation
function watchString($string) {
  return $string;
}

function resource_sidemenu($selected_link = '') {
  global $user;
  $links = array(
    'product' => 'butterflyfishicon',
    'acquisitions' => 'acquisitions',
    'access' => 'key',
    'cataloging' => 'cataloging',
    'contacts' => 'contacts',
    'accounts' => 'lock',
    'issues' => 'help',
    'attachments' => 'attachment',
    'routing' => 'routing',
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
    if ($key == 'product' && $class == 'sidemenuselected') {
      $image .= '.jpg';
    } else {
      $image .= '.gif';
    }
    if ($key != 'accounts' || $user->accountTabIndicator == '1') {
    ?>
    <div class="<?php echo $class; ?>" style='position: relative; width: 105px'><span class='icon' id='<?php echo $icon_id; ?>'><img src='<?php echo $image; ?>' alt=""></span><span class='link'><a href='javascript:void(0)' class='show<?php echo $name; ?>' title="<?php echo $name; ?>"><?php echo _($key); ?></a></span>
      <?php if ($key == 'attachments') { ?>
        <span class='span_AttachmentNumber smallGreyText' style='clear:right; margin-left:18px;'></span>
      <?php } ?>
    </div>
    <?php
    }
  }
}

function buildSelectableHours($fieldNameBase,$defaultHour=8) {
  $html = "<select name=\"{$fieldNameBase}[hour]\">";
  for ($hour=1;$hour<13;$hour++) {
    $html .= "<option".(($hour == $defaultHour) ? ' selected':'').">{$hour}</option>";
  }
  $html .= '</select>';
  return $html;
}

function buildSelectableMinutes($fieldNameBase,$intervals=4) {
  $html = "<select name=\"{$fieldNameBase}[minute]\">";
  for ($minute=0;$minute<=($intervals-1);$minute++) {
    $html .= "<option>".sprintf("%02d",$minute*(60/$intervals))."</option>";
  }
  $html .= '</select>';
  return $html;
}

function buildSelectableMeridian($fieldNameBase) {
  return "<select name=\"{$fieldNameBase}[meridian]\">
          <option>AM</option>
          <option>PM</option>
        </select>";
}

function buildTimeForm($fieldNameBase,$defaultHour=8,$minuteIntervals=4) {
  return buildSelectableHours($fieldNameBase,$defaultHour).buildSelectableMinutes($fieldNameBase,$minuteIntervals).buildSelectableMeridian($fieldNameBase);
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
    $codeL = str_replace("-","_",substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,5));
    $http_lang = $lang_name->getLanguage($codeL);
    if($http_lang == "")
      $http_lang = "en_US";
}
putenv("LC_ALL=$http_lang");
setlocale(LC_ALL, $http_lang.".utf8");
bindtextdomain("messages", dirname(__FILE__) . "/locale");
textdomain("messages");

?>
