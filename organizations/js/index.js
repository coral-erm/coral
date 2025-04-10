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

        updateSearch();

	//perform search if enter is hit
	$('#searchOrganizationName').keyup(function(e) {
	      if(e.keyCode == 13) {
		updateSearch();
	      }
	});

	//perform search if enter is hit
	$('#searchContactName').keyup(function(e) {
	      if(e.keyCode == 13) {
		updateSearch();
	      }
	});

 });


var orderBy = "TRIM(LEADING 'THE ' FROM UPPER(O.name)) asc";
var pageStart = '1';
var numberOfRecords = 25;
var startWith = '';

function updateSearch(){
      $("#div_feedback").html("<img src='images/circle.gif'>  <span style='font-size:90%'>"+_("Processing...")+"</span>");


      $.ajax({
         type:       "GET",
         url:        "ajax_htmldata.php",
         cache:      false,
         data:       "action=getSearchOrganizations&organizationName=" + $("#searchOrganizationName").val() + "&organizationRoleID=" + $("#searchOrganizationRoleID").val() + "&contactName=" + $("#searchContactName").val() + "&orderBy=" + orderBy + "&pageStart=" + pageStart + "&numberOfRecords=" + numberOfRecords + "&startWith=" + startWith,
         success:    function(html) {
         	$("#div_feedback").html("");
         	$('#div_searchResults').html(html);
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
  	$("#searchOrganizationName").val("");
  	$("#searchOrganizationRoleID").val("");
  	$("#searchContactName").val("");

  	//reset startwith background color
  	$("#span_letter_" + startWith).removeClass('searchLetterSelected').addClass('searchLetter');
  	startWith='';

	orderBy = "TRIM(LEADING 'THE ' FROM UPPER(O.name)) asc";
	pageStart = '1';
  	updateSearch();
  });


  $("#searchOrganizationName").focus(function () {
  	$("#div_searchName").css({'display':'block'});
  });

  $("#searchContactName").focus(function () {
  	$("#div_searchContact").css({'display':'block'});
  });
