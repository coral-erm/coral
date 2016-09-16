<?php
/*
**************************************************************************************************************************
** CORAL Organizations Module v. 1.0
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

include_once 'user.php';

$util = new Utility();
$config = new Configuration();

//get the current page to determine which menu button should be depressed
$currentPage = $_SERVER["SCRIPT_NAME"];
$parts = Explode('/', $currentPage);
$currentPage = $parts[count($parts) - 1];


//this is a workaround for a bug between autocomplete and thickbox causing a page refresh on the add/edit license form when 'enter' key is hit
//this will redirect back to the actual license record
if ((isset($_GET['editLicenseForm'])) && ($_GET['editLicenseForm'] == "Y")){
	if (((isset($_GET['licenseShortName'])) && ($_GET['licenseShortName'] == "")) && ((isset($_GET['licenseOrganizationID'])) && ($_GET['licenseOrganizationID'] == ""))){
		$err="<span style='color:red;text-align:left;'>" . _("Both license name and organization must be filled out.  Please try again.") . "</span>";
	}else{
		$util->fixLicenseFormEnter($_GET['editLicenseID']);
	}
}

//get CORAL URL for 'Change Module' and logout link
$coralURL = $util->getCORALURL();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Management Module - <?php echo $pageTitle; ?></title>
<link rel="stylesheet" href="css/style.css" type="text/css" />
<link rel="stylesheet" href="css/thickbox.css" type="text/css" media="screen" />
<link rel="stylesheet" href="css/datePicker.css" type="text/css" media="screen" />
<link rel="stylesheet" href="css/jquery.autocomplete.css" type="text/css" media="screen" />
<link rel="stylesheet" href="css/jquery.tooltip.css" type="text/css" media="screen" />
<link rel="SHORTCUT ICON" href="images/favicon.ico" />
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" />
<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="js/plugins/jquery.js"></script>
<script type="text/javascript" src="js/plugins/ajaxupload.3.5.js"></script>
<script type="text/javascript" src="js/plugins/thickbox.js"></script>
<script type="text/javascript" src="js/plugins/Gettext.js"></script>
<?php
    // Add translation for the JavaScript files
    global $http_lang;
    $str = substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,5);
    $default_l = $lang_name->getLanguage($str);
    if($default_l==null || empty($default_l)){$default_l=$str;}
    if(isset($_COOKIE["lang"])){
        if($_COOKIE["lang"]==$http_lang && $_COOKIE["lang"] != "en_US"){
            echo "<link rel='gettext' type='application/x-po' href='../locale/".$http_lang."/LC_MESSAGES/messages.po' />";
        }
    }else if($default_l==$http_lang && $default_l != "en_US"){
            echo "<link rel='gettext' type='application/x-po' href='../locale/".$http_lang."/LC_MESSAGES/messages.po' />";
    }
?>
<script type="text/javascript" src="js/plugins/translate.js"></script>
<script type="text/javascript" src="js/plugins/date.js"></script>
<script type="text/javascript" src="js/plugins/jquery.datePicker.js"></script>
<script type="text/javascript" src="js/plugins/jquery.autocomplete.js"></script>
<script type="text/javascript" src="js/plugins/jquery.tooltip.js"></script>
<script type="text/javascript" src="js/common.js"></script>
</head>
<body>
<noscript><font face='arial'><?php echo _("JavaScript must be enabled in order for you to use CORAL. However, it seems JavaScript is either disabled or not supported by your browser. To use CORAL, enable JavaScript by changing your browser options, then ");?><a href=""><?php echo _("try again");?></a>. </font></noscript>
<center>
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
                <img src="images/title-icon-management.png" />
                <span id="main-title-text"><?php echo _("Management"); ?></span>
                <span id="powered-by-text"><?php echo _("Powered by");?><img src="images/logo-coral.jpg" /></span>
            </div>

            <div id="menu-login" style='margin-top:1px;'>
                <span class='smallText' style='color:#526972;'>
                <?php
                	echo _("Hello") . ", ";
                	//user may not have their first name / last name set up
                	if ($user->lastName){
                		echo $user->firstName . " " . $user->lastName;
                	}else{
                		echo $user->loginID;
                	}
                ?>
                </span><br />

            <?php if($config->settings->authModule == 'Y'){ echo "<a href='" . $coralURL . "auth/?logout' id='logout'>" . _("logout") . "</a><span id='divider'> | </span><a href='https://js-erm-helps.bc.sirsidynix.net' id='help' target='_blank'>" . _("Help") . "</a><span id='divider'> | </span>"; } ?>

                <span id="setLanguage">
            		<select name="lang" id="lang" class="dropDownLang">
                       <?php
                        // Get all translations on the '../locale' folder
                        $route='../locale';
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
                        $defLang = substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,5);
                        
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
<?php

/*---

//build main navigation based on user privileges
if ($user->isAdmin()){
	$mainnav = array(array("name"=>"home","path"=>"index.php"),
					 array("name"=>"new document","path"=>"ajax_forms.php?action=getLicenseForm&height=530&width=400&modal=true&newLicenseID=","thickbox"=>true,"cssid"=>"newLicense"),
					 array("name"=>"admin","path"=>"admin.php"));

} elseif ($user->canEdit()) {
	$mainnav = array(array("name"=>"home","path"=>"index.php"),
					 array("name"=>"new document","path"=>"ajax_forms.php?action=getLicenseForm&height=265&width=260&modal=true&newLicenseID=","thickbox"=>true,"cssid"=>"newLicense"));
} else {
	$mainnav = array(array("name"=>"home","path"=>"index.php"));
}
//display main navigation
echo '<div class="mainnav">';
foreach ($mainnav as $nav) {
	$attributes = NULL;
	if ($nav['thickbox'] && $nav['cssid']) {
		$attributes = " class=\"thickbox\" id=\"{$nav['cssid']}\"";
	} elseif ($nav['path'] == $currentPage) {
		$attributes = ' class="active"';
	}
	echo "<a".(($attributes) ? $attributes:'')." href=\"{$nav['path']}\">{$nav['name']}</a>";
}
echo '</div>';

---*/

?>

<?php if ($user->isAdmin()){ ?>

    <a href='index.php'>
        <div class="main-menu-link <?php if ($currentPage == 'index.php') { echo "active"; } ?>">
            <img src="images/menu/icon-home.png" />
            <span><?php echo _("Home");?></span>
        </div>
    </a>

    <a href='ajax_forms.php?action=getLicenseForm&height=350&width=300&modal=true&newLicenseID=' class='thickbox' id='newLicense'>
        <div class="main-menu-link">
            <img src="images/menu/icon-new-doc.png" />
            <span><?php echo _("New Document");?></span>
        </div>
    </a>

    <a href='admin.php'>
        <div class="main-menu-link <?php if ($currentPage == 'admin.php') { echo "active"; } ?>">
            <img src="images/menu/icon-admin.png" />
            <span><?php echo _("Admin");?></span>
        </div>
    </a>   

<?php }else if ($user->canEdit()){ ?>
	
    <a href='index.php'>
        <div class="main-menu-link <?php if ($currentPage == 'index.php') { echo "active"; } ?>">
            <img src="images/menu/icon-home.png" />
            <span><?php echo _("Home");?></span>
        </div>
    </a>

    <a href='ajax_forms.php?action=getLicenseForm&height=350&width=300&modal=true&newLicenseID=' class='thickbox' id='newLicense'>
        <div class="main-menu-link">
            <img src="images/menu/icon-new-doc.png" />
            <span><?php echo _("New Document");?></span>
        </div>
    </a>

<?php }else{ ?>

    <a href='index.php'>
        <div class="main-menu-link <?php if ($currentPage == 'index.php') { echo "active"; } ?>">
            <img src="images/menu/icon-home.png" />
            <span><?php echo _("Home");?></span>
        </div>
    </a>

    <a href='ajax_forms.php?action=getLicenseForm&height=350&width=300&modal=true&newLicenseID=' class='thickbox' id='newLicense'>
        <div class="main-menu-link">
            <img src="images/menu/icon-new-doc.png" />
            <span><?php echo _("New Document");?></span>
        </div>
    </a>

<?php } ?>


</td>

<td style='width:130px;height:19px;' align='right'>
<?php

//only show the 'Change Module' if there are other modules installed or if there is an index to the main CORAL page
$config = new Configuration();

if ((file_exists($util->getCORALPath() . "index.php")) || ($config->settings->organizationsModuleInstalled == 'Y') || ($config->settings->resourcesModule == 'Y') || ($config->settings->licensingModule == 'Y') || ($config->settings->usageModule == 'Y')) {

	?>

	<div style='text-align:left;'>
		<ul class="tabs">
			<li id="change-mod-menu"><span><?php echo _("Change Module");?></span><i class="fa fa-chevron-down"></i>
				<ul class="coraldropdown">
					<?php if (file_exists($util->getCORALPath() . "index.php")) {?>
					<li class="change-mod-item"><a href="<?php echo $coralURL; ?>" target='_blank'><img src='images/change/icon-mod-main.png'><span><?php echo _("Main Menu");?></span></a></li>
					<?php
					}
					if ($config->settings->resourcesModule == 'Y') {
					?>
					<li class="change-mod-item"><a href="<?php echo $coralURL; ?>resources/" target='_blank'><img src='images/change/icon-mod-resources.png'><span><?php echo _("Resources");?></span></a></li>
					<?php
					}
					if ($config->settings->organizationsModuleInstalled == 'Y') {
					?>
					<li class="change-mod-item"><a href="<?php echo $coralURL; ?>organizations/" target='_blank'><img src='images/change/icon-mod-organizations.png'><span><?php echo _("Organizations");?></span></a></li>
					<?php
					}
					if ($config->settings->licensingModule == 'Y') {
					?>
					<li class="change-mod-item"><a href="<?php echo $coralURL; ?>licensing/" target='_blank'><img src='images/change/icon-mod-licensing.png'><span><?php echo _("Licensing");?></span></a></li>
					<?php
					}
					if ($config->settings->usageModule == 'Y') {
					?>
					<li class="change-mod-item"><a href="<?php echo $coralURL; ?>usage/" target='_blank'><img src='images/change/icon-mod-usage.png'><span><?php echo _("Usage Statistics");?></span></a></li>
					<?php } ?>
				</ul>
			</li>
		</ul>
	</div>

	<?php

} else {
	echo "&nbsp;";
}

?>

</td>
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
<span id='span_message' style='color:red;text-align:left;'><?php if (isset($err)) echo $err; ?></span>
