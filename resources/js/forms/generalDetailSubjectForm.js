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
	$(".adddetailedSubject").on('click', function () {

		var detailedSubjectID = $('.newdetailedSubjectTable .detailedSubjectID').val();

		if ((detailedSubjectID == '') || (detailedSubjectID == null)){
			$('#div_errordetailedSubject').html(_("Error - Detailed Subject is required"));
			return false;

		}else{
			$('#div_errordetailedSubject').html('');

			//first copy the new subject being added
			var originalTR = $('.newdetailedSubjectTR').clone();

			// Remove all options that are not selected.  This will make the drop down added
			// Just one value.  This avoids having to deal with change.
			$('.changeSelect option:not(:selected)').each(function () {
				$(this).remove();
			});

			var detailID = $('.changeSelect option').val();
			var firstFlag = true;

			$('.changeSelect option').each(function () {

				if (($(this).val() == detailID) && (!firstFlag)) {
					$('#div_errordetailedSubject').html(_("Error - Detail Subject Already Added"));
					$('#div_errordetailedSubject').html(_("Error - Detail Subject Already Added"));
					$('.newdetailedSubjectTR').remove();

					//next put the original clone back, we just need to reset the values
					originalTR.appendTo('.newdetailedSubjectTable');
					$('.newdetailedSubjectTable .detailedSubjectID').val('');

					detailID = -1;
					return false;
				} else {
					firstFlag = false;
				}

			});

			// If we have a duplicate then exit the add function
			if (detailID == -1) {
				return false;
			}

			//next append to to the existing table
			//it's too confusing to chain all of the children.
			$('.newdetailedSubjectTR').appendTo('.detailedSubjectTable');

			$('.newdetailedSubjectTR .actions .adddetailedSubject').replaceWith("<img src='images/cross.gif' class='remove' alt='" + _("remove detailed subject") + "' title='" + _("remove detailed subject") + "'/>");
			$('.detailedSubjectID').addClass('changeSelect');
			$('.detailedSubjectID').addClass('idleField');
			$('.detailedSubjectID').css("background-color","");


			$('.adddetailedSubject').removeClass('adddetailedSubject');
			$('.newdetailedSubjectTR').removeClass('newdetailedSubjectTR');

			//next put the original clone back, we just need to reset the values
			originalTR.appendTo('.newdetailedSubjectTable');
			$('.newdetailedSubjectTable .detailedSubjectID').val('');


			return false;
		}
	});





	$(".remove").on('click', function () {
	    $(this).closest('tr').fadeTo(400, 0, function () {
				$(this).remove();
	    });
	    return false;
	});
 });


 function validateDetailSubject(){
 	myReturn=0;
 	if (!validateRequired('shortName',"<br />"+_("Short Name must be entered to continue.")+"<br />")) myReturn="1";


 	if (myReturn == "1"){
		return false;
 	}else{
 		return true;
 	}
}





function submitDetailSubject(){

	detailSubjectList ='';
	$(".detailedSubjectID").each(function(id) {
	      detailSubjectList += $(this).val() + ":::";
	});

	if (validateDetailSubject() === true) {
		$('#submitDetailSubjectForm').attr("disabled", "disabled");
		  $.ajax({
			 type:       "POST",
			 url:        "ajax_processing.php?action=submitDetailSubject",
			 cache:      false,
			 data:       { generalSubjectID: $("#editgeneralSubjectID").val(), shortName: $("#shortName").val(), detailSubjectsList: detailSubjectList  },
			 success:    function(html) {
				if (html){
					$("#span_errors").html(html);
					$("#submitDetailSubjectForm").removeAttr("disabled");
				}else{
					myDialogPOST();
					window.parent.updateSubjectsTable();
					return false;
				}
			 }


		 });
	}



}



//kill all binds done by jquery live
function kill(){
	$('.adddetailedSubject').die('click');
	$('.remove').die('click');
}
