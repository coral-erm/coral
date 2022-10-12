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
	<table class="headerTable">
	<tr><td align='left'>
	<span class="headerText"><?php echo _("Users");?></span>&nbsp;&nbsp;<span id='span_User_response'></span><span id='span_newUser' class='adminAddInput'><a href='javascript:void(0)' onclick='myDialog("ajax_forms.php?action=getAdminUserUpdateForm&height=185&width=250&modal=true",250,250)' class='thickbox' id='expression'><?php echo "<img id='Add' class='addIcon' src='images/plus.gif' title= '"._("Add user")."' />";?></a></span>

	<br />
	<div id='div_User'>
	<img src = "images/circle.gif"><?php echo _("Loading...");?>
	</div>
	</td></tr>
	</table>

	<br />
	<br />


	<table class="headerTable">
	<tr><td align='left'>
	<span class="headerText"><?php echo _("Organization Role");?></span>&nbsp;&nbsp;<span id='span_OrganizationRole_response'></span><span id='span_newOrganizationRole' class='adminAddInput'><a href='javascript:showAdd("OrganizationRole");'><?php echo "<img id='Add' class='addIcon' src='images/plus.gif' title= '"._("Add organization role")."' />";?></a></span>

	<br />
	<div id='div_OrganizationRole'>
	<img src = "images/circle.gif"><?php echo _("Loading...");?>
	</div>
	</td></tr>
	</table>

	<br />
	<br />

	<table class="headerTable">
	<tr><td align='left'>
	<span class="headerText"><?php echo _("Contact Role");?></span>&nbsp;&nbsp;<span id='span_ContactRole_response'></span><span id='span_newContactRole' class='adminAddInput'><a href='javascript:showAdd("ContactRole");'><?php echo "<img id='Add' class='addIcon' src='images/plus.gif' title= '"._("Add contact role")."' />";?></a></span>

	<br />
	<div id='div_ContactRole'>
	<img src = "images/circle.gif"><?php echo _("Loading...");?>
	</div>
	</td></tr>
	</table>


	<br />
	<br />

	<table class="headerTable">
	<tr><td align='left'>
	<span class="headerText"><?php echo _("Alias Type");?></span>&nbsp;&nbsp;<span id='span_AliasType_response'></span>	<span id='span_newAliasType' class='adminAddInput'><a href='javascript:showAdd("AliasType");'><?php echo "<img id='Add' class='AdminAddIcon' src='images/plus.gif' title= '"._("Add alias type")."' />";?></a></span>

	<br />
	<div id='div_AliasType'>
	<img src = "images/circle.gif"><?php echo _("Loading...");?>
	</div>
	</td></tr>
	</table>

	<br />
	<br />

	<table class="headerTable">
	<tr><td align='left'>
	<span class="headerText"><?php echo _("External Login Type");?></span>&nbsp;&nbsp;<span id='span_ExternalLoginType_response'></span><span id='span_newExternalLoginType' class='adminAddInput'><a href='javascript:showAdd("ExternalLoginType");'><?php echo "<img id='Add' class='addIcon' src='images/plus.gif' title= '"._("Add external login type")."' />";?></a></span>

	<br />
	<div id='div_ExternalLoginType'>
	<img src = "images/circle.gif"><?php echo _("Loading...");?>
	</div>
	</td></tr>
	</table>


  <br /><br /><table class="headerTable">
    <tr><td align='left'>
      <span class="headerText"><?php echo _("Issue Type");?></span>&nbsp;&nbsp;<span id='span_IssueLogType_response'></span><span id='span_newIssueLogType' class='adminAddInput'><a href='javascript:showAdd("IssueLogType");'><?php echo "<img id='Add' class='addIcon' src='images/plus.gif' title= '"._("Add issue type")."' />";?></a></span>

      <br />
      <div id='div_IssueLogType'>
        <img src = "images/circle.gif"><?php echo _("Loading...");?>
      </div>
    </td></tr>
  </table>
  <br />
  <br />

	<script type="text/javascript" src="js/admin.js"></script>
	<?php

//end else for admin
}else{
	echo _("You do not have permissions to access this screen.");
}

include 'templates/footer.php';
?>
