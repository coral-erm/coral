<?php

/*
**************************************************************************************************************************
** CORAL Management Module v. 1.0
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

//set referring page
$_SESSION['ref_script']=$currentPage;

$pageTitle=_('Administration');
include 'templates/header.php';

if (!$user->isAdmin()) {
	echo _("You don't have permission to access this page");
	exit;
}
?>

<main id="main-content">
	<article>
		<h2><?php echo _("Administration");?></h2>
		<div class="header">
			<h3><?php echo _("Users");?></h3>
			<span id='span_newUser' class='adminAddInput addElement'><a href='javascript:void(0)' onclick='myDialog("ajax_forms.php?action=getAdminUserUpdateForm&height=202&width=288&modal=true",350,450)' class='thickbox' id='expression'><?php echo "<img id='Add' class='addIcon' src='images/plus.gif' title= '"._("Add")."' />";?></a></span>
		</div>

		<span id='span_User_response' class='error'></span>

		<div id='div_User'>
			<img src = "images/circle.gif" /><?php echo _("Loading...");?>
		</div>

		<div class="header">
			<h3><?php echo _("Document Types");?></h3>
			<span id='span_newDocumentType' class='adminAddInput addElement'><a href='javascript:showAdd("DocumentType", "<?php echo _('New document type') ?>");'><?php echo "<img id='Add' class='addIcon' src='images/plus.gif' title= '"._("Add")."' />";?></a></span>
		</div>

		<span id='span_DocumentType_response'></span>

		<div id='div_DocumentType'>
			<img src = "images/circle.gif"><?php echo _("Loading...");?>
		</div>

		<div class="header">
			<h3 class="headerText"><?php echo _("Note Types");?></h3>
			<span id='span_newDocumentNoteType' class='adminAddInput addElement'><a href='javascript:showAdd("DocumentNoteType", <?php echo _('New document note type') ?>);'><?php echo "<img id='Add' class='addIcon' src='images/plus.gif' title= '"._("Add")."' />";?></a></span>
		</div>
		
		<span id='span_DocumentNoteType_response'></span>

		<div id='div_DocumentNoteType'>
			<img src = "images/circle.gif"><?php echo _("Loading...");?>
		</div>
		
		
<?php

$config = new Configuration;

//if the org module is not installed, display provider list for updates
if ($config->settings->organizationsModule != 'Y'){ ?>

<div class="header">
	<h3><?php echo _("Categories");?></h3>
	<span id='span_newConsortium' class='adminAddInput'><a href='javascript:showAdd("Consortium", <?php echo _('New consortium') ?>);'><?php echo "<img id='Add' class='addIcon' src='images/plus.gif' title= '"._("Add")."' />";?></a></span>
	
</div>
	<span id='span_Consortium_response'></span>
	
	<div id='div_Consortium'>
	<img src = "images/circle.gif"><?php echo _("Loading...");?>
	</div>
<?php } ?>

</article>
</main>
<?php

include 'templates/footer.php';
?>
<script src="js/admin.js"></script>
</body>
</html>