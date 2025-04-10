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


 $(document).ready(function(){

      updateUserList();
      updateForm('Organization');
      updateForm('Consortium');
      updateForm('DocumentType');
      updateForm('DocumentNoteType');
      updateExpressionTypeList();
      updateForm('SignatureType');
      updateForm('Status');
      updateQualifierList();



 });




 function updateForm(tableName){

       $.ajax({
          type:       "GET",
          url:        "ajax_htmldata.php",
          cache:      false,
          data:       "action=getAdminList&tableName=" + tableName,
          success:    function(html) { $('#div_' + tableName).html(html);
          }
      });




 }


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



  function updateExpressionTypeList(){

        $.ajax({
           type:       "GET",
           url:        "ajax_htmldata.php",
           cache:      false,
           data:       "action=getExpressionTypeList",
           success:    function(html) { $('#div_ExpressionType').html(html);
           }
       });

  }



   function updateQualifierList(){

         $.ajax({
            type:       "GET",
            url:        "ajax_htmldata.php",
            cache:      false,
            data:       "action=getQualifierList",
            success:    function(html) { $('#div_Qualifier').html(html);
            }
        });

  }

 function addData(tableName){

       if ($('#new' + tableName).val()) {
       		$('#span_' + tableName + "_response").html("<img src='images/circle.gif'>&nbsp;&nbsp;" + _("Processing..."));
			  $.ajax({
				 type:       "POST",
				 url:        "ajax_processing.php?action=checkForDuplicates",
				 cache:      false,
				 data:       { shortName: $('#new' + tableName).val(), newType: tableName },
				 success:    function(data) {
								if (data == "1") {
							       $.ajax({
								  type:       "POST",
								  url:        "ajax_processing.php?action=addData",
								  cache:      false,
								  data:       { tableName: tableName, shortName: $('#new' + tableName).val() },
								  success:    function(html) {
								  $('#span_' + tableName + "_response").html(html);

								  // close the span in 3 secs
								  setTimeout("emptyResponse('" + tableName + "');",3000);

								  showAdd(tableName);
								  updateForm(tableName);

								  }
							      });
								} else {
									var displayName = tableName;
									if (tableName == 'Consortium') {
										displayName = 'Category';
									}
									$('#span_' + tableName + "_response").html(_("That ") + displayName + _(" is already in use."));
								}
							}
				});
	}

 }

function updateData(tableName, updateID){
    if(validateUpdateData() === true){
        $.ajax({
            type:       "POST",
            url:        "ajax_processing.php?action=updateData",
            cache:      false,
            data:       { tableName: tableName, updateID: updateID, shortName: $('#updateVal').val() },
            success:    function(html) {
                updateForm(tableName);
                myCloseDialog();
            }
        });
    }
}

// Validate updateData
function validateUpdateData(){
    if($("#updateVal").val() == ''){
        $("#span_errors").html('Error - Please enter a value');
        $("#updateVal").focus();
        return false;
    }else{
        return true;
    }
}

function submitUserData(orgLoginID){
    if(validateUserForm() === true){
        $.ajax({
            type:       "POST",
            url:        "ajax_processing.php?action=submitUserData",
            cache:      false,
            data:       { orgLoginID: orgLoginID, loginID: $('#loginID').val(), firstName: $('#firstName').val(), lastName: $('#lastName').val(), privilegeID: $('#privilegeID').val(), emailAddressForTermsTool: $('#emailAddressForTermsTool').val() },
            success:    function(html) {
                updateUserList();
                myCloseDialog();
            }
        });
    }
}

// Validate user form
function validateUserForm() {
    if($("#loginID").val() == ''){
        $("#span_errors").html('Error - Please add a Login ID for the user');
        $("#loginID").focus();
        return false;
    }else{
        return true;
    }
}

 function submitExpressionType(){
	$.ajax({
          type:       "POST",
          url:        "ajax_processing.php?action=submitExpressionType",
          cache:      false,
          data:       { expressionTypeID: $('#expressionTypeID').val(), shortName: $('#shortName').val(), noteType: $('#noteType').val() },
          success:    function(html) {
          updateExpressionTypeList();
          myCloseDialog();
          }
       });

 }





 function submitQualifier(){
 	$("#submitQualifier").attr("disabled","disabled");
	$.ajax({
          type:       "POST",
          url:        "ajax_processing.php?action=submitQualifier",
          cache:      false,
          data:       { qualifierID: $('#qualifierID').val(), shortName: $('#shortName').val(), expressionTypeID: $('#expressionTypeID').val() },
          success:    function(html) {
          updateQualifierList();
          myCloseDialog();
          }
       });

 }

 function deleteData(tableName, deleteID){

 	if (confirm(_("Do you really want to delete this data?")) == true) {

	       $('#span_' + tableName + "_response").html("<img src = 'images/circle.gif'>&nbsp;&nbsp;" + _("Processing..."));
	       $.ajax({
		  type:       "GET",
		  url:        "ajax_processing.php",
		  cache:      false,
		  data:       "action=deleteData&tableName=" + tableName + "&deleteID=" + deleteID,
		  success:    function(html) {
		  $('#span_' + tableName + "_response").html(html);

		  // close the span in 3 secs
		  setTimeout("emptyResponse('" + tableName + "');",5000);

		  updateForm(tableName);
		  }
	      });

	}
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



 function deleteExpressionType(deleteID){

 	if (confirm(_("Do you really want to delete this expression type?  Any associated Qualifiers will be deleted as well.")) == true) {

	       $("#span_ExpressionType_response").html("<img src='images/circle.gif'>&nbsp;&nbsp;" + _("Processing..."));
	       $.ajax({
		  type:       "GET",
		  url:        "ajax_processing.php",
		  cache:      false,
		  data:       "action=deleteExpressionType&expressionTypeID=" + deleteID,
		  success:    function(html) {
		  $("#span_ExpressionType_response").html(html);

		  // close the span in 5 secs
		  setTimeout("emptyResponse('ExpressionType');",5000);

		  updateExpressionTypeList();
		  updateQualifierList();
		  }
	      });

	}
 }



 function deleteQualifier(deleteID){

 	if (confirm(_("Do you really want to delete this data?")) == true) {

	       $("#span_Qualifier_response").html("<img src = 'images/circle.gif'>&nbsp;&nbsp;" + _("Processing..."));
	       $.ajax({
		  type:       "GET",
		  url:        "ajax_processing.php",
		  cache:      false,
		  data:       "action=deleteData&tableName=Qualifier&deleteID=" + deleteID,
		  success:    function(html) {
		  $("#span_Qualifier_response").html(html);

		  // close the span in 5 secs
		  setTimeout("emptyResponse('Qualifier');",5000);

		  updateQualifierList();
		  }
	      });

	}
 }


function showAdd(tableName, label){
       $('#span_new' + tableName).html("<input type='text' name='new" + tableName + "' id='new" + tableName + "' class='adminAddInput' aria-label='"+label+"' />  <button type='button' class='btn' onclick='addData(\"" + tableName + "\");'>"+_("add") + "</button>");

       //attach enter key event to new input and call add data when hit
       $('#new' + tableName).keyup(function(e) {

               if(e.keyCode == 13) {
               	   addData(tableName);
               }
        });

}


function emptyResponse(tableName){
	$('#span_' + tableName + "_response").html("");
}

