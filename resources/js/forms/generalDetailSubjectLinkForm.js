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



 $(".generalDetailSubjectLink").click(function () {
	  updateResourceSubjectTable($(this).attr("resourceID"), $(this).attr("generalSubjectID"), $(this).attr("detailedSubjectID"));
 });



function updateResourceSubjectTable(resourceID, generalSubjectID, detailedSubjectID){

	$.ajax({
		 type:       "POST",
		 url:        "ajax_processing.php?action=updateResourceSubject",
		 cache:      false,
		 data:       { className: $("#editClassName").val(), shortName: $("#updateVal").val(), updateID: $("#editUpdateID").val()},
		 success:    function(html) {
			if (html){
				$("#span_errors").html(html);
				$("#submitDetailedSubjectForm").removeAttr("disabled");
			}else{
				myDialogPOST();
				window.parent.updateProduct();
				return false;
			}
		 }


	 });

}



//kill all binds done by jquery live
function kill(){

	$('.generalDetailSubjectLink').die('click');
	$('.changeDefault').die('blur');
	$('.changeDefault').die('focus');
	$('.changeInput').die('blur');
	$('.changeInput').die('focus');
	$('.select').die('blur');
	$('.select').die('focus');
	$('.remove').die('click');

}
