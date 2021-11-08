<?php
/*
**************************************************************************************************************************
** CORAL Licensing Module v. 1.0
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

$licenseID=$_GET['licenseID'];
$license = new License(new NamedArguments(array('primaryKey' => $licenseID)));

//set this to turn off displaying the title header in header.php
$pageTitle=$license->shortName;
$noHead=1;
include 'templates/header.php';

//set referring page
$_SESSION['ref_script']=$currentPage;


//determine if we should display the SFX tab - if user is admin and if configured in settings to use SFX
$util = new Utility();
$displaySFX = 0;
if (($user->isAdmin()) && ($util->useTermsTool())){
	$displaySFX=1;
}

//as long as license id is valid...
if ($license->shortName){

?>


<input type='hidden' name='licenseID' id='licenseID' value='<?php echo $licenseID; ?>'>

<div id='div_licenseHead'>

</div>

</center>

<input type='hidden' name='licenseID' id='licenseID' value='<?php echo $license->licenseID; ?>'>

<div style="width: 100%;" id ='div_displayDocuments'>
	<table cellpadding="0" cellspacing="0" style="width: 100%;">
		<tr>
			<td class="sidemenu" style='margin-right: 15px;'>
				<div class="sidemenuselected" style='position: relative; width: 91px'><a href='javascript:void(0)' class='showDocuments'><?php echo _("Documents");?></a></div>
				<div class='sidemenuunselected'><a href='javascript:void(0)' class='showExpressions'><?php echo _("Expressions");?></a></div>
				<?php if ($displaySFX == "1"){ ?><div class='sidemenuunselected'><a href='javascript:void(0)' class='showSFXProviders'><?php echo _("Terms Tool");?></a></div><?php } ?>
				<div class='sidemenuunselected'><a href='javascript:void(0)' class='showAttachments'><?php echo _("Attachments");?></a>&nbsp;<span class='span_AttachmentNumber'></span></div>
			</td>
			<td class='mainContent'>

				<div id='div_documents'>
				<img src = "images/circle.gif"><?php echo _("Loading...");?>
				</div>
				<br />
				<div id='div_archives'>
				</div>
			</td>
			<td class='helpfulLinks'>
				<div style='float:right; vertical-align:top; width:303px; text-align:left; padding:0; margin:0 0 0 15px; background-color:white;' id='div_fullRightPanel' class='rightPanel'>
					<div style="width:265px;text-align:left;padding:10px;">
						<div id="side-menu-title"><?php echo _("Helpful Links"); ?></div>
						<div style='margin:10px 8px 0px 8px;' id='div_rightPanel'>
						</div>
					</div>
					<div>
						<?php if ($config->settings->feedbackEmailAddress != '') {?>
							<div style='margin:0px 8px 10px 8px;'>
								<div style='width:219px; padding:7px; margin-bottom:5px;'>
									<a href="mailto: <?php echo $config->settings->feedbackEmailAddress; ?>?subject=<?php echo $resource->titleText . ' (Resource ID: ' . $resource->resourceID . ')'; ?>" class='helpfulLink'><?php echo _("Send feedback on this resource");?></a>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</td>
		</tr>
	</table>
</div>



<div id ='div_displayExpressions' style='display:none;width:899px;'>
	<table cellpadding="0" cellspacing="0" style="width: 100%;">
		<tr>
			<td class="sidemenu">
				<div class="sidemenuunselected"><a href='javascript:void(0)' class='showDocuments'><?php echo _("Documents");?></a></div>
				<div class='sidemenuselected' style='position: relative; width: 91px'><a href='javascript:void(0)' class='showExpressions'><?php echo _("Expressions");?></a></div>
				<?php if ($displaySFX == "1"){ ?><div class='sidemenuunselected'><a href='javascript:void(0)' class='showSFXProviders'><?php echo _("Terms Tool");?></a></div><?php } ?>
				<div class='sidemenuunselected'><a href='javascript:void(0)' class='showAttachments'><?php echo _("Attachments");?></a>&nbsp;<span class='span_AttachmentNumber'></span></div>
			</td>
			<td class='mainContent'>

				<div id='div_expressions'>
				<img src = "images/circle.gif"><?php echo _("Loading...");?>
				</div>
			</td>
		</tr>
	</table>
</div>


<div id ='div_displaySFXProviders' style='display:none;width:899px;'>
	<table cellpadding="0" cellspacing="0" style="width: 100%;">
		<tr>
			<td class="sidemenu">
				<div class="sidemenuunselected"><a href='javascript:void(0)' class='showDocuments'><?php echo _("Documents");?></a></div>
				<div class='sidemenuunselected'><a href='javascript:void(0)' class='showExpressions'><?php echo _("Expressions");?></a></div>
				<div class='sidemenuselected' style='position: relative; width: 91px'><a href='javascript:void(0)' class='showSFXProviders'><?php echo _("Terms Tool");?></a></div>
				<div class='sidemenuunselected'><a href='javascript:void(0)' class='showAttachments'><?php echo _("Attachments");?></a>&nbsp;<span class='span_AttachmentNumber'></span></div>
			</td>
			<td class='mainContent'>
				<div id='div_sfxProviders'>
				<img src = "images/circle.gif"><?php echo _("Loading...");?>
				</div>
			</td>
		</tr>
	</table>
</div>


<div id ='div_displayAttachments' style='display:none;width:899px;'>
	<table cellpadding="0" cellspacing="0" style="width: 100%;">
		<tr>
			<td class="sidemenu">
				<div class="sidemenuunselected"><a href='javascript:void(0)' class='showDocuments'><?php echo _("Documents");?></a></div>
				<div class='sidemenuunselected'><a href='javascript:void(0)' class='showExpressions'><?php echo _("Expressions");?></a></div>
				<?php if ($displaySFX == "1"){ ?><div class='sidemenuunselected'><a href='javascript:void(0)' class='showSFXProviders'><?php echo _("Terms Tool");?></a></div><?php } ?>
				<div class='sidemenuselected' style='position: relative; width: 91px'><a href='javascript:void(0)' class='showAttachments'><?php echo _("Attachments");?></a>&nbsp;<span class='span_AttachmentNumber'></span></div>
			</td>
			<td class='mainContent'>

				<div id='div_attachments'>
				<img src = "images/circle.gif"><?php echo _("Loading...");?>
				</div>
			</td>
		</tr>
	</table>
</div>


<script type="text/javascript" src="js/license.js"></script>

<?php
} //end license validity check

include 'templates/footer.php';
?>

