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

 $(function(){



	//bind all of the inputs

	 $("#submitLicense").click(function () {
		submitLicenseForm();
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


	$('.changeAutocomplete').on('focus', function() {
		if (this.value == this.defaultValue){
			this.value = '';
		}

	 });


	 $('.changeAutocomplete').on('blur', function() {
		if(this.value == ''){
			this.value = this.defaultValue;
		}
	 });




 });




function submitLicenseForm(){


	$('#submitLicense').attr("disabled", "disabled");
	  $.ajax({
		 type:       "POST",
		 url:        "ajax_processing.php?action=submitLicenseUpdate",
		 cache:      false,
		 data:       { resourceID: $("#editResourceID").val(), licenseStatusID: $("#licenseStatusID").val()  },
		 success:    function(html) {
			if (html){
				$("#span_errors").html(html);
				$("#submitLicense").removeAttr("disabled");
			}else{
				myDialogPOST();
				window.parent.updateAcquisitions();
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
	$('.remove').die('click');

}
