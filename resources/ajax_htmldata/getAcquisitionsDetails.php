<?php
	$config = new Configuration();
	$enhancedCostFlag = ((isset($config->settings->enhancedCostHistory)) && (strtoupper($config->settings->enhancedCostHistory) == 'Y')) ? 1 : 0;
	$enhancedCostFlag = (strtoupper($config->settings->enhancedCostHistory) == 'Y') ? 1 : 0;
	if ($enhancedCostFlag){
		$numCols = 12;
		$tableWidth = 760;
		$formWidth = 1084;
                ?>
		<!-- Hide the helpful links, etc. -->
        	<script>
			$('#div_fullRightPanel').hide(); 
		</script>
                <?php
	}else{
		$numCols = 4;
		$tableWidth = 646;
		$formWidth = 564;
	}

	$resourceID = $_GET['resourceID'];
	$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));

	$orderType = new OrderType(new NamedArguments(array('primaryKey' => $resource->orderTypeID)));
		$acquisitionType = new AcquisitionType(new NamedArguments(array('primaryKey' => $resource->acquisitionTypeID)));

		//get purchase sites
		$sanitizedInstance = array();
		$instance = new PurchaseSite();
		$purchaseSiteArray = array();
		foreach ($resource->getResourcePurchaseSites() as $instance) {
			$purchaseSiteArray[]=$instance->shortName;
		}

		//get payments
		$sanitizedInstance = array();
		$instance = new ResourcePayment();
		$paymentArray = array();
		foreach ($resource->getResourcePayments() as $instance) {
				foreach (array_keys($instance->attributeNames) as $attributeName) {
					$sanitizedInstance[$attributeName] = $instance->$attributeName;
				}

				$sanitizedInstance[$instance->primaryKeyName] = $instance->primaryKey;

				$selector = new User(new NamedArguments(array('primaryKey' => $instance->selectorLoginID)));
				$sanitizedInstance['selectorName'] = $selector->firstName . " " . $selector->lastName;

				$orderType = new OrderType(new NamedArguments(array('primaryKey' => $instance->orderTypeID)));
				$sanitizedInstance['orderType'] = $orderType->shortName;

				$costDetails = new CostDetails(new NamedArguments(array('primaryKey' => $instance->costDetailsID)));
				$sanitizedInstance['costDetails'] = $costDetails->shortName;
				if ($enhancedCostFlag && 0){
					$sanitizedInstance['amountChange'] = $instance->getPaymentAmountChangeFromPreviousYear();
				}

				array_push($paymentArray, $sanitizedInstance);

		}


		//get license statuses
		$sanitizedInstance = array();
		$instance = new ResourceLicenseStatus();
		$licenseStatusArray = array();
		foreach ($resource->getResourceLicenseStatuses() as $instance) {
				foreach (array_keys($instance->attributeNames) as $attributeName) {
					$sanitizedInstance[$attributeName] = $instance->$attributeName;
				}

				$sanitizedInstance[$instance->primaryKeyName] = $instance->primaryKey;

				$changeUser = new User(new NamedArguments(array('primaryKey' => $instance->licenseStatusChangeLoginID)));
				if (($changeUser->firstName) || ($changeUser->lastName)) {
					$sanitizedInstance['changeName'] = $changeUser->firstName . " " . $changeUser->lastName;
				}else{
					$sanitizedInstance['changeName'] = $instance->licenseStatusChangeLoginID;
				}

				$licenseStatus = new LicenseStatus(new NamedArguments(array('primaryKey' => $instance->licenseStatusID)));
				$sanitizedInstance['licenseStatus'] = $licenseStatus->shortName;


				array_push($licenseStatusArray, $sanitizedInstance);

		}



		//get licenses (already returned in array)
		$licenseArray = $resource->getLicenseArray();

?>
			<table class='linedFormTable' style='width:<?php echo $tableWidth; ?>px;padding:0x;margin:0px;height:100%;'>
			<tr>
			<th colspan='2' style='vertical-align:bottom;'>
			<span style='float:left;vertical-align:bottom;'><?php echo _("Order");?></span>
			<?php if ($user->canEdit()){ ?>
				<span style='float:right;vertical-align:bottom;'><a href='ajax_forms.php?action=getOrderForm&height=400&width=440&modal=true&resourceID=<?php echo $resourceID; ?>' class='thickbox' id='editOrder'><img src='images/edit.gif' alt='<?php echo _("edit");?>' title='<?php echo _("edit order information");?>'></a></span>
			<?php } ?>

			</th>
			</tr>

			<?php if ($resource->acquisitionTypeID) { ?>
				<tr>
				<td style='vertical-align:top;width:110px;'><?php echo _("Acquisition Type:");?></td>
				<td style='width:350px;'><?php echo $acquisitionType->shortName; ?></td>
				</tr>
			<?php } ?>

			<?php if ($resource->orderNumber) { ?>
				<tr>
				<td style='vertical-align:top;width:110px;'><?php echo _("Order Number:");?></td>
				<td style='width:350px;'><?php echo $resource->orderNumber; ?></td>
				</tr>
			<?php } ?>

			<?php if ($resource->systemNumber) { ?>
				<tr>
				<td style='vertical-align:top;width:110px;'><?php echo _("System Number:");?></td>
				<td style='width:350px;'>
				<?php
					echo $resource->systemNumber;
					if ($config->settings->catalogURL != ''){
						echo "&nbsp;&nbsp;<a href='" . $config->settings->catalogURL . $resource->systemNumber . "' target='_blank'>"._("catalog view")."</a>";
					}
				?>
				</td>
				</tr>
			<?php } ?>

			<?php if (count($purchaseSiteArray) > 0) { ?>
				<tr>
				<td style='vertical-align:top;width:110px;'><?php echo _("Purchasing Sites:");?></td>
				<td style='width:350px;'><?php echo implode(", ", $purchaseSiteArray); ?></td>
				</tr>
			<?php } ?>

			<?php if (($resource->currentStartDate) && ($resource->currentStartDate != '0000-00-00')) { ?>
			<tr>
			<td style='vertical-align:top;width:110px;'><?php echo _("Sub Start:");?></td>
			<td style='width:350px;'><?php echo format_date($resource->currentStartDate); ?></td>
			</tr>
			<?php } ?>

			<?php if (($resource->currentEndDate) && ($resource->currentEndDate != '0000-00-00')) { ?>
			<tr>
			<td style='vertical-align:top;width:110px;'>Current Sub End:</td>
			<td style='width:350px;'><?php echo format_date($resource->currentEndDate); ?>&nbsp;&nbsp;
			<?php if ($resource->subscriptionAlertEnabledInd == "1") { echo "<i>"._("Expiration Alert Enabled")."</i>"; } ?>
			</td>
			</tr>
			<?php } ?>

			</table>
			<?php if ($user->canEdit()){ ?>
				<a href='ajax_forms.php?action=getOrderForm&height=400&width=440&modal=true&resourceID=<?php echo $resourceID; ?>' class='thickbox'><?php echo _("edit order information");?></a>
			<?php } ?>
			<br />
			<br />
			<br />

			<table class='linedFormTable formTable' style='width:<?php echo $tableWidth; ?>px;margin-bottom:5px;'>
<thead>
			<tr>
			<th colspan='<?php echo $numCols; ?>' style='vertical-align:bottom;'>
			<span style='float:left;vertical-align:bottom;'><?php echo _("Cost History");?></span>
			<?php if ($user->canEdit()){ ?>
				<span style='float:right;vertical-align:bottom;'><a href='ajax_forms.php?action=getCostForm&height=400&width=<?php echo $formWidth; ?>&modal=true&resourceID=<?php echo $resourceID; ?>' class='thickbox' id='editCost'><img src='images/edit.gif' alt='<?php echo _("edit");?>' title='<?php echo _("edit cost history");?>'></a></span>
			<?php } ?>

			</th>
			</tr>
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
		<?php if ($enhancedCostFlag && 0){ ?>
			<th style='text-align: right'>%</th>
		<?php } ?>
			<th><?php echo _("Type");?></th>
		<?php if ($enhancedCostFlag){ ?>
			<th><?php echo _("Details");?></th>
		<?php } ?>
			<th><?php echo _("Notes");?></th>
		<?php if ($enhancedCostFlag){ ?>
			<th><?php echo _("Invoice");?></th>
		<?php } ?>
			</tr>
</thead>

<tbody>
			<?php
			if (count($paymentArray) > 0){
				$i=0;
				foreach ($paymentArray as $payment){
				$i++;
				if ($i % 2 == 0){
					$classAdd="class='alt'";
				}else{
					$classAdd="";
				}
				$year = $payment['year'] ? $payment['year'] : "&nbsp;";
				$subStart = $payment['subscriptionStartDate'] ? normalize_date($payment['subscriptionStartDate']) : "&nbsp;";
				$subEnd = $payment['subscriptionEndDate'] ? normalize_date($payment['subscriptionEndDate']) : "&nbsp;";
				$fundName = $payment['fundName'] ? $payment['fundName'] : "&nbsp;";
                $taxRate = $payment['taxRate'] ? integer_to_cost($payment['taxRate']) . '&nbsp;%' : "&nbsp;";
                foreach (Array('priceTaxExcluded', 'priceTaxIncluded', 'paymentAmount') as $amount) { 
                  if (integer_to_cost($payment[$amount])){
                    $cost[$amount] = $payment['currencyCode'] . " " . integer_to_cost($payment[$amount]);
                  }else{
                    $cost[$amount] = "&nbsp;";
                  }
                }
				$costDetails = $payment['costDetails'] ? $payment['costDetails'] : "&nbsp;";
				$costNote = $payment['costNote'] ? $payment['costNote'] : "&nbsp;";
				$invoiceNum = $payment['invoiceNum'] ? $payment['invoiceNum'] : "&nbsp;";

				?>
				<tr>
			<?php if ($enhancedCostFlag){ ?>
				<td <?php echo $classAdd;?> ><?php echo $year; ?></td>
				<td <?php echo $classAdd;?> ><?php echo $subStart; ?></td>
				<td <?php echo $classAdd;?> ><?php echo $subEnd; ?></td>
			<?php } ?>
				<td <?php echo $classAdd;?>><?php echo $fundName; ?></td>
			<?php if ($enhancedCostFlag && 0){ ?>
				<td <?php echo $classAdd;?> style='text-align: right'><?php echo $payment['amountChange']; ?></td>
            <?php } ?>
            <?php if ($enhancedCostFlag){ ?>
				<td <?php echo $classAdd;?>><?php echo $cost['priceTaxExcluded']; ?></td>
                <td <?php echo $classAdd;?>><?php echo $taxRate; ?></td>
				<td <?php echo $classAdd;?>><?php echo $cost['priceTaxIncluded']; ?></td>
            <?php } ?>
				<td <?php echo $classAdd;?>><?php echo $cost['paymentAmount']; ?></td>
				<td <?php echo $classAdd;?>><?php echo $payment['orderType']; ?></td>
			<?php if ($enhancedCostFlag){ ?>
				<td <?php echo $classAdd;?> ><?php echo $costDetails; ?></td>
			<?php } ?>
				<td <?php echo $classAdd;?> ><?php echo $costNote; ?></td>
			<?php if ($enhancedCostFlag){ ?>
				<td <?php echo $classAdd;?> ><?php echo $invoiceNum; ?></td>
			<?php } ?>
				</tr>

				<?php
				}
			}else{
				echo "<tr><td colspan='" . $numCols . "'><i>"._("No payment information available").".</i></td></tr>";
			}
			?>
</tbody>
			</table>
			<?php if ($user->canEdit()){ ?>
				<a href='ajax_forms.php?action=getCostForm&height=400&width=<?php echo $formWidth; ?>&modal=true&resourceID=<?php echo $resourceID; ?>' class='thickbox'><?php echo _("edit cost history");?></a>
			<?php } ?>
			<br />
			<br />
			<br />

			<table class='linedFormTable' style='width:<?php echo $tableWidth; ?>px;padding:0x;margin:0px;height:100%;'>
			<tr>
			<th colspan='2'>
			<span style='float:left;vertical-align:bottom;'><?php echo _("License");?></span>
			<?php if ($user->canEdit()){ ?>
				<span style='float:right;vertical-align:bottom;'><a href='ajax_forms.php?action=getLicenseForm&height=420&width=385&modal=true&resourceID=<?php echo $resourceID; ?>' class='thickbox' id='editLicense'><img src='images/edit.gif' alt='<?php echo _("edit");?>' title='<?php echo _("edit resource");?>'></a></span>
			<?php } ?>
			</th>
			</tr>

			<tr>
			<td style='vertical-align:top;width:110px;'><?php echo _("Status:");?></td>
			<td style='width:350px;'>

			<?php
			if (count($licenseStatusArray) > 0){
				foreach ($licenseStatusArray as $licenseStatus){
					echo $licenseStatus['licenseStatus'] . _(" on ")."<i>" . format_date($licenseStatus['licenseStatusChangeDate']) . _(" by ") . $licenseStatus['changeName'] . "</i><br />";
				}
			}else{
				echo "<i>"._("No license status information available.")."</i>";
			}

			?>
			</td>
			</tr>

			<?php if ($config->settings->licensingModule == "Y"){ ?>

			<tr>
			<td style='vertical-align:top;width:110px;'><?php echo _("Licenses:");?></td>
			<td style='width:350px;'>
			<?php

			if (count($licenseArray) > 0){
				foreach ($licenseArray as $license){
					echo $license['license'] . "&nbsp;&nbsp;<a href='" . $util->getLicensingURL() . $license['licenseID'] . "' target='_blank'><img src='images/arrow-up-right.gif' alt='"._("View License")."' title='"._("View License")."' style='vertical-align:top;'></a><br />";
				}
			}else{
				echo "<i>"._("No associated licenses available.")."</i>";
			}

			?>


			</td>
			</tr>

			<?php } ?>

			</table>
			<?php if ($user->canEdit()){ ?>
				<?php if ($config->settings->licensingModule == "Y"){ ?>
					<a href='ajax_forms.php?action=getLicenseForm&height=420&width=378&modal=true&resourceID=<?php echo $resourceID; ?>' class='thickbox'><?php echo _("edit license and status");?></a>
				<?php }else{ ?>
					<a href='ajax_forms.php?action=getLicenseForm&height=300&width=378&modal=true&resourceID=<?php echo $resourceID; ?>' class='thickbox'><?php echo _("edit license status");?></a>
				<?php } ?>
			<?php } ?>
			<br /><br /><br /><br />



		<?php

		//get notes for this tab
		$sanitizedInstance = array();
		$noteArray = array();
		foreach ($resource->getNotes('Acquisitions') as $instance) {
			foreach (array_keys($instance->attributeNames) as $attributeName) {
				$sanitizedInstance[$attributeName] = $instance->$attributeName;
			}

			$sanitizedInstance[$instance->primaryKeyName] = $instance->primaryKey;

			$updateUser = new User(new NamedArguments(array('primaryKey' => $instance->updateLoginID)));

			//in case this user doesn't have a first / last name set up
			if (($updateUser->firstName != '') || ($updateUser->lastName != '')){
				$sanitizedInstance['updateUser'] = $updateUser->firstName . " " . $updateUser->lastName;
			}else{
				$sanitizedInstance['updateUser'] = $instance->updateLoginID;
			}

			$noteType = new NoteType(new NamedArguments(array('primaryKey' => $instance->noteTypeID)));
			if (!$noteType->shortName){
				$sanitizedInstance['noteTypeName'] = 'General Note';
			}else{
				$sanitizedInstance['noteTypeName'] = $noteType->shortName;
			}

			array_push($noteArray, $sanitizedInstance);
		}

		if (count($noteArray) > 0){
		?>
			<table class='linedFormTable' style='width:<?php echo $tableWidth; ?>px;padding:0x;margin:0px;height:100%;'>
				<tr>
				<th><?php echo _("Additional Notes");?></th>
				<th>
				<?php if ($user->canEdit()){?>
					<a href='ajax_forms.php?action=getNoteForm&height=233&width=410&tab=Acquisitions&resourceID=<?php echo $resourceID; ?>&resourceNoteID=&modal=true' class='thickbox'><?php echo _("add new note");?></a>
				<?php } ?>
				</th>
				</tr>
				<?php foreach ($noteArray as $resourceNote){ ?>
					<tr>
					<td style='width:110px;'><?php echo $resourceNote['noteTypeName']; ?><br />
					<?php if ($user->canEdit()){?>
					<a href='ajax_forms.php?action=getNoteForm&height=233&width=410&tab=Acquisitions&resourceID=<?php echo $resourceID; ?>&resourceNoteID=<?php echo $resourceNote['resourceNoteID']; ?>&modal=true' class='thickbox'><img src='images/edit.gif' alt='<?php echo _("edit");?>' title='<?php echo _("edit note");?>'></a>  <a href='javascript:void(0);' class='removeNote' id='<?php echo $resourceNote['resourceNoteID']; ?>' tab='Acquisitions'><img src='images/cross.gif' alt='<?php echo _("remove note");?>' title='<?php echo _("remove note");?>'></a>
					<?php } ?>
					</td>
					<td><?php echo nl2br($resourceNote['noteText']); ?><br /><i><?php echo format_date($resourceNote['updateDate']) . _(" by ") . $resourceNote['updateUser']; ?></i></td>
					</tr>
				<?php } ?>
			</table>
		<?php
		}else{
			if ($user->canEdit()){
			?>
				<a href='ajax_forms.php?action=getNoteForm&height=233&width=410&tab=Acquisitions&resourceID=<?php echo $resourceID; ?>&resourceNoteID=&modal=true' class='thickbox'><?php echo _("add new note");?></a>
			<?php
			}
		}

?>

