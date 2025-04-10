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


	$('.date-pick').datePicker({startDate:'01/01/2025'});
	$('.date-pick').attr('placeholder', Date.format);

	//bind all of the inputs

	 $("#submitCost").click(function () {
		submitCostForm();
	 });


	//the following are all to change the look of the inputs when they're clicked
	$('.changeDefault').on('focus', function(e) {
		if (this.value == this.defaultValue){
			this.value = '';
		}
	});

	 $('.changeDefault').on('blur', function() {
		if(this.value == ''){
			this.value = this.defaultValue;
		}
	 });

	$(".costHistoryAction.remove").on('click', function () {
			parentRow = $(this).closest('tr');
	    parentRow.fadeTo(400, 0, parentRow.remove());
	    return false;
	});

	$(".priceTaxExcluded").change(function() {
			parentRow = $(this).closest('tr');
    	pte = $(this).val();
    	taxRate = parentRow.find(".taxRate").val();
    	if (pte && taxRate) {
				amount = calcPriceTaxIncluded(pte, taxRate);
				amount = numberFormat(amount);
				parentRow.find(".priceTaxIncluded").val(amount);
				parentRow.find(".paymentAmount").val(amount);
    	}
	});

	$(".taxRate").change(function() {
			parentRow = $(this).closest('tr');
    	taxRate = $(this).val();
    	pte = parentRow.find(".priceTaxExcluded").val();
    	if (pte && taxRate) {
				amount = calcPriceTaxIncluded(pte, taxRate);
				amount = numberFormat(amount);
				parentRow.find(".priceTaxIncluded").val(amount);
				parentRow.find(".paymentAmount").val(amount);
	    }
	});

	$(".addPayment").click(function () {
			$('#div_errorPayment').html('');

			var newPaymentTR = $('.newPaymentTR');
			var duplicateTR = newPaymentTR.clone(); //copy the payment being added
			var selectedOptions=newPaymentTR.find('select'); //get selected options
			duplicateTR.find('select').map(function(index, item) {
				item.value = selectedOptions[index].value;
			});
			duplicateTR.removeClass('newPaymentTR'); //remove newPaymentTR class from duplicate
			duplicateTR.find('.dp-choose-date').remove(); //remove date pickers from clone
			duplicateTR.find('.date-pick').datePicker({startDate:'01/01/2025'}); //add new date pickers to clone
			replaceInputWithImage=duplicateTR.find('.addPayment');
			replaceInputWithImage.replaceWith("<img src='images/cross.gif' class='remove' alt='" + _("remove this payment") + "' title='" + _("remove this payment") + "'/>");

			duplicateTR.appendTo('.newPaymentTable');

			//reset the add line values
			duplicateTR.find('.year').val('');
			duplicateTR.find('.subscriptionStartDate').val('');
			duplicateTR.find('.subscriptionEndDate').val('');
			duplicateTR.find('.fundID').val('');
			duplicateTR.find('.priceTaxExcluded').val('');
			duplicateTR.find('.taxRate').val('');
			duplicateTR.find('.priceTaxIncluded').val('');
			duplicateTR.find('.paymentAmount').val('');
			duplicateTR.find('.orderTypeID').val('');
			duplicateTR.find('.costDetailsID').val('');
			duplicateTR.find('.costNote').val('');
			duplicateTR.find('.invoiceNum').val('');
			return true;
	});
});



function submitCostForm()
{
	//check if anything is on the add line
	var y          = $('.newPaymentTR').find('.year').val();
	var ssd        = $('.newPaymentTR').find('.subscriptionStartDate').val();
	var sed        = $('.newPaymentTR').find('.subscriptionEndDate').val();
	var fName      = $('.newPaymentTR').find('.fundID').val();
	var pAmount    = $('.newPaymentTR').find('.paymentAmount').val();
	var typeID     = $('.newPaymentTR').find('.orderTypeID').val();
	var detailsID  = $('.newPaymentTR').find('.costDetailsID').val();
	var cNote      = $('.newPaymentTR').find('.costNote').val();
	var invoiceNum = $('.newPaymentTR').find('.invoiceNum').val();

	if(fName != '' || pAmount != '' || typeID != '')
	{
		if(confirm('There is unsaved information on the add line. To discard this information, click OK, otherwise click Cancel.')==false)
		{
			return;
		}
	}
	if(validateTable($('.paymentTable tbody tr')))
	{
		purchaseSitesList ='';
		$(".paymentTable").find(".check_purchaseSite:checked").each(function(id) {
		      purchaseSitesList += $(this).val() + ":::";
		});

		yearList ='';
		$(".paymentTable").find(".year").each(function(id) {
		      yearList += $(this).val() + ":::";
		});

		subStartList ='';
		$(".paymentTable").find(".subscriptionStartDate").each(function(id) {
		      subStartList += $(this).val() + ":::";
		});

		subEndList ='';
		$(".paymentTable").find(".subscriptionEndDate").each(function(id) {
		      subEndList += $(this).val() + ":::";
		});

		fundNameList ='';
		$(".paymentTable").find(".fundID").each(function(id) {
		      fundNameList += $(this).val() + ":::";
		});


		priceTaxExcludedList ='';
		$(".paymentTable").find(".priceTaxExcluded").each(function(id) {
			priceTaxExcludedList += parseFloatI18n($(this).val()) * 100 + ":::";
		});

		taxRateList ='';
		$(".paymentTable").find(".taxRate").each(function(id) {
			taxRateList += parseFloatI18n($(this).val()) * 100 + ":::";
		});

		priceTaxIncludedList ='';
		$(".paymentTable").find(".priceTaxIncluded").each(function(id) {
			priceTaxIncludedList += parseFloatI18n($(this).val()) * 100 + ":::";
		});

		paymentAmountList ='';
		$(".paymentTable").find(".paymentAmount").each(function(id) {
			paymentAmountList += parseFloatI18n($(this).val()) * 100 + ":::";
		});

		currencyCodeList ='';
		$(".paymentTable").find(".currencyCode").each(function(id) {
		      currencyCodeList += $(this).val() + ":::";
		});

		orderTypeList ='';
		$(".paymentTable").find(".orderTypeID").each(function(id) {
		      orderTypeList += $(this).val() + ":::";
		});

		detailsList ='';
		$(".paymentTable").find(".costDetailsID").each(function(id) {
		      detailsList += $(this).val() + ":::";
		});

		costNoteList ='';
		$(".paymentTable").find(".costNote").each(function(id) {
		      costNoteList += $(this).val() + ":::";
		});

		invoiceList ='';
		$(".paymentTable").find(".invoiceNum").each(function(id) {
		      invoiceList += $(this).val() + ":::";
		});
                $('#submitCost').attr("disabled", "disabled");
		$.ajax({
			type:  "POST",
			url:   "ajax_processing.php?action=submitCost",
			cache: false,
			data: {
				resourceID: $("#editResourceID").val(),
				resourceAcquisitionID: $("#editResourceAcquisitionID").val(),
				years: yearList,
				subStarts: subStartList,
				subEnds: subEndList,
				fundIDs: fundNameList,
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
				} else {
					myCloseDialog();
					window.parent.updateAcquisitions();
					return false;
				}

			}
		});
	}
	else
	{
		$("#span_errors").html(_("Validation Failed"));
		$("#submitCost").removeAttr("disabled");
	}
}

function calcPriceTaxIncluded(priceTaxExcluded, taxRate) {
    priceTaxExcluded = parseFloatI18n(priceTaxExcluded);
    taxRate = parseFloatI18n(taxRate);
    return priceTaxExcluded + (priceTaxExcluded * taxRate / 100);
}

function validateTable(objRows)
{
	//var currentRow = 0;
	var hasNoErrors = true;

 	$(objRows).find('.div_errorPayment').each(function() {$(this).html('');}); //clear existing errors
 	//while(typeof objRows[currentRow] !== "undefined")
  for (var currentRow = 0; currentRow < objRows.length; currentRow += 2)
 	{
		var y          = $(objRows[currentRow]).find('.year').val();
		var ssd        = $(objRows[currentRow]).find('.subscriptionStartDate').val();
		var sed        = $(objRows[currentRow]).find('.subscriptionEndDate').val();
		var fName      = $(objRows[currentRow]).find('.fundID').val();
		var pAmount    = $(objRows[currentRow]).find('.paymentAmount').val();
		var typeID     = $(objRows[currentRow]).find('.orderTypeID').val();
		var detailsID  = $(objRows[currentRow]).find('.costDetailsID').val();
		var pti        = $(objRows[currentRow]).find('.priceTaxIncluded').val();
		var pte        = $(objRows[currentRow]).find('.priceTaxExcluded').val();
		var cNote      = $(objRows[currentRow]).find('.costNote').val();
		var invoiceNum = $(objRows[currentRow]).find('.invoiceNum').val();

		if ((pAmount == '' || pAmount == null) && (fName == '' || fName == null))
		{
			$(objRows[currentRow+1]).find('.div_errorPayment').html(_("Error - Either amount or fund is required"));
			hasNoErrors = false;
		}
		else if((typeID == '') || (typeID == null))
		{
			$(objRows[currentRow+1]).find('.div_errorPayment').html(_("Error - order type is a required field"));
			hasNoErrors = false;
		}
		else if ((pAmount != '') && (pAmount != null) && (isAmount(pAmount) === false))
		{
			$(objRows[currentRow+1]).find('.div_errorPayment').html(_("Error - Price (payment) is not numeric"));
			hasNoErrors = false;
		}
		else if ((pte != '') && (pte != null) && (isAmount(pte) === false)){
			$(objRows[currentRow+1]).find('.div_errorPayment').html('Error - Price (tax excluded) is not numeric');
			hasNoErrors = false;
		}
		else if ((pti != '') && (pti != null) && (isAmount(pti) === false)){
			$(objRows[currentRow+1]).find('.div_errorPayment').html('Error - Price (tax included) is not numeric');
			hasNoErrors = false;
		}
 	}
 	return hasNoErrors;
}

//kill all binds done by jquery live
function kill()
{
	$('.addPayment').die('click');
	$('.changeDefault').die('blur');
	$('.changeDefault').die('focus');
	$('.changeInput').die('blur');
	$('.changeInput').die('focus');
	$('.select').die('blur');
	$('.select').die('focus');
	$('.remove').die('click');
}
