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

// Define the MODULE base directory, ending with |/|.
define('BASE_DIR', dirname(__FILE__) . '/');

require_once BASE_DIR . "../common/common_directory.php";

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
    'Product',
    'Orders',
    'Acquisitions',
    'Access',
    'Cataloging',
    'Contacts',
    'Issues',
    'Attachments',
    'Workflow',
  );

  foreach ($links as $key) {
    $name = ucfirst($key);
    $selected_link = ucfirst($selected_link);
    if ($selected_link == $key) {
      $class = 'sidemenuselected';
      $icon_id = "icon_$key";
    } else {
      $class = 'sidemenuunselected';
      $icon_id = "";
    }
    if ($key != 'accounts' || $user->accountTabIndicator == '1') {
    ?>
    <div class="<?php echo $class; ?>" style='position: relative; width: 105px'><span class='link'><a href='javascript:void(0)' class='show<?php echo $name; ?>' title="<?php echo $name; ?>"><?php echo _($key); ?></a></span>
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

?>
