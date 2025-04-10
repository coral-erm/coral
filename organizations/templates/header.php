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

/* CORAL setup */
include_once 'directory.php';
include_once 'user.php';

/* module setup */
$moduleTitle = _('Organizations');

$moduleMenu = array(
    'index' => array(
        'url' => 'index.php',
        'text' => _("Home")
    ),
    'new' => array(
        'action' => "javascript:myDialog('ajax_forms.php?action=getOrganizationForm&height=',1000,1000)",
        'classes' => 'thickbox',
        'id' => 'newLicense',
        'text' => _("New Organization")
    ),
    'admin' => array(
        'url' => 'admin.php',
        'text' => _("Admin")
    ),
);

if ( !$user->isAdmin() ) {
    unset( $moduleMenu['admin'] );
}

if ( !$user->canEdit() ) {
    unset( $moduleMenu['new'] );
}

$target = getTarget();

include_once '../templates/header.php';
?>