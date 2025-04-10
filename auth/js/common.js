/*
**************************************************************************************************************************
** CORAL Usage Statistics Reporting Module v. 1.0
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



$(function(){



});





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
	if ($('#' + field)[0].checked) {
		return 1;
	}else{
		return 0;
	}
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


function myDialog(loadForm, h,w){
	$('<div/>').dialog({
			modal: true,
			open: function () {
				if ($(this).is(':empty')) {
						$(this).load(loadForm, function() {
							$('.ui-dialog-title').prepend($('.ui-dialog-content h2'));
						});
				}
			}
	});
	$('.ui-dialog').removeAttr('aria-describedby');
}

function myCloseDialog(){
     $('.ui-dialog-content').dialog('destroy');
}

