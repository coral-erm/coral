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


	 $("#submitDocumentNoteForm").click(function () {
		submitDocumentNote();
	 });
         
         
         $(".removeNote").unbind('click').click(function () {
          $documentID=$("#editDocumentID").val();
	  if (confirm(_("Do you really want to delete this note?")) == true) {
		  $.ajax({
			 type:       "GET",
			 url:        "ajax_processing.php",
			 cache:      false,
			 data:       "action=deleteDocumentNote&documentNoteID=" + $(this).attr("id"),
			 success:    function(html) {
				//eval("update" + "();");
                                                            
                                updateDocumentNotes($documentID);
                                
			 }
		 });
	  }
   });



 });

function updateDocumentNotes($documentID){

  $.ajax({
	 type:       "GET",
	 url:        "ajax_forms.php",
	 cache:      false,
	 data:       "action=getDocumentNotes&documentID="+ $documentID,
	 success:    function(html) {
                 //update div #div_noteForm with new content
                $( '#div_noteForm').replaceWith(html);
                tb_reinit();//reregister a.thickbox element found in AJAX updated div #div_noteForm
	 }


  });
  }


 function validateForm (){
 	myReturn=0;
 	if (!validateRequired('noteText',"<br />"+_("Note must be entered to continue."))) myReturn="1";


 	if (myReturn == "1"){
		return false;
 	}else{
 		return true;
 	}
}



function submitDocumentNote(){
                $documentID=$("#editDocumentID").val();
		if (validateForm() === true) {
			$('#submitDocumentNoteForm').attr("disabled", "disabled");
			  $.ajax({
				 type:       "POST",
				 url:        "ajax_processing.php?action=submitDocumentNote",
				 cache:      false,
				 data:       { documentNoteID: $("#editDocumentNoteID").val(), noteTypeID: $("#noteTypeID").val(), noteText: $("#noteText").val(), documentID: $documentID },
				 success:    function(html) {
					if (html){
						$("#span_errors").html(html);
						$("#submitDocumentNoteForm").removeAttr("disabled");
					}else{
						 
                                                updateDocumentNotes($documentID);
					}
				 }


			 });

		}

}
