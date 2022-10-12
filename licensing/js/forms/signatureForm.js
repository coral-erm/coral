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


$(function(){
  $('.date-pick').datePicker({startDate:'01/01/1996'});


  $("#signerName").autocomplete('ajax_processing.php?action=getSigners', {
	minChars: 2,
	max: 20,
	mustMatch: false,
	width: 120,
	delay: 200,
	matchContains: true,
	formatItem: function(row) {
		return "<span style='font-size: 80%;'>" + row[1] + "</span>";
	},
	formatResult: function(row) {
		return row[1].replace(/(<.+?>)/gi, '');
	}

  });


  function log(event, data, formatted) {
	$("<li>").html( !data ? _("No match!") : _("Selected: ") + formatted).html("#result");

  }


});


$("#commitUpdate").click(function () {
    if(validateSignature() === true){
        $.ajax({
            type:       "POST",
            url:        "ajax_processing.php?action=submitSignature",
            cache:      false,
            data:       { signatureID: $("#signatureID").val(), signerName: $("#signerName").val(), signatureTypeID: $("#signatureTypeID").val(), signatureDate: $("#signatureDate").val(), documentID: $("#documentID").val() },
            success:    function(response) {
                updateSignatureForm();
                myCloseDialog();
            }
        });
    }
});

function validateSignature() {
    if($("#signerName").val() == '') {
        $("#span_errors").html('Error - Please add a signer name');
        $("#signerName").focus();
        return false;
    }else if($("#signatureDate").val() == ''){
        $("#span_errors").html('Error - Please add a date for the signature');
        $("#signatureDate").focus();
        return false;
    }else{
        return true;
    }
}

function updateSignatureForm(signatureID){

  $.ajax({
	 type:       "GET",
	 url:        "ajax_forms.php",
	 cache:      false,
	 data:       "action=getSignatureForm&documentID=" + $("#documentID").val() + "&signatureID=" + signatureID,
	 success:    function(html) {
		$("#div_signatureForm").html(html);
        myCloseDialog();
	 }


 });

}



function removeSignature(signatureID){
  if (confirm(_("Do you really want to delete this signature?")) == true) {
	  $.ajax({
		 type:       "GET",
		 url:        "ajax_processing.php",
		 cache:      false,
		 data:       "action=deleteSignature&signatureID=" + signatureID,
		 success:    function(html) {
			updateSignatureForm();
            myCloseDialog();
		 }


	 });
  }

}



function newSignatureType(){
  $('#span_newSignatureType').html("<input type='text' name='newSignatureType' id='newSignatureType' style='width:80px;' />  <a href='javascript:addSignatureType();'>"+_("add")+"</a>");
}


function addSignatureType(){
	//add signatureType to db and returns updated select box
  $.ajax({
	 type:       "POST",
	 url:        "ajax_processing.php?action=addSignatureType",
	 cache:      false,
	 data:       { shortName: $("#newSignatureType").val() },
	 success:    function(html) { $('#span_signatureType').html(html); $('#span_newSignatureType').html("<font color='red'>"+_("SignatureType has been added")+"</font>"); }
 });
}

