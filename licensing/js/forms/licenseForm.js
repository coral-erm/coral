/*
**************************************************************************************************************************
** CORAL Licensing Module v. 1.0
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
	$("#licenseShortName").keyup(function() {
		  $.ajax({
			 type:       "GET",
			 url:        "ajax_processing.php",
			 cache:      false,
			 async:	     true,
			 data:       "action=getExistingLicenseName&shortName=" + $("#licenseShortName").val(),
			 success:    function(exists) {
				if ((exists == "0") || (exists == $("#editLicenseID").val())){
					$("#span_error_licenseShortName").html("");
					$("#submitLicense").removeAttr("disabled");
				}else{
				  $("#span_error_licenseShortName").html(_("This name is already being used!"));
				  $("#submitLicense").attr("disabled","disabled");

				}
			 }
		  });


	});


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
				if (exists == "0"){
					$("#licenseOrganizationID").val("");
					$("#span_error_organizationNameResult").html("<br />"+_("Warning!  This organization will be added new."));

				}else{
					$("#licenseOrganizationID").val(exists);
					$("#span_error_organizationNameResult").html("");

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
					if (exists == "0"){
					        $("#licenseOrganizationID").val("");
					        $("#span_error_organizationNameResult").html("<br />"+_("Warning!  This organization will be added new."));

					}else{
						$("#licenseOrganizationID").val(exists);
						$("#span_error_organizationNameResult").html("");

					}
				 }
			  });

		    }
		}
	 });


	//once something has been selected, change the hidden input value
	$("#organizationName").result(function(event, data, formatted) {
		if (data[0]){
			$("#licenseOrganizationID").val(data[0]);
			$("#span_error_organizationNameResult").html("");
		}
	});


});

 //attach enter key event to new input and call add data when hit
 $('#licenseConsortiumID').keyup(function(e) {
		 if(e.keyCode == 13) {
			   doSubmitLicense();
		 }
 });



$("#submitLicense").click(function () {
  	doSubmitLicense();
});


function doSubmitLicense(){
  if (validateForm() === true) {
	// ajax call to add/update
	$.post("ajax_processing.php?action=submitLicense", { licenseID: $("#editLicenseID").val(),shortName: $("#licenseShortName").val(), organizationID: $("#licenseOrganizationID").val(), organizationName: $("#organizationName").val(), consortiumID: $("#licenseConsortiumID").val()  } ,
		function(data){$("#div_licenseForm").html(data);});


	return false;

  }
}

//the following are only used when interoperability with organizations module is turned off
function newConsortium(){
  $('#span_newConsortium').html("<input type='text' name='newConsortium' id='newConsortium' class='licenseAddInput' />  <button type='button' class='btn' onclick='addConsortium();'>"+_("add")+"</button>");

	 //attach enter key event to new input and call add data when hit
	 $('#span_newConsortium').keyup(function(e) {

			 if(e.keyCode == 13) {
				   addConsortium();
			 }
	 });
}


function addConsortium(){
  //add consortium to db and returns updated select box
  $.ajax({
	 type:       "GET",
	 url:        "ajax_processing.php",
	 cache:      false,
	 data:       "action=addConsortium&shortName=" + $("#newConsortium").val(),
	 success:    function(html) { $('#span_consortium').html(html); $('#span_newConsortium').html("<span class='error'>"+_("Consortium has been added")+"</span>"); }
 });
}



//validates fields
function validateForm (){
	myReturn=0;
	if (!validateRequired('licenseShortName',_("License Name is required."))) myReturn="1";
	if (!validateRequired('organizationName',_("Provider is required."))) myReturn="1";

	if (myReturn == "1"){
		return false;
	}else{
		return true;
	}
}
