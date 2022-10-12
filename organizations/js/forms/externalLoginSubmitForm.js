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

$(document).ready(function(){

	 $("#submitExternalLoginForm").click(function () {
		submitExternalLogin();
	 });


	//do submit if enter is hit
	$('#loginURL').keyup(function(e) {
	      if(e.keyCode == 13) {
		submitExternalLogin();
	      }
	});

	//do submit if enter is hit
	$('#emailAddress').keyup(function(e) {
	      if(e.keyCode == 13) {
		submitExternalLogin();
	      }
	});

	//do submit if enter is hit
	$('#username').keyup(function(e) {
	      if(e.keyCode == 13) {
		submitExternalLogin();
	      }
	});

	//do submit if enter is hit
	$('#password').keyup(function(e) {
	      if(e.keyCode == 13) {
		submitExternalLogin();
	      }
	});

 });

// Validate External Login form

function validateExternalLogin() {
    if($("#username").val() == ''){
        $("#span_errors").html('<br />Please enter a username');
        $("#username").focus();
        return false;
    }else if($("#password").val() == ''){
        $("#span_errors").html('<br />Please enter a password');
        $("#password").focus();
        return false;
    }else{
        return true;
    }
}

function submitExternalLogin(){
    if(validateExternalLogin() === true){
        $.ajax({
            type:       "POST",
            url:        "ajax_processing.php?action=submitExternalLogin",
            cache:      false,
            data:       { externalLoginID: $("#editExternalLoginID").val(), organizationID: $("#editOrganizationID").val(), externalLoginTypeID: $("#externalLoginTypeID").val(), loginURL: $("#loginURL").val(), emailAddress: $("#emailAddress").val(), username: $("#username").val(), password: $("#password").val(), noteText: $("#noteText").val() },
            success:    function(html) {
                if (html.length > 1){
                    $("#span_errors").html(html);
                }else{
                    window.parent.updateAccount();
                    myCloseDialog();
                    return false;
                }
            }
        });
    }
}
