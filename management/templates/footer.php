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
/*
//used to default to previously selected values when back button is pressed
//if the startWith is defined set it so that it will default to the first letter picked
if ((CoralSession::get('res_startWith')) && ($reset != 'Y')){
	echo "startWith = '" . CoralSession::get('res_startWith') . "';";
	echo "$(\"#span_letter_" . CoralSession::get('res_startWith') . "\").removeClass('searchLetter').addClass('searchLetterSelected');";
}

if ((CoralSession::get('res_pageStart')) && ($reset != 'Y')){
	echo "pageStart = '" . CoralSession::get('res_pageStart') . "';";
}

if ((CoralSession::get('res_recordsPerPage')) && ($reset != 'Y')){
	echo "recordsPerPage = '" . CoralSession::get('res_recordsPerPage') . "';";
}

if ((CoralSession::get('res_orderBy')) && ($reset != 'Y')){
	echo "orderBy = \"" . CoralSession::get('res_orderBy') . "\";";
}
/**/
include '../templates/footer.php'; ?>
