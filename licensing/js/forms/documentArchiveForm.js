/*
**************************************************************************************************************************
** CORAL Licensing Module v. 1.0
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
	$('.date-pick').datePicker({startDate:'01/01/2025'});
	$('.date-pick').attr('placeholder', Date.format);
});



$("#submitArchive").click(function () {
  $.ajax({
	 type:       "GET",
	 url:        "ajax_processing.php",
	 cache:      false,
	 data:       "action=archiveDocument&expirationDate=" + $("#expirationDate").val() + "&documentID=" + $("#documentID").val(),
	 success:    function(html) {
		if (html){
			$("#span_errors").html(html);
		}else{
			window.parent.updateDocuments();
			window.parent.updateExpressions();
            myCloseDialog();
			return false;
		}
	 }
   });


});


