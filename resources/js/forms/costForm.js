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
	//Build the first row.
	if(paymentRows && paymentRows.length>0){
		for(i=0;i<paymentRows.length;i++){
			addPaymentRow(paymentRows[i]);
		}
	} else {addPaymentRow();}

	//bind all of the inputs
	 $("#submitCost").click(function () {
		submitCostForm();
		return false;
	 });

	$(".costHistoryAction>.remove").on('click', function () {
		parentRow = $(this).closest('tr');
	    parentRow.remove();
	    return false;
	});

	//Calculate the Price Tax Included and Payment Amount automatically when PriceTaxExcluded AND Tax Rate are calculated.
	$(".priceTaxExcluded, .taxRate").change(function() {
		let parentRow = $(this).closest('tr');
		let pte = parentRow.find(".priceTaxExcluded").val();
		let taxRate = parentRow.find(".taxRate").val();
    	if (pte && taxRate) {
				amount = calcPriceTaxIncluded(pte, taxRate);
				amount = numberFormat(amount);
				parentRow.find(".priceTaxIncluded").val(amount);
				parentRow.find(".paymentAmount").val(amount);
    	}
	});
});

newID = (typeof newID == 'undefined') ? 0 : newID; //This is just to provide a consistent id for any new Rows that get added. 

function addPaymentRow(data = []){
	let newTR = $('.newPaymentTR').clone(true); //Clone the template row. Use True to include events.
	newTR.removeClass('newPaymentTR');
	newTR.removeAttr('hidden');
	let dataRows = Object.keys(data);
	let dataRowsExist = dataRows.length > 0;
	let paymentIDExists = data.hasOwnProperty('resourcePaymentID'); //This would be passed along if the data came from the database.
	let updateData = (dataRowsExist && paymentIDExists);
	if(updateData){
		//Okay, we can expect this is preexisting data being passed at the start of the page.
		let paymentID = data.resourcePaymentID;
		for (const property in data){
			let propCheck = newTR.find('.'+property);
			if(propCheck.length>0){
				//Property Exists. Add the action.
				let propName = propCheck.attr('name');
				let addAction = propName.replace('\[action\]', '[update]');
				propCheck.attr('name', addAction);
				//Set the value.
				propCheck.val(data[property]);
				//Update the ID. Make sure to reset propName because we've already updated the attribute.
				propName = propCheck.attr('name');
				let newName = propName.replace('\[id\]', '['+paymentID+']');
				propCheck.attr('name', newName);
			}
		}
	} else {
		//We can expect this is a new row, blank.
		let tdList = newTR.children();
		tdList.each(function(){
			//Each TD should have a child with a name attribute we're looking for.
			tdKids = $(this).children();
			tdKids.each(function(){
				let name = $(this).attr('name');
				if(name){
					let addAction = name.replace('\[action\]', '[new]');
					$(this).attr('name', addAction);
					let updatedName = $(this).attr('name');
					let newName = updatedName.replace('\[id\]', '['+newID+']');
					$(this).attr('name', newName);
				}
			});
		});
		newID++;
	}
	newTR.appendTo('.newPaymentTable');
}

function submitCostForm()
{
	let form = document.getElementById("resourceForm");
	let formData = new FormData(form);
	$("#submitCost").attr("disabled", true);
	
	//Validate the formData really fast. 
	let validForm = validateFormData();
	if(validForm){
		$.ajax({
			type:  "POST",
			url:   "ajax_processing.php?action=submitCost",
			cache: false,
			processData: false,
			contentType: false,
			data: formData,
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
	} else {
		$("#submitCost").removeAttr("disabled");
	}
}

function calcPriceTaxIncluded(priceTaxExcluded, taxRate) {
	//We're taking in numeric inputs from the HTML form. Confirm that they are numeric (or convert them if they aren't) and calculate.
	//priceTaxExcluded is the strict amounts (up to the hundredth; the input can be it'd be like 100.00
	let priceTaxExcludedFloat = Number.parseFloat(priceTaxExcluded);
	priceTaxExcludedFloat = priceTaxExcludedFloat.toFixed(2); //Ensures if someone erroneously types more than .00 it rounds, at least for this calculation.
	//Tax rate is by percentage point up to the hundredth (so it's input as 1.05 (%) but is literally .0105).
	let taxRateFloat = Number.parseFloat(taxRate);
	taxRateFloat = taxRateFloat.toFixed(2);

	let calculatedTaxPercentage = 1+(taxRateFloat / 100); //We'll be adding this back into the value that's being multiplied for tax purposes, so just save a step and add 1(00%).
	let priceWithTax = (priceTaxExcludedFloat * calculatedTaxPercentage);
	priceWithTax = priceWithTax.toFixed(2);
	console.log(priceWithTax);
    return priceWithTax;
}
function addError(errorObject, errorMsg){
	if(!errorObject){errorObject = [];}
	errorObject.push(errorMsg);
	return errorObject;
}

function integerLength(integer = 0){
	let string = (integer == null) ? "" : integer.toString();
	let length = string.length;
	return length; 
}
//Validation Functions. The overarching validation function is at the top, followed by each sub-validation function in alphabetical order.
function validateFormData()
{
	let form = $('.newPaymentTable');
	let backupTR = $('.newPaymentTR').clone(true); //Clone the template row. Use True to include events.
	form.find('.newPaymentTR')[0].remove(); //Remove the template row so that it isn't used to validate anything.
	let errorDiv = form.find('.div_errorPayment');
	errorDiv.html(''); //Reset the error Div.
	let errorList = {}; //Create an empty Error List object.

	//Get all the Year entries of the form.
	let years = form.find('.year');
	years.each(function(i) {
		if(!validateYear($(this).val())){
			errorList[i] = addError(errorList[i], 'Invalid Year Provided');
		}
	});

	//Validate the Subscription Start and End Dates.
	let subStart = form.find('.subscriptionStartDate');
	subStart.each(function(i){
		if(!validateSubDate($(this).val())){
			errorList[i] = addError(errorList[i], 'Invalid Subscription Start Dates Provided');
		}
	});

	let subEnd = form.find('.subscriptionEndDate');
	subEnd.each(function(i){
		if(!validateSubDate($(this).val())){
			errorList[i] = addError(errorList[i], 'Invalid Subscription End Dates Provided');
		}
	});

	//Validate the funds provided.
	let funds = form.find('.fundID');
	funds.each(function(i){
		let value = $(this).val();
		if(!validateFund(value)){
			errorList[i] = addError(errorList[i], 'Invalid Fund Code Provided in Row');
		}
		//Each line needs either a Fund or a Payment; we're just going to do a quick check to make sure it has one or the other if fundCode isBlank.
		if(value == ''){
			//It's blank; make sure there's a payment listed.
			let parentTR = $(this).parents('tr');
			let payment = parentTR.find('.paymentAmount');
			if(payment.val() == ''){
				errorList[i] = addError(errorList[i], 'A fund or payment amount is required for each row. Neither found');
			}
		}
	});

	//Validate the Tax and Payment fields.
	let taxExcludes = form.find('.priceTaxExcluded');
	taxExcludes.each(function(i){
		if(!validatePayment($(this).val())){
			errorList[i] = addError(errorList[i], 'Invalid Tax Excludes Value Provided');
		}
	});

	let taxRate = form.find('.taxrate');
	taxRate.each(function(i){
		if(!validatePayment($(this).val(), true)){
			errorList[i] = addError(errorList[i], 'Invalid Tax Rate Value Provided');
		}
	});

	let taxIncludes = form.find('.priceTaxIncluded');
	taxIncludes.each(function(i){
		if(!validatePayment($(this).val())){
			errorList[i] = addError(errorList[i], 'Invalid Tax Includes Value Provided');
		}
	});

	let paymentAmount = form.find('.paymentAmount');
	paymentAmount.each(function(i){
		if(!validatePayment($(this).val())){
			errorList[i] = addError(errorList[i], 'Invalid Payment Amount Value Provided');
		}
	});

	//Validate the Currency field.
	let currencyTypes = form.find('.currencyCode');
	currencyTypes.each(function(i){
		if(!validateCurrency($(this).val())){
			errorList[i] = addError(errorList[i], 'Invalid Currency Type Provided');
		}
	});

	//Validate the Payment Type field.
	let paymentTypes = form.find('.orderTypeID');
	paymentTypes.each(function(i){
		if(!validatePaymentTypes($(this).val())){
			errorList[i] = addError(errorList[i], 'Invalid Payment Type Provided');
		}
	});

	//Validate the Cost Details Select.
	let costDetailsIDs = form.find('.costDetailsID');
	costDetailsIDs.each(function(i){
		if(!validateCostDetails($(this).val())){
			errorList[i] = addError(errorList[i], 'Invalid Cost Type Provided');
		}
	});

	//Validate the Note.
	let costNotes = form.find('.costNote');
	costNotes.each(function(i){
		if(!validateCostNotes($(this).val())){
			errorList[i] = addError(errorList[i], 'Invalid Cost Note Provided');
		}
	});

	//Validate the Invoice.
	let invoiceField = form.find('.invoiceNum');
	invoiceField.each(function(i){
		if(!validateInvoice($(this).val())){
			errorList[i] = addError(errorList[i], 'Invalid Invoice Field Provided');
		}
	});

	//Everything validated - let's make sure that we have a value in the Payment Amount OR the Fund Selection.

	//Did we find any Errors?
	let validatedResult = (Object.keys(errorList).length == 0);
	if(!validatedResult){
		console.log('Errors Found');
		console.log(validatedResult);
		//Indeed we did.
		let errorOutputHTML = "";
		for (const [key, array] of Object.entries(errorList)) {
			let rowNum = parseInt(key)+1;
			errorOutputHTML += "Row "+rowNum+" Errors:<br>"+array.join("<br>")+"<hr>";
		}
		errorDiv.html(errorOutputHTML); //Present the Error Messages.
	}
	//Return the Template row to the top.
	$('#costHistoryBody').prepend(backupTR);

	return validatedResult;
}

function validateCostDetails(costCode = 0){
	let intLength = integerLength(costCode);
	let underMaxLength = (intLength <= 11);
	let isInteger = (validateNumber(costCode));
	let isBlank = costCode == '';
	let inList = (validatedCostDetails.includes(costCode) || isBlank);
	return (underMaxLength && isInteger && inList);
}

function validateCostNotes(costNote = ""){
	let isAString = validateString(costNote);
	let underMaxLength = (costNote.length <= 65535);
	return (isAString && underMaxLength);
}

function validateCurrency(currencyCode = ""){
	let isAString = validateString(currencyCode);
	let underMaxLength = (currencyCode.length <= 3);
	let inList = (validatedCurrencies.includes(currencyCode));
	return (isAString && inList && underMaxLength);
}

function validateFund(fundCode = 0){
	//We have a list of unarchived fund codes from the getCostForm.php script that we can use to confirm the provided fund code is valid. fundID is also an int(10) field.
	let intLength = integerLength(fundCode);
	let underMaxLength = (intLength <= 10);
	let isInteger = (validateNumber(fundCode));
	let isBlank = fundCode == '';
	let inList = (validatedFundIDs.includes(fundCode) || isBlank);
	return (underMaxLength && isInteger && inList);
}

function validateInvoice(invoice = ""){
	let isAString = validateString(invoice);
	let underMaxLength = (invoice.length <= 20);
	return (isAString && underMaxLength);
}

function validateNumber(integer = 0){
	return (!Number.isNaN(integer));
}

function validatePayment(payment = "", positiveOnly = false){
	//Confirm that the value is within the range allowable by MySQL.
	let minimum = (positiveOnly) ? 0 : -2147483648;
	let maximum = (positiveOnly) ? 4294967295 : 2147483647;
	let inRange = (minimum <= payment <= maximum);
	let isAnAmount = isAmount(payment);
	return (inRange && isAnAmount);
}

function validatePaymentTypes(paymentType = 0){
	let intLength = integerLength(paymentType);
	let underMaxLength = (intLength <= 10);
	let isInteger = (validateNumber(paymentType));
	let inList = (validatedOrderTypes.includes(paymentType));
	return (underMaxLength && isInteger && inList);
}

function validateString(string = ""){
	return (typeof string === 'string');
}

function validateSubDate(subDate = ""){
	//Both the subscription end and start dates are date fields in MySQL. These are strings and we're basically testing to see if it can create a Javascript Date (which the default HTML output should be able to do) before passing it along.
	let isAString = validateString(subDate);
	let canCreateADate = (!isNaN((new Date(subDate)).getTime()));
	return (isAString && canCreateADate);
}

function validateYear(year = ""){
	//In the SQL database a year is a VARCHAR(20) field. It should be a string and no more than 20 characters.
	let underMaxLength = (year.length <= 20);
	let isAString = validateString(year);
	return (underMaxLength && isAString);
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
