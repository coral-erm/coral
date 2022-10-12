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


 });



function showPublisherList(platformID){
	divID = 'div_' + platformID;

	if (typeof displayInds[divID] == "undefined") displayInds[divID] = 1;

	toggleDivState(divID, displayInds[divID]);

	if (displayInds[divID] == 0) {
		$('#image_' + platformID).attr('src', "images/arrowright.gif");
		$('#link_' + platformID).text(_("show Publisher list"));
		displayInds[divID]=1;
	} else {
		$('#image_' + platformID).attr('src', "images/arrowdown.gif");
		$('#link_' + platformID).text(_("hide Publisher list"));
		displayInds[divID]=0;
	}


}



function updateReportDisplayName(){

    if(validateDisplay() === true){
        if ($('#type').val() == 'platform'){
              updateDisplayPlatform($('#updateID').val());
        }else{
              updateDisplayPublisher($('#updateID').val());
        }
    }
}

function validateDisplay(){
    if($('#reportDisplayName').val() == ''){
        $("#span_errors").html(_('Error - Please enter a value.'));
        $('#reportDisplayName').focus();
        return false;
    }else{
        return true;
    }
}

function updateDisplayPlatform(platformID){

	$.ajax({
          type:       "GET",
          url:        "ajax_processing.php",
          cache:      false,
          data:       "action=updatePlatformDisplay&updateID=" + platformID + "&reportDisplayName=" + escape($('#reportDisplayName').val()),
          success:    function(html) {

		$('#span_platform_' + platformID + '_response').html(html + "<br />");

		// close the span in 3 secs
		setTimeout("emptyResponse('platform_" + platformID + "');",3000);

		updatePlatformDisplay(platformID);
	    myCloseDialog();
          }
       });

}


function updatePlatformDisplay(platformID){
	divID = "div_platform_" + platformID;

	$.ajax({
          type:       "GET",
          url:        "ajax_htmldata.php",
          cache:      false,
          data:       "action=getPlatformReportDisplay&platformID=" + platformID,
          success:    function(html) {
		 $('#' + divID).html(html);
          }
       });


}



function updateDisplayPublisher(publisherPlatformID){

	$.ajax({
          type:       "GET",
          url:        "ajax_processing.php",
          cache:      false,
          data:       "action=updatePublisherDisplay&updateID=" + publisherPlatformID + "&reportDisplayName=" + escape($('#reportDisplayName').val()),
          success:    function(html) {

		updatePublisherDisplay(publisherPlatformID);
              myCloseDialog();
          }
       });

}


function updatePublisherDisplay(publisherPlatformID){
	divID = "div_publisher_" + publisherPlatformID;

	$.ajax({
          type:       "GET",
          url:        "ajax_htmldata.php",
          cache:      false,
          data:       "action=getPublisherReportDisplay&publisherPlatformID=" + publisherPlatformID,
          success:    function(html) {
		 $('#' + divID).html(html);
          }
       });


}



function updatePlatformDropDown(platformID){

	$.ajax({
          type:       "GET",
          url:        "ajax_processing.php",
          cache:      false,
          data:       "action=updatePlatformDropDown&platformID=" + platformID + "&dropDownInd=" + getCheckboxValue('chk_platform_' + platformID),
          success:    function(html) {
		 $('#span_platform_' + platformID + '_response').html(html + "<br />");

		  // close the span in 3 secs
		  setTimeout("emptyResponse('platform_" + platformID + "');",3000);
          }
       });

}



function updatePublisherDropDown(publisherPlatformID){

	$.ajax({
          type:       "GET",
          url:        "ajax_processing.php",
          cache:      false,
          data:       "action=updatePublisherDropDown&publisherPlatformID=" + publisherPlatformID + "&dropDownInd=" + getCheckboxValue('chk_publisher_' + publisherPlatformID),
          success:    function(html) {
		 $('#span_publisher_' + publisherPlatformID + '_response').html(html + "<br />");

		  // close the span in 3 secs
		  setTimeout("emptyResponse('publisher_" + publisherPlatformID + "');",3000);
          }
       });

}






 function emptyResponse(tableName){
 	$('#span_' + tableName + "_response").html("");
 }


