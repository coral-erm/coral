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

//set referring page
if ((isset($_GET['ref'])) && ($_GET['ref'] == 'new')){
	$_SESSION['ref_script']="new";
}else{
	$_SESSION['ref_script']=$currentPage;
}


//set this to turn off displaying the title header in header.php
$pageTitle=$organization->name;
include 'templates/header.php';

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

<main id="main-content">
	<article>
		<h2 id='span_orgName'><?php echo $organization->name; ?></h2>
		
		<input type='hidden' name='organizationID' id='organizationID' value='<?php echo $organizationID; ?>'>
		<input type='hidden' name='numLicenses' id='numLicenses' value='<?php echo $numLicenses; ?>'>

		<div class="tabpanel" id='div_organization'>
			<div class='mainContent'>
				<div id='div_organizationDetails'></div>
			</div>
		</div>

		<div class="tabpanel" id='div_aliases'>
			<div class='mainContent'>
				<div id='div_aliasDetails'></div>
			</div>
		</div>

		<div class="tabpanel" id='div_contacts'>
		
					<div class='mainContent'>
						<div id='div_contactDetails'></div>
						<div id='div_archivedContactDetails'></div>
						<?php if ($user->canEdit()){ ?>
						<p>
              <a href='javascript:void(0)' onclick='myDialog("ajax_forms.php?action=getContactForm&type=named&organizationID=<?php echo $organizationID; ?>",500,800)' class='thickbox'><?php echo _("add contact");?></a><br />
						</p>
						<?php } ?>

					</div>

		</div>


    <div class="tabpanel" id='div_account'>
			<div class='mainContent'>
				<div id='div_accountDetails'></div>
			</div>
		</div>


<?php
if ($config->settings->resourcesIssues == "Y") {
?>
		<div class="tabpanel" id='div_resourceissues'>
					<div class='mainContent'>
						<div id='div_resourceissueDetails'></div>
					</div>
		</div>
<?php
} else {
?>
		<div class="tabpanel" id='div_issues'>
					<div class='mainContent'>
						<div id='div_issueDetails'></div>
					</div>
		</div>
<?php
}
?>
		<?php
		if ($showLicensing == "Y") {
		?>
			<div class="tabpanel" id='div_licenses'>
						<div class='mainContent'>
							<div id='div_licenseDetails'></div>
						</div>
			</div>

		<?php
		}
		?>
</article>

<?php
        if ($config->settings->resourcesModule == 'Y'){ ?>
        <aside id="links">
					<div id="search_box" role="search">
						<label for="search_organization" class="block"><?php echo _('Search Organizations'); ?></label>
						<div class="flex search">
							<input type="search" id="search_organization" name="search_organization" value="<?php if (isset($_GET['search_organization'])) { echo $_GET['search_organization']; } ?>" class='swap_value' />
							<input type='hidden' id='search_organizationID' name='search_organizationID' value='' />
							<i class="fa fa-search"></i>
						</div>
					</div>

        	<h3 id="side-menu-title"><?php echo _("Helpful Links"); ?></h3>
            <div class='rightPanelLink'><a href='summary.php?organizationID=<?php echo $organizationID; ?>' <?php echo getTarget(); ?> class='helpfulLink'><?php echo _("Print View");?></a></div>

            <?php
            //get all possible roles, sort by name, get associated resources
            $org_role_obj = new OrganizationRole();
            $org_roles = $org_role_obj->allAsArray();
            usort($org_roles, function ($a, $b) { return strcmp($a["shortName"], $b["shortName"]); });
            foreach ($org_roles as $role) {
                $resources = $organization->getResources($role["organizationRoleID"]);
                if (is_array($resources) && count($resources) > 0) {
                    ?>
            <h4><?php printf(_("%s of:"), $issueStartDate);?></h4>
            <ul id="org-name" class="unstyled">
            <?php
            foreach ($resources as $resource) {
                $classes = "";
								echo "<li>";
                if ($resource['archived']) {
                    echo "<span class='archived'>"._("(archived)")." </span>";
										$classes = "archived";
                }
                echo "<a href='" . $util->getResourceRecordURL() . $resource['resourceID'] . "' ". getTarget() ." class='$classes'>" .  $resource['titleText'] . "</a></li>";
            }
            ?>
            </ul>
            <?php
                }
            }
            ?>
        </aside>
        <?php } ?>

<nav id="side" aria-label="<?php echo sprintf(_('%s Details'), $organization->name); ?>">
	<!-- TODO: WAI-ARIA Tab Panel -->
	<ul class="nav side">
		<li><a href='javascript:showTabPanel("#div_organization");' aria-controls="div_organization"><?php echo _("Organization");?></a></li>
		<li><a href='javascript:showTabPanel("#div_aliases");' aria-controls="div_aliases"><?php echo _("Aliases");?></a></li>
		<li><a href='javascript:showTabPanel("#div_contacts");' aria-controls="div_contacts"><?php echo _("Contacts");?></a></li>
		<li><a href='javascript:showTabPanel("#div_account");' aria-controls="div_account"><?php echo _("Accounts");?></a></li>
		<li><a href='javascript:showTabPanel("#div_issues");' aria-controls="div_issues"><?php echo _("Issues");?></a></li>
	<?php if ($showLicensing == "Y") { ?>
		<li>
			<a href='javascript:showTabPanel("#div_licenses");' aria-controls="div_licenses"><?php echo _("Licenses");?> 
				<span class='span_AttachmentNumber count'> (<?php if ($numLicenses == "1") { echo $numLicenses . _(" record"); }else{ echo $numLicenses . _(" records"); } ?>)</span>
			</a>
		</li>
	<?php } ?>
	</ul>
</nav>
</main>
	<?php
//end if organization valid
}else{
	echo _("invalid organization");
}

?>
<script src="js/orgDetail.js"></script>
<?php
//print footer
include 'templates/footer.php';
?>
</body>
</html>