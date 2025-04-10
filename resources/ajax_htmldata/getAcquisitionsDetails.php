<?php
	$config = new Configuration();

    if (!extension_loaded('intl')) {
        echo "<p>" . _("PHP's extension php-intl doesn't seem to be installed or activated on your installation. Please install and activate php-intl to use cost history.") . "</p>";
        return 1;
    }

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
	$resourceAcquisitionID = $_GET['resourceAcquisitionID'];
	$resourceAcquisition = new ResourceAcquisition(new NamedArguments(array('primaryKey' => $resourceAcquisitionID)));

		//get payments
		$sanitizedInstance = array();
		$instance = new ResourcePayment();
		$paymentArray = array();
		foreach ($resourceAcquisition->getResourcePayments() as $instance) {
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

				$fund = new Fund(new NamedArguments(array('primaryKey' => $instance->fundID)));
				$sanitizedInstance['fundCode'] = $fund->shortName . " [" . $fund->fundCode . "]";

				array_push($paymentArray, $sanitizedInstance);

		}


		//get license statuses
		$sanitizedInstance = array();
		$instance = new ResourceLicenseStatus();
		$licenseStatusArray = array();
		foreach ($resourceAcquisition->getResourceLicenseStatuses() as $instance) {
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
		$licenseArray = $resourceAcquisition->getLicenseArray();

?>
	<div class="header">
		<h3><?php echo _("Cost History");?></h3>
		<?php if ($user->canEdit()){ ?>
			<a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getCostForm&height=400&width=<?php echo $formWidth; ?>&modal=true&resourceID=<?php echo $resourceID; ?>&resourceAcquisitionID=<?php echo $resourceAcquisitionID; ?>",400,<?php echo $formWidth; ?>)' class='thickbox addElement' id='editCost'><img src='images/edit.gif' alt='<?php echo _("edit");?>' title='<?php echo _("edit cost history");?>'></a>
		<?php } ?>
	</div>
		
		<table class='table-border table-striped'>
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
					<?php if ($enhancedCostFlag && 0){ ?>
						<th class="numeric"><?php echo _('%'); ?></th>
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
			if (is_array($paymentArray) && count($paymentArray) > 0) {

				foreach ($paymentArray as $payment){
					$year = $payment['year'] ? $payment['year'] : "&nbsp;";
					$subStart = $payment['subscriptionStartDate'] ? normalize_date($payment['subscriptionStartDate']) : "&nbsp;";
					$subEnd = $payment['subscriptionEndDate'] ? normalize_date($payment['subscriptionEndDate']) : "&nbsp;";
					$fundCode = $payment['fundCode'] ? $payment['fundCode'] : "&nbsp;";
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
				<td><?php echo $year; ?></td>
				<td><?php echo $subStart; ?></td>
				<td><?php echo $subEnd; ?></td>
			<?php } ?>
				<td><?php echo $fundCode; ?></td>
			<?php if ($enhancedCostFlag && 0){ ?>
				<td class="numeric"><?php echo $payment['amountChange']; ?></td>
            <?php } ?>
            <?php if ($enhancedCostFlag){ ?>
				<td class="numeric"><?php echo $cost['priceTaxExcluded']; ?></td>
                <td><?php echo $taxRate; ?></td>
				<td class="numeric"><?php echo $cost['priceTaxIncluded']; ?></td>
            <?php } ?>
				<td class="numeric"><?php echo $cost['paymentAmount']; ?></td>
				<td><?php echo $payment['orderType']; ?></td>
			<?php if ($enhancedCostFlag){ ?>
				<td><?php echo $costDetails; ?></td>
			<?php } ?>
				<td><?php echo $costNote; ?></td>
			<?php if ($enhancedCostFlag){ ?>
				<td><?php echo $invoiceNum; ?></td>
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
        <a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getCostForm&height=400&width=<?php echo $formWidth; ?>&modal=true&resourceID=<?php echo $resourceID; ?>&resourceAcquisitionID=<?php echo $resourceAcquisitionID; ?>",400,<?php echo $formWidth; ?>)' class='thickbox' id='editCost'><img src='images/edit.gif' alt='<?php echo _("edit");?>'><?php echo _("edit cost history");?></a>
			<?php } ?>

			<h3>
				<?php echo _("License");?>
				<?php if ($user->canEdit()){ ?>
					<span class="addElement"><a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getLicenseForm&height=420&width=385&modal=true&resourceID=<?php echo $resourceID; ?>&resourceAcquisitionID=<?php echo $resourceAcquisitionID; ?>",450,400)' class='thickbox' id='editLicense'><img src='images/edit.gif' alt='<?php echo _("edit");?>' title='<?php echo _("edit license");?>'></a></span>
				<?php } ?>
			</h3>

			
			<h4><?php echo _("Status:");?></h4>
			<?php
			if (is_array($licenseStatusArray) && count($licenseStatusArray) > 0) {
				echo "<ul>";
				foreach ($licenseStatusArray as $licenseStatus){
					echo "<li>" . sprintf(_("%s on %s by %s"), $licenseStatus['licenseStatus'], format_date($licenseStatus['licenseStatusChangeDate']), $licenseStatus['changeName']) . "</i></li>";
				}
				echo "</ul>";
			}else{
				echo "<p><i>"._("No license status information available.")."</i></p>";
			}

			?>

			<?php if ($config->settings->licensingModule == "Y"){ ?>
			
			<h4><?php echo _("Licenses:");?></h4>
			
			<?php

			if (is_array($licenseArray) && count($licenseArray) > 0) {
				echo "<ul>";
				foreach ($licenseArray as $license){
					echo "<li>". $license['license'] . "&nbsp;&nbsp;<a href='" . $util->getLicensingURL() . $license['licenseID'] . "' " . getTarget() . "><img src='images/arrow-up-right.gif' alt='"._("View License")."' title='"._("View License")."'></a></li>";
				}
				echo "</ul>";
			}else{
				echo "<p><i>"._("No associated licenses available.")."</i></p>";
			}

			?>


			<?php } ?>

			<?php if ($user->canEdit()){ ?>
				<p>
				<?php if ($config->settings->licensingModule == "Y"){ ?>
					<a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getLicenseForm&height=420&width=378&modal=true&resourceID=<?php echo $resourceID; ?>&resourceAcquisitionID=<?php echo $resourceAcquisitionID; ?>",450,400)' class='thickbox'><?php echo _("edit license and status");?></a>
				<?php }else{ ?>
					<a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getLicenseForm&height=300&width=378&modal=true&resourceID=<?php echo $resourceID; ?>&resourceAcquisitionID=<?php echo $resourceAcquisitionID; ?>",320,400)' class='thickbox'><?php echo _("edit license status");?></a>
				<?php } ?>
				</p>
			<?php } ?>
			
		<?php

		//get notes for this tab
		$sanitizedInstance = array();
		$noteArray = array();
		foreach ($resourceAcquisition->getNotes('Acquisitions') as $instance) {
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
		?>
		<div class="header">
			<h3><?php echo _("Additional Notes");?></h3>
			<?php if ($user->canEdit()){?>
				<a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getNoteForm&height=233&width=410&tab=Acquisitions&entityID=<?php echo $resourceAcquisitionID; ?>&resourceNoteID=&modal=true",250,430)' class='thickbox addElement'><?php echo "<img src='images/plus.gif' title= '"._("Add")."' />";?></a>
			<?php } ?>
		</div>
		
		<?php
		if (is_array($noteArray) && count($noteArray) > 0) {
		?>
		<div class="form-grid">				
			<?php foreach ($noteArray as $resourceNote){ ?>
					<h4><?php echo $resourceNote['noteTypeName']; ?>
					
						<a  href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getNoteForm&height=233&width=410&tab=Acquisitions&entityID=<?php echo $resourceAcquisitionID; ?>&resourceNoteID=<?php echo $resourceNote['resourceNoteID']; ?>&modal=true",250,430)' class='thickbox'><img src='images/edit.gif'  alt='<?php echo _("edit");?>' title='<?php echo _("edit note");?>'></a>
						<a href='javascript:void(0);' class='removeNote'   id='<?php echo $resourceNote['resourceNoteID']; ?>' tab='Acquisitions'><img src='images/cross.gif' alt='<?php echo _("remove note");?>' title='<?php echo _("remove note");?>'></a>
				</h4>
				<div class="form-group">
					<?php echo nl2br($resourceNote['noteText']); ?>
					<p class="byline"><?php printf(_("%s by %s"), format_date($resourceNote['updateDate']), $resourceNote['updateUser']); ?></p>
					<?php } ?>
			</div>
		<?php
		}else{
			if ($user->canEdit()){
			?>
				<p>
					<a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getNoteForm&height=233&width=410&tab=Acquisitions&entityID=<?php echo $resourceAcquisitionID; ?>&resourceNoteID=&modal=true",350,430)' class='thickbox'><?php echo _("add note");?></a>
				</p>
			<?php
			}
		}

?>
