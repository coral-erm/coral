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


	 $("#updatePlatformForm").click(function () {
	 	updatePlatform();
	 });


	//do submit if enter is hit
	$('#platformName').keyup(function(e) {
	      if(e.keyCode == 13) {
		updatePlatform();
	      }
	});


 });


function updatePlatform(){


  if (validateForm() === true) {
	  $('#span_error_Platform').html('');

	  $.ajax({
		 type:       "POST",
		 url:        "ajax_processing.php?action=updatePlatform",
		 cache:      false,
		 data:       { platformName: $("#platformName").val(), platformID: $('#platformID').val() },
		 success:    function(platformID) {
			window.parent.tb_remove();
			window.location  = 'publisherPlatform.php?platformID=' + $('#platformID').val();
			return false;
		 }


	 });

   }

}



//validates fields
function validateForm (){
	myReturn=0;
	if ($("#platformName").val() == ""){
		$('#span_error_Platform').html("<br />" + _("Platform must be entered."));
		myReturn=1;
	}


	if (myReturn == "1"){
		return false;
	}else{
		return true;
	}
}
