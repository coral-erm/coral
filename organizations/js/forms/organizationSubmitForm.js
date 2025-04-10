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

$(function(){

    if (CORAL_ILS_LINK) {
         $( "#organizationName" ).autocomplete('ajax_processing.php?action=getILSVendorList', {
            minChars: 3,
            max: 20,
            mustMatch: false,
            width: 220,
            delay: 1000,
            matchContains: false,
            formatItem: function(row) {
                return "<span>" + row[0] + "</span>";
            },
            formatResult: function(row) {
                return row[0].replace(/(<.+?>)/gi, '');
            }
        });

        //once an ILS vendor is selected, check if it doesn't already exist in Coral
        $("#organizationName").result(function(event, data, formatted) {
            existsInCoral();
            retrieveILSVendor();
        });

    } else {
        $("#organizationName").keyup(function() {
            existsInCoral();
        });
    }

	 $("#openOrganizationURL").click(function () {
		window.open($("#companyURL").val());
		return false;
	 });



	 $("#submitOrganizationChanges").click(function () {
		submitOrganization();
	 });


	 $("#parentOrganization").autocomplete('ajax_processing.php?action=getOrganizationList', {
		minChars: 2,
		max: 20,
		mustMatch: false,
		width: 220,
		delay: 10,
		matchContains: false,
		formatItem: function(row) {
			return "<span>" + row[0] + "</span>";
		},
		formatResult: function(row) {
			return row[0].replace(/(<.+?>)/gi, '');
		}

	  });


	//once something has been selected, change the hidden input value
	$("#parentOrganization").result(function(event, data, formatted) {
		$("#parentOrganizationID").val(data[1]);
	});


	//do submit if enter is hit
	$('#organizationName').keyup(function(e) {
	      if(e.keyCode == 13) {
		submitOrganization();
	      }
	});


	//do submit if enter is hit
	$('#parentOrganization').keyup(function(e) {
	      if(e.keyCode == 13) {
		submitOrganization();
	      }
	});

	//do submit if enter is hit
	$('#companyURL').keyup(function(e) {
	      if(e.keyCode == 13) {
		submitOrganization();
	      }
	});


 });

function retrieveILSVendor() {
    $.ajax({
         type:       "GET",
         url:        "ajax_processing.php",
         cache:      false,
         async:      true,
         data:       "action=getILSVendorInfos&name=" + $("#organizationName").val(),
         success:    function(vendorString) {
            vendor = $.parseJSON(vendorString);
            if (vendor == null) return false;
            $("#accountDetailText").text(vendor['accountnumber']);
            $('#accountDetailText').attr("disabled", "disabled");
            $("#noteText").text(vendor['notes']);
            $('#noteText').attr("disabled", "disabled");
            $("#companyURL").val(vendor['url']);
            $('#companyURL').attr("disabled", "disabled");
            $('#organizationName').attr("disabled", "disabled");
            $('.ils_role').attr('checked', true);
        }
     });
}


//check this name to make sure it isn't already being used
function existsInCoral() {
      $.ajax({
         type:       "GET",
         url:        "ajax_processing.php",
         cache:      true,
         async:      true,
         data:       "action=getExistingOrganization&name=" + $("#organizationName").val() + "&organizationID=" + $("#editOrganizationID").val(),
         success:    function(exists) {
            if (exists == 0){
                $("#span_errors").html("");
                $("#submitOrganizationChanges").removeAttr("disabled");
            }else{
                $("#span_errors").html("<br />"+_("This organization already exists!"));
                $("#submitOrganizationChanges").attr("disabled","disabled");
            }
         }
      });
}




 function validateForm (){
 	myReturn=0;
 	if (!validateRequired('organizationName',"<br />"+_("Name must be entered to continue."))) myReturn=1;


 	if (myReturn == 1){
		return false;
 	}else{
 		return true;
 	}
}




function submitOrganization(){
	organizationRolesList ='';
	$(".check_roles:checked").each(function(id) {
	      organizationRolesList += $(this).val() + ",";
	});

	if (validateForm() === true) {
		$('#submitOrganizationChanges').attr("disabled", "disabled");
		  $.ajax({
			 type:       "POST",
			 url:        "ajax_processing.php?action=submitOrganization",
			 cache:      false,
			 data:       { organizationID: $("#editOrganizationID").val(), parentOrganizationID: $("#parentOrganizationID").val(),parentOrganization: $("#parentOrganization").val(), name: $("#organizationName").val(), companyURL: $("#companyURL").val(), noteText: $("#noteText").val(), accountDetailText: $("#accountDetailText").val(), organizationRoles: organizationRolesList },
			 success:    function(html) {
				//if this is a new organization
				if ($("#editOrganizationID").val()==null || $("#editOrganizationID").val()=="") {
					window.parent.location=("orgDetail.php?ref=new&organizationID=" + html);
					return false;
				//if this was an edit for an existing organization
				}else{
					if (html.length > 1){
						$("#span_errors").html(html);
						$("#submitOrganizationChanges").removeAttr("disabled");
					}else{
						window.parent.updateOrganization();
                        myCloseDialog();
						return false;
					}
				}
			 }


		 });

	}

}
