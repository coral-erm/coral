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


 $(document).ready(function(){

      updateImportTable();


      	//do submit if enter is hit
      	$('#submitFile').keyup(function(e) {
      	      if(e.keyCode == 13) {
      		submitForm();
      	      }
	});

 });



 var pageStart = '1';

 function updateImportTable(){
       $('#span_feedback').html("<img src = 'images/circle.gif'>&nbsp;&nbsp;" + _("Loading..."));
       $.ajax({
          type:       "GET",
          url:        "ajax_htmldata.php",
          cache:      false,
          data:       "action=getImportTable&pageStart=" + pageStart,
          success:    function(html) {
          	$('#span_feedback').html('');
          	$('#div_recentImports').html(html);
          }
       });

 }



function setPageStart(pageStartNumber){
 	pageStart=pageStartNumber;
 	updateImportTable();
}



function validateForm(){

	  if ($("#usageFile").val() == '') {
	  	$('#span_error').html('&nbsp;' + _("Please select a file."));
	  	return false;
	  }else{
	  	$('#span_error').html('');
	  	return true;
	  }

}

