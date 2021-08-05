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

	//perform search if enter is hit
	$('#shortName').keyup(function(e) {
	      if(e.keyCode == 13) {
		submitSFXForm();
	      }
	});


});



$("#submitSFX").click(function () {
	submitSFXForm();
});

function submitSFXForm(){
	if (validateForm() === true) {
	  $.ajax({
		 type:       "POST",
		 url:        "ajax_processing.php?action=submitSFXProvider",
		 cache:      false,
		 data:       { providerID: $("#sfxProviderID").val(), documentID: $("#documentID").val(), shortName: $("#shortName").val() },
		 success:    function(html) {
			if (html){
				$("#span_errors").html(html);
			}else{
				//window.parent.tb_remove();
				window.parent.updateSFXProviders();
				return false;
			}
		 }
	 });
	}
}

function validateForm (){
	myReturn=0;
	if (!validateRequired('documentID',"<br />"+_("A document must be selected to continue."))) myReturn="1";
	if (!validateRequired('shortName',"<br />"+_("Terms Tool Resource must be entered to continue."))) myReturn="1";


	if (myReturn == "1"){
		return false;
	}else{
		return true;
	}
}
