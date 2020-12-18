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

 $(function(){
	$('.date-pick').datePicker({startDate:'01/01/1996'});
    $('.date-pick').attr('placeholder', Date.format);

	//bind all of the inputs

	 $("#submitOrder").click(function () {
		submitOrderForm();
	 });


	//do submit if enter is hit
	$('#orderNumber').keyup(function(e) {
	      if(e.keyCode == 13) {
		submitOrderForm();
	      }
	});

	//do submit if enter is hit
	$('#systemNumber').keyup(function(e) {
	      if(e.keyCode == 13) {
		submitOrderForm();
	      }
	});


	//the following are all to change the look of the inputs when they're clicked
	$('.changeDefault').live('focus', function(e) {
		if (this.value == this.defaultValue){
			this.value = '';
		}
	});

	 $('.changeDefault').live('blur', function() {
		if(this.value == ''){
			this.value = this.defaultValue;
		}
	 });


	$('.changeInput').addClass("idleField");

	$('.changeInput').live('focus', function() {


		$(this).removeClass("idleField").addClass("focusField");

		if(this.value != this.defaultValue){
			this.select();
		}

	 });


	 $('.changeInput').live('blur', function() {
		$(this).removeClass("focusField").addClass("idleField");
	 });




	$('select').addClass("idleField");
	$('select').live('focus', function() {
		$(this).removeClass("idleField").addClass("focusField");

	});

	$('select').live('blur', function() {
		$(this).removeClass("focusField").addClass("idleField");
	});



	$('textarea').addClass("idleField");
	$('textarea').focus(function() {
		$(this).removeClass("idleField").addClass("focusField");
	});

	$('textarea').blur(function() {
		$(this).removeClass("focusField").addClass("idleField");
	});

    $(".organizationName").autocomplete('ajax_processing.php?action=getOrganizationList', {
        minChars: 2,
        max: 20,
        mustMatch: false,
        width: 164,
        delay: 10,
        matchContains: true,
        formatItem: function(row) {
            return "<span style='font-size: 80%;'>" + row[0] + "</span>";
        },
        formatResult: function(row) {
            return row[0].replace(/(<.+?>)/gi, '');
        }

    });


    //once something has been selected, change the hidden input value
    $(".organizationName").result(function(event, data, formatted) {
        $(this).parent().children('.organizationID').val(data[1]);
    });




	$(".remove").live('click', function () {
	    $(this).parent().parent().parent().fadeTo(400, 0, function () {
		$(this).remove();
	    });
	    return false;
	});



	$(".addPayment").live('click', function () {

		var y         = $('.newPaymentTable').children().children().children().children('.year').val();
		var ssd       = $('.newPaymentTable').children().children().children().children('.susbcriptionStartDate').val();
		var sed       = $('.newPaymentTable').children().children().children().children('.susbcriptionEndDate').val();
		var fName     = $('.newPaymentTable').children().children().children().children('.fundName').val();
		var typeID    = $('.newPaymentTable').children().children().children().children('.orderTypeID').val();
		var detailsID = $('.newPaymentTable').children().children().children().children('.costDetailsID').val();
		var pAmount   = $('.newPaymentTable').children().children().children().children('.paymentAmount').val();
		var cNote     = $('.newPaymentTable').children().children().children().children('.costNote').val();

		if ((pAmount == '' || pAmount == null) && (fName == '' || fName == null)){
			$('#div_errorPayment').html(_("Error - Either amount or fund is required"));
			return false;
		}else if((typeID == '') || (typeID == null)){
			$('#div_errorPayment').html(_("Error - order type is a required field"));
			return false;
		}else if ((pAmount != '') && (pAmount != null) && (isAmount(pAmount) === false)){
			$('#div_errorPayment').html(_("Error - price is not numeric"));
			return false;
		}else{

			//we're going to strip out the $ of the payment amount
			pAmount = pAmount.replace('$','');

			$('#div_errorPayment').html('');

			//first copy the new payment being added
			var originalTR = $('.newPaymentTR').clone();

			//next append to to the existing table
			//it's too confusing to chain all of the children.
			$('.newPaymentTR').appendTo('.paymentTable');

			$('.newPaymentTR').children().children().children('.addPayment').attr({
			  src: 'images/cross.gif',
			  alt: _("remove this payment"),
			  title: _("remove this payment")
			});
			$('.newPaymentTR').children().children().children('.addPayment').addClass('remove').css("text-align","center");
			$('.newPaymentTR').children().children('.paymentAmount').val(pAmount);
			$('.paymentTypeID').addClass('changeSelect');
			$('.paymentTypeID').addClass('idleField');
			$('.paymentTypeID').css("background-color","");
			$('.paymentName').addClass('changeInput');
			$('.paymentName').addClass('idleField');


			$('.addPayment').removeClass('addPayment');
			$('.newPaymentTR').removeClass('newPaymentTR');

			//next put the original clone back, we just need to reset the values
			originalTR.appendTo('.newPaymentTable');
			$('.newPaymentTable').children().children().children().children('.orderTypeID').val('');
			$('.newPaymentTable').children().children().children().children('.year').val('');
			$('.newPaymentTable').children().children().children().children('.fundName').val('');
			$('.newPaymentTable').children().children().children().children('.paymentAmount').val('');
			$('.newPaymentTable').children().children().children().children('.costNote').val('');


			return false;
		}
	});


});



function submitOrderForm(){

	purchaseSitesList ='';
	$(".check_purchaseSite:checked").each(function(id) {
	      purchaseSitesList += $(this).val() + ":::";
	});

	if (validateForm() === true) {
		$('#submitOrder').attr("disabled", "disabled"); 
		  $.ajax({
			 type:  "POST",
			 url:   "ajax_processing.php?action=submitAcquisitions",
			 cache: false,
			 data:  { resourceAcquisitionID: $("#editResourceAcquisitionID").val(),
                      resourceID: $("#editResourceID").val(),
                      op: $("#op").val(),
                      acquisitionTypeID: $("#acquisitionTypeID").val(),
                      orderNumber: $("#orderNumber").val(),
                      systemNumber: $("#systemNumber").val(),
                      libraryNumber: $("#libraryNumber").val(),
                      currentStartDate: $("#currentStartDate").val(),
                      currentEndDate: $("#currentEndDate").val(),
                      organizationID: $("#organizationID").val(),
                      subscriptionAlertEnabledInd: $("#subscriptionAlertEnabledInd:checked").val(),
                      purchaseSites: purchaseSitesList,
                    },
			 success:   function(html) {
				if (html){
					$("#span_errors").html(html);
					$("#submitOrder").removeAttr("disabled");
				}else{
                    if ($("#editResourceAcquisitionID").val() == '' || $("#op").val() == 'clone') {
                        //TODO: it would be better to go directly to the newly created order
                        var newLoc = location.search;
                        if (newLoc.includes('showTab')) {
                            newLoc = newLoc.replace(/showTab=[^&$]*/i, 'showTab=Orders');
                        } else {
                            newLoc += "&showTab=Orders";
                        }
                        location.search = newLoc;

                    } else {
                        kill();
                        window.parent.tb_remove();
                        window.parent.updateOrders();
                        window.parent.updateRightPanel();
                        return false;
                    }
				}

			 }


		 });
	 }

}






 function validateForm (){
 	myReturn=0;

	var typeID = $('#acquisitionTypeID').val();

	if((typeID == '') || (typeID == null)){
		$('#span_errors').html(_("Error - acquisition type is a required field"));
		myReturn="1";
	}

 	if (myReturn == "1"){
		return false; 	
 	}else{
 		return true;
 	}
}
 





//kill all binds done by jquery live
function kill(){

	$('.addPayment').die('click');
	$('.changeDefault').die('blur');
	$('.changeDefault').die('focus');
	$('.changeInput').die('blur');
	$('.changeInput').die('focus');
	$('.select').die('blur');
	$('.select').die('focus');
	$('.remove').die('click');

}
