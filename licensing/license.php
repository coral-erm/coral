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

//as long as license id is valid...
if ($license->shortName){

	//set this to turn off displaying the title header in header.php
	$pageTitle=$license->shortName;

	include 'templates/header.php';
	
	//determine if we should display the SFX tab - if user is admin and if configured in settings to use SFX
	$util = new Utility();
	$displaySFX = 0;
	if (($user->isAdmin()) && ($util->useTermsTool())){
		$displaySFX=1;
	}

	//set referring page
	$_SESSION['ref_script']=$currentPage;

?>
<main id="main-content">
	<article>
		<input type='hidden' name='licenseID' id='licenseID' value='<?php echo $license->licenseID; ?>'>

		<div id='div_licenseHead'></div>

		<div id ='div_displayDocuments' class="tabpanel">
			<div class='mainContent'>
				<h2><?php echo _('Documents'); ?></h2>
				<div id='div_documents'>
					<img src = "images/circle.gif"><?php echo _("Loading...");?>
				</div>
				<div id='div_archives'></div>
			</div>
		</div>

		<div id ='div_displayExpressions' class="tabpanel">
			<div class='mainContent'>
				<h2><?php echo _('Expressions'); ?></h2>
				<div id='div_expressions'>
				<img src = "images/circle.gif"><?php echo _("Loading...");?>
				</div>
			</div>
		</div>

		<div id ='div_displaySFXProviders' class="tabpanel">
			<div class='mainContent'>
				<h2><?php echo _('SFX Providers'); ?></h2>
				<div id='div_sfxProviders'>
				<img src = "images/circle.gif"><?php echo _("Loading...");?>
				</div>
			</div>
		</div>

		<div id ='div_displayAttachments' class="tabpanel">
			<div class='mainContent'>
				<h2><?php echo _('Attachments'); ?></h2>
				<div id='div_attachments'>
				<img src = "images/circle.gif"><?php echo _("Loading...");?>
				</div>
			</div>
		</div>

	</article>
	<div id="div_rightPanel"></div>

	<nav id="side" aria-label="<?php echo _('License Details'); ?>">
		<!-- TODO: WAI-ARIA Tab Panel -->
		<ul class="nav side">
			<li><a href='javascript:showTabPanel("#div_displayDocuments")' aria-controls='div_displayDocuments'><?php echo _("Documents");?></a></li>
			<li><a href='javascript:showTabPanel("#div_displayExpressions")' aria-controls='div_displayExpressions'><?php echo _("Expressions");?></a></li>
			<?php if ($displaySFX == "1"){ ?>
				<li><a href='javascript:showTabPanel("#div_displaySFXProviders")' aria-controls='div_displaySFXProviders'><?php echo _("Terms Tool");?></a></li>
				<?php } ?>
			<li><a href='javascript:showTabPanel("#div_displayAttachments")' aria-controls='div_displayAttachments'><?php echo _("Attachments");?> <span class='span_AttachmentNumber count'></span></a></li>
		</ul>
	</nav>
</main>
<?php
} //end license validity check

include 'templates/footer.php';
?>
<script src="js/license.js"></script>
</body>
</html>
