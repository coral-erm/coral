<?php

/*
**************************************************************************************************************************
** CORAL Organizations Module
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


/**
 * Multibyte capable wordwrap
 *   Found in comments on php.net by Sam B
 * @param string $str
 * @param int $width
 * @param string $break
 * @return string
 */
function mb_wordwrap($str, $width=74, $break="\r\n")
{
    // Return short or empty strings untouched
    if(empty($str) || mb_strlen($str, 'UTF-8') <= $width)
        return $str;

    $br_width  = mb_strlen($break, 'UTF-8');
    $str_width = mb_strlen($str, 'UTF-8');
    $return = '';
    $last_space = false;

    for($i=0, $count=0; $i < $str_width; $i++, $count++)
    {
        // If we're at a break
        if (mb_substr($str, $i, $br_width, 'UTF-8') == $break)
        {
            $count = 0;
            $return .= mb_substr($str, $i, $br_width, 'UTF-8');
            $i += $br_width - 1;
            continue;
        }

        // Keep a track of the most recent possible break point
        if(mb_substr($str, $i, 1, 'UTF-8') == " ")
        {
            $last_space = $i;
        }

        // It's time to wrap
        if ($count > $width)
        {
            // There are no spaces to break on!  Going to truncate :(
            if(!$last_space)
            {
                $return .= $break;
                $count = 0;
            }
            else
            {
                // Work out how far back the last space was
                $drop = $i - $last_space;

                // Cutting zero chars results in an empty string, so don't do that
                if($drop > 0)
                {
                    $return = mb_substr($return, 0, -$drop);
                }

                // Add a break
                $return .= $break;

                // Update pointers
                $i = $last_space + ($br_width - 1);
                $last_space = false;
                $count = 0;
            }
        }

        // Add character from the input string to the output
        $return .= mb_substr($str, $i, 1, 'UTF-8');
    }
    return $return;
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
bindtextdomain("messages", dirname(__FILE__) . "/../locale");
textdomain("messages");
?>