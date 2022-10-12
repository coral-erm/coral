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


$(document).ready(function(){

        updateTermsReport('');

});


function updateTermsReport(){

      $.ajax({
         type:       "GET",
         url:        "ajax_htmldata.php",
         cache:      false,
         data:       "action=getTermsReport&expressionTypeID=" + $('#expressionTypeID').val(),
         success:    function(html) {
         	$('#div_report').html(html);
         	}


     });

}


function showFullDocumentText(expressionID){
	$('#text_short_' + expressionID).hide();
	$('#text_full_' + expressionID).show();

}

function hideFullDocumentText(expressionID){
	$('#text_full_' + expressionID).hide();
	$('#text_short_' + expressionID).show();

}
