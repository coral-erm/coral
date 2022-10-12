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

$pageTitle=_('Administration');
include 'templates/header.php';

//set referring page
$_SESSION['ref_script']=$currentPage;

if ($user->isAdmin()){

?>


<table class="headerTable">
<tr><td>
<span class="headerText"><?php echo _("Users");?></span>&nbsp;&nbsp;<span id='span_User_response' class='redText'></span><span id='span_newUser' class='adminAddInput'><a href='javascript:void(0)' onclick='myDialog("ajax_forms.php?action=getAdminUserUpdateForm&height=202&width=288&modal=true",350,450)' class='thickbox' id='expression'><?php echo "<img id='Add' class='addIcon' src='images/plus.gif' title= '"._("Add")."' />";?></a></span>

<br /><br />
<div id='div_User'>
<img src = "images/circle.gif" /><?php echo _("Loading...");?>
</div>
</td></tr>
</table>

<br />
<br />

<table class="headerTable">
<tr><td>
<span class="headerText"><?php echo _("Document Types");?></span>&nbsp;&nbsp;<span id='span_DocumentType_response'></span><span id='span_newDocumentType' class='adminAddInput'><a href='javascript:showAdd("DocumentType");'><?php echo "<img id='Add' class='addIcon' src='images/plus.gif' title= '"._("Add")."' />";?></a></span>

<br /><br />
<div id='div_DocumentType'>
<img src = "images/circle.gif"><?php echo _("Loading...");?>
</div>
</td></tr>
</table>

<br />
<br />

<table class="headerTable">
<tr><td>
<span class="headerText"><?php echo _("Note Types");?></span>&nbsp;&nbsp;<span id='span_DocumentNoteType_response'></span><span id='span_newDocumentNoteType' class='adminAddInput'><a href='javascript:showAdd("DocumentNoteType");'><?php echo "<img id='Add' class='addIcon' src='images/plus.gif' title= '"._("Add")."' />";?></a></span>

<br /><br />
<div id='div_DocumentNoteType'>
<img src = "images/circle.gif"><?php echo _("Loading...");?>
</div>
</td></tr>
</table>

<!--
<table class="headerTable">
<tr><td>
<span class="headerText">Expression Types</span>&nbsp;&nbsp;<span id='span_ExpressionType_response'></span>
<br /><span id='span_newExpressionType' class='adminAddInput'><a href='javascript:void(0)' onclick='myDialog("ajax_forms.php?action=getExpressionTypeForm&height=148&width=265&modal=true",150,270)' class='thickbox' id='expressionType'>add new expression type</a></span>
<br /><br />
<div id='div_ExpressionType'>
<img src = "images/circle.gif">Loading...
</div>
</td></tr>
</table>
-->

<!--

<br />
<br />
<table class="headerTable">
<tr><td>
<span class="headerText">Qualifiers</span>&nbsp;&nbsp;<span id='span_Qualifier_response'></span>
<br /><span id='span_newQualifier' class='adminAddInput'><a href='javascript:void(0)' onclick='myDialog("ajax_forms.php?action=getQualifierForm&height=148&width=295&modal=true",150,300)' class='thickbox'>add new qualifier</a></span>
<br /><br />
<div id='div_Qualifier'>
<img src = "images/circle.gif">Loading...
</div>
</td></tr>
</table>

<br />
<br />

-->

<!--
<table class="headerTable">
<tr><td>
<span class="headerText">Signature Types</span>&nbsp;&nbsp;<span id='span_SignatureType_response'></span>
<br /><span id='span_newSignatureType' class='adminAddInput'><a href='javascript:showAdd("SignatureType");'>add new signature type</a></span>
<br /><br />
<div id='div_SignatureType'>
<img src = "images/circle.gif">Loading...
</div>
</td></tr>
</table>

<br />
<br />
-->

<!--
<table class="headerTable">
<tr><td>
<span class="headerText">License Statuses</span>&nbsp;&nbsp;<span id='span_Status_response'></span>
<br /><span id='span_newStatus' class='adminAddInput'><a href='javascript:showAdd("Status");'>add new license status</a></span>
<br /><br />
<div id='div_Status'>
<img src = "images/circle.gif">Loading...
</div>
</td></tr>
</table>

<br />
<br />
-->


<?php

$config = new Configuration;

//if the org module is not installed, display provider list for updates
if ($config->settings->organizationsModule != 'Y'){ ?>


	<br />
	<br />

	<table class="headerTable">
	<tr><td>
	<span class="headerText"><?php echo _("Categories");?></span>&nbsp;&nbsp;<span id='span_Consortium_response'></span><span id='span_newConsortium' class='adminAddInput'><a href='javascript:showAdd("Consortium");'><?php echo "<img id='Add' class='addIcon' src='images/plus.gif' title= '"._("Add")."' />";?></a></span>

	<br /><br />
	<div id='div_Consortium'>
	<img src = "images/circle.gif"><?php echo _("Loading...");?>
	</div>
	</td></tr>
	</table>

	<br />
	<br />
<!--
	<table class="headerTable">
	<tr><td>
	<span class="headerText">Providers</span>&nbsp;&nbsp;<span id='span_Organization_response'></span>
	<br /><span id='span_newOrganization' class='adminAddInput'><a href='javascript:showAdd("Organization");'>add new provider</a></span>
	<br /><br />
	<div id='div_Organization'>
	<img src = "images/circle.gif">Loading...
	</div>
	</td></tr>
	</table>
-->
<?php } ?>

<br />

<script type="text/javascript" src="js/admin.js"></script>
								</center>

<?php
}else{
	echo _("You don't have permission to access this page");
}

include 'templates/footer.php';
?>
