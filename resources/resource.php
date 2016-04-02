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

include_once 'directory.php';

$resourceID = $_GET['resourceID'];
$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));
$status = new Status(new NamedArguments(array('primaryKey' => $resource->statusID)));

//used to get default email address for feedback link in the right side panel
$config = new Configuration();


//set referring page
if ((isset($_GET['ref'])) && ($_GET['ref'] == 'new')){
  CoralSession::set('ref_script', 'new');
}else{
  CoralSession::set('ref_script', $currentPage);
}

//set this to turn off displaying the title header in header.php
$pageTitle=$resource->titleText;;
include 'templates/header.php';


if ($resource->titleText){
	?>
	<input type='hidden' name='resourceID' id='resourceID' value='<?php echo $resourceID; ?>'>

	<table style="background-image:url('images/header.gif');background-repeat:no-repeat;margin:0; padding:0; width:100%;">
	<tr>
	<td style='margin:0;padding:0;text-align:left;'>

		<div style='vertical-align:top; width:100%; height:35px; margin-left:5px;padding:0;'>
			<span class="headerText" id='span_resourceName' style='float:left;vertical-align:text-top;'><?php echo $resource->titleText; ?>&nbsp;</span>
			<div id='div_new' style='float:left;vertical-align:bottom;font-weight:115%;margin-top:3px;' class='darkRedText'><?php if ($_GET['ref'] == 'new'){ ?>&nbsp;&nbsp;<img src='images/red_checkmark.gif' />
				<span class='boldText'><?php echo _("Success!");?></span>&nbsp;&nbsp;<?php echo _("New resource added"); } ?>
			</div>
		</div>

	</td>
	</tr>
	</table>

	<div style='width:100%;'>
	<div style='float:left; width:597px;vertical-align:top;margin:0; padding:0;'>
		<?php if (!isset($_GET['showTab'])){ ?>
		<div style="width: 597px;" id='div_product' class="resource_tab_content">
		<?php } else { ?>
		<div style="display:none;width: 597px;" id='div_product' class="resource_tab_content">
		<?php } ?>
			<table cellpadding="0" cellspacing="0" style="width: 100%; table-layout: fixed;">
				<tr>
					<td class="sidemenu">
						<?php echo resource_sidemenu('product'); ?>
					</td>
					<td class='mainContent'>

						<div class='div_mainContent'>
						</div>
					</td>
				</tr>
			</table>

		</div>

		<?php if ((isset($_GET['showTab'])) && ($_GET['showTab'] == 'acquisitions')){ ?>
		<div style="width: 897px;" id='div_acquisitions' class="resource_tab_content">
		<?php } else { ?>
		<div style="display:none;width: 897px;" id='div_acquisitions' class="resource_tab_content">
		<?php } ?>
			<table cellpadding="0" cellspacing="0" style="width: 100%; table-layout: fixed;">
				<tr>
					<td class="sidemenu">
						<?php echo resource_sidemenu('acquisitions'); ?>
					</td>
					<td class='mainContent'>

						<div class='div_mainContent'>
						</div>
					</td>
				</tr>
			</table>

		</div>





		<?php if ((isset($_GET['showTab'])) && ($_GET['showTab'] == 'access')){ ?>
		<div style="width: 597px;" id='div_access' class="resource_tab_content">
		<?php } else { ?>
		<div style="display:none;width: 597px;" id='div_access' class="resource_tab_content">
		<?php } ?>

			<table cellpadding="0" cellspacing="0" style="width: 100%; table-layout: fixed;">
				<tr>
					<td class="sidemenu">
						<?php echo resource_sidemenu('access'); ?>
					</td>
					<td class='mainContent'>

						<div class='div_mainContent'>
						</div>
					</td>
				</tr>
			</table>

		</div>



		<div style="display:none;width: 597px;" id='div_contacts' class="resource_tab_content">
			<table cellpadding="0" cellspacing="0" style="width: 100%; table-layout: fixed;">
				<tr>
					<td class="sidemenu">
						<?php echo resource_sidemenu('contacts'); ?>
					</td>
					<td class='mainContent'>

						<div class='div_mainContent'></div>
						<div id='div_archivedContactDetails'></div>

					</td>
				</tr>
			</table>

		</div>

		<div style="display:none;width: 597px;" id='div_issues' class="resource_tab_content">
			<table cellpadding="0" cellspacing="0" style="width: 100%; table-layout: fixed;">
				<tr>
					<td class="sidemenu">
						<?php echo resource_sidemenu('issues'); ?>
					</td>
					<td class='mainContent'>

						<div class='div_mainContent'></div>

					</td>
				</tr>
			</table>

		</div>


		<?php if ($user->accountTabIndicator == '1') { ?>


		<div style="display:none;width: 597px;" id='div_accounts' class="resource_tab_content">
			<table cellpadding="0" cellspacing="0" style="width: 100%; table-layout: fixed;">
				<tr>
					<td class="sidemenu">
						<?php echo resource_sidemenu('accounts'); ?>
					</td>
					<td class='mainContent'>

						<div class='div_mainContent'>
						</div>
					</td>
				</tr>
			</table>

		</div>


		<?php } ?>

		<div style="display:none;width: 597px;" id='div_attachments' class="resource_tab_content">
			<table cellpadding="0" cellspacing="0" style="width: 100%; table-layout: fixed;">
				<tr>
					<td class="sidemenu">
						<?php echo resource_sidemenu('attachments'); ?>
					</td>
					<td class='mainContent'>

						<div class='div_mainContent'>
						</div>
					</td>
				</tr>
			</table>

		</div>

		<div style="display:none;width: 897px;" id='div_routing' class="resource_tab_content">
			<table cellpadding="0" cellspacing="0" style="width: 100%; table-layout: fixed;">
				<tr>
					<td class="sidemenu">
						<?php echo resource_sidemenu('routing'); ?>
					</td>
					<td class='mainContent'>

						<div class='div_mainContent'>
						</div>
					</td>
				</tr>
			</table>

		</div>
		
		<?php if ((isset($_GET['showTab'])) && ($_GET['showTab'] == 'cataloging')){ ?>
		<div style="width: 597px;" id='div_cataloging' class="resource_tab_content">
		<?php } else { ?>
		<div style="display:none;width: 597px;" id='div_cataloging' class="resource_tab_content">
		<?php } ?>
			<table cellpadding="0" cellspacing="0" style="width: 100%; table-layout: fixed;">
				<tr>
					<td class="sidemenu">
						<?php echo resource_sidemenu('cataloging'); ?>
					</td>
					<td class='mainContent'>

						<div class='div_mainContent'>
						</div>
					</td>
				</tr>
			</table>

		</div>
	</div>
	<div style='float:right; vertical-align:top; width:303px; text-align:left; padding:0; margin:0; background-color:white;' id='div_fullRightPanel' class='rightPanel'>
		<div style="background-image:url('images/helpfullinks-top.jpg');background-repeat:no-repeat;width:265px;text-align:left;padding:6px;margin:10px 19px 0px 19px;">
			<div style='margin:29px 8px 0px 8px;' id='div_rightPanel'>
			</div>

		</div>

		<div style="background-image:url('images/helpfullinks-bottom.jpg');background-repeat:no-repeat;width:265px;height:50px;padding:6px; margin:0px 19px 15px 19px;">


					<?php if ($config->settings->feedbackEmailAddress != '') {?>
						<div style='margin:0px 8px 10px 8px;'>
						<div style='width:219px; padding:7px; margin-bottom:5px;'>
						<a href="mailto: <?php echo $config->settings->feedbackEmailAddress; ?>?subject=<?php echo $resource->titleText . ' (Resource ID: ' . $resource->resourceID . ')'; ?>" class='helpfulLink'><?php echo _("Send feedback on this resource");?></a>
						</div>
						</div>
					<?php } ?>

		</div>

	</div>
	</div>
	<script type="text/javascript" src="js/resource.js"></script>
  <?php if ((isset($_GET['showTab'])) && ($_GET['showTab'] == 'cataloging')){ ?>
    <script>
      $(document).ready(function() {
        $('a.showCataloging').click();
      });
    </script>
  <?php } ?>
	<?php

}

//print footer
include 'templates/footer.php';
?>
