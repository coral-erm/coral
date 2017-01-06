<?php
$resourceID = $_GET['resourceID'];
$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));

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
foreach ($resource->getResourcePayments() as $instance)
{
	foreach (array_keys($instance->attributeNames) as $attributeName)
	{
		$sanitizedInstance[$attributeName] = $instance->$attributeName;
	}
	$sanitizedInstance[$instance->primaryKeyName] = $instance->primaryKey;
	array_push($paymentArray, $sanitizedInstance);
}

// Table geometry is different if enhanced cost history is enabled
$baseWidth = 345;
$numCols = 6;
if ($enhancedCostFlag){
	$baseWidth += 688;
	$numCols += 8; // year, sub start, sub end, cost details, invoice num
}

?>

		<div id='div_resourceForm'>
		<form id='resourceForm'>
		<input type='hidden' name='editResourceID' id='editResourceID' value='<?php echo $resourceID; ?>'>

		<div class='formTitle' style='width:<?php echo $baseWidth + 46 ?>px; margin-bottom:5px;'><span class='headerText'><?php echo _("Edit Cost Information");?></span></div>

		<span class='smallDarkRedText' id='span_errors'></span>

		<table class='noBorder' style='width:<?php echo $baseWidth + 45 ?>px;'>
		<tr style='vertical-align:top;'>
		<td>
			<span class='surroundBoxTitle'>&nbsp;&nbsp;<label for='resourcePayments'><b><?php echo _("Cost History");?></b></label>&nbsp;&nbsp;</span>
			<table class='surroundBox' style='width:<?php echo $baseWidth - 65; ?>px;'>
			<tr>
			<td>
				<table class='newPaymentTable' style='margin:7px 15px 0 15px;'>
					<thead>
						<tr>
							<?php if ($enhancedCostFlag){ ?>
							<th><?php echo _("Year");?></th>
							<th><?php echo _("Sub Start");?></th>
							<th><?php echo _("Sub End");?></th>
							<?php } ?>
							<th><?php echo _("Fund");?></th>
							<?php if ($enhancedCostFlag){ ?>
							<th><?php echo _("Tax Excl.");?></th>
							<th><?php echo _("Tax Rate");?></th>
							<th><?php echo _("Tax Incl.");?></th>
							<?php } ?>
							<th><?php echo _("Payment");?></th>
							<th><?php echo _("Currency");?></th>
							<th><?php echo _("Type");?></th>
							<?php if ($enhancedCostFlag){ ?>
							<th><?php echo _("Cost Details");?></th>
							<?php } ?>
							<th><?php echo _("Note");?></th>
							<?php if ($enhancedCostFlag){ ?>
							<th><?php echo _("Invoice");?></th>
							<?php } ?>
							<th>&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<tr class='newPaymentTR'>
							<?php if ($enhancedCostFlag){ ?>
							<td>
								<input type='text' value='' class='changeDefaultWhite changeInput year costHistoryYear' />
							</td>
							<td>
								<input type='text' value='' class='date-pick changeDefaultWhite changeInput subscriptionStartDate costHistorySubStart' placeholder='mm/dd/yyyy' />
							</td>
							<td>
								<input type='text' value='' class='date-pick changeDefaultWhite changeInput subscriptionEndDate costHistorySubEnd' placeholder='mm/dd/yyyy' />
							</td>
							<?php } ?>
							<td>
								<select class='changeDefaultWhite changeInput fundID costHistoryFund' id='searchFundID'>
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
								<input type='text' value='' style='width:60px;' class='changeDefaultWhite changeInput priceTaxExcluded' />
							</td>
						    <td>
								<input type='text' value='' style='width:60px;' class='changeDefaultWhite changeInput taxRate' />
							</td>
						    <td>
								<input type='text' value='' style='width:60px;' class='changeDefaultWhite changeInput priceTaxIncluded' />
							</td>
							<?php } ?>
							<td>
								<input type='text' value='' class='changeDefaultWhite changeInput paymentAmount costHistoryPayment' />
							</td>
							<td>
								<select class='changeSelect currencyCode costHistoryCurrency'>
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
								<select class='changeSelect orderTypeID costHistoryType'>
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
								<select class='changeSelect costDetailsID costHistoryCostDetails'>
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
								<input type='text' value='' class='changeDefaultWhite changeInput costNote costHistoryNote' />
							</td>
							<?php if ($enhancedCostFlag){ ?>
							<td>
								<input type='text' value='' class='changeDefaultWhite changeInput invoiceNum costHistoryInvoice' />
							</td>
							<?php } ?>
							<td class='costHistoryAction'>
								<a href='javascript:void();'>
									<input class='addPayment add-button' title='<?php echo _("add payment");?>' type='button' value='<?php echo _("Add");?>'/>
								</a>
							</td>


						</tr>
						<tr>
							<td colspan='<?php echo $numCols; ?>'>
								<div class='smallDarkRedText div_errorPayment' style='margin:0px 20px 0px 26px;'>
								</div>
								<hr style='width:<?php echo $baseWidth; ?>px;margin:0px 0px 5px 5px;' />
							</td>
						</tr>
					</tbody>
				</table>
				<div class='paymentTableDiv'>
					<table class='paymentTable' style='margin:7px 15px 0 15px; max-height: 100px; overflow: auto;'>
						<tbody>
						<?php
							if (count($paymentArray) > 0){
								foreach ($paymentArray as $payment){
						?>
							<tr>
								<?php if ($enhancedCostFlag){ ?>
								<td>
									<input type='text' value='<?php echo $payment['year']; ?>' class='changeInput year costHistoryYear' />
								</td>
								<td>
									<input type='text' value='<?php echo normalize_date($payment['subscriptionStartDate']); ?>' class='date-pick changeInput subscriptionStartDate costHistorySubStart' placeholder="mm/dd/yyyy" />
								</td>
								<td>
									<input type='text' value='<?php echo normalize_date($payment['subscriptionEndDate']); ?>' class='date-pick changeInput subscriptionEndDate costHistorySubEnd' placeholder="mm/dd/yyyy" />
								</td>
								<?php } ?>
								<td>
									<select class='changeDefaultWhite changeInput fundID costHistoryFund' id='searchFundID'>
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
									<input type='text' value='<?php echo integer_to_cost($payment['priceTaxExcluded']); ?>' style='width:60px;' class='changeInput priceTaxExcluded' />
								</td>
						        <td>
									<input type='text' value='<?php echo integer_to_cost($payment['taxRate']); ?>' style='width:60px;' class='changeInput taxRate' />
								</td>
						        <td>
									<input type='text' value='<?php echo integer_to_cost($payment['priceTaxIncluded']); ?>' style='width:60px;' class='changeInput priceTaxIncluded' />
								</td>
								<?php } ?>
								<td>
									<input type='text' value='<?php echo integer_to_cost($payment['paymentAmount']); ?>' class='changeInput paymentAmount costHistoryPayment' />
								</td>
								<td>
									<select class='changeSelect currencyCode costHistoryCurrency'>
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
									<select class='changeSelect orderTypeID costHistoryType'>
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
									<select class='changeSelect costDetailsID costHistoryCostDetails'>
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
									<input type='text' value='<?php echo $payment['costNote']; ?>' class='changeInput costNote costHistoryNote' />
								</td>
								<?php if ($enhancedCostFlag){ ?>
								<td>
									<input type='text' value='<?php echo $payment['invoiceNum']; ?>' class='changeInput invoiceNum costHistoryInvoice' />
								</td>
								<?php } ?>
								<td class='costHistoryAction'>
									<a href='javascript:void();'>
										<img src='images/cross.gif' alt='remove this payment' title='remove this payment' class='remove' />
									</a>
								</td>
							</tr>
							<tr>
								<td colspan='<?php echo $numCols; ?>'>
									<div class='smallDarkRedText div_errorPayment' style='margin:0px 20px 0px 26px;'></div>
								</td>
							</tr>
						<tbody>

						<?php }} ?>
					</table>
				</div>
			</td>
			</tr>
		</table>
		</td>
		</tr>
			</table>

		</td>
		<td>

		&nbsp;

		</td>
		</tr>
		</table>


		<hr style='width:100%;margin:15px 0px 10px 0px;' />

		<table class='noBorderTable' style='width:125px;'>
			<tr>
				<td style='text-align:left'><input type='button' value='<?php echo _("submit");?>' name='submitCost' id ='submitCost' class='submit-button'></td>
				<td style='text-align:right'><input type='button' value='<?php echo _("cancel");?>' onclick="kill(); tb_remove();" class='cancel-button'></td>
			</tr>
		</table>


		<script type="text/javascript" src="js/forms/costForm.js?random=<?php echo rand(); ?>"></script>
