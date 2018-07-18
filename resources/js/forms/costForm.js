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

    addShowFormula();

    $(".paymentTR").each(function() {
        var $this = $(this);
        var formulaSelect = $this.find(".formulaSelect");
        var formulaID = $this.find("input[name='paymentPricingFormulaID']").val();
        var resourcePaymentID = $this.find("input[name='resourcePaymentID']").val();
        getFormulaForm(formulaID, resourcePaymentID, formulaSelect, true);
    });

    $(".formulaSelect").change(function() {
        var formulaID = $(this).val();
        var resourcePaymentID = $(this).parent().find("input[name='resourcePaymentID']").val();
        getFormulaForm(formulaID, resourcePaymentID, $(this));
    });

    $("#addFormulaButton").live('click', function(e){
		e.preventDefault();
		var values = $(e.currentTarget).parent().find("input[name^='field']").map(function() { return this.name + "=" + this.value }).get().join("&");
		$.ajax({
			type:       "GET",
			url:        "ajax_processing.php",
			cache:      false,
			data:       "action=getPaymentFromPricingFormula&pricingFormulaID=" + $(this).parent().find(".formulaID").val() + "&" + values,
			success:    function(html) {
				$(e.currentTarget).parent().parent().parent().prev('tr').prev('tr').find(".paymentAmount").val(html);
                $(e.currentTarget).parent().parent().parent().hide();
			}
		});

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
	    	$(this).next().remove(); //remove the error line first
	    	$(this).next().remove(); //remove the formula line
			$(this).remove(); //then remove the row containing the data
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

	$(".addPayment").click(function () {
		var y         = $('.newPaymentTable').find('.year').val();
		var ssd       = $('.newPaymentTable').find('.susbcriptionStartDate').val();
		var sed       = $('.newPaymentTable').find('.susbcriptionEndDate').val();
		var fName     = $('.newPaymentTable').find('.fundName').val();
		var pte       = $('.newPaymentTable').find('.priceTaxExcluded').val();
		var tr        = $('.newPaymentTable').find('.taxRate').val();
		var pti       = $('.newPaymentTable').find('.priceTaxIncluded').val();
		var typeID    = $('.newPaymentTable').find('.orderTypeID').val();
		var detailsID = $('.newPaymentTable').find('.costDetailsID').val();
		var pAmount   = $('.newPaymentTable').find('.paymentAmount').val();
		var cNote     = $('.newPaymentTable').find('.costNote').val();
		if(validateTable($('.newPaymentTable tbody tr')))
		{
			//we're going to strip out the $ of the payment amount
			pAmount = pAmount.replace('$','');

			$('#div_errorPayment').html('');

			var newPaymentTR = $('.newPaymentTR')
			var duplicateTR = newPaymentTR.clone(); //copy the payment being added
			var selectedOptions=newPaymentTR.find('select'); //get selected options
			duplicateTR.find('select').map(function(index, item) {
				item.value = selectedOptions[index].value;
			});
			duplicateTR.removeClass('newPaymentTR'); //remove newPaymentTR class from duplicate
			duplicateTR.find('.dp-choose-date').remove(); //remove date pickers from clone
			duplicateTR.find('.date-pick').datePicker({startDate:'01/01/1996'}); //add new date pickers to clone
			replaceInputWithImage=duplicateTR.children().last().find('.addPayment');
			replaceInputWithImage.replaceWith("<img src='images/cross.gif' class='remove' alt='" + _("remove this payment") + "' title='" + _("remove this payment") + "'/>");

			duplicateTR.appendTo('.paymentTable');
            $('<tr><td colspan="11"><div class="smallDarkRedText div_errorPayment" style="margin:0px 20px 0px 26px;"></div></td></tr>').appendTo('.paymentTable');

            var duplicateFormulaTR = $('.newFormulaTR').clone();

			duplicateFormulaTR.removeClass('newFormulaTR'); //remove newPaymentTR class from duplicate
			duplicateFormulaTR.addClass('formulaTR');
            duplicateFormulaTR.appendTo('.paymentTable');
            addShowFormula();

			//reset the add line values
			$('.newPaymentTable').find('.year').val('');
			$('.newPaymentTable').find('.subscriptionStartDate').val('');
			$('.newPaymentTable').find('.subscriptionEndDate').val('');
			$('.newPaymentTable').find('.fundID').val('');
			$('.newPaymentTable').find('.priceTaxExcluded').val('');
			$('.newPaymentTable').find('.taxRate').val('');
			$('.newPaymentTable').find('.priceTaxIncluded').val('');
			$('.newPaymentTable').find('.paymentAmount').val('');
			$('.newPaymentTable').find('.orderTypeID').val('');
			$('.newPaymentTable').find('.costDetailsID').val('');
			$('.newPaymentTable').find('.costNote').val('');
			$('.newPaymentTable').find('.invoiceNum').val('');
            $('.newPaymentTable').find('.formulaSelect').val('');
			var tableDiv=$('.paymentTableDiv')[0];
			tableDiv.scrollTop=tableDiv.scrollHeight;
			return true;
		}
		return false;
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

	if(y != '' || ssd != '' || sed != '' || fName != '' || pAmount != '' || typeID != '' || detailsID != '' || cNote != '' || invoiceNum != '')
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
			priceTaxExcludedList += $(this).val() + ":::";
		});

		taxRateList ='';
		$(".paymentTable").find(".taxRate").each(function(id) {
			taxRateList += $(this).val() + ":::";
		});

		priceTaxIncludedList ='';
		$(".paymentTable").find(".priceTaxIncluded").each(function(id) {
			priceTaxIncludedList += $(this).val() + ":::";
		});

		paymentAmountList ='';
		$(".paymentTable").find(".paymentAmount").each(function(id) {
			paymentAmountList += $(this).val() + ":::";
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

        formulaIDList = '';
        $(".paymentTable").find(".formulaSelect").each(function(id) {
            formulaIDList += $(this).val() + ":::";
        });

        formulaList = '';

        $(".formulaTR").each(function() {
            var $this = $(this);
            var singleFormulaArray = new Array();
            $this.find("input[name^='field']").each(function() {
                singleFormulaArray.push($(this).val());
            });
            formulaList += singleFormulaArray.join(':') + ":::";
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
				invoices: invoiceList,
                formulas: formulaList,
                formulaIDS: formulaIDList
			},
			success:   function(html) {
				if (html){
					$("#span_errors").html(html);
					$("#submitCost").removeAttr("disabled");
				} else {
					kill();
					window.parent.tb_remove();
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

function validateTable(objRows)
{
	//var currentRow = 0;
	var hasNoErrors = true;

 	$(objRows).find('.div_errorPayment').each(function() {$(this).html('');}); //clear existing errors
 	//while(typeof objRows[currentRow] !== "undefined")
        for (var currentRow = 0; currentRow < objRows.length; currentRow += 3)
 	{
		var y          = $(objRows[currentRow]).find('.year').val();
		var ssd        = $(objRows[currentRow]).find('.subscriptionStartDate').val();
		var sed        = $(objRows[currentRow]).find('.subscriptionEndDate').val();
		var fName      = $(objRows[currentRow]).find('.fundID').val();
		var pAmount    = $(objRows[currentRow]).find('.paymentAmount').val();
		var typeID     = $(objRows[currentRow]).find('.orderTypeID').val();
		var detailsID  = $(objRows[currentRow]).find('.costDetailsID').val();
		var pte        = $(objRows[currentRow]).find('.priceTaxIncluded').val();
		var pti        = $(objRows[currentRow]).find('.priceTaxExcluded').val();
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
			$(objRows[currentRow+1]).find('.div_errorPayment').html(_("Error - price is not numeric"));
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

function addShowFormula() {
    $(".showFormula").unbind('click').click(function() {
        $(this).parent().parent().next('tr').next('tr').toggle();
    });
}

function getFormulaForm(pricingFormulaID, resourcePaymentID, origin, hide = false) {
    $.ajax({
        type:       "GET",
        url:        "ajax_forms.php",
        cache:      false,
        data:       "action=getPricingFormulaForm&pricingFormulaID=" + pricingFormulaID + "&resourcePaymentID=" + resourcePaymentID,
        success:    function(html) {
            origin.parent().parent().next('tr').next('tr').find(".formulaDiv").html(html);
            origin.parent().parent().next('tr').next('tr').show();
            if (hide == true) {
                origin.parent().parent().next('tr').next('tr').hide();
            }
        }
    });
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
