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

	$(".addUser").on('click', function () {
		let selectElement = $('select#loginSelect');
		let selectedOption = $(selectElement).find(":selected");
		let loginID = selectedOption.val();
		let selectedUserName = selectedOption.text();
		let invalidLoginSelected = ((loginID == '') || (loginID == null));
		if (invalidLoginSelected){
			$('#div_errorUser').html(_("Error - User is required"));
			return false;
		}else{
			$('#div_errorUser').html('');
			selectedOption.attr('disabled', true);
			selectElement.val('');
			let newRow = $('#newUserSkeleton').clone();
				newRow.addClass('validUser');
			let dataElement = $('#newUserDisplayName', newRow);
				dataElement.val(selectedUserName);
				dataElement.attr("data-loginid", loginID);
				dataElement.removeAttr('id');
			$(".remove", newRow).on('click', function() {
				removeUser($(this));
			});
			newRow.removeAttr('id');
			newRow.removeAttr('hidden');
			newRow.appendTo('.userTable');
			checkNoUsers();
			return false;
		}
	});

	$(".remove").on('click', function() {
		removeUser($(this));
	});
 });

function removeUser(btn) {
	btn.closest('.newUser').fadeTo(400, 0, function () {
		let dataInput = $(this).find('input[name="loginIDs[]"]');
		let loginID = dataInput.data('loginid');
		let selectOption = $('select#loginSelect').find('option[value="'+loginID+'"]');
		selectOption.attr('disabled', false);
		$(this).remove();
		checkNoUsers();
	});
	return false;
}

function checkNoUsers(){
	let userList = $('.userTable').find('.validUser');
	let noUsersLeft = (userList.length == 0);
	$('#noUsers').attr('hidden', !noUsersLeft);
}

function validateUserGroup(){
 	myReturn=0;
 	if (!validateRequired('groupName',"<br />"+_("Group name must be entered to continue.")+"<br />")) 
		myReturn="1";
 	if (myReturn == "1"){
		return false;
 	}else{
 		return true;
 	}
}

function submitUserGroup(){
	let selectedUserList = $('input[name="loginIDs[]"]').not('#newUserDisplayName');
	let userList = [];
	selectedUserList.each(function(){
		userList.push($(this).data('loginid'));
	})

	if (validateUserGroup() === true) {
		$('#submitUserGroupForm').attr("disabled", "disabled");
		  $.ajax({
			 type:       "POST",
			 url:        "ajax_processing.php?action=submitUserGroup",
			 cache:      false,
			 data:       { 
				userGroupID: $("#userGroupID").val(), 
				groupName: $("#groupName").val(), 
				emailAddress: $("#emailAddress").val(), 
				usersList: userList  
			},
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
	$('.remove').die('click');
}
