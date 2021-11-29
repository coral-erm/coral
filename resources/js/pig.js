/*
**************************************************************************************************************************
** CORAL PIG Add-On v. 1.0
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

    //perform search if enter is hit
    $('#searchName').keyup(function(e) {
        if(e.keyCode == 13) {
            updateSearch();
        }
    });

    //perform search if enter is hit
    $('#searchDescriptionText').keyup(function(e) {
        if(e.keyCode == 13) {
            updateSearch();
        }
    });

    //perform search if enter is hit
    $('#searchProviderText').keyup(function(e) {
          if(e.keyCode == 13) {
        updateSearch();
          }
    });


    //bind change event to Records Per Page drop down
    $("#numberRecordsPerPage").on('change', function () {
        setNumberOfRecords($(this).val())
    });


    //bind change event to each of the page start
    $(".setPage").on('click', function () {
        setPageStart($(this).attr('id'));
    });

    $('#resourcePigSearchForm select').change(function() {
        updateSearch();
    });

    $('#resourcePigSearchForm').submit(function() {
        updateSearch();
        return false;
    });

    $(".searchButton").click(function() {
        $('#resourcePigSearchForm').submit();
        return false;
    })

    $('#titleTextckbox').change(function() {
        $('#resourcePigSearchForm').submit();
        return false;
    });

    $('#providerTextckbox').change(function() {
        $('#resourcePigSearchForm').submit();
        return false;
    });

    $('#descriptionTextckbox').change(function() {
        $('#resourcePigSearchForm').submit();
        return false;
    });

    $('#generalSubjectckbox').change(function() {
        $('#resourcePigSearchForm').submit();
        return false;
    });

    $('#resourceTypeckbox').change(function() {
        $('#resourcePigSearchForm').submit();
        return false;
    });

    $('#resourcePigSearchForm checkbox').change(function() {
        alert('Handler for .change() called.');
    });

 });


function updateSearch(pageNumber) {
    $("#div_feedback").html("<img src='images/circle.gif'>  <span style='font-size:90%'>Processing...</span>");
    if (!pageNumber) {
        pageNumber = 1;
    }
    $('#searchPage').val(pageNumber);

    var form = $('#resourcePigSearchForm');
    $.post(
        form.attr('action'),
        form.serialize(),
        function(html) {
            $("#div_feedback").html("&nbsp;");
            $('#div_searchResults').html(html);
        }
    );

    window.scrollTo(0, 0);
}



function setOrder(column, direction){
    $("#searchOrderBy").val(column + " " + direction)
    updateSearch();
}


function setPageStart(pageStartNumber){
    updateSearch(pageStartNumber);
}


function setNumberOfRecords(recordsPerPageNumber){
    $("#searchRecordsPerPage").val(recordsPerPageNumber);
    updateSearch();
}



$(".newSearch").click(function () {
    //reset fields
    $('#resourcePigSearchForm input[type=hidden]').not('#searchRecordsPerPage').val("");
    $('#resourcePigSearchForm input[type=text]').val("");
    $('#resourcePigSearchForm select').val("");


    //reset startwith background color
    $("span.searchLetterSelected").removeClass('searchLetterSelected').addClass('searchLetter');
    updateSearch();
});


$("#searchName").focus(function () {
    $("#div_searchName").css({'display':'block'});
});

$("#searchDescriptionText").focus(function () {
    $("#div_searchDescriptionText").css({'display':'block'});
});

$("#searchProviderText").focus(function () {
    $("#div_searchProviderText").css({'display':'block'});
});
