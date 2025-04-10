<?php
//Determine whether the Enhanced Cost History form is used and get the Default Currency.
$config = new Configuration();
$enhancedCostFlag = ($config->settings->enhancedCostHistory == 'Y');
$defaultCurrency = ($config->settings->defaultCurrency) ?? 'USD'; //Absolutely defaulting to USD just to provide a value. Feel free to change (or, better yet, set the setting in the configuration.ini file) if you do not want to default to USD. The Database cannot allow NULL values for this.

//Get the Resource Acquisition ID and Object.
$resourceAcquisitionID = ($_POST['editResourceAcquisitionID']) ?? FALSE;
$resourceAcquisition = new ResourceAcquisition(new NamedArguments(array('primaryKey' => $resourceAcquisitionID)));

//First we're going to update all preexisting rows tied to this resource, then we will insert all new rows.
//Get a list of the submitted rows that were preexisting (part of the update portion of the form). Then get a list of IDs (the keys for the form rows).
$updatedPaymentRows = ($_POST['payment']['update']) ?? FALSE;
$updatePaymentIDs = ($updatedPaymentRows) ? array_keys($updatedPaymentRows) : [];

//Get a list of all Resource Payments currently listed for the Resource.
$resourcePayments = $resourceAcquisition->getResourcePayments();
foreach($resourcePayments as $payment){
	//Grab the primary key of the current Resource Payment and check whether it's in the list of updated Keys or not.
	$primaryKey = $payment->primaryKey;
	$keyStillExists = in_array($primaryKey, $updatePaymentIDs);
	if($keyStillExists){
		//This key is still here and just needs to be updated with any new information. Get the formInfo and validate it.
		$formInfo = $updatedPaymentRows[$primaryKey];
		$validatedFormInfo = validateResourcePayment($formInfo);
		//We can now set the payment object with these values.
		foreach($validatedFormInfo as $property => $value){
			$payment->$property = $value;
		}
		//Now update the payment history row.
		try {
			$payment->save();
		} catch (Exception $e) {
			$errorMsg = $e->getMessage();
			echo "<span class='error'>{$errorMsg}</span>";
		}
	} else {
		//This key does not exist when the form was submitted. We are presuming that it means the row was deleted and should therefore be deleted in the database.
		$payment->delete();
	}
}

//All that's left are any new rows that were added. 
$newPaymentRows = ($_POST['payment']['new']) ?? FALSE;
//Check to see if new payment history rows exist and that there is a resourceAcquisitionID.
if(count($newPaymentRows)>0 && $resourceAcquisitionID){
	//We have new payment history rows.
	foreach($newPaymentRows as $paymentRow){
		$validatedFormData = validateResourcePayment($paymentRow);
		$resourcePayment = new ResourcePayment();
		$resourcePayment->resourceAcquisitionID = $resourceAcquisitionID;
		foreach($validatedFormData as $property => $value){
			$resourcePayment->$property = $value;
		}
		//Now insert the payment history row.
		try {
			$resourcePayment->save();
		} catch (Exception $e) {
			$errorMsg = $e->getMessage();
			echo "<span class='error'>{$errorMsg}</span>";
		}
	}
}
function dateValidation($dateString){
	//Validation of Date Strings stolen from Stack Overflow.
	$testDate = DateTime::createFromFormat("Y-m-d", $dateString);
	$parseableDate = ($testDate !== false);
	$noErrors = (!array_sum($testDate::getLastErrors()));
	$validDate = ($parseableDate && $noErrors);
	return $validDate;
}

function stringValidation($string, $numericString = FALSE){
	//Confirm a string exists, isn't blank, and is a string. Occasionally we're throwing Numeric Strings in here, so just also confirm they're a number (if we pass TRUE to that variable)
	$stringExists = isset($string);
	$stringHasCharacters = (trim($string) !== '');
	$stringIsString = is_string($string);
	$numberValidity = ($numericString) ? is_numeric($string) : TRUE;
	return ($stringExists && $stringHasCharacters && $stringIsString && $numberValidity);
}

//Validate a Resource Payment and return the completed Array. Putting this in a function because both new and updated Payments will do this.
function validateResourcePayment($resourceArray){
	//Get the global values set at the top of this script.
	global $enhancedCostFlag;
	global $defaultCurrency;
	//Preset all values to NULL.
	$output = [
		'year' => NULL,
		'subscriptionStartDate' => NULL,
		'subscriptionEndDate' => NULL,
		'fundID' => NULL,
		'priceTaxExcluded' => NULL,
		'taxRate' => NULL,
		'priceTaxIncluded' => NULL,
		'paymentAmount' => NULL,
		'currencyCode' => NULL,
		'orderTypeID' => NULL,
		'costDetailsID' => NULL,
		'costNote' => NULL,
		'invoiceNum' => NULL,
	];

	//Set the fundID if it's available and not blank. It should be an integer according to the Database.
	$output['fundID'] = (stringValidation($resourceArray['fundID'], TRUE)) ? intval($resourceArray['fundID']) : NULL;

	//Set the paymentAmount if it's available and not blank. It should be an integer according to the Database. The input allows decimals (to the hundredth) so we should multiply by 100 since the Database is essentially counting to the hundredth (as an integer).
	$output['paymentAmount'] = (stringValidation($resourceArray['paymentAmount'], TRUE)) ? intval($resourceArray['paymentAmount'])*100 : NULL;
	
	//Set the currencyCode value if it's available and not blank. This value cannot be NULL, so set it to the Resource Module's Default Currency 
	$output['currencyCode'] = (stringValidation($resourceArray['currencyCode'])) ? $resourceArray['currencyCode'] : $defaultCurrency;

	//Set the orderTypeID if it's available and not blank. It should be an integer according to the Database.
	$output['orderTypeID'] = (stringValidation($resourceArray['orderTypeID'], TRUE)) ? intval($resourceArray['orderTypeID']) : NULL;
	
	//Set the costNote if it's available and not blank.
	$output['costNote'] = (stringValidation($resourceArray['costNote'])) ? $resourceArray['costNote'] : NULL;

	
	//Now set any of the Enhanced values (if enhancedCost is activated).
	if($enhancedCostFlag){
		//Set the year if it's available and not blank.
		$output['year'] = (stringValidation($resourceArray['year'])) ? $resourceArray['year'] : NULL;

		//Set the start and end dates so long as they're valid dates. 
		$output['subscriptionStartDate'] = (dateValidation($resourceArray['subscriptionStartDate'])) ? $resourceArray['subscriptionStartDate'] : NULL;
		$output['subscriptionEndDate'] = (dateValidation($resourceArray['subscriptionEndDate'])) ? $resourceArray['subscriptionEndDate'] : NULL;

		//Set the priceTaxExcluded, taxRate, priceTaxIncluded, and costDetailsID values if they're available and not blank. They should be integers according to the Database.
		$output['priceTaxExcluded'] = (stringValidation($resourceArray['priceTaxExcluded'], TRUE)) ? intval($resourceArray['priceTaxExcluded'])*100 : NULL;
		$output['taxRate'] = (stringValidation($resourceArray['taxRate'], TRUE)) ? intval($resourceArray['taxRate'])*100 : NULL;
		$output['priceTaxIncluded'] = (stringValidation($resourceArray['priceTaxIncluded'], TRUE)) ? intval($resourceArray['priceTaxIncluded'])*100 : NULL;
		$output['costDetailsID'] = (stringValidation($resourceArray['costDetailsID'], TRUE)) ? intval($resourceArray['costDetailsID']) : NULL;
		
		//Set the invoiceNum  if it's available and not blank.
		$output['invoiceNum'] = (stringValidation($resourceArray['invoiceNum'])) ? $resourceArray['invoiceNum'] : NULL;
	}
	
	return $output;

}
?>