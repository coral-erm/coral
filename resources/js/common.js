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

//image preloader
(function($) {
  var cache = [];
  // Arguments are image paths relative to the current page.
  $.preLoadImages = function() {
    var args_len = arguments.length;
    for (var i = args_len; i--;) {
      var cacheImage = document.createElement('img');
      cacheImage.src = arguments[i];
      cache.push(cacheImage);
    }
  }
})(jQuery)


//Required for date picker
Date.firstDayOfWeek = 0;


$(function(){
	refreshContext();

	$("#search_organization").autocomplete('ajax_processing.php?action=getOrganizationList', {
		minChars: 2,
		max: 20,
		mustMatch: true,
		width: 142,
		delay: 20,
		matchContains: false,
		formatItem: function(row) {
			return "<span style='font-size: 80%;'>" + row[0] + "</span>";
		},
		formatResult: function(row) {
			return row[0].replace(/(<.+?>)/gi, '');
		}

	 });


	 //once something has been selected, go directly to that page
	 $("#search_organization").result(function(event, data, formatted) {
	 	if (data[0] != null){
			replacedOrg = data[0].replace(/&/g, "&amp;");
			window.location  = 'orgDetail.php?organizationID=' + data[1] + '&search_organization=' + escape(data[0]);
		}
	 });


	 function log(event, data, formatted) {
		$("<li>").html( !data ? _("No match!") : _("Selected: ") + formatted).html("#result");

	 }

	//used for swapping the value on the search box
	 swapValues = [];
	 $(".swap_value").each(function(i){
		swapValues[i] = $(this).val();
		$(this).focus(function(){
		    if ($(this).val() == swapValues[i]) {
			$(this).val("");
		    }
		}).blur(function(){
		    if ($.trim($(this).val()) == "") {
			$(this).val(swapValues[i]);
		    }
		});
	 });



	 //for swapping menu images
	$('.rollover').hover(function() {
		var currentImg = $(this).attr('src');
		$(this).attr('src', $(this).attr('hover'));
		$(this).attr('hover', currentImg);

		if ($(this).attr('id') == 'menu-last'){
			var endImg = $("#menu-end").attr('src');
			$('#menu-end').attr('src', $("#menu-end").attr('hover'));
			$('#menu-end').attr('hover', endImg);
		}
	    }, function() {
		var currentImg = $(this).attr('src');
		$(this).attr('src', $(this).attr('hover'));
		$(this).attr('hover', currentImg);

		if ($(this).attr('id') == 'menu-last'){
			var endImg = $("#menu-end").attr('src');
			$('#menu-end').attr('src', $("#menu-end").attr('hover'));
			$('#menu-end').attr('hover', endImg);
		}

	 });


	 //for the Change Module drop down
	 $('.coraldropdown').each(function () {
		$(this).parent().eq(0).hover(function () {
			$('.coraldropdown:eq(0)', this).slideDown(100);
			}, function () {
			$('.coraldropdown:eq(0)', this).slideUp(100);
		});
	 });



});


function refreshContext() {
	$('.date-pick').datePicker({startDate:'01/01/1996'});
}

// 1 visible, 0 hidden
function toggleDivState(divID, intDisplay) {
	if(document.layers){
	   document.layers[divID].display = intDisplay ? "block" : "none";
	}
	else if(document.getElementById){
		var obj = document.getElementById(divID);
		obj.style.display = intDisplay ? "block" : "none";
	}
	else if(document.all){
		document.all[divID].style.display = intDisplay ? "block" : "none";
	}
}

//if (typeof expressionTypeId == 'undefined') expressionTypeId = '';


//This prototype is provided by the Mozilla foundation and
//is distributed under the MIT license.
//http://www.ibiblio.org/pub/Linux/LICENSES/mit.license

if (!Array.prototype.indexOf)
{
  Array.prototype.indexOf = function(elt /*, from*/)
  {
    var len = this.length;

    var from = Number(arguments[1]) || 0;
    from = (from < 0)
         ? Math.ceil(from)
         : Math.floor(from);
    if (from < 0)
      from += len;

    for (; from < len; from++)
    {
      if (from in this &&
          this[from] === elt)
        return from;
    }
    return -1;
  };
}

function getCheckboxValue(field){
	if ($('#' + field + ':checked').attr('checked')) {
		return 1;
	}else{
		return 0;
	}
}

function validateEmail(email) {
	console.log("Validating email.");
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}

function validateRequired(field,alerttxt){
	fieldValue=$("#" + field).val();

	  if (fieldValue==null || fieldValue=="") {
	    $("#span_error_" + field).html(alerttxt);
	    $("#" + field).focus();
	    return false;
	  } else {
	    $("#span_error_" + field).html('');
	    return true;
	  }
}



function validateRadioChecked(field,alerttxt,defaulttxt){
	fieldValue=$('input:radio[name=' + field + ']:checked').val()

	  if (fieldValue==null || fieldValue=="") {
	    $("#span_error_" + field).html(alerttxt);
	    $("#" + field).focus();
	    return false;
	  } else {
	    $("#span_error_" + field).html(defaulttxt);
	    return true;
	  }
}



function validateDate(field,alerttxt) {
     $("#span_error_" + field).html('');
     sDate =$("#" + field).val();

     if (sDate){

	   var re = /^\d{1,2}\/\d{1,2}\/\d{4}$/
	   if (re.test(sDate)) {
	      var dArr = sDate.split("/");
	      var d = new Date(sDate);

	      if (!(d.getMonth() + 1 == dArr[0] && d.getDate() == dArr[1] && d.getFullYear() == dArr[2])) {
		$("#span_error_" + field).html(alerttxt);
	       $("#" + field).focus();
		return false;
	      }else{
		return true;
	      }

	   } else {
	      $("#span_error_" + field).html(alerttxt);
	      $("#" + field).focus();
	      return false;
	   }
     }

     return true;
}



function isAmount(amount){
    var thousandSeparator = (1111).toLocaleString(CORAL_NUMBER_LOCALE).replace(/1/g, '');
    var decimalSeparator = (1.1).toLocaleString(CORAL_NUMBER_LOCALE).replace(/1/g, '');

    // We remove spaces, all thousand separators, and the first decimal separator
    amount = amount.replace(/\s/g,'');
    amount = amount.replace(new RegExp('\\' + thousandSeparator, 'g'), '');
    amount = amount.replace(new RegExp('\\' + decimalSeparator), '');

    // What is left must be digits only
    var regex = RegExp('^[0-9]*$');
    return regex.test(amount);
}



function postwith (to,p) {
  var myForm = document.createElement("form");
  myForm.method="post" ;
  myForm.action = to ;
  for (var k in p) {
    var myInput = document.createElement("input") ;
    myInput.setAttribute("name", k) ;
    myInput.setAttribute("value", p[k]);
    myForm.appendChild(myInput) ;
  }
  document.body.appendChild(myForm) ;
  myForm.submit() ;
  document.body.removeChild(myForm) ;
}


function isValidDate(dateString)
{
    // First check for the pattern
    var regex_date = /^\d{1,2}\/\d{1,2}\/\d{4}$/;

    if(!regex_date.test(dateString))
    {
        return false;
    }

    // Parse the date parts to integers
    //borrowed from http://jsfiddle.net/niklasvh/xfrLm/
    //http://stackoverflow.com/questions/6177975/how-to-validate-date-with-format-mm-dd-yyyy-in-javascript
    var parts   = dateString.split("/");
    var day     = parseInt(parts[1], 10);
    var month   = parseInt(parts[0], 10);
    var year    = parseInt(parts[2], 10);


    // Check the ranges of month and year
    if(year < 1000 || year > 3000 || month == 0 || month > 12)
    {
        return false;
    }

    var monthLength = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];

    // Adjust for leap years
    if(year % 400 == 0 || (year % 100 != 0 && year % 4 == 0))
    {
        monthLength[1] = 29;
    }

    // Check the range of the day
    return day > 0 && day <= monthLength[month - 1];
}
