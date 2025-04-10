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

$( document ).ready(function() {
    $('.date-pick').datePicker({startDate:'01/01/2025'});
		$('.date-pick').attr('placeholder', Date.format);
    $("#upload_button").change(uploadFile);
});

$(function(){

	canSubmit=1;

	//check this name to make sure it isn't already being used
	$("#shortName").keyup(function(e) {
		  $.ajax({
			 type:       "GET",
			 url:        "ajax_processing.php",
			 cache:      false,
			 async:	     true,
			 data:       "action=getExistingDocumentName&shortName=" + $("#shortName").val() + "&documentID=" + $("#documentID").val(),
			 success:    function(exists) {
				if (exists == "0"){
					$("#span_error_shortName").html("&nbsp;");
					$("#submitDocument").removeAttr("disabled");
					canSubmit=1;
				}else{
				  	$("#span_error_shortName").html(_("This name is already being used!"));
				  	$("#submitDocument").attr("disabled","disabled");
				  	canSubmit=0;

				}
			 }
		  });


                 if(e.keyCode == 13) {
                 	if(canSubmit == 1){
                 		doSubmitDocument();
                 	}
                 }

	});


    $("#parentDocumentID")
	.mouseover(function(){
	    //if($.browser.msie){
	//	    var cssObj = {
	//	      'width' : 'auto',
	//	      'position' : 'absolute',
	//	      'top' : '120px'
	//	    }
       
        //    $(this).css(cssObj);
	//   }
	})

	.change(function(){
	  //  if($.browser.msie){
	//	    var cssObj = {
	//	      'width' : '185px',
	//	      'position' : ''
	//	    }
       //	$(this).css(cssObj);
	//    }
	})






});


var fileName = $("#upload_button").val();
var exists = '';

//verify filename isn't already used
function checkUploadDocument (file){
	$("#div_file_message").html("");
	 $.ajax({
		 type:       "POST",
		 url:        "ajax_processing.php?action=checkUploadDocument",
		 cache:      false,
		 async:      false,
		 data:       { uploadDocument: file },
		 success:    function(response) {
			if (response == "1"){
				exists = "1";
				$("#div_file_message").html("  <span class='error'>"+_("File name is already being used...")+"</span>");
				return false;
			}else if (response == "2"){
				exists = "2";
				$("#div_file_message").html("  <span class='error'>"+_("File name may not contain special characters - ampersand, single quote, double quote or less than/greater than characters")+"</span>");
				return false;
			} else if (response == "3"){
				exists = "3";
				$("#div_file_message").html("  <span class='error'>"+_("The documents directory is not writable.")+"</span>");
				return false;
			}else{
				exists = "";
			}
		 }


	});
}

function uploadFile() {
    var file_data = $('#upload_button').prop('files')[0];
    var file_name = $('input[type=file]').val().replace(/.*(\/|\\)/, '');
    if (!file_name) { return false; }
    checkUploadDocument(file_name);
    if (exists) { return false; }
    var form_data = new FormData();
    form_data.append('myfile', file_data);
    $.ajax({
        url: 'ajax_processing.php?action=uploadDocument',
        type: 'POST',
        dataType: 'text',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        success: function(result) {
            $("#div_file_message").html("<img src='images/paperclip.gif'>" + _("%s successfully uploaded.", file_name));
            $("#div_uploadFile").html("<br />");
            fileName = file_name;
        },
        error: function(result) {
            $("#div_file_message").html("<span class='error'>" +  _("The file upload failed for the following reason: ") + result.status + " " + result.statusText + " / " + $(result.responseText).text() + "</span>");
        }
    });
}


//submit document to be entered into db
$("#submitDocument").click(function () {
	//prevent double-adding
	$("#submitDocument").attr("disabled","disabled");
	doSubmitDocument();
});

function doSubmitDocument(){
  if (validateForm() === true) {
	  $.ajax({
		 type:       "POST",
		 url:        "ajax_processing.php?action=submitDocument",
		 cache:      false,
		 data:       { effectiveDate: $("#effectiveDate").val(), documentTypeID: $("#documentTypeID").val(), parentDocumentID: $("#parentDocumentID").val(), shortName: $("#shortName").val(), uploadDocument: fileName, archiveInd: getCheckboxValue('archiveInd'), licenseID: $("#licenseID").val(), documentID: $("#documentID").val() },
		 success:    function(html) {
			if (html){
				$("#span_errors").html(html);
			}else{
				window.parent.updateDocuments();
				window.parent.updateArchivedDocuments();
                myCloseDialog();
				return false;
			}
		 }
	 });

  }
}

//validates fields
function validateForm (){

	myReturn=0;
	if (!validateRequired('documentTypeID',_("Document Type is required."))) myReturn="1";
	if (!validateRequired('shortName',_("Short Name is required."))) myReturn="1";

	if (myReturn == "1"){
		return false;
	}else{
		return true;
	}
}


function newDocumentType(){
  $('#span_newDocumentType').html("<input type='text' name='newDocumentType' id='newDocumentType' aria-label='"+_('New document type')+"' />  <button type='button' class='btn' onclick='addDocumentType();'>"+_("add")+"</button>");

         //attach enter key event to new input and call add data when hit
         $('#newDocumentType').keyup(function(e) {

                 if(e.keyCode == 13) {
                 	   addDocumentType();
                 }
        });
}


function addDocumentType(){
  //add documentType to db and returns updated select box
  $.ajax({
	 type:       "POST",
	 url:        "ajax_processing.php?action=addDocumentType",
	 cache:      false,
	 data:       { shortName: $("#newDocumentType").val() },
	 success:    function(html) { $('#span_documentType').html(html); $('#span_newDocumentType').html("<span class='error'>"+_("DocumentType has been added")+"</span>"); }
 });
}
