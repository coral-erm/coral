/*
**************************************************************************************************************************
** CORAL Authentication Module v. 1.0
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

var gt = new Gettext({ 'domain' : 'messages' });
function _(msgid) {
    return gt.gettext(msgid);
}

$(document).ready(function(){

	updateUsers();

});



function updateUsers() {

  $.ajax({
	 type:       "GET",
	 url:        "ajax_htmldata.php",
	 cache:      false,
	 data:       "action=getUsers",
	 success:    function(html) {
		$("#div_users").html(html);
		bind_removes();
	 }


  });
}


function submitUserForm(){
  if (validateForm() === true) {
	// ajax call to add/update
	$.post("ajax_processing.php?action=submitUser", { loginID: $("#textLoginID").val(), editLoginID: $("#editLoginID").val(), password: $("#password").val(), adminInd: getCheckboxValue('adminInd')  } ,
		function(data){
			myCloseDialog();
			updateUsers();
			return false;
		}
	);
	return false;

  }
}

function validateForm (){
    var control=true;
    if (($("#password").val() != '') && ($("#password").val() != $("#passwordReenter").val())){
        $("#span_errors").html(_("Passwords do not match"));
        $("#passwordReenter").focus();
        control = false;
    }

    if (($("#editLoginID").val() == '') && (($("#password").val() == ''))){
        $("#span_errors").html(_("Password is required"));
        $("#password").focus();
        control = false;
    }
    if (($("#textLoginID").val() == '')){
        $("#span_errors").html(_("UserID is required"));
        $("#textLoginID").focus();
        control = false;
    }
    return control;
}

  function bind_removes(){


  	 $(".deleteUser").unbind('click').click(function () {
	  if (confirm(_("Do you really want to delete this user?")) == true) {
		  $.ajax({
			 type:       "GET",
			 url:        "ajax_processing.php",
			 cache:      false,
			 data:       "action=deleteUser&loginID=" + $(this).attr("id"),
			 success:    function(html) {
				 updateUsers();
			 }



		 });
	  }
  	 });
  }
