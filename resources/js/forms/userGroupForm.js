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
		var loginID = $('.newUserTR .loginID').val();
		if ((loginID == '') || (loginID == null)){
			$('#div_errorUser').html(_("Error - User is required"));
			return false;
		}else{
			$('#div_errorUser').html('');
			var newRow = $('#newUserSkeleton').clone();
			newRow.removeAttr('id');
			newRow.removeAttr('hidden');
			
			var userOption = $('.loginID option:selected', $(this).closest('.newUserTR'));
			//console.log(userOption);
			if (!userOption.length) {
				return false;
			}

			var userName = userOption.text();
			var groupName = $('#groupName').val();
			/*
			console.log("userName: ", userName);
			console.log("userID: ", userOption.val());
			console.log("groupName: ", groupName);
			/**/
			
			$('#newUserID', newRow).val(userOption.val());
			$('#newUserID', newRow).removeAttr('id');
			$('#newUserDisplayName', newRow).val(userName);
			$('#newUserDisplayName', newRow).removeAttr('id');
			$(".remove", newRow).on('click', function() {
				removeUser($(this));
			});
			newRow.appendTo('.userTable');
			$('#noUsers').attr('hidden', true);
			return false;
		}
	});

	$(".remove").on('click', function() {
		removeUser($(this));
	});
 });

 function removeUser(btn) {
		btn.closest('.newUser').fadeTo(400, 0, function () {
			var userTable = $(this).closest('userTable');
			$(this).remove();
			if ($('.newUser', userTable).length == 0)
				$('#noUsers').removeAttr('hidden');
		});
		return false;
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
	$('.remove').die('click');
}
