<?php
$resourceID = $_GET['resourceID'];
$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));

$resourceAcquisitionID = $_GET['resourceAcquisitionID'];
$resourceAcquisition = new ResourceAcquisition(new NamedArguments(array('primaryKey' => $resourceAcquisitionID)));


//used to get default currency
$config = new Configuration();
$enhancedCostFlag = ($config->settings->enhancedCostHistory == 'Y') ? 1 : 0;

//get all currency for output in drop down
$currencyArray = array();
$currencyObj = new Currency();
$currencyArray = $currencyObj->allAsArray();

//get all Order Types for output in drop down
$orderTypeArray = array();
$orderTypeObj = new OrderType();
$orderTypeArray = $orderTypeObj->allAsArray();

//get all Cost Details for output in drop down
$costDetailsArray = array();
$costDetailsObj = new CostDetails();
$costDetailsArray = $costDetailsObj->allAsArray();

//get payments
$sanitizedInstance = array();
$instance = new ResourcePayment();
$paymentArray = array();
foreach ($resourceAcquisition->getResourcePayments() as $instance)
{
	foreach (array_keys($instance->attributeNames) as $attributeName)
	{
		$sanitizedInstance[$attributeName] = $instance->$attributeName;
	}
	$sanitizedInstance[$instance->primaryKeyName] = $instance->primaryKey;
	array_push($paymentArray, $sanitizedInstance);
}

?>

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
							<th scope="col" id="taxrate"><?php echo _("Tax Rate");?></th>
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
							<th scope="col"><?php echo _("Action"); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr class='newPaymentTR'>
							<?php if ($enhancedCostFlag){ ?>
							<td>
								<input type='text' value='' aria-labelledby='year' class='changeDefaultWhite changeInput year costHistoryYear' />
							</td>
							<td>
								<input type='text' value='' aria-labelledby='substart' class='date-pick changeDefaultWhite changeInput subscriptionStartDate costHistorySubStart' placeholder='mm/dd/yyyy' />
							</td>
							<td>
								<input type='text' value='' aria-labelledby='subend' class='date-pick changeDefaultWhite changeInput subscriptionEndDate costHistorySubEnd' placeholder='mm/dd/yyyy' />
							</td>
							<?php } ?>
							<td>
								<select aria-labelledby='fund' class='changeDefaultWhite changeInput fundID costHistoryFund' id='searchFundID'>
									<option value='' selected></option>
									<?php
										$FundType = new Fund();
										foreach($FundType->getUnArchivedFunds() as $fund)
										{
											$fundCodeLength = strlen($fund['fundCode']) + 3;
											$combinedLength = strlen($fund['shortName']) + $fundCodeLength;
											$fundName = ($combinedLength <=50) ? $fund['shortName'] : substr($fund['shortName'],0,49-$fundCodeLength) . "&hellip;";
											$fundName .= " [" . $fund['fundCode'] . "]</option>";
											echo "<option value='" . $fund['fundID'] . "'>" . $fundName . "</option>";
										}
									?>
								</select>
							</td>
							<?php if ($enhancedCostFlag){ ?>
						    <td>
								<input type='text' value='' aria-labelledby='taxexcl' class='changeDefaultWhite changeInput priceTaxExcluded' />
							</td>
						    <td>
								<input type='text' value='' aria-labelledby='taxrate' class='changeDefaultWhite changeInput taxRate' />
							</td>
						    <td>
								<input type='text' value='' aria-labelledby='taxincl' class='changeDefaultWhite changeInput priceTaxIncluded' />
							</td>
							<?php } ?>
							<td>
								<input type='text' value='' aria-labelledby='payment' class='changeDefaultWhite changeInput paymentAmount costHistoryPayment' />
							</td>
							<td>
								<select aria-labelledby='currency' class='changeSelect currencyCode costHistoryCurrency'>
								<?php
									foreach ($currencyArray as $currency)
									{
										if ($currency['currencyCode'] == $config->settings->defaultCurrency)
										{
											echo "<option value='" . $currency['currencyCode'] . "' selected class='changeSelect'>" . $currency['currencyCode'] . "</option>\n";
										}
										else
										{
											echo "<option value='" . $currency['currencyCode'] . "' class='changeSelect'>" . $currency['currencyCode'] . "</option>\n";
										}
									}
								?>
								</select>
							</td>
							<td>
								<select aria-labelledby='paymentType' class='changeSelect orderTypeID costHistoryType'>
									<option value='' selected></option>
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
								<select aria-labelledby='costDetails' class='changeSelect costDetailsID costHistoryCostDetails'>
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
								<input type='text' value='' aria-labelledby='paymentNote' class='changeDefaultWhite changeInput costNote costHistoryNote' />
							</td>
							<?php if ($enhancedCostFlag){ ?>
							<td>
								<input type='text' value='' aria-labelledby='invoice' class='changeDefaultWhite changeInput invoiceNum costHistoryInvoice' />
							</td>
							<?php } ?>
							<td class='costHistoryAction'>
								<input class='addPayment add-button' title='<?php echo _("add payment");?>' type='button' value='<?php echo _("Add");?>'/>
							</td>


						</tr>
					
						<?php
							if (is_array($paymentArray) && count($paymentArray) > 0) {
								foreach ($paymentArray as $payment){
						?>
							<tr>
								<?php if ($enhancedCostFlag){ ?>
								<td>
									<input type='text' value='<?php echo $payment['year']; ?>' aria-labelledby='year' class='changeInput year costHistoryYear' />
								</td>
								<td>
									<input type='text' value='<?php echo normalize_date($payment['subscriptionStartDate']); ?>' aria-labelledby='substart' class='date-pick changeInput subscriptionStartDate costHistorySubStart' />
								</td>
								<td>
									<input type='text' value='<?php echo normalize_date($payment['subscriptionEndDate']); ?>' aria-labelledby='subend' class='date-pick changeInput subscriptionEndDate costHistorySubEnd' />
								</td>
								<?php } ?>
								<td>
									<select class='changeDefaultWhite changeInput fundID costHistoryFund' id='searchFundID' aria-labelledby='fund'>
										<option value=''></option>
										<?php
											$FundType = new Fund();
											$Funds = array();
											if (array_key_exists('fundID', $payment) && isset($payment['fundID']))
											{
												$Funds = $FundType->getUnArchivedFundsForCostHistory($payment['fundID']);
											}
											else
											{
												$Funds = $FundType->getUnArchivedFunds();
											}
											foreach($Funds as $fund)
											{
												$fundCodeLength = strlen($fund['fundCode']) + 3;
												$combinedLength = strlen($fund['shortName']) + $fundCodeLength;
												$fundName = ($combinedLength <=50) ? $fund['shortName'] : substr($fund['shortName'],0,49-$fundCodeLength) . "&hellip;";
												$fundName .= " [" . $fund['fundCode'] . "]</option>";
												echo "<option";
												if ($payment['fundID'] == $fund['fundID'])
												{
													echo " selected";
												}
												echo " value='" . $fund['fundID'] . "'>" . $fundName . "</option>";
											}
										?>
									</select>
								</td>
								<?php if ($enhancedCostFlag){ ?>
						        <td>
											<input type='text' value='<?php echo integer_to_cost($payment['priceTaxExcluded']); ?>' aria-labelledby='taxexcl' class='changeInput priceTaxExcluded' />
										</td>
						        <td>
											<input type='text' value='<?php echo integer_to_cost($payment['taxRate']); ?>' aria-labelledby='taxrate' class='changeInput taxRate' />
										</td>
						        <td>
											<input type='text' value='<?php echo integer_to_cost($payment['priceTaxIncluded']); ?>' aria-labelledby='taxincl' class='changeInput priceTaxIncluded' />
										</td>
								<?php } ?>
								<td>
									<input type='text' value='<?php echo integer_to_cost($payment['paymentAmount']); ?>' aria-labelledby='payment' class='changeInput paymentAmount costHistoryPayment' />
								</td>
								<td>
									<select aria-labelledby='currency' class='changeSelect currencyCode costHistoryCurrency'>
									<?php
										foreach ($currencyArray as $currency)
										{
											if ($currency['currencyCode'] == $payment['currencyCode'])
											{
												echo "<option value='" . $currency['currencyCode'] . "' selected class='changeSelect'>" . $currency['currencyCode'] . "</option>\n";
											}
											else
											{
												echo "<option value='" . $currency['currencyCode'] . "' class='changeSelect'>" . $currency['currencyCode'] . "</option>\n";
											}
										}
										?>
									</select>
								</td>
								<td>
									<select aria-labelledby='paymentType' class='changeSelect orderTypeID costHistoryType'>
										<option value=''></option>
										<?php
											foreach ($orderTypeArray as $orderType)
											{
												if (!(trim(strval($orderType['orderTypeID'])) != trim(strval($payment['orderTypeID']))))
												{
													echo "<option value='" . $orderType['orderTypeID'] . "' selected class='changeSelect'>" . $orderType['shortName'] . "</option>\n";
												}
												else
												{
													echo "<option value='" . $orderType['orderTypeID'] . "' class='changeSelect'>" . $orderType['shortName'] . "</option>\n";
												}
											}
										?>
									</select>
								</td>
								<?php if ($enhancedCostFlag){ ?>
								<td>
									<select aria-labelledby='costDetails' class='changeSelect costDetailsID costHistoryCostDetails'>
										<option value=''></option>
										<?php
											foreach ($costDetailsArray as $costDetails)
											{
												if (trim(strval($costDetails['costDetailsID'])) == trim(strval($payment['costDetailsID'])))
												{
													echo "<option value='" . $costDetails['costDetailsID'] . "' selected class='changeSelect'>" . $costDetails['shortName'] . "</option>\n";
												}
												else
												{
													echo "<option value='" . $costDetails['costDetailsID'] . "' class='changeSelect'>" . $costDetails['shortName'] . "</option>\n";
												}
											}
										?>
									</select>
								</td>
								<?php } ?>
								<td>
									<input type='text' value='<?php echo $payment['costNote']; ?>' aria-labelledby='paymentNote' class='changeInput costNote costHistoryNote' />
								</td>
								<?php if ($enhancedCostFlag){ ?>
								<td>
									<input type='text' value='<?php echo $payment['invoiceNum']; ?>' aria-labelledby='invoice' class='changeInput invoiceNum costHistoryInvoice' />
								</td>
								<?php } ?>
								<td class='costHistoryAction actions'>
									<button type="button" class="btn remove">
										<img src='images/cross.gif' alt='remove this payment' title='remove this payment' class='remove' />
									</button>
								</td>
							</tr>
						<tbody>

						<?php }} ?>
						<tfoot>
							<tr>
								<td colspan="<?php if ($enhancedCostFlag) echo '11'; else echo '6'; ?>"><div class="error div_errorPayment"></div></td>
							</tr>
						</tfoot>
					</table>
					
		<p class='actions'>
			<input type='submit' value='<?php echo _("submit");?>' name='submitCost' id ='submitCost' class='submit-button primary'>
			<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='cancel-button secondary'>
		</p>
		<script type="text/javascript" src="js/forms/costForm.js?random=<?php echo rand(); ?>"></script>
