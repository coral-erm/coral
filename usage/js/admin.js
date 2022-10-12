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


 $(document).ready(function(){

      updateUserList();
      updateLogEmailAddressTable();
      updateOutlierTable();


 });




 function updateUserList(){

       $.ajax({
          type:       "GET",
          url:        "ajax_htmldata.php",
          cache:      false,
          data:       "action=getAdminUserList",
          success:    function(html) { $('#div_User').html(html);
          }
      });

 }


 function submitUserData(orgLoginID){
	$.ajax({
          type:       "GET",
          url:        "ajax_processing.php",
          cache:      false,
          data:       "action=submitUserData&orgLoginID=" + orgLoginID + "&loginID=" + $('#loginID').val() + "&firstName=" + $('#firstName').val() + "&lastName=" + $('#lastName').val() + "&privilegeID=" + $('#privilegeID').val() + "&emailAddressForTermsTool=" + $('#emailAddressForTermsTool').val(),
          success:    function(html) {
          updateUserList();
          myCloseDialog();
          }
       });

 }

 function deleteUser(loginID){

 	if (confirm(_("Do you really want to delete this user?")) == true) {

	       $('#span_User_response').html("<img src = 'images/circle.gif'>&nbsp;&nbsp;" + _("Processing..."));
	       $.ajax({
		  type:       "GET",
		  url:        "ajax_processing.php",
		  cache:      false,
		  data:       "action=deleteUser&loginID=" + loginID,
		  success:    function(html) {
		  $('#span_User_response').html(html);

		  // close the span in 5 secs
		  setTimeout("emptyResponse('User');",5000);

		  updateUserList();
		  }
	      });

	}
 }



 function updateLogEmailAddressTable(){

       $.ajax({
          type:       "GET",
          url:        "ajax_htmldata.php",
          cache:      false,
          data:       "action=getLogEmailAddressTable",
          success:    function(html) {
          	$('#div_emailAddresses').html(html);
          }
      });

 }



function doSubmitLogEmailAddress(){
    if(validateLogEmail() === true){
        $.ajax({
            type:       "GET",
            url:        "ajax_processing.php",
            cache:      false,
            data:       "action=submitLogEmailAddress&logEmailAddressID=" + $('#updateLogEmailAddressID').val() + "&emailAddress=" + encodeURIComponent($('#emailAddress').val()),
            success:    function(html) {
                updateLogEmailAddressTable();
                myCloseDialog();
            }
        });
    }
}

// Validate Log Email Address
function validateLogEmail(){
    if($("#emailAddress").val() == ''){
        $("#span_errors").html(_('Error - Please enter a value.'));
        $("#emailAddress").focus();
        return false;
    }else if(!/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z0-9]+$/.test( $("#emailAddress").val() )){
        $("#span_errors").html(_('Error - Please enter a valid email address.'));
        $("#emailAddress").focus();
        return false;
    }else{
        return true;
    }
}

  function deleteLogEmailAddress(addressID){

     if (confirm(_("Do you really want to delete this email address?")) == true) {
	$.ajax({
          type:       "GET",
          url:        "ajax_processing.php",
          cache:      false,
          data:       "action=deleteLogEmailAddress&logEmailAddressID=" + addressID,
          success:    function(html) {
		  updateLogEmailAddressTable();
          }
       });
     }

  }




 function updateOutlierTable(){

       $.ajax({
          type:       "GET",
          url:        "ajax_htmldata.php",
          cache:      false,
          data:       "action=getOutlierTable",
          success:    function(html) {
          	$('#div_outliers').html(html);
          }
      });

 }



 function emptyResponse(tableName){
 	$('#span_' + tableName + "_response").html("");
 }




  function updateOutlier(){

	  if (validateForm() === true) {
		$.ajax({
		  type:       "GET",
		  url:        "ajax_processing.php",
		  cache:      false,
		  data:       "action=updateOutlier&outlierID=" + $('#updateOutlierID').val() + "&overageCount=" + $('#overageCount').val() + "&overagePercent=" + $('#overagePercent').val(),
		  success:    function(html) {
			  updateOutlierTable();
			  myCloseDialog();
		  }
	       });

	  }

 }



 //validates fields for outlier form
 function validateForm (){
 	myReturn=0;
 	if (!validateNumber('overageCount', _("Count over must be a number."))) myReturn="1";
 	if (!validateNumber('overagePercent', _("% over must be a number."))) myReturn="1";

 	if (myReturn == "1"){
 		return false;
 	}else{
 		return true;
 	}
}


