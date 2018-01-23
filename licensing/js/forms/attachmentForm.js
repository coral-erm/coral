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
	//$('.date-pick').datePicker({startDate:'01/01/1996'});//disable old datePicker
});




var fileName = $("#upload_attachment_button").val();
var exists = '';
var URLArray = [];

function checkUploadAttachment (file, extension){
	$("#div_file_message").html("");
	 $.ajax({
		 type:       "POST",
		 url:        "ajax_processing.php?action=checkUploadAttachment",
		 cache:      false,
		 async: 	 false,
		 data:       { uploadAttachment: file },
		 success:    function(response) {
					if (response == "1"){
						$("#div_file_message").html("  <font color='red'>"+_("File name is already being used...")+"</font>");
						exists=1;
						return false;
    			} else if (response == "3"){
    				exists = "3";
    				$("#div_file_message").html("  <font color='red'>"+_("The attachments directory is not writable.")+"</font>");
    				return false;
          }


					//check if it's already been uploaded in current array
					//note: using indexOf prototype in common.js for IE
					 if (URLArray.indexOf(file) >= 0){
						$("#div_file_message").html("  <font color='red'>"+_("File name is already being used...")+"</font>");
						exists=1;
						return false;
					 }


					 exists='';
					 return true;
		 }


	});
}

new AjaxUpload('upload_attachment_button',
	{action: 'ajax_processing.php?action=uploadAttachment',
			name: 'myfile',
			onChange : function (file, extension){checkUploadAttachment(file, extension);},
			onComplete : function(data,response){
				fileName=data;
                                
                                if (exists == "1"){
 						$("#div_file_message").html("  <font color='red'>"+_("File name is already being used.")+"</font>");
 					}else{
 						arrayLocation = URLArray.length;
  					URLArray.push(fileName);

  					$("#div_file_success").append("<div id='div_" + arrayLocation + "'><img src='images/paperclip.gif'>" + fileName + _(" successfully uploaded.")+"  <a class='smallLink' href='javascript:removeFile(\"" + arrayLocation + "\");'>"+_("remove")+"</a><br /></div>");

 					}

				
	}
});










function removeFile(arrayLocation){
	if (confirm(_("Do you really want to delete this attachment?")) == true) {
		//URLArray.splice(URLArray.indexOf(value), 1);
		URLArray.splice(arrayLocation, 1);
		$("#div_" + arrayLocation).remove();
	}
}

function removeExistingAttachment(attachmentFileID){
	if (confirm(_("Do you really want to delete this attachment?")) == true) {
		$.get("ajax_processing.php?action=deleteAttachmentFile&attachmentFileID=" + attachmentFileID,
			function(data){
			$("#div_existing_" + attachmentFileID).remove();
		});
	}
}

$("#submitAttachment").click(function () {

  $.ajax({
	 type:       "POST",
	 url:        "ajax_processing.php?action=submitAttachment",
	 cache:      false,
	 async:      false,
	 data:       { attachmentID: $("#attachmentID").val(), licenseID: $("#licenseID").val(),sentDate: $("#sentDate").val(), attachmentText: $("#attachmentText").val()  } ,
	 success:    function(html) {
		if (isNaN(html)){
			$("#span_errors").html(html);
		}else{
			elID=$("#attachmentID").val();

			//returns attachment log id to insert
			if (elID == '') elID = html;

			//now insert files
			jQuery.each(URLArray, function() {
				$.get("ajax_processing.php?action=addAttachmentFile&attachmentID=" + elID + "&attachmentURL=" + this ,
					function(data){});
			});


			window.parent.tb_remove();
			window.parent.updateAttachments();
			return false;
		}
	 }
   });
   return false;
});



