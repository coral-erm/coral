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


	 $("#submitResourceNoteForm").click(function () {
		submitResourceNote();
	 });



 });




 function validateForm (){
 	myReturn=0;
 	if (!validateRequired('noteText',"<br />"+_("Note must be entered to continue."))) myReturn="1";


 	if (myReturn == "1"){
		return false;
 	}else{
 		return true;
    }
}



function submitResourceNote(){
		if (validateForm() === true) {
			$('#submitResourceNoteForm').attr("disabled", "disabled");
			  $.ajax({
				 type:       "POST",
				 url:        "ajax_processing.php?action=submitResourceNote",
				 cache:      false,
				 data:       { resourceNoteID: $("#editResourceNoteID").val(), noteTypeID: $("#noteTypeID").val(), tabName: $("#tab").val(), noteText: $("#noteText").val(), entityID: $("#editEntityID").val() },
				 success:    function(html) {
					if (html){
						$("#span_errors").html(html);
						$("#submitResourceNoteForm").removeAttr("disabled");
					}else{
						myDialogPOST();
						eval("window.parent.update" + $("#tab").val() + "();");
						return false;
					}			
				 }


			 });

		}

}
