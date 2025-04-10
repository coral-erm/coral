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


$(function(){


	//check this name to make sure it isn't already being used
	//in case user doesn't use the Autofill and the organization already exists
	$("#organizationName").keyup(function() {
		  $.ajax({
			 type:       "GET",
			 url:        "ajax_processing.php",
			 cache:      false,
			 async:	     true,
			 data:       "action=getExistingOrganizationName&shortName=" + $("#organizationName").val(),
			 success:    function(exists) {
				if ((exists == 0) && ($("#organizationName").val() != '')){
					$("#organizationID").val("");
					$("#span_error_organizationNameResult").html("<br />" + _("This organization doesn't exist in the CORAL Organizations module."));
					$('#submitOrganization').attr("disabled", "disabled");

				}else{
					$("#organizationID").val(exists);
					$("#span_error_organizationNameResult").html("");
					$('#submitOrganization').removeAttr("disabled");

				}
			 }
		  });


	});



	//used for autocomplete formatting
         formatItem = function (row){
             return "<span>" + row[1] + "</span>";
         }

         formatResult = function (row){
             return row[1].replace(/(<.+?>)/gi, '');
         }

	$("#organizationName").autocomplete('ajax_processing.php?action=getOrganizations', {
		minChars: 2,
		max: 50,
		mustMatch: false,
		width: 233,
		delay: 20,
		cacheLength: 10,
		matchSubset: true,
		matchContains: true,
		formatItem: formatItem,
		formatResult: formatResult,
		parse: function(data){
		    var parsed = [];
		    var rows = data.split("\n");
		    for (var i=0; i < rows.length; i++) {
		      var row = $.trim(rows[i]);
		      if (row) {
			row = row.split("|");
			parsed[parsed.length] = {
			  data: row,
			  value: row[0],
			  result: formatResult(row, row[0]) || row[0]
			};
		      }
		    }

		    if (parsed.length == 0) {

			  $.ajax({
				 type:       "GET",
				 url:        "ajax_processing.php",
				 cache:      false,
				 async:	     true,
				 data:       "action=getExistingOrganizationName&shortName=" + $("#organizationName").val(),
				 success:    function(exists) {
					if ((exists == 0) && ($("#organizationName").val() != '')){
					        $("#organizationID").val("");
					        $("#span_error_organizationNameResult").html("<br />" + _("This organization doesn't exist in the CORAL Organizations Module."));
					        $('#submitOrganization').attr("disabled", "disabled");

					}else{
						$("#organizationID").val(exists);
						$("#span_error_organizationNameResult").html("");
						$('#submitOrganization').removeAttr("disabled");

					}
				 }
			  });

		    }
		}
	 });


	//once something has been selected, change the hidden input value
	$("#organizationName").result(function(event, data, formatted) {
		if (data[0]){
			$("#organizationID").val(data[0]);
			$("#span_error_organizationNameResult").html("");
			$('#submitOrganization').removeAttr("disabled");
		}
	});


});

 //attach enter key event to new input and call add data when hit
 $('#organizationName').keyup(function(e) {
		 if(e.keyCode == 13) {
			   doSubmitOrganization();
		 }
 });



$("#submitOrganization").click(function () {
  	doSubmitOrganization();
});


function doSubmitOrganization(){
  $.ajax({
	 type:       "GET",
	 url:        "ajax_processing.php",
	 cache:      false,
	 data:       "action=submitOrganization&platformID=" + $("#platformID").val() + "&publisherPlatformID=" + $("#publisherPlatformID").val() + "&organizationID=" + $("#organizationID").val(),
	 success:    function(html) {
		if (html){
			$("#span_errors").html(html);
		}else{
			myCloseDialog();
			window.parent.updateLoginDetails();
			return false;
		}
	 }
 });

}
