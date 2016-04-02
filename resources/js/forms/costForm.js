/*
**************************************************************************************************************************
** CORAL Resources Module v. 1.0
**
** Copyright (c) 2010-2014 University of Notre Dame
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

	//bind all of the inputs

	 $("#submitCost").click(function () {
		submitCostForm();
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




	$(".remove").live('click', function () {
	    $(this).parent().parent().parent().fadeTo(400, 0, function () { 
		$(this).remove();
	    });
	    return false;
	});

  $(".priceTaxExcluded").change(function() {
    pte = $(this).val();
    taxRate = $(this).parent().next().children(".taxRate").val();
    if (pte && taxRate) {
      amount = parseFloat(pte) + (pte * taxRate / 100);
      $(this).parent().next().next().children(".priceTaxIncluded").val(amount);
      $(this).parent().next().next().next().children(".paymentAmount").val(amount);
    }
  });

  $(".taxRate").change(function() {
    taxRate = $(this).val();
    pte = $(this).parent().prev().children(".priceTaxExcluded").val();
    if (pte && taxRate) {
      amount = parseFloat(pte) + (pte * taxRate / 100);
      $(this).parent().next().children(".priceTaxIncluded").val(amount);
      $(this).parent().next().next().children(".paymentAmount").val(amount);

    }
  });

	$(".addPayment").live('click', function () {

		var y         = $('.newPaymentTable').children().children().children().children('.year').val();
		var ssd       = $('.newPaymentTable').children().children().children().children('.susbcriptionStartDate').val();
		var sed       = $('.newPaymentTable').children().children().children().children('.susbcriptionEndDate').val();
		var fName     = $('.newPaymentTable').children().children().children().children('.fundName').val();
		var pte       = $('.newPaymentTable').children().children().children().children('.priceTaxExcluded').val();
		var tr        = $('.newPaymentTable').children().children().children().children('.taxRate').val();
		var pti       = $('.newPaymentTable').children().children().children().children('.priceTaxIncluded').val();
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
			$('.paymentTypeID').addClass('changeSelect idleField').css("background-color","");
			//$('.paymentName').addClass('changeInput idleField');

			
			$('.addPayment').removeClass('addPayment');
			$('.newPaymentTR').removeClass('newPaymentTR');

			//next put the original clone back, we just need to reset the values
			originalTR.appendTo('.newPaymentTable');
			$('.newPaymentTable').children().children().children().children('.year').val('');
			$('.newPaymentTable').children().children().children().children('.subscriptionStartDate').val('');
			$('.newPaymentTable').children().children().children().children('.subscriptionEndDate').val('');
			$('.newPaymentTable').children().children().children().children('.fundName').val('');
			$('.newPaymentTable').children().children().children().children('.priceTaxExcluded').val('');
			$('.newPaymentTable').children().children().children().children('.taxRate').val('');
			$('.newPaymentTable').children().children().children().children('.priceTaxIncluded').val('');
			$('.newPaymentTable').children().children().children().children('.paymentAmount').val('');
			$('.newPaymentTable').children().children().children().children('.orderTypeID').val('');
			$('.newPaymentTable').children().children().children().children('.costDetailsID').val('');
			$('.newPaymentTable').children().children().children().children('.costNote').val('');
			$('.newPaymentTable').children().children().children().children('.invoiceNum').val('');
			

			// Remove datepickers from clone
			originalTR.find('.dp-choose-date').remove();

			// Re-add date pickers to clone
			originalTR.find('.date-pick').datePicker({startDate:'01/01/1996'});

			return false;
		}
	});


});



function submitCostForm(){
	
	purchaseSitesList ='';
	$(".check_purchaseSite:checked").each(function(id) {
	      purchaseSitesList += $(this).val() + ":::";
	}); 
	
	yearList ='';
	$(".year").each(function(id) {
	      yearList += $(this).val() + ":::";
	}); 

	subStartList ='';
	$(".subscriptionStartDate").each(function(id) {
	      subStartList += $(this).val() + ":::";
	}); 

	subEndList ='';
	$(".subscriptionEndDate").each(function(id) {
	      subEndList += $(this).val() + ":::";
	}); 

	fundNameList ='';
	$(".fundName").each(function(id) {
	      fundNameList += $(this).val() + ":::";
	}); 

	priceTaxExcludedList ='';
	$(".priceTaxExcluded").each(function(id) {
	      priceTaxExcludedList += $(this).val() + ":::";
	}); 

	taxRateList ='';
	$(".taxRate").each(function(id) {
	      taxRateList += $(this).val() + ":::";
	}); 

    priceTaxIncludedList ='';
	$(".priceTaxIncluded").each(function(id) {
	      priceTaxIncludedList += $(this).val() + ":::";
	});

	paymentAmountList ='';
	$(".paymentAmount").each(function(id) {
	      paymentAmountList += $(this).val() + ":::";
	}); 

	currencyCodeList ='';
	$(".currencyCode").each(function(id) {
	      currencyCodeList += $(this).val() + ":::";
	}); 
	
	orderTypeList ='';
	$(".orderTypeID").each(function(id) {
	      orderTypeList += $(this).val() + ":::";
	}); 

	detailsList ='';
	$(".costDetailsID").each(function(id) {
	      detailsList += $(this).val() + ":::";
	}); 

	costNoteList ='';
	$(".costNote").each(function(id) {
	      costNoteList += $(this).val() + ":::";
	}); 

	invoiceList ='';
	$(".invoiceNum").each(function(id) {
	      invoiceList += $(this).val() + ":::";
	}); 

	if (validateForm() === true) {
		$('#submitCost').attr("disabled", "disabled"); 
		  $.ajax({
			 type:  "POST",
			 url:   "ajax_processing.php?action=submitCost",
			 cache: false,
			 data:  { resourceID: $("#editResourceID").val(),
                      years: yearList,
                      subStarts: subStartList,
                      subEnds: subEndList,
                      fundNames: fundNameList,
                      pricesTaxExcluded: priceTaxExcludedList,
                      taxRates: taxRateList,
                      pricesTaxIncluded: priceTaxIncludedList,
                      paymentAmounts: paymentAmountList,
                      currencyCodes: currencyCodeList,
                      orderTypes: orderTypeList,
                      costDetails: detailsList,
                      costNotes: costNoteList,
                      invoices: invoiceList
                    },
			 success:   function(html) {
				if (html){
					$("#span_errors").html(html);
					$("#submitCost").removeAttr("disabled");
				}else{
					kill();
					window.parent.tb_remove();
					window.parent.updateAcquisitions();
					return false;
				}					

			 }


		 });
	 }

}





 
 function validateForm (){
 	myReturn=0;

	var y = $('.newPaymentTable').children().children().children().children('.year').val();
	var fName = $('.newPaymentTable').children().children().children().children('.fundName').val();
	var typeID = $('.newPaymentTable').children().children().children().children('.orderTypeID').val();
	var detailsID = $('.newPaymentTable').children().children().children().children('.costDetailsID').val();
	var pte = $('.newPaymentTable').children().children().children().children('.priceTaxIncluded').val();
	var pti = $('.newPaymentTable').children().children().children().children('.priceTaxExcluded').val();
	var pAmount = $('.newPaymentTable').children().children().children().children('.paymentAmount').val();
	var cNote = $('.newPaymentTable').children().children().children().children('.costNote').val();

	//also perform same checks on the current record in case add button wasn't clicked
	if ((((pAmount == '') || (pAmount == null)) && ((fName == '') || (fName == null))) && ((pAmount != '') || (fName != ''))){
		$('#div_errorPayment').html(_("Error - Either price or fund is required"));
		myReturn="1";		

	}

	if(((typeID == '') || (typeID == null)) && ((pAmount != '') || (fName != ''))){
		$('#div_errorPayment').html(_("Error - order type is a required field"));
		myReturn="1";
	}

	
	if ((pAmount != '') && (pAmount != null) && (isAmount(pAmount) === false)){
		$('#div_errorPayment').html(_("Error - price is not numeric"));
		myReturn="1";		
	}

  if ((pte != '') && (pte != null) && (isAmount(pte) === false)){
		$('#div_errorPayment').html('Error - Price (tax excluded) is not numeric');
		myReturn="1";		
	}

 	if ((pti != '') && (pti != null) && (isAmount(pti) === false)){
		$('#div_errorPayment').html('Error - Price (tax included) is not numeric');
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
