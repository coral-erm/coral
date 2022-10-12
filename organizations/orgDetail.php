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


session_start();

include_once 'directory.php';

//if system number's passed in, it's a new request
$organizationID = $_GET['organizationID'];
$organization = new Organization(new NamedArguments(array('primaryKey' => $organizationID)));


//set this to turn off displaying the title header in header.php
$pageTitle=$organization->name;
include 'templates/header.php';

//set referring page
if ((isset($_GET['ref'])) && ($_GET['ref'] == 'new')){
	$_SESSION['ref_script']="new";
}else{
	$_SESSION['ref_script']=$currentPage;
}

//as long as organization is valid...
if ($organization->name){


	//if the licensing module is installed display licensing tab
	$config = new Configuration;

	$showLicensing='N';
	if ($config->settings->licensingModule == 'Y'){
		$showLicensing = 'Y';
		$numLicenses = count($organization->getLicenses());
	}

	?>

	<table class="headerTable" style="background-image:url('images/header.gif');background-repeat:no-repeat;">
	<tr><td align='left'>
		<table style='width:100%;'>
		<tr style='vertical-align:top'>
		<td><span class="headerText" id='span_orgName'><?php echo $organization->name; ?></span><br />
		<br />

		</td>
		<td style='text-align:right;width:50px;'>
		<div id="search_box">
				<input type="text" id="search_organization" name="search_organization" value="<?php if (isset($_GET['search_organization'])) { echo $_GET['search_organization']; } else { echo _('Organization Search'); } ?>" class='swap_value' />
				<input type='hidden' id='search_organizationID' name='search_organizationID' value='' />
				<i class="fa fa-search"></i>
		</div>
		</td>
		</tr>
		</table>

		<input type='hidden' name='organizationID' id='organizationID' value='<?php echo $organizationID; ?>'>
		<input type='hidden' name='numLicenses' id='numLicenses' value='<?php echo $numLicenses; ?>'>

        <?php
        if ($config->settings->resourcesModule == 'Y'){ ?>
        <div id="print-view">
        	<div id="side-menu-title"><?php echo _("Helpful Links"); ?></div>
            <div class='rightPanelLink'><a href='summary.php?organizationID=<?php echo $organizationID; ?>' target='_blank' class='helpfulLink'><?php echo _("Print View");?></a></div>

            <?php
            //get all possible roles, sort by name, get associated resources
            $org_role_obj = new OrganizationRole();
            $org_roles = $org_role_obj->allAsArray();
            usort($org_roles, function ($a, $b) { return strcmp($a["shortName"], $b["shortName"]); });
            foreach ($org_roles as $role) {
                $resources = $organization->getResources($role["organizationRoleID"]);
                if (count($resources) > 0) {
                    ?>
            <h4 style="margin-top:8px"><?php echo $role["shortName"]. _(" of:");?></h4>
            <div id="org-name">
            <?php
            foreach ($resources as $resource) {
                $temp_style = "";
                if ($resource['archived']) {
                    echo "<span style='color:grey; font-size:80%;'>"._("(archived)")." </span>";
                    $temp_style = "style='color:#888888'";
                }
                echo "<a href='" . $util->getResourceRecordURL() . $resource['resourceID'] . "' target='_BLANK' $temp_style>" .  $resource['titleText'] . "</a><br />";
            }
            ?>
            </div>
            <?php
                }
            }
            ?>
        </div>
        <?php } ?>
		<?php if (!isset($_GET['showTab'])){ ?>
		<div style="width: 625px;" id ='div_organization'>
		<?php } else { ?>
		<div style="display:none;width: 625px;" id ='div_organization'>
		<?php } ?>
			<table cellpadding="0" cellspacing="0" style="width: 100%;">
				<tr>
					<td class="sidemenu">
						<div class="sidemenuselected" style='position: relative; width: 99px'><span class='link'><?php echo _("Organization");?></span></div>
						<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showAliases'><?php echo _("Aliases");?></a></span></div>
						<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showContacts'><?php echo _("Contacts");?></a></span></div>
						<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showAccount'><?php echo _("Accounts");?></a></span></div>
<?php
if ($config->settings->resourcesIssues == "Y") {
	echo 	"			<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showResourceIssues'>"._("Issues")."</a></span></div>";
} else {
	echo 	"			<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showIssues'>"._("Issues")."</a></span></div>";
}
?>
						<?php if ($showLicensing == "Y") { ?><div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showLicenses'><?php echo _("Licenses");?></a></span><span class='span_AttachmentNumber smallGreyText' style='clear:right; margin-left:8px;'>&nbsp;(<?php if ($numLicenses == "1") { echo $numLicenses . _(" record"); }else{ echo $numLicenses . _(" records"); } ?>)</span></div><?php } ?>
					</td>
					<td class='mainContent'>

						<div id='div_organizationDetails'>
						</div>
					</td>
				</tr>
			</table>

		</div>

		<?php if ((isset($_GET['showTab'])) && ($_GET['showTab'] == 'alias')){ ?>
		<div style="width: 625px;" id ='div_aliases'>
		<?php } else { ?>
		<div style="display:none;width: 625px;" id ='div_aliases'>
		<?php } ?>
			<table cellpadding="0" cellspacing="0" style="width: 100%;">
				<tr>
					<td class="sidemenu">
						<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showOrganization'><?php echo _("Organization");?></a></span></div>
						<div class="sidemenuselected" style='position: relative; width: 99px'><span class='link'><?php echo _("Aliases");?></span></div>
						<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showContacts'><?php echo _("Contacts");?></a></span></div>
						<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showAccount'><?php echo _("Accounts");?></a></span></div>
<?php
if ($config->settings->resourcesIssues == "Y") {
	echo 	"			<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showResourceIssues'>"._("Issues")."</a></span></div>";
} else {
	echo 	"			<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showIssues'>"._("Issues")."</a></span></div>";
}
?>
						<?php if ($showLicensing == "Y") { ?><div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showLicenses'><?php echo _("Licenses");?></a></span><span class='span_AttachmentNumber smallGreyText' style='clear:right; margin-left:8px;'>&nbsp;(<?php if ($numLicenses == "1") { echo $numLicenses . _(" record"); }else{ echo $numLicenses . _(" records"); } ?>)</span></div><?php } ?>
					</td>
					<td class='mainContent'>

						<div id='div_aliasDetails'>
						</div>
					</td>
				</tr>
			</table>

		</div>






		<?php if ((isset($_GET['showTab'])) && ($_GET['showTab'] == 'contacts')){ ?>
		<div style="width: 625px;" id ='div_contacts'>
		<?php } else { ?>
		<div style="display:none;width: 625px;" id ='div_contacts'>
		<?php } ?>

			<table cellpadding="0" cellspacing="0" style="width: 100%;">
				<tr>
					<td class="sidemenu">
						<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showOrganization'><?php echo _("Organization");?></a></span></div>
						<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showAliases'><?php echo _("Aliases");?></a></span></div>
						<div class="sidemenuselected" style='position: relative; width: 99px'><span class='link'><?php echo _("Contacts");?></span></div>
						<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showAccount'><?php echo _("Accounts");?></a></span></div>
<?php
if ($config->settings->resourcesIssues == "Y") {
	echo 	"			<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showResourceIssues'>"._("Issues")."</a></span></div>";
} else {
	echo 	"			<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showIssues'>"._("Issues")."</a></span></div>";
}
?>
						<?php if ($showLicensing == "Y") { ?><div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showLicenses'><?php echo _("Licenses");?></a></span><span class='span_AttachmentNumber smallGreyText' style='clear:right; margin-left:8px;'>&nbsp;(<?php if ($numLicenses == "1") { echo $numLicenses . _(" record"); }else{ echo $numLicenses . _(" records"); } ?>)</span></div><?php } ?>
					</td>
					<td class='mainContent'>

						<div id='div_contactDetails'></div>
						<div id='div_archivedContactDetails'></div>
						<?php if ($user->canEdit()){ ?>
						<br />
                        <a href='javascript:void(0)' onclick='myDialog("ajax_forms.php?action=getContactForm&type=named&organizationID=<?php echo $organizationID; ?>",500,345)' class='thickbox'><?php echo _("add contact");?></a><br />
						<?php } ?>

					</td>
				</tr>
			</table>

		</div>


		<?php if ((isset($_GET['showTab'])) && ($_GET['showTab'] == 'accounts')){ ?>
          <div style="width: 625px;" id ='div_account'>
		<?php } else { ?>
          <div style="display:none;width: 625px;" id ='div_account'>
		<?php } ?>
			<table cellpadding="0" cellspacing="0" style="width: 100%;">
				<tr>
					<td class="sidemenu">
						<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showOrganization'><?php echo _("Organization");?></a></span></div>
						<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showAliases'><?php echo _("Aliases");?></a></span></div>
						<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showContacts'><?php echo _("Contacts");?></a></span></div>
						<div class="sidemenuselected" style='position: relative; width: 99px'><span class='link'><?php echo _("Accounts");?></span></div>
<?php
if ($config->settings->resourcesIssues == "Y") {
	echo 	"			<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showResourceIssues'>"._("Issues")."</a></span></div>";
} else {
	echo 	"			<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showIssues'>"._("Issues")."</a></span></div>";
}
?>
						<?php if ($showLicensing == "Y") { ?><div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showLicenses'><?php echo _("Licenses");?></a></span><span class='span_AttachmentNumber smallGreyText' style='clear:right; margin-left:8px;'>&nbsp;(<?php if ($numLicenses == "1") { echo $numLicenses . _(" record"); }else{ echo $numLicenses . _(" records"); } ?>)</span></div><?php } ?>
					</td>
					<td class='mainContent'>

						<div id='div_accountDetails'>
						</div>
					</td>
				</tr>
			</table>

		</div>


<?php
if ($config->settings->resourcesIssues == "Y") {
?>
		<div style="display:none;width: 625px;" id ='div_resourceissues'>
			<table cellpadding="0" cellspacing="0" style="width: 100%;">
				<tr>
					<td class="sidemenu">
						<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showOrganization'>Organization</a></span></div>
						<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showAliases'>Aliases</a></span></div>
						<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showContacts'>Contacts</a></span></div>
						<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showAccount'>Accounts</a></span></div>
						<div class='sidemenuselected' style='position: relative; width: 99px'><span class='link'><?php echo _("Issues");?></span></div>
						<?php if ($showLicensing == "Y") { ?><div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showLicenses'>Licenses</a></span><br /><span class='span_AttachmentNumber smallGreyText' style='clear:right; margin-left:8px;'>&nbsp;(<?php if ($numLicenses == "1") { echo $numLicenses . " record"; }else{ echo $numLicenses . " records"; } ?>)</span></div><?php } ?>
					</td>
					<td class='mainContent'>

						<div id='div_resourceissueDetails'>
						</div>
					</td>
				</tr>
			</table>
		</div>
<?php
} else {
?>
		<div style="display:none;width: 625px;" id ='div_issues'>
			<table cellpadding="0" cellspacing="0" style="width: 100%;">
				<tr>
					<td class="sidemenu">
						<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showOrganization'><?php echo _("Organization");?></a></span></div>
						<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showAliases'><?php echo _("Aliases");?></a></span></div>
						<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showContacts'><?php echo _("Contacts");?></a></span></div>
						<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showAccount'><?php echo _("Accounts");?></a></span></div>
						<div class="sidemenuselected" style='position: relative; width: 99px'><span class='link'><?php echo _("Issues");?></span></div>
						<?php if ($showLicensing == "Y") { ?><div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showLicenses'><?php echo _("Licenses");?></a></span><span class='span_AttachmentNumber smallGreyText' style='clear:right; margin-left:8px;'>&nbsp;(<?php if ($numLicenses == "1") { echo $numLicenses . _(" record"); }else{ echo $numLicenses . _(" records"); } ?>)</span></div><?php } ?>
					</td>
					<td class='mainContent'>

						<div id='div_issueDetails'>
						</div>
					</td>
				</tr>
			</table>

		</div>
<?php
}
?>
		<?php
		if ($showLicensing == "Y") {
		?>
			<div style="display:none;width: 625px;" id ='div_licenses'>
				<table cellpadding="0" cellspacing="0" style="width: 100%;">
					<tr>
						<td class="sidemenu">
							<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showOrganization'><?php echo _("Organization");?></a></span></div>
							<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showAliases'><?php echo _("Aliases");?></a></span></div>
							<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showContacts'><?php echo _("Contacts");?></a></span></div>
							<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showAccount'><?php echo _("Accounts");?></a></span></div>
<?php
if ($config->settings->resourcesIssues == "Y") {
	echo 	"			<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showResourceIssues'>"._("Issues")."</a></span></div>";
} else {
	echo 	"			<div class='sidemenuunselected'><span class='link'><a href='javascript:void(0)' class='showIssues'>"._("Issues")."</a></span></div>";
}
?>
							<div class="sidemenuselected" style='position: relative; width: 99px'><span class='link'><?php echo _("Licenses");?></span><br /><span class='smallGreyText' style='clear:right; margin-left:8px;'>&nbsp;(<?php if ($numLicenses == "1") { echo $numLicenses . _(" record"); }else{ echo $numLicenses . _(" records"); } ?>)</span></div>
						</td>
						<td class='mainContent'>

							<div id='div_licenseDetails'>
							</div>
						</td>
					</tr>
				</table>

			</div>

		<?php
		}
		?>

	</td></tr>
	</table>
	<script type="text/javascript" src="js/orgDetail.js"></script>

	<?php
//end if organization valid
}else{
	echo _("invalid organization");
}


//print footer
include 'templates/footer.php';
?>
