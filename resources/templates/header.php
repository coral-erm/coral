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


include_once 'user.php';

$moduleTitle = _('Resources');
include_once '../templates/header.php';

// TODO: is this needed?
header('Cache-Control: public');
?>
<body>
<noscript>
    <font face='arial'>
        <?php echo _("JavaScript must be enabled in order for you to use CORAL. However, it seems JavaScript is either disabled or not supported by your browser. To use CORAL, enable JavaScript by changing your browser options, then ");?>
        <a href=""><?php echo _("try again");?></a>.
    </font>
</noscript>

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

            <?php if($config->settings->authModule == 'Y'){ echo "<a href='" . $coralURL . "auth/?logout' id='logout' title='" . _("logout") . "'>" . _("logout") . "</a><span id='divider'> | </span><a href='http://docs.coral-erm.org/' id='help' target='_blank'>" . _("Help") . "</a><span id='divider'> | </span>"; } ?>

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

    <?PHP // TODO: ---PICK UP HERE--- ?>

    <a href='index.php' title="<?php echo _("Home") ?>">
        <div class="main-menu-link <?php if ($currentPage == 'index.php') { echo "active"; } ?>">
            <img src="images/menu/icon-home.png" />
            <span><?php echo _("Home");?></span>
        </div>
    </a>

<?php if ($user->isAdmin() || $user->canEdit()){ ?>
    <a href='ajax_forms.php?action=getNewResourceForm&height=503&width=775&resourceID=&modal=true' class='thickbox' id='newResource' title="<?php echo _("New Resource"); ?>">
        <div class="main-menu-link">
            <img src="images/menu/icon-plus-square.png" />
            <span><?php echo _("New Resource");?></span>
        </div>
    </a>

    <a href='queue.php' title="<?php echo _("My Queue");?>">
        <div class="main-menu-link <?php if ($currentPage == 'queue.php') { echo "active"; } ?>">
            <img src="images/menu/icon-queue.png" />
            <span><?php echo _("My Queue");?></span>
        </div>
    </a>

    <a href='import.php' title="<?php echo _("Import");?>">
        <div class="main-menu-link <?php if ($currentPage == 'import.php') { echo "active"; } ?>">
            <img src="images/menu/icon-import.png" />
            <span><?php echo _("File Import");?></span>
        </div>
    </a> 

	<?php if ($user->isAdmin()) { ?>
    <a href='admin.php' title="<?php echo _("Admin");?>">
        <div class="main-menu-link <?php if ($currentPage == 'admin.php') { echo "active"; } ?>">
            <img src="images/menu/icon-admin.png" />
            <span><?php echo _("Admin");?></span>
        </div>
    </a>
	<?php } ?>
<?php } ?>

</td>

<td style='width:130px;height:19px;' align='right'>
<?php

//only show the 'Change Module' if there are other modules installed or if there is an index to the main CORAL page

if ((file_exists($util->getCORALPath() . "index.php")) || ($config->settings->licensingModule == 'Y') || ($config->settings->organizationsModule == 'Y') || ($config->settings->managementModule == 'Y') || ($config->settings->usageModule == 'Y')) {

	?>

	<div style='text-align:left;'>
		<ul class="tabs">
	        <li id="change-mod-menu"><span><?php echo _("Change Module");?></span><i class="fa fa-chevron-down"></i>
			<ul class="coraldropdown">
				<?php if (file_exists($util->getCORALPath() . "index.php")) {?>
				<li class="change-mod-item"><a href="<?php echo $coralURL; ?>" target='_blank' title="<?php echo _("Main Menu"); ?>"><img src='images/change/icon-mod-main.png'><span><?php echo _("Main Menu");?></span></a></li>
				<?php
				}
				if ($config->settings->organizationsModule == 'Y') {
				?>
				<li class="change-mod-item"><a href="<?php echo $coralURL; ?>organizations/" target='_blank' title="<?php echo _("Organizations module"); ?>"><img src='images/change/icon-mod-organizations.png'><span><?php echo _("Organizations");?></span></a></li>
				<?php
				}
				if ($config->settings->licensingModule == 'Y') {
				?>
				<li class="change-mod-item"><a href="<?php echo $coralURL; ?>licensing/" target='_blank' title="<?php echo _("Licensing module"); ?>"><img src='images/change/icon-mod-licensing.png'><span><?php echo _("Licensing");?></span></a></li>
				<?php
				}
				if ($config->settings->usageModule == 'Y') {
				?>
				<li class="change-mod-item"><a href="<?php echo $coralURL; ?>usage/" target='_blank' title="<?php echo _("Usage Statistics module"); ?>"><img src='images/change/icon-mod-usage.png'><span><?php echo _("Usage Statistics");?></span></a></li>
				<?php
				}
				if ($config->settings->managementModule == 'Y') {
				?>
				<li class="change-mod-item"><a href="<?php echo $coralURL; ?>management/" target='_blank' title="<?php echo _("Management module"); ?>"><img src='images/change/icon-mod-management.png'><span><?php echo _("Management");?></span></a></li>
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
