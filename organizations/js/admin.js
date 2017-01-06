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

      updateUserForm();
      updateForm('OrganizationRole');
      updateForm('ContactRole');
      updateForm('AliasType');
      updateForm('ExternalLoginType');
      updateForm('IssueLogType');
      
 });
 

 function updateUserForm(){

       $.ajax({
          type:       "GET",
          url:        "ajax_forms.php",
          cache:      false,
          data:       "action=getAdminUserForm",
          success:    function(html) { $('#div_User').html(html);
          	tb_reinit();
          }
      });
            
 }


 function submitUserData(orgloginID){
	$.ajax({
          type:       "GET",
          url:        "ajax_processing.php",
          cache:      false,
          data:       "action=submitUserData&orgloginID=" + orgloginID + "&loginID=" + $('#loginID').val() + "&firstName=" + $('#firstName').val() + "&lastName=" + $('#lastName').val() + "&privilegeID=" + $('#privilegeID').val(),
          success:    function(html) { 
          updateUserForm();
          window.parent.tb_remove();
          }
       });

 }



 function updateForm(className){

       $.ajax({
          type:       "GET",
          url:        "ajax_forms.php",
          cache:      false,
          data:       "action=getAdminDisplay&className=" + className,
          success:    function(html) { $('#div_' + className).html(html);
          	tb_reinit();
          }
      });
      
      


 }

 function addData(className){

       if ($('#new' + className).val()) {
       	       $('#span_' + className + "_response").html("<img src = 'images/circle.gif'>&nbsp;&nbsp;"+_("Processing..."));
       	       
	       $.ajax({
		  type:       "GET",
		  url:        "ajax_processing.php",
		  cache:      false,
		  data:       "action=addData&className=" + className + "&shortName=" + encodeURIComponent($('#new' + className).val()),
		  success:    function(html) { 
		  $('#span_' + className + "_response").html(html);  

		  // close the span in 3 secs
		  setTimeout("emptyResponse('" + className + "');",3000); 
		  
		  showAdd(className);
		  updateForm(className); 
		  
		  }
	      });
	}

 }

function updateData(className, updateId){
    if(validateUpdateData() === true){
        $.ajax({
            type:       "GET",
            url:        "ajax_processing.php",
            cache:      false,
            data:       "action=updateData&className=" + className + "&updateId=" + updateId + "&shortName=" + $('#updateVal').val(),
            success:    function(html) { 
                updateForm(className);
                window.parent.tb_remove();
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

 function deleteData(className, deleteId){
 
 	if (confirm(_("Do you really want to delete this data?")) == true) {

	       $('#span_' + className + "_response").html("<img src = 'images/circle.gif'>&nbsp;&nbsp;"+_("Processing..."));
	       $.ajax({
		  type:       "GET",
		  url:        "ajax_processing.php",
		  cache:      false,
		  data:       "action=deleteInstance&class=" + className + "&id=" + deleteId,
		  success:    function(html) { 
		  $('#span_' + className + "_response").html(html);  

		  // close the span in 3 secs
		  setTimeout("emptyResponse('" + className + "');",3000); 

		  updateForm(className);  
		  tb_reinit();
		  }
	      });

	}
 }
 


 function deleteUser(deleteId){
 
 	if (confirm(_("Do you really want to delete this user?")) == true) {

	       $('#span_User_response').html("<img src = 'images/circle.gif'>&nbsp;&nbsp;"+_("Processing..."));
	       $.ajax({
		  type:       "GET",
		  url:        "ajax_processing.php",
		  cache:      false,
		  data:       "action=deleteInstance&class=User&id=" + deleteId,
		  success:    function(html) { 
		  $('#span_User_response').html(html);  

		  // close the span in 3 secs
		  setTimeout("emptyResponse('User');",3000); 

		  updateUserForm();  
		  tb_reinit();
		  }
	      });

	}
 } 
 
 
function showAdd(className){
       $('#span_new' + className).html("<input type='text' name='new" + className + "' id='new" + className + "' class='adminAddInput' />  <a href='javascript:addData(\"" + className + "\");'>"+_("add")+"</a>");

       //attach enter key event to new input and call add data when hit
       $('#new' + className).keyup(function(e) {
      
               if(e.keyCode == 13) {
               	   addData(className);
               }
        });

}


function emptyResponse(className){
	$('#span_' + className + "_response").html("");
}


function updateOrderGroups(){
       $('#span_CancelReason_response').html("<img src = 'images/circle.gif'>&nbsp;&nbsp;"+_("Processing..."));
       
       
       $.ajax({
	  type:       "GET",
	  url:        "ajax_proxy.php",
	  cache:      false,
	  data:       "action=getOrderGroups",
	  success:    function(html) { 
	  $('#span_CancelReason_response').html('');  

	  // close the span in 3 secs
	  setTimeout("emptyResponse('CancelReason');",3000); 

	  updateForm('CancelReason'); 

	  }
	  
	});

}
