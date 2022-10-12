/*
**************************************************************************************************************************
** CORAL Management Module v. 1.0
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


$("#submitNote").click(function () {

  $.ajax({
	 type:       "POST",
	 url:        "ajax_processing.php?action=submitNote",
	 cache:      false,
	 async:      false,
	 data:       { documentNoteID: $("#documentNoteID").val(), licenseID: $("#licenseID").val(), body: $("#notebody").val(), documentNoteTypeID: $("#documentNoteTypeID").val(), documentID: $("#documentID").val() } ,
	 success:    function(html) {
		myCloseDialog();
		window.parent.updateNotes();
		return false;
	 }
   });
   return false;
});

function newNoteType(){
  $('#span_newNoteType').html("<input type='text' name='newNoteType' id='newNoteType' class='licenseAddInput' />  <a href='javascript:addNoteType();'>"+_("add")+"</a>");

	 //attach enter key event to new input and call add data when hit
	 $('#span_newNoteType').keyup(function(e) {

			 if(e.keyCode == 13) {
				   addDocumentType();
			 }
	 });
}

function addNoteType(){
  //check for duplicates
  $.ajax({
	 type:       "POST",
	 url:        "ajax_processing.php?action=checkForDuplicates",
	 cache:      false,
	 data:       { shortName: $("#newNoteType").val(), newType: "DocumentNoteType" },
	 success:    function(data) {
					if (data == "1") {
					  $.ajax({
						 type:       "GET",
						 url:        "ajax_processing.php",
						 cache:      false,
						 data:       "action=addNoteType&shortName=" + $("#newNoteType").val(),
						 success:    function(html) { $('#span_noteType').html(html); $('#span_newNoteType').html("<font color='red'>" + _("Note Type has been added") + "</font>"); }
						});
					} else {
						alert(_("That Note Type is already in use."));
					}
	 			}
 	});
}


