<?php
	$config = new Configuration();
	$resourceID = $_GET['resourceID'];
	$resourceAcquisitionID = $_GET['resourceAcquisitionID'];
	$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));
	$resourceAcquisition = new ResourceAcquisition(new NamedArguments(array('primaryKey' => $resourceAcquisitionID)));


		//get license statuses
		$sanitizedInstance = array();
		$instance = new ResourceLicenseStatus();
		$resourceLicenseStatusArray = array();
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


				array_push($resourceLicenseStatusArray, $sanitizedInstance);

		}

		$currentLicenseStatusID = $resourceAcquisition->getCurrentResourceLicenseStatus();

		//get licenses (already returned in array)
		$licenseArray = $resourceAcquisition->getLicenseArray();



		//get all resource licenses for output in drop down
		$licenseStatusArray = array();
		$licenseStatusObj = new LicenseStatus();
		$licenseStatusArray = $licenseStatusObj->allAsArray();
?>
		<div id='div_licenseForm'>
		<form id='licenseForm'>
		<input type='hidden' name='editResourceAcquisitionID' id='editResourceAcquisitionID' value='<?php echo $resourceAcquisitionID; ?>'>

		<div class='formTitle' style='width:360px; margin-bottom:5px;'><span class='headerText'><?php echo _("Edit Licenses");?></span></div>

		<span class='smallDarkRedText' id='span_errors'></span>

		<table class='noBorder' style='width:360px;'>
		<tr style='vertical-align:top;'>
		<td style='vertical-align:top;'>


			<?php if ($config->settings->licensingModule == 'Y'){ ?>
			<span class='surroundBoxTitle'>&nbsp;&nbsp;<label for='licenseRecords'><b><?php echo _("License Records");?></b></label>&nbsp;&nbsp;</span>


			<table class='surroundBox' style='width:350px;'>
			<tr>
			<td>

				<table class='noBorder smallPadding newLicenseTable' style='width:310px; margin:15px 20px 0px 20px'>
				<tr class='newLicenseTR'>

				<td style='vertical-align:top;text-align:left;'>
				<input type='text' value = '' style='width:260px;background:#f5f8fa;' class='changeAutocomplete licenseName' />
				<input type='hidden' class='licenseID' value = '' />
				</td>

				<td style='vertical-align:top;text-align:left;width:40px;'>
				<a href='javascript:void();' class='addLicense'><input class='addLicense add-button' title='<?php echo _("add license");?>' type='button' value='<?php echo _("Add");?>'/></a>
				</td>
				</tr>
				</table>
				<div class='smallDarkRedText' id='div_errorLicense' style='margin:0px 20px 7px 26px;'></div>


				<table class='noBorder smallPadding licenseTable' style='width:310px; margin:0px 20px 15px 20px;'>
				<tr>
				<td colspan='2'>
					<hr style='width:290px;margin:0px 0px 5px 5px;' />
				</td>
				</tr>

				<?php
				if (count($licenseArray) > 0){

					foreach ($licenseArray as $license){
					?>
						<tr>

						<td style='vertical-align:top;text-align:left;'>
						<input type='text' class='changeInput licenseName' value = '<?php echo $license['license']; ?>' style='width:260px;' class='changeInput' />
						<input type='hidden' class='licenseID' value = '<?php echo $license['licenseID']; ?>' />
						</td>

						<td style='vertical-align:top;text-align:left;width:40px;'>
							<a href='javascript:void();'><img src='images/cross.gif' alt='<?php echo _("remove license link");?>' title='<?php echo _("remove ").$license['license']._(" license"); ?>' class='remove' /></a>
						</td>

						</tr>

					<?php
					}
				}

				?>

				</table>




			</td>
			</tr>
			</table>


			<div style='height:15px;'>&nbsp;</div>

			<?php } ?>

			<span class='surroundBoxTitle'>&nbsp;&nbsp;<label for='licenseStatus'><b><?php echo _("Licensing Status");?></b></label></span>

			<table class='surroundBox' style='width:350px;'>
			<tr>
			<td>

				<table class='noBorder smallPadding' style='width:310px; margin:15px 20px 0px 20px'>
				<tr>
				<td style='vertical-align:top;text-align:left;width:60px;'><?php echo _("Status:");?></td>
				<td style='vertical-align:top;text-align:left;'>
				<select class='changeSelect' id='licenseStatusID'>
				<option value=''></option>
				<?php
				foreach ($licenseStatusArray as $licenseStatus){
					if (!(trim(strval($licenseStatus['licenseStatusID'])) != trim(strval($currentLicenseStatusID)))){
						echo "<option value='" . $licenseStatus['licenseStatusID'] . "' selected class='changeSelect'>" . $licenseStatus['shortName'] . "</option>\n";
					}else{
						echo "<option value='" . $licenseStatus['licenseStatusID'] . "' class='changeSelect'>" . $licenseStatus['shortName'] . "</option>\n";
					}
				}
				?>
				</select>
				</td>

				</tr>
				</table>

				<hr style='width:310px;margin:8px 20px 7px 20px;' />


				<table class='noBorder' style='width:310px; margin:5px 15px;'>
				<tr>
				<td style='vertical-align:top;width:60px;'><?php echo _("History:");?></td>
				<td>

				<?php
				if (count($resourceLicenseStatusArray) > 0){
					foreach ($resourceLicenseStatusArray as $licenseStatus){
						echo $licenseStatus['licenseStatus'] . " - <i>" . format_date($licenseStatus['licenseStatusChangeDate']) . _(" by ") . $licenseStatus['changeName'] . "</i><br />";
					}
				}else{
					echo "<i>"._("No license status information available.")."</i>";
				}

				?>
				</td>
				</tr>

				</table>


			</td>
			</tr>
			</table>

		</td>
		</tr>
		</table>

		<br />

		<table class='noBorderTable' style='width:125px;'>
			<tr>
				<td style='text-align:left'><input type='button' value='<?php echo _("submit");?>' name='submitLicense' id ='submitLicense' class='submit-button'></td>
				<td style='text-align:right'><input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='cancel-button'></td>
			</tr>
		</table>

		<?php if ($config->settings->licensingModule == 'Y'){ ?>
		<script type="text/javascript" src="js/forms/licenseForm.js?random=<?php echo rand(); ?>"></script>
		<?php }else{ ?>
		<script type="text/javascript" src="js/forms/licenseStatusOnlyForm.js?random=<?php echo rand(); ?>"></script>
		<?php } ?>

