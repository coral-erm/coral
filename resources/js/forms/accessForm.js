/*
**************************************************************************************************************************
** CORAL Resources Module v. 1.2
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


	 $("#submitAccessChanges").click(function () {
		submitAccess();
	 });


	//do submit if enter is hit
	$('#authenticationUserName').keyup(function(e) {
	      if(e.keyCode == 13) {
		submitAccess();
	      }
	});


	//do submit if enter is hit
	$('#authenticationPassword').keyup(function(e) {
	      if(e.keyCode == 13) {
		submitAccess();
	      }
	});

	//do submit if enter is hit
	$('#coverageText').keyup(function(e) {
	      if(e.keyCode == 13) {
		submitAccess();
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



function submitAccess(){

	administeringSitesList ='';
	$(".check_administeringSite:checked").each(function(id) {
	      administeringSitesList += $(this).val() + ":::";
	});


	authorizedSitesList ='';
	$(".check_authorizedSite:checked").each(function(id) {
	      authorizedSitesList += $(this).val() + ":::";
	});
	
	
	$('#submitAccessChanges').attr("disabled", "disabled");
	  $.ajax({
		 type:       "POST",
		 url:        "ajax_processing.php?action=submitAccess",
		 cache:      false,
		 data:       { resourceID: $("#editResourceID").val(), resourceAcquisitionID: $("#editResourceAcquisitionID").val(), authenticationTypeID: $("#authenticationTypeID").val(), accessMethodID: $("#accessMethodID").val(), coverageText: $("#coverageText").val(), authenticationUserName: $("#authenticationUserName").val(), authenticationPassword: $("#authenticationPassword").val(), storageLocationID: $("#storageLocationID").val(), userLimitID: $("#userLimitID").val(), administeringSites: administeringSitesList, authorizedSites: authorizedSitesList },
		 success:    function(html) {
			if (html){
				$("#span_errors").html(html);
				$("#submitAccessChanges").removeAttr("disabled");
			}else{
				myDialogPOST();
				window.parent.updateAccess();
				window.parent.updateRightPanel();
				return false;
			}
		 }


	 });

}


//kill all binds done by jquery live
function kill(){

	$('.changeDefault').die('blur');
	$('.changeDefault').die('focus');
	$('.changeInput').die('blur');
	$('.changeInput').die('focus');
	$('.select').die('blur');
	$('.select').die('focus');

}
