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


/* CORAL setup */
include_once 'user.php';

$util = new Utility();
$config = new Configuration();

/* module setup */
$moduleTitle = _('Resources');

if ($user->isAdmin() || $user->canEdit()) {
    $moduleMenu = array(
        'index' => array(
            'url' => 'index.php',
            'text' => _("Home")
        ),
        'new' => array(
            'action' => "javascript:myDialog('ajax_forms.php?action=getNewResourceForm',1000,1000)",
            'id' => 'newResource',
            'classes' => 'thickbox',
            'text' => _("New Resource")
        ),
        'queue' => array(
            'url' => 'queue.php',
            'text' => _("My Queue")
        ),
        'import' => array(
            'url' => 'import.php',
            'text' => _("File Import")
        ),
        'ebsco_kb' => array(
            'url' => 'ebsco_kb.php',
            'text' => _("EBSCO Kb")
        ),
        'dashboards' => array(
            'url' => 'dashboard_menu.php',
            'text' => _("Dashboards")
        ),
        'admin' => array(
            'url' => 'admin.php',
            'text' => _("Admin")
        )
    );

    if ($config->settings->ebscoKbEnabled !== 'Y') {
        unset( $moduleMenu['ebsco_kb'] );
    }

    if ($config->settings->enhancedCostHistory !== "Y") {
        unset( $moduleMenu['dashboards'] );
    }

    if ( !$user->isAdmin() ) {
        unset( $moduleMenu['admin'] );
    }
}

include_once '../templates/header.php';
?>
