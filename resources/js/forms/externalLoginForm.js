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




function submitExternalLogin(){
	$('#submitExternalLoginForm').attr("disabled", "disabled");
	  $.ajax({
		 type:       "POST",
		 url:        "ajax_processing.php?action=submitExternalLogin",
		 cache:      false,
		 data:       { externalLoginID: $("#editExternalLoginID").val(), externalLoginTypeID: $("#externalLoginTypeID").val(), loginURL: $("#loginURL").val(), emailAddress: $("#emailAddress").val(), username: $("#username").val(), password: $("#password").val(), noteText: $("#noteText").val(),resourceID: $("#editResourceID").val()  },
		 success:    function(html) {
			if (html){
				$("#span_errors").html(html);
				$("#submitExternalLoginForm").removeAttr("disabled");
			}else{
				myDialogClose();
				window.parent.updateAccounts();
				return false;
			}
		 }


	 });
}
