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

include_once 'directory.php';

$pageTitle=_('Administration');
include 'templates/header.php';

//set referring page
$_SESSION['ref_script']=$currentPage;

//ensure user has admin permissions
if ($user->isAdmin()){
	?>
<main id="main-content">
	<article>
		<h2><?php echo _("Administration");?></h2>

	<div class="header">
		<h3><?php echo _("Users");?></h3>
		<span id='span_newUser' class='addElement'><button type="button" class="btn" onclick='myDialog("ajax_forms.php?action=getAdminUserUpdateForm&height=185&width=250&modal=true",250,250)' class='thickbox' id='expression'><?php echo "<img id='Add' class='addIcon' src='images/plus.gif' title= '"._("Add user")."' />";?></button></span>
	</div>

	<p class="msg" id='span_User_response'></p>
	
	<div id='div_User'>
		<img src = "images/circle.gif"><?php echo _("Loading...");?>
	</div>
	<div class="header">
		<h3><?php echo _("Organization Role");?></h3>
		<span id='span_newOrganizationRole' class='addElement'><button type="button" class="btn" onclick='showAdd("OrganizationRole", "<?php echo _("New organization role") ?>");'><?php echo "<img id='Add' class='addIcon' src='images/plus.gif' title= '"._("Add organization role")."' />";?></button></span>
	</div>

	<span id='span_OrganizationRole_response'></span>

	<div id='div_OrganizationRole'>
	<img src = "images/circle.gif"><?php echo _("Loading...");?>
	</div>
	<div class="header">
		<h3><?php echo _("Contact Role");?></h3>
		<span id='span_newContactRole' class='adminAddInput addElement'><button type="button" class="btn" onclick='showAdd("ContactRole", "<?php echo _("New contact role") ?>");'><?php echo "<img id='Add' class='addIcon' src='images/plus.gif' title= '"._("Add contact role")."' />";?></button></span>
	</div>

		
	<p id='span_ContactRole_response'></p>

	<div id='div_ContactRole'>
	<img src = "images/circle.gif"><?php echo _("Loading...");?>
	</div>
	
	<div class="header">
		<h3><?php echo _("Alias Type");?></h3>
		<span id='span_newAliasType' class='addElement'><button type="button" class="btn" onclick='showAdd("AliasType", "<?php echo _("New alias type") ?>");'><?php echo "<img id='Add' class='AdminAddIcon' src='images/plus.gif' title= '"._("Add alias type")."' />";?></button></span>
	</div>

	<p id='span_AliasType_response'></p>
	
	<div id='div_AliasType'>
		<img src = "images/circle.gif"><?php echo _("Loading...");?>
	</div>
	<div class="header">
		<h3><?php echo _("External Login Type");?></h3>
		<span id='span_newExternalLoginType' class='addElement'><button type="button" class="btn" onclick='showAdd("ExternalLoginType", "<?php echo _("New external login type") ?>");'><?php echo "<img id='Add' class='addIcon' src='images/plus.gif' title= '"._("Add external login type")."' />";?></button></span>
	</div>

	<p id='span_ExternalLoginType_response'></p>

	<div id='div_ExternalLoginType'>
	<img src = "images/circle.gif"><?php echo _("Loading...");?>
	</div>
	
	<div class="header">
			<h3><?php echo _("Issue Type");?></h3>
			<span id='span_newIssueLogType' class='addElement'><button type="button" class="btn" onclick='showAdd("IssueLogType", "<?php echo _("New issue login type") ?>");'><?php echo "<img id='Add' class='addIcon' src='images/plus.gif' title= '"._("Add issue type")."' />";?></button></span>
		</div>
		<p id='span_IssueLogType_response'></p>

		<div id='div_IssueLogType'>
			<img src = "images/circle.gif"><?php echo _("Loading...");?>
		</div>
	</article>
</main>
	<script type="text/javascript" src="js/admin.js"></script>
	<?php

//end else for admin
}else{
	echo _("You do not have permissions to access this screen.");
}

include 'templates/footer.php';
?>
</body>
</html>