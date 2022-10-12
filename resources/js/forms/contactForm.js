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

$(document).ready(function(){


	 $("#submitContactForm").click(function () {
		submitContact();
	 });


	//do submit if enter is hit
	$('#contactName').keyup(function(e) {
	      if(e.keyCode == 13) {
		submitContact();
        }
	});


	//do submit if enter is hit
	$('#contactTitle').keyup(function(e) {
	      if(e.keyCode == 13) {
		submitContact();
	      }
	});


	//do submit if enter is hit
	$('#phoneNumber').keyup(function(e) {
	      if(e.keyCode == 13) {
		submitContact();
	      }
	});


	//do submit if enter is hit
	$('#altPhoneNumber').keyup(function(e) {
	      if(e.keyCode == 13) {
		submitContact();
	      }
	});

	//do submit if enter is hit
	$('#faxNumber').keyup(function(e) {
	      if(e.keyCode == 13) {
		submitContact();
	      }
	});


	//do submit if enter is hit
	$('#emailAddress').keyup(function(e) {
	      if(e.keyCode == 13) {
		submitContact();
	      }
	});




	//the following are all to change the look of the inputs when they're clicked
	$('.changeDefault').on('focus', function(e) {
		if (this.value == this.defaultValue){
			this.value = '';
		}
	});

	 $('.changeDefault').on('blur', function() {
		if(this.value == ''){
			this.value = this.defaultValue;
		}
	 });


        $('.changeInput').addClass("idleField");

        $('.changeInput').on('focus', function() {


		$(this).removeClass("idleField").addClass("focusField");

		if(this.value != this.defaultValue){
			this.select();
		}

	 });


	 $('.changeInput').on('blur', function() {
		$(this).removeClass("focusField").addClass("idleField");
	 });




	$('select').addClass("idleField");
	$('select').on('focus', function() {
		$(this).removeClass("idleField").addClass("focusField");

	});

	$('select').on('blur', function() {
		$(this).removeClass("focusField").addClass("idleField");
	});



	$('textarea').addClass("idleField");
	$('textarea').focus(function() {
		$(this).removeClass("idleField").addClass("focusField");
	});

	$('textarea').blur(function() {
		$(this).removeClass("focusField").addClass("idleField");
	});



 });



function submitContact(){
	contactRolesList ='';
	$(".check_roles:checked").each(function(id) {
	      contactRolesList += $(this).val() + ",";
	});

	if (validateForm() === true) {
		$('#submitContactForm').attr("disabled", "disabled");
		  $.ajax({
			 type:       "POST",
			 url:        "ajax_processing.php?action=submitContact",
			 cache:      false,
			 data:       { contactID: $("#editContactID").val(), resourceID: $("#editResourceID").val(), resourceAcquisitionID: $("#editResourceAcquisitionID").val(), name: $("#contactName").val(), title: $("#contactTitle").val(), addressText: $("#addressText").val(), phoneNumber: $("#phoneNumber").val(), altPhoneNumber: $("#altPhoneNumber").val(), faxNumber: $("#faxNumber").val(), emailAddress: $("#emailAddress").val(), archiveInd: getCheckboxValue('invalidInd'), noteText: $("#noteText").val(),  contactRoles: contactRolesList },
			 success:    function(html) {
				if (html){
					$("#span_errors").html(html);
					$("#submitContactForm").removeAttr("disabled");
				}else{
					myDialogPOST();
					window.parent.updateContacts();
					window.parent.updateArchivedContacts();
					return false;
				}
			 }


		 });
	}
}




 function validateForm (){
 	myReturn=0;

	contactRolesList ='';
	$(".check_roles:checked").each(function(id) {
	      contactRolesList += $(this).val() + ",";
	});

 	if (contactRolesList == ''){
 	    $("#span_error_contactRole").html(_("Please choose at least one role."));
 	    myReturn="1";
 	} else {
 	    $("#span_error_contactRole").html('');
	}


 	if (myReturn == "1"){
		return false;
 	}else{
 		return true;
 	}
}
