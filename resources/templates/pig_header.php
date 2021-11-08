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
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="public">
<title>Resources Module - <?php echo $pageTitle; ?></title>
<link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
<link rel="stylesheet" href="css/thickbox.css" type="text/css" media="screen" />
<link rel="stylesheet" href="../css/datePicker.css" type="text/css" media="screen" />
<link rel="stylesheet" href="../css/jquery.autocomplete.css" type="text/css" media="screen" />
<link rel="stylesheet" href="css/jquery.tooltip.css" type="text/css" media="screen" />
<link rel="SHORTCUT ICON" href="images/favicon.ico" />
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700' rel='stylesheet' type='text/css'>
<!--<script type="text/javascript" src="../js/plugins/jquery-1.4.4.js"></script>-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
<script type="text/javascript" src="../js/plugins/ajaxupload.3.5.js"></script>
<script type="text/javascript" src="js/plugins/thickbox.js"></script>
<script type="text/javascript" src="../js/plugins/jquery.autocomplete.js"></script>
<script type="text/javascript" src="../js/plugins/Gettext.js"></script>
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
<script type="text/javascript" src="../js/plugins/translate.js"></script>
<script type="text/javascript" src="../js/plugins/datejs-patched-for-i18n.js"></script>
<script type="text/javascript" src="../js/plugins/jquery.datePicker-patched-for-i18n.js"></script>
<script type="text/javascript" src="js/common.js"></script>
</head>
<body>
<noscript><font face='arial'><?php echo _("JavaScript must be enabled in order for you to use CORAL. However, it seems JavaScript is either disabled or not supported by your browser. To use CORAL, enable JavaScript by changing your browser options, then ");?><a href=""><?php echo _("try again");?></a>. </font></noscript>

<div class="wrapper">
<center>
<table id="main-table">

<tr>
<td style='vertical-align:top;'>
<div style="text-align:left;">

<center>
<table class="titleTable" style="width:1024px;text-align:left;">

    <tr style='vertical-align:top;'>
        <td style='height:53px;' colspan='3'>


            <div id="main-title">
                <img src="images/title-icon-resources.png" />
                <span id="main-title-text"><?php echo _("Resources"); ?></span>
                <span id="powered-by-text"><?php echo _("Powered by");?><img src="images/logo-coral.jpg" /></span>
            </div>

            <div id="menu-login" style='margin-top:1px;'>
                <span class='smallText' style='color:#526972;'>
                </span><br />

                <span id="setLanguage">
                    <select name="lang" id="lang" class="dropDownLang">
                       <?php
                        // Get all translations on the 'locale' folder
                        $route='locale';
                        $lang[]="en_US"; // add default language
                        if (is_dir($route)) {
                            if ($dh = opendir($route)) {
                                while (($file = readdir($dh)) !== false) {
                                    if (is_dir("$route/$file") && $file!="." && $file!=".."){
                                        $lang[]=$file;
                                    }
                                }
                                closedir($dh);
                            }
                        }else {
                            echo "<br>"._("Invalid translation route!");
                        }
                        // Get language of navigator
                        $defLang = $lang_name->getBrowserLanguage();

                        // Show an ordered list
                        sort($lang);
                        for($i=0; $i<count($lang); $i++){
                            if(isset($_COOKIE["lang"])){
                                if($_COOKIE["lang"]==$lang[$i]){
                                    echo "<option value='".$lang[$i]."' selected='selected'>".$lang_name->getNameLang($lang[$i])."</option>";
                                }else{
                                    echo "<option value='".$lang[$i]."'>".$lang_name->getNameLang($lang[$i])."</option>";
                                }
                            }else{
                                if($defLang==substr($lang[$i],0,5)){
                                    echo "<option value='".$lang[$i]."' selected='selected'>".$lang_name->getNameLang($lang[$i])."</option>";
                                }else{
                                    echo "<option value='".$lang[$i]."'>".$lang_name->getNameLang($lang[$i])."</option>";
                                }
                            }
                        }
                        ?>

                    </select>
                </span>
            </div>

        </td>
    </tr>

    <tr style='vertical-align:top'>
        <td style='width:870px;height:19px;' id="main-menu-titles" colspan="2">

            <a href='index.php' title="<?php echo _("Home") ?>">
                <div class="main-menu-link <?php if ($currentPage == 'index.php') { echo "active"; } ?>">
                    <img src="images/menu/icon-home.png" />
                    <span><?php echo _("Home");?></span>
                </div>
            </a>

        </td>

        <td style='width:130px;height:19px;' align='right'>&nbsp;</td>
    </tr>
</table>
    <script>
        $("#lang").change(function() {
            setLanguage($("#lang").val());
            location.reload();
        });

        function setLanguage(lang) {
            var wl = window.location, now = new Date(), time = now.getTime();
            var cookievalid=2592000000; // 30 days (1000*60*60*24*30)
            time += cookievalid;
            now.setTime(time);
            document.cookie ='lang='+lang+';path=/'+';domain='+wl.host+';expires='+now;
        }
    </script>
<span id='span_message' class='darkRedText' style='text-align:left;'><?php if (isset($_POST['message'])) echo $_POST['message']; if (isset($errorMessage)) echo $errorMessage; ?></span>
