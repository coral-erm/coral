<?php
/*
**************************************************************************************************************************
** CORAL Organizations Module v. 1.0
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

$util = new Utility();
$config = new Configuration();

//get the current page to determine which menu button should be depressed
$currentPage = $_SERVER["SCRIPT_NAME"];
$parts = Explode('/', $currentPage);
$currentPage = $parts[count($parts) - 1];


//this is a workaround for a bug between autocomplete and thickbox causing a page refresh on the add/edit license form when 'enter' key is hit
//this will redirect back to the actual license record
if ((isset($_GET['editLicenseForm'])) && ($_GET['editLicenseForm'] == "Y")){
	if (((isset($_GET['licenseShortName'])) && ($_GET['licenseShortName'] == "")) && ((isset($_GET['licenseOrganizationID'])) && ($_GET['licenseOrganizationID'] == ""))){
		$err="<span style='color:red;text-align:left;'>" . _("Both license name and organization must be filled out.  Please try again.") . "</span>";
	}else{
		$util->fixLicenseFormEnter($_GET['editLicenseID']);
	}
}

//get CORAL URL for 'Change Module' and logout link
$coralURL = $util->getCORALURL();

$target = getTarget();


/* module setup */
$moduleTitle = _('Management');

$moduleMenu = array(
    'index' => array(
        'url' => 'index.php',
        'text' => _("Home")
    ),
    'new' => array(
        'action' => "javascript:myDialog('ajax_forms.php?action=getLicenseForm&height=350&width=300&modal=true&newLicenseID=',1000,1000)",
        'text' => _("New Document"),
        'class' => 'thickbox',
        'id' => 'newLicense'
    ),
    'admin' => array(
        'url' => 'admin.php',
        'text' => _("Admin")
	)
  );

  if ( !$user->isAdmin() ) {
    unset( $moduleMenu['admin'] );
  }

  if ( !$user->canEdit() ) {
    unset( $moduleMenu['new'] );
  }

include_once '../templates/header.php';
?>