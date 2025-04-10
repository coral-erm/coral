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
		<form id='licenseForm' class="form-grid">
		<input type='hidden' name='editResourceAcquisitionID' id='editResourceAcquisitionID' value='<?php echo $resourceAcquisitionID; ?>'>

		<h2 class='headerText'><?php echo _("Edit Licenses");?></h2>

		<span class='error' id='span_errors'></span>

			<?php if ($config->settings->licensingModule == 'Y'){ ?>
			<fieldset class="subgrid">
				<legend class="wide"><?php echo _("License Records");?></legend>

				<label for="licenseName"><?php echo _('License Name'); ?></label>
				<div class="form-group">
					<input type='text' id="licenseName" value = '' />
					<input type='hidden' class='licenseID' value = '' />
					
					<a href='javascript:void();' class='addLicense'><input class='addLicense add-button' title='<?php echo _("add license");?>' type='button' value='<?php echo _("Add");?>'/></a>
					<div class='error' id='div_errorLicense'></div>
				</div>
				<?php
				if (is_array($licenseArray) && count($licenseArray) > 0) {

					foreach ($licenseArray as $license){
					?>
						<label for="licenseName-<?php echo $license['licenseID']; ?>"><?php echo _('License Name'); ?></label>
						<div class="form-group">
							<input type='text' id="licenseName-<?php echo $license['licenseID']; ?>" class='changeInput licenseName' value = '<?php echo $license['license']; ?>' />
							<input type='hidden' class='licenseID' value = '<?php echo $license['licenseID']; ?>' />
						
							<a href='javascript:void();'><img src='images/cross.gif' alt='<?php echo _("remove license link");?>' title='<?php echo _("remove ").$license['license']._(" license"); ?>' class='remove' /></a>
						</div>
					<?php
					}
				}

				?>
			</fieldset>
			<?php } ?>
			<fieldset class="subgrid">
				<legend class="wide"><?php echo _("Licensing Status");?></legend>
				
				<label for='licenseStatusID'><?php echo _("Status:");?></label>

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
			</fieldset>
				<?php
				if (is_array($resourceLicenseStatusArray) && count($resourceLicenseStatusArray) > 0) {
					?>
					<table class="table-border">
					<caption><?php echo _("History:"); ?></caption>
					<thead>
						<tr>
							<th scope="col"><?php echo _('Status'); ?></th>
							<th scope="col"><?php echo _('Date'); ?></th>
							<th scope="col"><?php echo _('Change'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ($resourceLicenseStatusArray as $licenseStatus) { ?>
							<tr> 
								<th scope='row'><?php echo $licenseStatus['licenseStatus']; ?></th> 
								<td class="date"><?php echo format_date($licenseStatus['licenseStatusChangeDate']); ?></td> 
								<td><?php echo $licenseStatus['changeName']; ?></td> 
							</tr>
						<?php
						}
						?>
						</tbody>
					</table>
				<?php
				}else{
					echo "<p class='wide'><i>"._("No license status history available.")."</i></p>";
				}
				?>

			<p class="actions">
				<input type='button' value='<?php echo _("close");?>' onclick="myCloseDialog()" class='cancel-button secondary'>
			</p>
		</form>
	</div>
	<?php if ($config->settings->licensingModule == 'Y'){ ?>
		<script type="text/javascript" src="js/forms/licenseForm.js?random=<?php echo rand(); ?>"></script>
	<?php }else{ ?>
		<script type="text/javascript" src="js/forms/licenseStatusOnlyForm.js?random=<?php echo rand(); ?>"></script>
	<?php } ?>

