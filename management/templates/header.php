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

/* Set variables for module-specific items used in templates */
$moduleTitle = _('Management');
$moduleIconPath = 'images/title-icon-management.png';
$titleTableWidth = 1024;

/* Include header template */
include_once '../templates/header.php';

/* Module-specific header content */
// TODO: is this necessary in the management module, or a remnant from the Licensing module?
//this is a workaround for a bug between autocomplete and thickbox causing a page refresh on the add/edit license form when 'enter' key is hit
//this will redirect back to the actual license record
if ((isset($_GET['editLicenseForm'])) && ($_GET['editLicenseForm'] == "Y")){
    if (((isset($_GET['licenseShortName'])) && ($_GET['licenseShortName'] == "")) && ((isset($_GET['licenseOrganizationID'])) && ($_GET['licenseOrganizationID'] == ""))){
        $err="<span style='color:red;text-align:left;'>" . _("Both license name and organization must be filled out.  Please try again.") . "</span>";
    }else{
        $util->fixLicenseFormEnter($_GET['editLicenseID']);
    }
}

/* Include title bar template */
include_once '../templates/title.php';