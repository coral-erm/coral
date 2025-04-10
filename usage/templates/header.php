<?php
/*
**************************************************************************************************************************
** CORAL Usage Statistics Module
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
include_once "user.php";


$util = new Utility();
$config = new Configuration();

/* module setup */
$moduleTitle = _('Usage Statistics');

$moduleMenu = array(
    'index' => array(
        'url' => 'index.php',
        'text' => _("Home")
    ),
    'import' => array(
        'url' => 'import.php',
        'text' => _("File Import")
    ),
    'sushi' => array(
        'url' => 'sushi.php',
        'text' => _("SUSHI")
    ),
    'reporting' => array(
        'url' => 'reporting.php',
        'text' => _("Report Options")
    ),
    'usage' => array(
        'url' => '../reports/',
        'text' => _("Usage Reports"),
        'target' => '_blank'
    ),
    'admin' => array(
        'url' => 'admin.php',
        'text' => _("Admin")
    )
  );

  if ( $config->settings->reportsModule !== "Y" ) {
    unset( $moduleMenu['usage'] );
  }

  if ( !$user->isAdmin() ) {
    unset( $moduleMenu['admin'] );
  }

  include_once '../templates/header.php';
  ?>