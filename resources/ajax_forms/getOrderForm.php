<?php
    $resourceAcquisitionID = isset($_GET['resourceAcquisitionID']) ? $_GET['resourceAcquisitionID'] : null;
	$resourceID = $_GET['resourceID'];
    $op = isset($_GET['op']) ? $_GET['op'] : null;
	$resourceAcquisition = new ResourceAcquisition(new NamedArguments(array('primaryKey' => $resourceAcquisitionID)));
	$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));
    if ($resourceAcquisition->organizationID) {
        $organization = $resourceAcquisition->getOrganization();
        $organizationName = $organization['organization'];
    }

		//used to get default currency
		$config = new Configuration();

		$startDate = normalize_date($resourceAcquisition->subscriptionStartDate);
		$endDate = normalize_date($resourceAcquisition->subscriptionEndDate);

		//get all purchase sites for output in checkboxes
		$purchaseSiteArray = array();
		$purchaseSiteObj = new PurchaseSite();
		$purchaseSiteArray = $purchaseSiteObj->allAsArray();

		//get all acquisition types for output in drop down
		$acquisitionTypeArray = array();
		$acquisitionTypeObj = new AcquisitionType();
		$acquisitionTypeArray = $acquisitionTypeObj->allAsArray();

		//get purchase sites
		$sanitizedInstance = array();
		$instance = new PurchaseSite();
		$resourcePurchaseSiteArray = array();
		foreach ($resourceAcquisition->getPurchaseSites() as $instance) {
			$resourcePurchaseSiteArray[] = $instance->purchaseSiteID;
		}
?>
<div id='div_resourceForm'>
	<form id='resourceForm' class='form-grid'>
		<input type='hidden' name='editResourceAcquisitionID' id='editResourceAcquisitionID' value='<?php echo $resourceAcquisitionID; ?>'>
		<input type='hidden' name='editResourceID' id='editResourceID' value='<?php echo $resourceID; ?>'>
		<input type='hidden' name='op' id='op' value='<?php echo $op; ?>'>

		<h2 class='headerText'><?php echo (($op == 'clone') ? _("Clone Order") : _("Edit Order")); ?></h2>

		<span class='error' id='span_errors'></span>
	
		<label for='acquisitionTypeID'><?php echo _("Acquisition Type:");?></label>
		<select name='acquisitionTypeID' id='acquisitionTypeID' class='changeSelect'>
				<option value=''></option>
				<?php
				foreach ($acquisitionTypeArray as $acquisitionType){
					if (trim(strval($acquisitionType['acquisitionTypeID'])) == trim(strval($resourceAcquisition->acquisitionTypeID))){
						echo "<option value='" . $acquisitionType['acquisitionTypeID'] . "' selected>" . $acquisitionType['shortName'] . "</option>\n";
					}else{
						echo "<option value='" . $acquisitionType['acquisitionTypeID'] . "'>" . $acquisitionType['shortName'] . "</option>\n";
					}
				}
				?>
		</select>
		
		<label for='orderNumber'><?php echo _("Order Number:");?></label>
		<input type='text' id='orderNumber' name='orderNumber' value = '<?php echo $resourceAcquisition->orderNumber; ?>' class='changeInput' />
		
		<label for='systemNumber'><?php echo _("System Number:");?></label>
		<input type='text' id='systemNumber' name='systemNumber' value = '<?php echo $resourceAcquisition->systemNumber; ?>' class='changeInput' />
		
		<label for='libraryNumber'><?php echo _("Library Number:");?></label>
		<input type='text' id='libraryNumber' name='libraryNumber' value = '<?php echo $resourceAcquisition->libraryNumber; ?>' class='changeInput' />
		
		<label for='currentStartDate'><?php echo _("Sub Start:");?></label>
		<div class="form-group">
			<input class='date-pick' id='currentStartDate' name='currentStartDate' value = '<?php echo $startDate; ?>' placeholder='mm/dd/yyyy' />
		</div>

		<label for='currentEndDate'><?php echo _("Current Sub End:");?></label>
		<div class="form-group">
			<input class='date-pick' id='currentEndDate' name='currentEndDate' value = '<?php echo $endDate; ?>' placeholder='mm/dd/yyyy' />
		</div>

		<label for='organizationName'><?php echo _("Organization");?></label>
		<input type='text' value='<?php if (isset($organizationName)) echo $organizationName; ?>' id="organizationName" class='changeAutocomplete organizationName' />
		<input type='hidden' id="organizationID" class='organizationID' value = '<?php echo $resourceAcquisition->organizationID; ?>' />

		<?php if ($config->settings->enableAlerts == 'Y'){ ?>
			<div class="subgrid">	
				<h3 class="label"><?php echo _("Alerts");?></h3>
				<div class="form-group">	
					<p class="checkbox">
						<label><input id='subscriptionAlertEnabledInd' type='checkbox' value='1' <?php if($resourceAcquisition->subscriptionAlertEnabledInd == 1) { echo "checked"; } ?> /><?php echo _("Enable Alert");?></label>
					</p>
				</div>
			</div>
		<?php } ?>

		<fieldset class="subgrid">	
			<legend><?php echo _("Purchasing Site(s)");?></legend>
			<div class="form-group">
				<?php
					if (is_array($purchaseSiteArray) && count($purchaseSiteArray) > 0) {
						echo '<ul class="unstyled">';
						foreach ($purchaseSiteArray as $purchaseSiteIns){
							$checked = '';
							if (in_array($purchaseSiteIns['purchaseSiteID'],$resourcePurchaseSiteArray)){
								$checked = ' checked ';
							}
							
							echo "<li><label><input class='check_purchaseSite' type='checkbox' name='" . $purchaseSiteIns['purchaseSiteID'] . "' id='" . $purchaseSiteIns['purchaseSiteID'] . "' value='" . $purchaseSiteIns['purchaseSiteID'] . "' " . $checked ." />   " . $purchaseSiteIns['shortName'] . "</label></li>\n";
						}
						echo '</ul>';
					}
				?>
			</div>
		</fieldset>
		
		<p class="actions">
			<input type='submit' value='<?php echo _("submit");?>' name='submitOrder' id ='submitOrder' class='submit-button primary'>
			<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='cancel-button secondary'>
		</p>

	</form>
</div>
<script type="text/javascript" src="js/forms/acquisitionsForm.js?random=<?php echo rand(); ?>"></script>

