<?php
$resourceID = $_GET['resourceID'];
$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));

$resourceAcquisitionID = $_GET['resourceAcquisitionID'];
$resourceAcquisition = new ResourceAcquisition(new NamedArguments(array('primaryKey' => $resourceAcquisitionID)));


//Determine whether the Enhanced Cost History form is used.
$config = new Configuration();
$enhancedCostFlag = ($config->settings->enhancedCostHistory == 'Y') ? 1 : 0;

//get all currency for output in drop down
$currencyArray = array();
$currencyObj = new Currency();
$currencyArray = $currencyObj->allAsArray();
$validatedCurrencies = array_column($currencyArray, "currencyCode");

//get all Order Types for output in drop down
$orderTypeArray = array();
$orderTypeObj = new OrderType();
$orderTypeArray = $orderTypeObj->allAsArray();
$validatedOrderTypes = array_column($orderTypeArray, 'orderTypeID');


//get all Cost Details for output in drop down
$costDetailsArray = array();
$costDetailsObj = new CostDetails();
$costDetailsArray = $costDetailsObj->allAsArray();
$validatedCostDetails = array_column($costDetailsArray, 'costDetailsID');

//get the funds for output in drop down
$validatedFundIDs = array();
$FundType = new Fund();
$fundListArray = $FundType->getUnArchivedFunds();
$validatedFundIDs = array_column($fundListArray, 'fundID');

//get payments
$sanitizedInstance = array();
$instance = new ResourcePayment();
$paymentArray = array();
//Four attributes are taking in different values than what we actually want in the form. priceTaxExcluded, taxRate, priceTaxIncluded, and paymentAmount are all taking in integers that are essentially to the hundredth (to use USD as an example, the Database is holding the amount in pennies but we're displaying dollar amounts). These will need to be divided by 100 to provide an accurate value to the UI.
$divisionAttributes = ['priceTaxExcluded', 'taxRate', 'priceTaxIncluded', 'paymentAmount'];
foreach ($resourceAcquisition->getResourcePayments() as $instance)
{
	foreach (array_keys($instance->attributeNames) as $attributeName)
	{
		$divideAttribute = (in_array($attributeName, $divisionAttributes));
		$sanitizedInstance[$attributeName] = ($divideAttribute) ? integer_to_cost($instance->$attributeName) : $instance->$attributeName;
	}
	$sanitizedInstance[$instance->primaryKeyName] = $instance->primaryKey;
	array_push($paymentArray, $sanitizedInstance);
}

function buildValidationList($phpArray, $variableName){
	$json = json_encode($phpArray);
	//The paymentRows value requires an exception because we always want to update it rather than keep it constant.
	$updatedOutput = ($variableName == 'paymentRows') ? $json : $variableName;
	return "{$variableName} = (typeof {$variableName} == 'undefined') ? {$json} : {$updatedOutput};";
}

?>
		<script type="text/javascript">
			<?php   

			?>
			<?php echo buildValidationList($paymentArray, 'paymentRows'); ?>
			<?php echo buildValidationList($validatedFundIDs, 'validatedFundIDs'); ?>
			<?php echo buildValidationList($validatedCurrencies, 'validatedCurrencies'); ?>
			<?php echo buildValidationList($validatedOrderTypes, 'validatedOrderTypes'); ?>
			<?php echo buildValidationList($validatedCostDetails, 'validatedCostDetails'); ?>
		</script>
		<div id='div_resourceForm'>
		<form id='resourceForm' class="large">
		<input type='hidden' name='editResourceID' id='editResourceID' value='<?php echo $resourceID; ?>'>
		<input type='hidden' name='editResourceAcquisitionID' id='editResourceAcquisitionID' value='<?php echo $resourceAcquisitionID; ?>'>

		<div class='formTitle'><h2 class='headerText'><?php echo _("Edit Cost Information");?></h2></div>

		<span class='error' id='span_errors'></span>

			<h3><?php echo _("Cost History");?></h3>
			<div class='error div_errorPayment'></div>
			<table class='newPaymentTable'>
					<thead>
						<tr>
							<?php if ($enhancedCostFlag){ ?>
							<th scope="col" id="year"><?php echo _("Year");?></th>
							<th scope="col" id="substart"><?php echo _("Sub Start");?></th>
							<th scope="col" id="subend"><?php echo _("Sub End");?></th>
							<?php } ?>
							<th scope="col" id="fund"><?php echo _("Fund");?></th>
							<?php if ($enhancedCostFlag){ ?>
							<th scope="col" id="taxexcl"><?php echo _("Tax Excl.");?></th>
							<th scope="col" id="taxrate"><?php echo _("Tax Rate (%)");?></th>
							<th scope="col" id="taxincl"><?php echo _("Tax Incl.");?></th>
							<?php } ?>
							<th scope="col" id="payment"><?php echo _("Payment Amount");?></th>
							<th scope="col" id="currency"><?php echo _("Currency");?></th>
							<th scope="col" id="paymentType"><?php echo _("Type");?></th>
							<?php if ($enhancedCostFlag){ ?>
							<th scope="col" id="costDetails"><?php echo _("Cost Details");?></th>
							<?php } ?>
							<th scope="col" id="paymentNote"><?php echo _("Note");?></th>
							<?php if ($enhancedCostFlag){ ?>
							<th scope="col" id="invoice"><?php echo _("Invoice");?></th>
							<?php } ?>
							<th scope="col"><?php echo _("Delete"); ?></th>
						</tr>
					</thead>
					<tbody id="costHistoryBody">
						<tr class='newPaymentTR' hidden>
							<?php if ($enhancedCostFlag){ ?>
							<td>
								<input type='text' maxlength="20" value='' aria-labelledby='year' name='payment[action][id][year]' class='changeDefaultWhite changeInput year costHistoryYear' />
							</td>
							<td>
								<input type='date' value='' aria-labelledby='substart' name='payment[action][id][subscriptionStartDate]' class='date-pick changeDefaultWhite changeInput subscriptionStartDate costHistorySubStart'/>
							</td>
							<td>
								<input type='date' value='' aria-labelledby='subend' name='payment[action][id][subscriptionEndDate]' class='date-pick changeDefaultWhite changeInput subscriptionEndDate costHistorySubEnd'/>
							</td>
							<?php } ?>
							<td>
								<select aria-labelledby='fund' name='payment[action][id][fundID]' class='changeDefaultWhite changeInput fundID costHistoryFund' id='searchFundID'>
									<option value='' selected></option>
									<?php
										foreach($fundListArray as $fund)
										{
											$fundCode = $fund['fundCode'];
											$shortName = $fund['shortName'];
											$fundID = $fund['fundID'];
											$fundCodeLength = strlen($fundCode) + 3; //Maxing out at 50 characters - the 3 helps account for the space and two brackets.
											$shortNameLength = strlen($shortName);
											$combinedLength = $shortNameLength + $fundCodeLength;
											$maxLength = 49-$fundCodeLength; //Since substr starts from 0, 50 characters ends at 49.
											$shortenedName = substr($shortName,0,$maxLength);
											//Set the fund name to either be the whole shortName or the shortened name with an ellipses.
											$fundName = ($combinedLength <= 50) ? $shortName : "{$shortenedName}&hellip;";
											echo "<option value='{$fundID}'>{$fundName} [{$fundCode}]</option>";
										}
									?>
								</select>
							</td>
							<?php if ($enhancedCostFlag){ ?>
						    <td>
								<input type='number' value='' step=".01" aria-labelledby='taxexcl' name='payment[action][id][priceTaxExcluded]' class='changeDefaultWhite changeInput priceTaxExcluded' />
							</td>
						    <td>
								<input type='number' value='' min="0" max="100" step=".01" aria-labelledby='taxrate' name='payment[action][id][taxRate]' class='changeDefaultWhite changeInput taxRate' />
							</td>
						    <td>
								<input type='number' value='' step=".01" aria-labelledby='taxincl' name='payment[action][id][priceTaxIncluded]' class='changeDefaultWhite changeInput priceTaxIncluded'/>
							</td>
							<?php } ?>
							<td>
								<input type='number' value='' step=".01" aria-labelledby='payment' name='payment[action][id][paymentAmount]' class='changeDefaultWhite changeInput paymentAmount costHistoryPayment'/>
							</td>
							<td>
								<select aria-labelledby='currency' name='payment[action][id][currencyCode]' class='changeSelect currencyCode costHistoryCurrency' required>
								<?php
									foreach ($currencyArray as $currency)
									{
										$code = $currency['currencyCode'];
										$selected = ($code == $config->settings->defaultCurrency) ? "selected" : "";
										echo "<option value='{$code}' class='changeSelect' {$selected}>{$code}</option>\n";
									}
								?>
								</select>
							</td>
							<td>
								<select aria-labelledby='paymentType' name='payment[action][id][orderTypeID]' class='changeSelect orderTypeID costHistoryType' required>
									<option value='' selected disabled></option>
									<?php
										foreach ($orderTypeArray as $orderType)
										{
											echo "<option value='" . $orderType['orderTypeID'] . "'>" . $orderType['shortName'] . "</option>\n";
										}
									?>
								</select>
							</td>
							<?php if ($enhancedCostFlag){ ?>
							<td>
								<select aria-labelledby='costDetails' name='payment[action][id][costDetailsID]' class='changeSelect costDetailsID costHistoryCostDetails'>
									<option value=''></option>
									<?php
										foreach ($costDetailsArray as $costDetails)
										{
											echo "<option value='" . $costDetails['costDetailsID'] . "'>" . $costDetails['shortName'] . "</option>\n";
										}
									?>
								</select>
							</td>
							<?php } ?>
							<td>
								<input type='text' value='' maxlength="65535" aria-labelledby='paymentNote' name='payment[action][id][costNote]' class='changeDefaultWhite changeInput costNote costHistoryNote' />
							</td>
							<?php if ($enhancedCostFlag){ ?>
							<td>
								<input type='text' value='' maxlength="20" aria-labelledby='invoice' name='payment[action][id][invoiceNum]' class='changeDefaultWhite changeInput invoiceNum costHistoryInvoice' />
							</td>
							<?php } ?>
							<td class='costHistoryAction actions'>
								<button type="button" class="btn remove">
									<img src='images/cross.gif' alt='remove this payment' title='remove this payment' class='remove' />
								</button>
							</td>
						</tr>
					</tbody>
						<tfoot>
							<tr>
								<td colspan="<?php if ($enhancedCostFlag) echo '11'; else echo '6'; ?>"><div class="error div_errorPayment"></div></td>
							</tr>
						</tfoot>
					</table>
					
		<p class='actions'>
			<input class='addPayment add-button' title='<?php echo _("add payment");?>' type='button' onclick="addPaymentRow()" value='<?php echo _("Add Payment Row");?>'/><br>
			<input type='button' value='<?php echo _("submit");?>' name='submitCost' id ='submitCost' class='submit-button primary'>
			<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='cancel-button secondary'>
		</p>
		<script type="text/javascript" src="js/forms/costForm.js?random=<?php echo rand(); ?>"></script>
