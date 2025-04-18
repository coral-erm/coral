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

$(document).ready(function(){

  updateSearch($('#searchPage').val());

	//bind change event to Records Per Page drop down
	$(document).on('change', '#numberRecordsPerPage', function () {
	  setNumberOfRecords($(this).val())
	});


	//bind change event to each of the page start
	$(document).on('click', '.setPage', function () {
		setPageStart($(this).attr('id'));
	});
/*
	$('#resourceSearchForm select').change(function() {
	  updateSearch();
	});
/***/
	$('#resourceSearchForm').submit(function() {
	  updateSearch();
	  return false;
	});

	$(".searchButton").click(function() {
	  $('#resourceSearchForm').submit();
	  return false;
	})
 });

function updateSearch(pageNumber) {
	$("#div_feedback").html("<img src='images/circle.gif'>  <span style='font-size:90%'>"+_("Processing...")+"</span>");
	if (!pageNumber) {
		pageNumber = 1;
	}
	$('#searchPage').val(pageNumber);

	var form = $('#resourceSearchForm');
	$.post(
		form.attr('action'),
		form.serialize(),
		function(html) {
			$("#div_feedback").html("");
			$("#div_searchResults").html(html);
		}
	);

	window.scrollTo(0, 0);
}


function setOrder(column, direction){
  if(column == 'R.titleText'){
   $('#searchOrderBy').val("TRIM(LEADING 'THE ' FROM (TRIM(LEADING 'EL ' FROM (TRIM(LEADING 'L\\\'' FROM (TRIM(LEADING 'LA ' FROM (TRIM(LEADING 'LE ' FROM (TRIM(LEADING 'LES ' FROM (TRIM(LEADING 'DER ' FROM (TRIM(LEADING 'DIE ' FROM (TRIM(LEADING 'DAS ' FROM UPPER(R.titleText)))))))))))))))))) " + direction);
  }else{
    $("#searchOrderBy").val(column + " " +direction + ", TRIM(LEADING 'THE ' FROM (TRIM(LEADING 'EL ' FROM (TRIM(LEADING 'L\\\'' FROM (TRIM(LEADING 'LA ' FROM (TRIM(LEADING 'LE ' FROM (TRIM(LEADING 'LES ' FROM (TRIM(LEADING 'DER ' FROM (TRIM(LEADING 'DIE ' FROM (TRIM(LEADING 'DAS ' FROM UPPER(R.titleText)))))))))))))))))) asc");
  }
  updateSearch();
}


function setPageStart(pageStartNumber){
  updateSearch(pageStartNumber);
}


function setNumberOfRecords(recordsPerPageNumber){
  $("#searchRecordsPerPage").val(recordsPerPageNumber);
  updateSearch();
}




  function setStartWith(startWithLetter){
    //first, set the previous selected letter (if any) to the regular class
  	$("span.searchLetterSelected").removeClass('searchLetterSelected').addClass('searchLetter');

    if ($('#searchStartWith').val() == startWithLetter) {
      $('#searchStartWith').val('');
    } else {
    	//next, set the new start with letter to show selected
    	$("#span_letter_" + startWithLetter).removeClass('searchLetter').addClass('searchLetterSelected');

    	$('#searchStartWith').val(startWithLetter);
  	}
  	updateSearch();
  }



  $(".newSearch").click(function () {
  	//reset fields
  	$('#resourceSearchForm input[type=hidden]').not('#searchRecordsPerPage').val("");
    $('#resourceSearchForm input[type=text]').val("");
  	$('#resourceSearchForm select').val("");


  	//reset startwith background color
  	$("span.searchLetterSelected").removeClass('searchLetterSelected').addClass('searchLetter');
  	updateSearch();
  });


