/*
**************************************************************************************************************************
** CORAL Usage Statistics Module
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


	 $("#submitIdentifierForm").click(function () {
	 	submitIdentifier();
	 });


	//do submit if enter is hit
	$('#Identifier').keyup(function(e) {
	      if(e.keyCode == 13) {
		submitIdentifier();
	      }
	});


 });






function submitIdentifier(){


  if (validateForm() === true) {
	  $('#span_' + $("#titleID").val() + '_feedback').html('');

	  $.ajax({
		 type:       "POST",
		 url:        "ajax_processing.php?action=addIdentifier",
		 cache:      false,
		 data:       { identifier: $("#identifier").val(), identifierType: $("#identifierType").val(), titleID: $("#titleID").val() },
		 success:    function(html) {
			myCloseDialog();
			window.parent.updateTitleDetails($("#titleID").val());
			return false;
		 }


	 });

   }

}



//validates fields
function validateForm (){
	myReturn=0;
	if (!isIdentifier($("#identifier").val())){
		$('#span_error_Identifier').html("<br />"+_("Identifier must be valid format."));
		myReturn=1;
	}


	if (myReturn == "1"){
		return false;
	}else{
		return true;
	}
}
