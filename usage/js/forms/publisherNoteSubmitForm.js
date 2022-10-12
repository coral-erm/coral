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


	 $("#submitPublisherNoteForm").click(function () {
	 	submitPublisherNote();
	 });



	//do submit if enter is hit
	$('#startYear').keyup(function(e) {
	      if(e.keyCode == 13) {
		submitPublisherNote();
	      }
	});


	//do submit if enter is hit
	$('#endYear').keyup(function(e) {
	      if(e.keyCode == 13) {
		submitPublisherNote();
	      }
	});



 });






function submitPublisherNote(){


	errorMessage='';
	if (isYear($("#startYear").val()) == false) { errorMessage = "<br />" + _("Start Year must be 4 numbers and start with 19 or 20.") + "\n"; }
	if ((isYear($("#endYear").val()) == false) && ($("#endYear").val() != '')) { errorMessage += "<br />" + _("End Year must be 4 numbers and start with 19 or 20.") + "\n"; }

	$('#span_errors').html(errorMessage);

	if (errorMessage) {
		return;
	}




	$('#submitPublisherNoteForm').attr("disabled", "disabled");
	  $.ajax({
		 type:       "POST",
		 url:        "ajax_processing.php?action=submitPublisherNote",
		 cache:      false,
		 data:       { publisherPlatformNoteID: $("#editPublisherPlatformNoteID").val(), publisherPlatformID: $("#publisherPlatformID").val(), startYear: $("#startYear").val(), endYear: $("#endYear").val(), noteText: $("#noteText").val() },
		 success:    function(html) {
				myCloseDialog();
				return false;
		 }


	 });

}
