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


	//do submit if enter is hit
	$('#licenseStatusID').keyup(function(e) {
	      if(e.keyCode == 13) {
		submitLicenseForm();
	      }
	});


	 $(".licenseName").autocomplete('ajax_processing.php?action=getLicenseList', {
		minChars: 2,
		max: 20,
		mustMatch: false,
		width: 265,
		delay: 10,
		matchContains: true,
		formatItem: function(row) {
			return "<span style='font-size: 80%;'>" + row[0] + "</span>";
		},
		formatResult: function(row) {
			return row[0].replace(/(<.+?>)/gi, '');
		}

	  });


	//once something has been selected, change the hidden input value
	$(".licenseName").result(function(event, data, formatted) {
		$(this).parent().children('.licenseID').val(data[1]);
		$('#div_errorLicense').html('');
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




	$(".remove").on('click', function () {
	    $(this).parent().parent().parent().fadeTo(400, 0, function () {
		$(this).remove();
	    });
	    return false;
	});




	$(".addLicense").on('click', function () {
		var lID = $('.newLicenseTable').children().children().children().children('.licenseID').val();

		if ((lID == '') || (lID == null)){
			$('#div_errorLicense').html(_("Error - Please choose a valid license"));
			return false;

		}else{
			$('#div_errorLicense').html('');

			//first copy the new license being added
			var originalTR = $('.newLicenseTR').clone();

			//next append to to the existing table
			//it's too confusing to chain all of the children.
			$('.newLicenseTR').appendTo('.licenseTable');

			$('.newLicenseTR').children().children().children('.addLicense').replaceWith("<img src='images/cross.gif' class='remove' alt='" + _("remove this license") + "' title='" + _("remove this license") + "'/>");
			$('.licenseRoleID').addClass('changeSelect');
			$('.licenseRoleID').addClass('idleField');
			$('.licenseRoleID').css("background-color","");
			$('.licenseName').addClass('changeInput').removeClass('changeAutocomplete');
			$('.licenseName').addClass('idleField');
			$('.licenseName').css("background-color","");

			$('.addLicense').removeClass('addLicense');
			$('.newLicenseTR').removeClass('newLicenseTR');



			//next put the original clone back, we just need to reset the values
			originalTR.appendTo('.newLicenseTable');
			$('.newLicenseTable').children().children().children().children('.licenseName').val('');
			$('.newLicenseTable').children().children().children().children('.licenseID').val('');


			//put autocomplete back
			$('.newLicenseTable').children().children().children().children('.licenseName').autocomplete('ajax_processing.php?action=getLicenseList', {
				minChars: 2,
				max: 20,
				mustMatch: false,
				width: 265,
				delay: 10,
				matchContains: true,
				formatItem: function(row) {
					return "<span style='font-size: 80%;'>" + row[0] + "</span>";
				},
				formatResult: function(row) {
					return row[0].replace(/(<.+?>)/gi, '');
				}

			});


			//once something has been selected, change the hidden input value
			$(".licenseName").result(function(event, data, formatted) {
				$(this).parent().children('.licenseID').val(data[1]);
				$('#div_errorLicense').html('');
			});

			return false;
		}
	});




 });




function submitLicenseForm(){

	licenseList ='';
	$(".licenseID").each(function(id) {
	      licenseList += $(this).val() + ":::";
	});


	if (validateForm() === true) {
		$('#submitLicense').attr("disabled", "disabled");
		  $.ajax({
			 type:       "POST",
			 url:        "ajax_processing.php?action=submitLicenseUpdate",
			 cache:      false,
			 data:       { resourceAcquisitionID: $("#editResourceAcquisitionID").val(), licenseStatusID: $("#licenseStatusID").val(), licenseList: licenseList  },
			 success:    function(html) {
				if (html){
					$("#span_errors").html(html);
					$("#submitLicense").removeAttr("disabled");
				}else{
					myDialogPOST();
				 	window.parent.updateAcquisitions();
					window.parent.updateRightPanel();
					return false;
				}

			 }


		 });
	}


}


function validateForm(){

	var lID = $('.newLicenseTable').children().children().children().children('.licenseID').val();
	var lName = $('.newLicenseTable').children().children().children().children('.licenseName').val();

	if (((lID == '') || (lID == null)) && (lName != '')){
		$('#div_errorLicense').html(_("Error - Please choose a valid license"));
		return false;
	}else{
		return true;
	}

}


//kill all binds done by jquery live
function kill(){

	$('.addLicense').die('click');
	$('.changeDefault').die('blur');
	$('.changeDefault').die('focus');
	$('.changeInput').die('blur');
	$('.changeInput').die('focus');
	$('.select').die('blur');
	$('.select').die('focus');
	$('.remove').die('click');

}
