/*
**************************************************************************************************************************
** CORAL Organizations Module v. 1.0
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
    //updates the qualifier and terms tool use fields from expression type
		updateQualifier();
		updateSearch();
});


var orderBy = "TRIM(LEADING 'THE ' FROM UPPER(L.shortName)) asc";
var pageStart = '1';
var numberOfRecords = 25;
var startWith = '';

function updateSearch(){
      $("#div_feedback").html("<img src='images/circle.gif'> " + _("Processing..."));


      $.ajax({
         type:       "GET",
         url:        "ajax_htmldata.php",
         cache:      false,
         data:       "action=getSearchLicenses&organizationID=" + $("#organizationID").val() + "&consortiumID=" + $("#consortiumID").val() + "&shortName=" + $("#searchName").val() + "&statusID=" + $("#statusID").val() + "&documentTypeID=" + $("#documentTypeID").val() + "&expressionTypeID=" + $("#expressionTypeID").val() + "&qualifierID=" + $("#qualifierID").val() + "&orderBy=" + orderBy + "&pageStart=" + pageStart + "&numberOfRecords=" + numberOfRecords + "&startWith=" + startWith,
         success:    function(html) {
         	$("#div_feedback").html("");
         	$('#searchResults').html(html);
         }


     });

}


function setOrder(column, direction){
 	orderBy = column + " " + direction;
 	updateSearch();
}


function setPageStart(pageStartNumber){
 	pageStart=pageStartNumber;
 	updateSearch();
}


function setNumberOfRecords(numberOfRecordsNumber){
	pageStart = '1';
 	numberOfRecords=$("#numberOfRecords").val();
 	updateSearch();
}

function setStartWith(startWithLetter){
  	//first, set the previous selected letter (if any) to the regular class
  	if (startWith != ''){
  		$("#span_letter_" + startWith).removeClass('searchLetterSelected').addClass('searchLetter');
  	}

  	//next, set the new start with letter to show selected
  	$("#span_letter_" + startWithLetter).removeClass('searchLetter').addClass('searchLetterSelected');

  	pageStart = '1';
	startWith=startWithLetter;
	updateSearch();
}

$(".searchButton").click(function () {
	pageStart = '1';
 	updateSearch();
});



$(".newSearch").click(function () {
  	//reset fields
 	$("#searchName").val("");
 	$("#organizationID").val("");
 	$("#consortiumID").val("");
 	$("#statusID").val("");
 	$("#documentTypeID").val("");
 	$("#expressionTypeID").val("");
 	$("#qualifierID").val("");

	updateQualifier();

  	//reset startwith background color
  	$("#span_letter_" + startWith).removeClass('searchLetterSelected').addClass('searchLetter');
  	startWith='';
	orderBy = "TRIM(LEADING 'THE ' FROM UPPER(L.shortName)) asc";
	pageStart = '1';

 	updateSearch();
});

$("#expressionTypeID").change(function () {
	$('#qualifierID').val('');
	updateQualifier();
	updateSearch();
});




function updateQualifier(){
      //first update qualifier
      $.ajax({
         type:       "GET",
         url:        "ajax_htmldata.php",
         cache:      false,
         data:       "action=getQualifierDropdownHTML&expressionTypeID=" + $("#expressionTypeID").val() + "&page=index" + "&reset=" + $("#reset").val(),
         success:    function(html) {
         	if (html != ''){
         		$("#tr_Qualifiers").show();
         		$("#div_Qualifiers").html(html);
         	}else{
         		$("#tr_Qualifiers").hide();
         		$("#div_Qualifiers").html("<input type='hidden' id='qualifierID' value='' />");
         	}
         }


     });


}


