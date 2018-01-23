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


$(document).ready(function(){

	updateLicenseHead();
	updateDocuments();
	updateArchivedDocuments();
	updateExpressions();
	updateSFXProviders();
	updateAttachmentsNumber();
      	updateAttachments();
  updateRightPanel();


	$('#div_displayDocuments').show();
	$('#div_displayExpressions').hide();
	$('#div_displaySFXProviders').hide();
	$('#div_displayAttachments').hide();


});



 viewAll=0;
 displayArchiveInd=2;
 showParentDocumentID='';
 showExpressionDocumentID='';
 var parentOrderBy = "DT.shortName asc, D.effectiveDate desc, max(signatureDate) desc, D.shortName asc";
 var childOrderBy = "parentDocumentID, expirationDate, DT.shortName asc, D.effectiveDate desc, max(signatureDate) desc, D.shortName asc";
 var parentArchivedOrderBy = "expirationDate, DT.shortName asc, D.effectiveDate desc, max(signatureDate) desc, D.shortName asc";
 var childArchivedOrderBy = "parentDocumentID, expirationDate, expirationDate, DT.shortName asc, D.effectiveDate desc, max(signatureDate) desc, D.shortName asc";



 $(".showDocuments").click(function () {
 	if (viewAll == "0"){
		$('#div_displayDocuments').show();
		$('#div_displayExpressions').hide();
		$('#div_displaySFXProviders').hide();
		$('#div_displayAttachments').hide();
	}
	return false;
 });



  $(".showExpressions").click(function () {
 	updateExpressions('');

  	if (viewAll == "0"){
 		$('#div_displayDocuments').hide();
 		$('#div_displayExpressions').show();
 		$('#div_displaySFXProviders').hide();
 		$('#div_displayAttachments').hide();
 	}

 	return false;
 });



  $(".showSFXProviders").click(function () {
  	if (viewAll == "0"){
 		$('#div_displayDocuments').hide();
 		$('#div_displayExpressions').hide();
 		$('#div_displaySFXProviders').show();
 		$('#div_displayAttachments').hide();
 	}
 	return false;
 });



 $(".showAttachments").click(function () {
 	if (viewAll == "0"){
		$('#div_displayDocuments').hide();
		$('#div_displayExpressions').hide();
		$('#div_displaySFXProviders').hide();
		$('#div_displayAttachments').show();
	}
	return false;
 });


 function deleteLicense(licenseID){
    if (confirm(_("Do you really want to delete this license?")) == true) {
       $.ajax({
          type:       "GET",
          url:        "ajax_processing.php",
          cache:      false,
          data:       "action=deleteLicense&licenseID=" + licenseID,
          success:    function(html) {
          	 //post return message to index
 		postwith('index.php',{message:html});
          }


      });

    }

 }


 function updateLicenseHead(){

       $.ajax({
          type:       "GET",
          url:        "ajax_htmldata.php",
          cache:      false,
          data:       "action=getLicenseHead&licenseID=" + $("#licenseID").val(),
          success:    function(html) { $('#div_licenseHead').html(html);
		tb_reinit();
          }


      });

 }

 function updateDocuments(showChildrenDocumentID){
       if (typeof(showChildrenDocumentID) != 'undefined'){
       	  showParentDocumentID=showChildrenDocumentID;
       }
       //alert(parentOrderBy);
       //console.log("action=getAllDocuments&licenseID=" + $("#licenseID").val() + "&showChildrenDocumentID=" + showParentDocumentID + "&parentOrderBy=" + parentOrderBy + "&childOrderBy=" + childOrderBy);
        $.ajax({
          type:       "GET",
          url:        "ajax_htmldata.php",
          cache:      false,
          data:       "action=getAllDocuments&licenseID=" + $("#licenseID").val() + "&showChildrenDocumentID=" + showParentDocumentID + "&parentOrderBy=" + parentOrderBy + "&childOrderBy=" + childOrderBy,
          success:    function(html) {
          	$('#div_documents').html(html);
                //alert($('.tree').size());
                //console.log($('.tree').size());
                 $('.tree').treegrid(
                 {
                  'initialState': 'collapsed',
                  'saveState': true,
                 });
          	tb_reinit();
          }


      });

 }

function updateRightPanel(){
  $("#div_rightPanel").append("<img src='images/circle.gif' />  "+_("Refreshing Contents..."));
  $.ajax({
   type:       "GET",
   url:        "ajax_htmldata.php",
   cache:      false,
   data:       "action=getRightPanel&licenseID=" + $("#licenseID").val(),
   success:    function(html) {
    $("#div_rightPanel").html(html + "&nbsp;");

   }


  });

}

 function updateArchivedDocuments(showDisplayArchiveInd, showChildrenDocumentID){

       if ((typeof(showDisplayArchiveInd) != 'undefined') && (showDisplayArchiveInd != '')){
       	  displayArchiveInd=showDisplayArchiveInd;
       }

       if (typeof(showChildrenDocumentID) != 'undefined'){
       	  showParentDocumentID=showChildrenDocumentID;
       }

       $.ajax({
          type:       "GET",
          url:        "ajax_htmldata.php",
          cache:      false,
          data:       "action=getAllDocuments&licenseID=" + $("#licenseID").val() + "&displayArchiveInd=" + displayArchiveInd + "&showChildrenDocumentID=" + showParentDocumentID + "&parentArchivedOrderBy=" + parentArchivedOrderBy + "&childArchivedOrderBy=" + childArchivedOrderBy,
          success:    function(html) {
               $('#div_archives').html(html);
               $('.tree_a').treegrid(
                 {
                  'initialState': 'collapsed',
                  'saveState': true,
                 });
          	tb_reinit();
          }


      });

 }


 function showExpressionForDocument(expressionDocumentID){
  	if (viewAll == "0"){
 		$('#div_displayDocuments').hide();
 		$('#div_displayExpressions').show();
 		$('#div_displaySFXProviders').hide();
 		$('#div_displayAttachments').hide();
 	}

 	updateExpressions(expressionDocumentID);
 }




 function updateExpressions(expressionDocumentID){
       if (typeof(expressionDocumentID) != 'undefined'){
       	  showExpressionDocumentID=expressionDocumentID;
       }


       $.ajax({
          type:       "GET",
          url:        "ajax_htmldata.php",
          cache:      false,
          data:       "action=getAllExpressions&licenseID=" + $("#licenseID").val() + "&documentID=" + showExpressionDocumentID,
          success:    function(html) { $('#div_expressions').html(html);
          	tb_reinit();
          }


      });

 }

 function updateSFXProviders(){


       $.ajax({
          type:       "GET",
          url:        "ajax_htmldata.php",
          cache:      false,
          data:       "action=getAllSFXProviders&licenseID=" + $("#licenseID").val(),
          success:    function(html) { $('#div_sfxProviders').html(html);
          	tb_reinit();
          }


      });

 }


 function updateAttachments(){


       $.ajax({
          type:       "GET",
          url:        "ajax_htmldata.php",
          cache:      false,
          data:       "action=getAllAttachments&licenseID=" + $("#licenseID").val(),
          success:    function(html) { $('#div_attachments').html(html);
          	updateAttachmentsNumber();
          	tb_reinit();
          }


      });

 }


function updateAttachmentsNumber(){
  $.ajax({
	 type:       "GET",
	 url:        "ajax_htmldata.php",
	 cache:      false,
	 data:       "action=getAttachmentsNumber&licenseID=" + $("#licenseID").val(),
	 success:    function(remaining) {
	 	if (remaining == "1"){
			$(".span_AttachmentNumber").html("(" + remaining + _(" record)"));
		}else{
			$(".span_AttachmentNumber").html("(" + remaining + _(" records)"));
		}
	 }
 });
}

 function updateStatus(){


       $.ajax({
          type:       "GET",
          url:        "ajax_processing.php",
          cache:      false,
          data:       "action=updateStatus&licenseID=" + $("#licenseID").val() + "&statusID=" + $("#statusID").val(),
          success:    function(html) {
          	$('#span_updateStatusResponse').html(html);

          	  // close the span in 3 secs
		  setTimeout("emptyDiv('span_updateStatusResponse');",3000);
          }


      });

 }

 function emptyDiv(divName){
	$('#' + divName).html("");
 }



 function archiveDocument(documentID){
    if (confirm(_("Do you really want to archive this document?")) == true) {
	  $.ajax({
		 type:       "GET",
		 url:        "ajax_processing.php",
		 cache:      false,
		 data:       "action=archiveDocument&documentID=" + documentID,
		 success:    function(html) {
			updateDocuments();
			updateArchivedDocuments();
			updateExpressions();
		 }
	 });
    }

 }


 function deleteDocument(documentID){
    if (confirm(_("Do you really want to delete this document? Please note: All child documents associated with this one will be also deleted!")) == true) {
       $.ajax({
          type:       "GET",
          url:        "ajax_processing.php",
          cache:      false,
          data:       "action=deleteDocument&documentID=" + documentID,
          success:    function(html) {
          	if (html) alert(_("There was a problem with deleting the document.  You may not delete a document if there are associated expressions.  Remove all expressions and try again."));
          	updateDocuments();
          	updateArchivedDocuments();
          }


      });

    }

 }


 function deleteExpression(expressionID){
    if (confirm(_("Do you really want to delete this expression?")) == true) {
       $.ajax({
          type:       "GET",
          url:        "ajax_processing.php",
          cache:      false,
          data:       "action=deleteExpression&expressionID=" + expressionID,
          success:    function(html) { updateExpressions(); }
      });

    }

 }


 function deleteAttachment(attachmentID){
    if (confirm(_("Do you really want to delete this attachment?  This will also delete all attached files.")) == true) {
       $.ajax({
          type:       "GET",
          url:        "ajax_processing.php",
          cache:      false,
          data:       "action=deleteAttachment&attachmentID=" + attachmentID,
          success:    function(html) { updateAttachments(); }


      });

    }

 }


 function deleteSFXProvider(sfxProviderID){
    if (confirm(_("Do you really want to delete this terms tool resource link?")) == true) {
       $.ajax({
          type:       "GET",
          url:        "ajax_processing.php",
          cache:      false,
          data:       "action=deleteSFXProvider&sfxProviderID=" + sfxProviderID,
          success:    function(html) { updateSFXProviders(); }


      });

    }

 }


function showFullAttachmentText(attachmentID){
	$('#attachment_short_' + attachmentID).hide();
	$('#attachment_full_' + attachmentID).show();

}

function hideFullAttachmentText(attachmentID){
	$('#attachment_full_' + attachmentID).hide();
	$('#attachment_short_' + attachmentID).show();

}

 var exists = '';

 function checkUploadDocument (file, extension){

 	 $.ajax({
 		 type:       "GET",
 		 url:        "ajax_processing.php",
 		 cache:      false,
 		 data:       "action=checkUploadDocument&uploadDocument=" + file,
 		 success:    function(response) {
 			if (response == "1"){
 				exists = "1";
 				$("#div_file_message").html("  <font color='red'>"+_("File name is already being used.")+"</font>");
 				return false;
 			}else{
 				$("#div_file_message").html("");
 				exists = "0";
 			}
 		 }


 });
 }




 function replaceFile(){
 	fileName = $("#upload_button").val();
 	//used for the Document Edit form - defaults to show current uploaded file with an option to replace
 	//replace html contents with browse for uploading document.
 	$('#div_uploadFile').html("<div id='uploadFile'><input type='file' name='upload_button' id='upload_button'></div>");

 	//also reinitialize the code for uploading the file
 	new AjaxUpload('upload_button',
 		{action: 'ajax_processing.php?action=uploadDocument',
 				name: 'myfile',
 				onChange : function (file, extension){checkUploadDocument(file, extension);},
 				onComplete : function(data){
 					fileName=data;

 					if (exists == "1"){
 						$("#div_file_message").html("  <font color='red'>"+_("File name is already being used.")+"</font>");
 					}else{
 						$("#div_uploadFile").html("<img src='images/paperclip.gif'>" + fileName + _(" successfully uploaded."));

 					}

 			}
 		});

 }





 function changeProdUse(expressionID){
       $.ajax({
          type:       "GET",
          url:        "ajax_processing.php",
          cache:      false,
          data:       "action=setProdUse&expressionID=" + expressionID + "&licenseID=" + $("#licenseID").val() + "&productionUseInd=" + getCheckboxValue("productionUseInd_" + expressionID),
          success:    function(html) {
          	$("#span_prod_use_" + expressionID).html(html);

          	  // close the span in 3 secs
		  setTimeout("emptyDiv('span_prod_use_" + expressionID + "');",3000);
          }


      });
 }




 function setParentOrder(column, direction){
  	parentOrderBy = column + " " + direction;
  	updateDocuments();
 }


  function setChildOrder(column, direction){
   	childOrderBy = column + " " + direction;
   	updateDocuments();
  }


  function setParentArchivedOrder(column, direction){
   	parentArchivedOrderBy = column + " " + direction;
   	updateArchivedDocuments();
  }


  function setChildArchivedOrder(column, direction){
   	childArchivedOrderBy = column + " " + direction;
   	updateArchivedDocuments();
  }
