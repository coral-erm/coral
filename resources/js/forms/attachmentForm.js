/*
**************************************************************************************************************************
** CORAL Resources Module v. 1.0
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

$( document ).ready(function() {
    $("#upload_button").change(uploadFile);
});


 $(function(){



	//bind all of the inputs
	 $("#submitAttachmentForm").click(function () {
		submitAttachment();
	 });

    	$('.changeInput').addClass("idleField");

	$('.changeInput').on('focus', function() {


		$(this).removeClass("idleField").addClass("focusField");

		if(this.value != this.defaultValue){
			this.select();
		}

	 });


	 $('.changeInput').on('blur', function() {
		$(this).removeClass("focusField").addClass("idleField");
	 });




	$('select').addClass("idleField");
	$('select').on('focus', function() {
		$(this).removeClass("idleField").addClass("focusField");

	});

	$('select').on('blur', function() {
		$(this).removeClass("focusField").addClass("idleField");
	});




	$('textarea').addClass("idleField");
	$('textarea').focus(function() {
		$(this).removeClass("idleField").addClass("focusField");
	});

	$('textarea').blur(function() {
		$(this).removeClass("focusField").addClass("idleField");
	});




 });




var fileName = $("#upload_button").val();
var exists = '';

//verify filename isn't already used
function checkUploadAttachment (file){
	$("#div_file_message").html("");
    exists = '';
	$.ajax({
		type:       "GET",
		url:        "ajax_processing.php",
		cache:      false,
		async: 	 false,
		data:       "action=checkUploadAttachment&uploadAttachment=" + file,
		success:    function(response) {
		  exists = "";
			if (response == "1"){
				exists = "1";
				$("#div_file_message").html("  <font color='red'>"+_("File name is already being used...")+"</font>");
				return false;
			} else if (response == "2"){
				exists = "2";
				$("#div_file_message").html("  <font color='red'>"+_("File name may not contain special characters - ampersand, single quote, double quote or less than/greater than characters")+"</font>");
				return false;
			} else if (response == "3"){
				exists = "3";
				$("#div_file_message").html("  <font color='red'>"+_("The attachments directory is not writable.")+"</font>");
				return false;
			}

		}

	});
}

function uploadFile() {
    var file_data = $('#upload_button').prop('files')[0];
    var file_name = $('input[type=file]').val().replace(/.*(\/|\\)/, '');
    if (!file_name) { return false; }
    checkUploadAttachment(file_name);
    if (exists) { return false; }
    var form_data = new FormData();
    form_data.append('myfile', file_data);
    $.ajax({
        url: 'ajax_processing.php?action=uploadAttachment',
        type: 'POST',
        dataType: 'text',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        success: function(result) {
            $("#div_file_message").html("<img src='images/paperclip.gif'>" + file_name + _(" successfully uploaded."));
            fileName = file_name;
        },
        error: function(result) {
            $("#div_file_message").html("<font color='red'>" +  _("The file upload failed for the following reason: ") + result.status + " " + result.statusText + " / " + $(result.responseText).text() + "</font>");
        }
    });
}

function replaceFile(){
    //used for the Attachment Edit form - defaults to show current uploaded file with an option to replace
    //replace html contents with browse for uploading attachment.
    $('#div_uploadFile').html("<div id='uploadFile'><input type='file' name='upload_button' id='upload_button'></div>");

    $("#upload_button").change(uploadFile);
}


 function validateForm (){
 	myReturn=0;
 	if (!validateRequired('shortName',"<br />"+_("Name must be entered to continue."))) myReturn="1";
 	if (!validateRequired('attachmentTypeID',"<br />"+_("Attachment Type must be selected to continue."))) myReturn="1";


 	if (myReturn == "1"){
		return false;
 	}else{
 		return true;
 	}
}






function submitAttachment(){

	if (fileName == ''){
		$("#div_file_message").html(_("A file must be uploaded"));
	}else{
		if (validateForm() === true) {
			$('#submitAttachment').attr("disabled", "disabled");
			  $.ajax({
				 type:       "POST",
				 url:        "ajax_processing.php?action=submitAttachment",
				 cache:      false,
				 data:       { resourceAcquisitionID: $("#editResourceAcquisitionID").val(), attachmentID: $("#editAttachmentID").val(), shortName: $("#shortName").val(), uploadDocument: fileName, descriptionText: $("#descriptionText").val(), attachmentTypeID: $("#attachmentTypeID").val()  },
				 success:    function(html) {
					if (html){
						$("#span_errors").html(html);
						$("#submitAttachment").removeAttr("disabled");
					}else{
						myCloseDialog();
						window.parent.updateAttachments();
						window.parent.updateAttachmentsNumber();
						return false;
					}

				 }


			 });

		}
	}

}


//kill all binds done by jquery live
function kill(){

	$('.changeInput').die('blur');
	$('.changeInput').die('focus');
	$('.select').die('blur');
	$('.select').die('focus');

}
