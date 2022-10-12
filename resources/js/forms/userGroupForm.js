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



	 $("#submitUserGroupForm").click(function () {
		submitUserGroup();
	 });


	//do submit if enter is hit
	$('#groupName').keyup(function(e) {
	      if(e.keyCode == 13) {
		submitUserGroup();
	      }
	});

	//do submit if enter is hit
	$('#emailAddress').keyup(function(e) {
	      if(e.keyCode == 13) {
		submitUserGroup();
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






	$(".addUser").on('click', function () {

		var loginID = $('.newUserTable').children().children().children().children('.loginID').val();

		if ((loginID == '') || (loginID == null)){
			$('#div_errorUser').html(_("Error - User is required"));
			return false;

		}else{
			$('#div_errorUser').html('');

			//first copy the new user being added
			var originalTR = $('.newUserTR').clone();


			//next append to to the existing table
			//it's too confusing to chain all of the children.
			$('.newUserTR').appendTo('.userTable');

			$('.newUserTR').children().children().children('.addUser').attr({
			  src: 'images/cross.gif',
			  alt: _("remove user from group"),
			  title: _("remove from group")
			});

			$('.newUserTR').children().children().children('.addUser').addClass('remove');
			$('.loginID').addClass('changeSelect');
			$('.loginID').addClass('idleField');
			$('.loginID').css("background-color","");


			$('.addUser').removeClass('addUser');
			$('.newUserTR').removeClass('newUserTR');

			//next put the original clone back, we just need to reset the values
			originalTR.appendTo('.newUserTable');
			$('.newUserTable').children().children().children().children('.loginID').val('');


			return false;
		}
	});





	$(".remove").on('click', function () {
	    $(this).parent().parent().parent().fadeTo(400, 0, function () {
		$(this).remove();
	    });
	    return false;
	});









 });


 function validateUserGroup(){
 	myReturn=0;
 	if (!validateRequired('groupName',"<br />"+_("Group name must be entered to continue.")+"<br />")) myReturn="1";


 	if (myReturn == "1"){
		return false;
 	}else{
 		return true;
 	}
}





function submitUserGroup(){



	userList ='';
	$(".loginID").each(function(id) {
	      userList += $(this).val() + ":::";
	});


	if (validateUserGroup() === true) {
		$('#submitUserGroupForm').attr("disabled", "disabled");
		  $.ajax({
			 type:       "POST",
			 url:        "ajax_processing.php?action=submitUserGroup",
			 cache:      false,
			 data:       { userGroupID: $("#editUserGroupID").val(), groupName: $("#groupName").val(), emailAddress: $("#emailAddress").val(), usersList: userList  },
			 success:    function(html) {
				if (html){
					$("#span_errors").html(html);
					$("#submitUserGroupForm").removeAttr("disabled");
				}else{
					myDialogPOST();
					window.parent.updateWorkflowTable();
					return false;
				}
			 }


		 });
	}
}




//kill all binds done by jquery live
function kill(){

	$('.addUser').die('click');
	$('.changeDefault').die('blur');
	$('.changeDefault').die('focus');
	$('.changeInput').die('blur');
	$('.changeInput').die('focus');
	$('.select').die('blur');
	$('.select').die('focus');
	$('.remove').die('click');

}
