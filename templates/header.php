<?php

/*
**************************************************************************************************************************
** CORAL header template
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

/**
 * Common header elements for each module.
 *
 * Gets included in module-specific header.php templates. Before including this template, the following variables should
 * be set:
 * $pageTitle = the name of the current page (done before including module-specific header on each page)
 * $moduleTitle = the name of the current module (done in the module-specific header before including common header)
 *
 * Module-specific header contents should be set after including this template.
 *
 * Usage example (in resources/templates/header.php):
 * $moduleTitle = _('Resources');
 * $moduleIconPath = 'images/title-icon-resources.png';
 * $titleTableWidth = 1024;
 * include_once '../templates/header.php';
 */

include_once 'directory.php';
include_once 'user.php';

$util = new Utility();
$config = new Configuration();

//get the current page to determine which menu button should be depressed
$currentPage = $_SERVER["SCRIPT_NAME"];
$parts = Explode('/', $currentPage);
$currentPage = $parts[count($parts) - 1];

//get CORAL URL for 'Change Module' and logout link.
$coralURL = $util->getCORALURL();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>
        <?php echo $moduleTitle . ' - ' . $pageTitle; ?>
    </title>
    <!-- Common stylesheets -->
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/thickbox.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/datePicker.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/jquery.autocomplete.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/jquery.tooltip.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" />
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700' rel='stylesheet' type='text/css'>
    <link rel="SHORTCUT ICON" href="images/favicon.ico" />
    <!-- Common scripts -->
    <script type="text/javascript" src="js/plugins/jquery.js"></script>
    <script type="text/javascript" src="js/plugins/ajaxupload.3.5.js"></script>
    <script type="text/javascript" src="js/plugins/thickbox.js"></script>
    <script type="text/javascript" src="js/plugins/jquery.autocomplete.js"></script>
    <script type="text/javascript" src="js/plugins/jquery.tooltip.js"></script>
    <script type="text/javascript" src="js/plugins/Gettext.js"></script>
    <script type="text/javascript" src="js/plugins/translate.js"></script>
    <script type="text/javascript" src="js/plugins/date.js"></script>
    <script type="text/javascript" src="js/plugins/jquery.datePicker.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <?php
    // Add translation for the JavaScript files
    global $http_lang;
    $str = substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,5);
    $default_l = $lang_name->getLanguage($str);
    if($default_l==null || empty($default_l)){$default_l=$str;}
    if(isset($_COOKIE["lang"])){
        if($_COOKIE["lang"]==$http_lang && $_COOKIE["lang"] != "en_US"){
            echo "<link rel='gettext' type='application/x-po' href='./locale/".$http_lang."/LC_MESSAGES/messages.po' />";
        }
    }else if($default_l==$http_lang && $default_l != "en_US"){
        echo "<link rel='gettext' type='application/x-po' href='./locale/".$http_lang."/LC_MESSAGES/messages.po' />";
    }
    ?>
</head>
