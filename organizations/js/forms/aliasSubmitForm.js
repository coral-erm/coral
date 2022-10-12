/*
**************************************************************************************************************************
** CORAL Organizations Module
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


	 $("#submitAliasForm").click(function () {
		submitAliases();
	 });




	//do submit if enter is hit
	$('#aliasName').keyup(function(e) {
	      if(e.keyCode == 13) {
		submitAliases();
	      }
	});


 });




 function validateForm (){
 	myReturn=0;
 	if (!validateRequired('aliasName',"<br />"+_("Name must be entered to continue."))) myReturn=1;


 	if (myReturn == 1){
		return false;
 	}else{
 		return true;
 	}
}



function submitAliases(){
		if (validateForm() === true) {
			$('#submitAliasForm').attr("disabled", "disabled");
			  $.ajax({
				 type:       "POST",
				 url:        "ajax_processing.php?action=submitAlias",
				 cache:      false,
				 data:       { aliasID: $("#editAliasID").val(), name: $("#aliasName").val(), aliasTypeID: $("#aliasTypeID").val(), organizationID: $("#editOrganizationID").val() },
				 success:    function(html) {
					if (html.length > 1){
						$("#span_errors").html(html);
						$("#submitAliasForm").removeAttr("disabled");
					}else{
						window.parent.updateAliases();
                        myCloseDialog();
						return false;
					}
				 }


			 });

		}

}
