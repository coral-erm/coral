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


// NOTE: similar to licensing/license.js

$(document).ready(function(){
   showTabPanel('#div_displayDocuments');
	updateLicenseHead();
	updateAttachmentsNumber();
});

 viewAll=0;
 displayArchiveInd=2;
 showParentDocumentID='';
 showExpressionDocumentID='';
 var parentOrderBy = "DT.shortName asc, D.effectiveDate desc, max(signatureDate) desc, D.shortName asc";
 var childOrderBy = "parentDocumentID, expirationDate, DT.shortName asc, D.effectiveDate desc, max(signatureDate) desc, D.shortName asc";
 var parentArchivedOrderBy = "expirationDate, DT.shortName asc, D.effectiveDate desc, max(signatureDate) desc, D.shortName asc";
 var childArchivedOrderBy = "parentDocumentID, expirationDate, expirationDate, DT.shortName asc, D.effectiveDate desc, max(signatureDate) desc, D.shortName asc";

 function showTabPanel(panelID) {
   $('#side .nav li a').removeAttr('aria-current');
   $('#side .nav li a[href*="'+panelID+'"]').attr('aria-current', 'page');

   if (viewAll == "0") {
      $('.tabpanel').hide();
		$(panelID).show();
      var panelName = panelID.replace('#div_display', '');
      switch (panelName) {
         case 'Documents':
            updateDocuments();
            updateArchivedDocuments();
            updateDocTitle(_('Documents'));
            break;
         case 'Expressions':
            updateExpressions();
            updateDocTitle(_('Expressions'));
            break;
         case 'SFXProviders':
            updateSFXProviders();
            updateDocTitle(_('Terms Tool'));
            break;
         case 'Attachments':
            updateAttachmentsNumber();
      	   updateAttachments();
            updateDocTitle(_('Attachments'));
            break;
         default: 
            break;
      }
	}
	return false;
 }

 function updateDocTitle(newTitle) {
	document.title = newTitle + ' - ' + baseTitle;
}

 function deleteLicense(licenseID){
    if (confirm(_("Do you really want to delete this document?")) == true) {
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
          }


      });

 }

 function updateDocuments(showChildrenDocumentID){
       if (typeof(showChildrenDocumentID) != 'undefined'){
       	  showParentDocumentID=showChildrenDocumentID;
       }

       $.ajax({
          type:       "GET",
          url:        "ajax_htmldata.php",
          cache:      false,
          data:       "action=getAllDocuments&licenseID=" + $("#licenseID").val() + "&showChildrenDocumentID=" + showParentDocumentID + "&parentOrderBy=" + parentOrderBy + "&childOrderBy=" + childOrderBy,
          success:    function(html) {
          	$('#div_documents').html(html);
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
          success:    function(html) { $('#div_archives').html(html);
          }
      });
	  updateNotes();
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
          }


      });

 }

 function updateNotes(){


       $.ajax({
          type:       "GET",
          url:        "ajax_htmldata.php",
          cache:      false,
          data:       "action=getAllNotes&licenseID=" + $("#licenseID").val(),
          success:    function(html) { $('#div_notes').html(html);
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
			$(".span_AttachmentNumber").html("(" + remaining + " " + _("record") + ")");
		}else{
			$(".span_AttachmentNumber").html("(" + remaining + " " + _("records") + ")");
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
    if (confirm(_("Do you really want to delete this document?")) == true) {
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

 function deleteNote(documentNoteID){
    if (confirm(_("Do you really want to delete this note?")) == true) {
       $.ajax({
          type:       "GET",
          url:        "ajax_processing.php",
          cache:      false,
          data:       "action=deleteNote&documentNoteID=" + documentNoteID,
          success:    function(html) { updateNotes(); }


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

function showFullNoteText(noteID){
	$('#note_short_' + noteID).hide();
	$('#note_full_' + noteID).show();

}

function hideFullNoteText(noteID){
	$('#note_full_' + noteID).hide();
	$('#note_short_' + noteID).show();

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
 				$("#div_file_message").html("  <span class='error'>" + _("File name is already being used.") + "</span>");
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

    $("#upload_button").change(uploadFile);
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
