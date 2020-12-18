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

// Define the MODULE base directory, ending with |/|.
define('BASE_DIR', dirname(__FILE__) . '/');

require_once "../common/common_directory.php";


//Watched function to catch the strings being passed into resource_sidemenu for translation
function watchString($string) {
  return $string;
}

function usage_sidemenu($selected_link = '') {
  global $user;
  $links = array(
    'imports' => _("Imports"),
    'titles' => _("Titles"),
    'statistics' => _("Statistics"),
    'logins' => _("Logins"),
    'sushi' => _("Sushi"),
  );

  foreach ($links as $key => $value) {
    $name = mb_convert_case($key, MB_CASE_TITLE, "UTF-8");
    if ($selected_link == $key) {
      $class = 'sidemenuselected';
    } else {
      $class = 'sidemenuunselected';
    }
    if ($key != 'accounts' || $user->accountTabIndicator == '1') {
    ?>
    <div class="<?php echo $class; ?>" style='position: relative; width: 105px'>
       <span class='link'><a href='javascript:void(0)' class='show<?php echo $name; ?>' title='<?php echo $value; ?>'><?php echo $value; ?></a></span>
    </div>
    <?php
    }
  }
}

?>
