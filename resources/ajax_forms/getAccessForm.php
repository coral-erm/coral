<?php
	$resourceID = $_GET['resourceID'];
	$resourceAcquisitionID = $_GET['resourceAcquisitionID'];
	$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));
	$resourceAcquisition = new ResourceAcquisition(new NamedArguments(array('primaryKey' => $resourceAcquisitionID)));

	//get all authentication types for output in drop down
	$authenticationTypeArray = array();
	$authenticationTypeObj = new AuthenticationType();
	$authenticationTypeArray = $authenticationTypeObj->allAsArray();

	//get all access methods for output in drop down
	$accessMethodArray = array();
	$accessMethodObj = new AccessMethod();
	$accessMethodArray = $accessMethodObj->allAsArray();

	//get all user limits for output in drop down
	//overridden for better sort
	$userLimitArray = array();
	$userLimitObj = new UserLimit();
	$userLimitArray = $userLimitObj->allAsArray();

	//get all storage locations for output in drop down
	$storageLocationArray = array();
	$storageLocationObj = new StorageLocation();
	$storageLocationArray = $storageLocationObj->allAsArray();

	//get all administering sites for output in checkboxes
	$administeringSiteArray = array();
	$administeringSiteObj = new AdministeringSite();
	$administeringSiteArray = $administeringSiteObj->allAsArray();


	//get administering sites for this resource
	$sanitizedInstance = array();
	$instance = new AdministeringSite();
	$resourceAdministeringSiteArray = array();
	foreach ($resourceAcquisition->getAdministeringSites() as $instance) {
		$resourceAdministeringSiteArray[] = $instance->administeringSiteID;
	}


	//get all authorized sites for output in checkboxes
	$authorizedSiteArray = array();
	$authorizedSiteObj = new AuthorizedSite();
	$authorizedSiteArray = $authorizedSiteObj->allAsArray();


	//get authorized sites for this resource
	$sanitizedInstance = array();
	$instance = new AuthorizedSite();
	$resourceAuthorizedSiteArray = array();
	foreach ($resourceAcquisition->getAuthorizedSites() as $instance) {
		$resourceAuthorizedSiteArray[] = $instance->authorizedSiteID;
	}
?>
		<div id='div_accessForm'>
		<form id='accessForm' class="large">
		<input type='hidden' name='editResourceID' id='editResourceID' value='<?php echo $resourceID; ?>'>
		<input type='hidden' name='editResourceAcquisitionID' id='editResourceAcquisitionID' value='<?php echo $resourceAcquisitionID; ?>'>

		<div class='formTitle'><h2 class='headerText'><?php echo _("Edit Access");?></div>

		<span class='error' id='span_errors'></span>

		<div class="form-grid grid-columns"> <!-- two columns -->
				<label for='accessHead'><?php echo _("Access");?></label>
				<select name='authenticationTypeID' id='authenticationTypeID' class='changeSelect'>
					<option value=''></option>
					<?php
					foreach ($authenticationTypeArray as $authenticationType){
						if (!(trim(strval($authenticationType['authenticationTypeID'])) != trim(strval($resourceAcquisition->authenticationTypeID)))){
							echo "<option value='" . $authenticationType['authenticationTypeID'] . "' selected>" . $authenticationType['shortName'] . "</option>\n";
						}else{
							echo "<option value='" . $authenticationType['authenticationTypeID'] . "'>" . $authenticationType['shortName'] . "</option>\n";
						}
					}
					?>
				</select>
				
					<label for='accessMethodID'><?php echo _("Access Method:");?></label>
					<select name='accessMethodID' id='accessMethodID' class='changeSelect'>
					<option value=''></option>
					<?php
					foreach ($accessMethodArray as $accessMethod){
						if (!(trim(strval($accessMethod['accessMethodID'])) != trim(strval($resourceAcquisition->accessMethodID)))){
							echo "<option value='" . $accessMethod['accessMethodID'] . "' selected>" . $accessMethod['shortName'] . "</option>\n";
						}else{
							echo "<option value='" . $accessMethod['accessMethodID'] . "'>" . $accessMethod['shortName'] . "</option>\n";
						}
					}
					?>
					</select>
			
					
					<label for='authenticationUserName'><?php echo _("Username:");?></label>
					<input type='text' id='authenticationUserName' name='authenticationUserName' value = '<?php echo $resourceAcquisition->authenticationUserName; ?>' class='changeInput'  />
						
					<label for='storageLocationID'><?php echo _("Storage Location:");?></label>
					<select name='storageLocationID' id='storageLocationID' class='changeSelect'>
						<option value=''></option>
						<?php
						foreach ($storageLocationArray as $storageLocation){
							if (!(trim(strval($storageLocation['storageLocationID'])) != trim(strval($resourceAcquisition->storageLocationID)))){
								echo "<option value='" . $storageLocation['storageLocationID'] . "' selected>" . $storageLocation['shortName'] . "</option>\n";
							}else{
								echo "<option value='" . $storageLocation['storageLocationID'] . "'>" . $storageLocation['shortName'] . "</option>\n";
							}
						}
						?>
					</select>

					
					<label for='authenticationPassword'><?php echo _("Password:");?></label>
					<input type='text' id='authenticationPassword' name='authenticationPassword' value = '<?php echo $resourceAcquisition->authenticationPassword; ?>' class='changeInput'  />
					
					
					<label for='userLimitID'><?php echo _("Simultaneous User Limit:");?></label>
					<select name='userLimitID' id='userLimitID' class='changeSelect'>
						<option value=''></option>
						<?php
						foreach ($userLimitArray as $userLimit){
							if (!(trim(strval($userLimit['userLimitID'])) != trim(strval($resourceAcquisition->userLimitID)))){
								echo "<option value='" . $userLimit['userLimitID'] . "' selected>" . $userLimit['shortName'] . "</option>\n";
							}else{
								echo "<option value='" . $userLimit['userLimitID'] . "'>" . $userLimit['shortName'] . "</option>\n";
							}
						}
						?>
					</select>

					<p class="wide subgrid">	
						<label for='coverageText'><?php echo _("Coverage:");?></label>
						<input type='text' id='coverageText' name='coverageText' value = "<?php echo $resourceAcquisition->coverageText; ?>" class='changeInput'/>
					</p>
				</div>
					<div class="flex">
						<div class="col">
							<label for='authorizedSiteID'><b><?php echo _("Authorized Site(s)");?></b></label>
									
							<?php
							if (is_array($authorizedSiteArray) && count($authorizedSiteArray) > 0) {
								echo "<ul class='unstyled'>";
								foreach ($authorizedSiteArray as $authorizedSiteIns){
									if (in_array($authorizedSiteIns['authorizedSiteID'],$resourceAuthorizedSiteArray)){
										echo "<li><label><input class='check_authorizedSite' type='checkbox' name='" . $authorizedSiteIns['authorizedSiteID'] . "' id='" . $authorizedSiteIns['authorizedSiteID'] . "' value='" . $authorizedSiteIns['authorizedSiteID'] . "' checked />   " . $authorizedSiteIns['shortName'] . "</label></li>\n";
									}else{
										echo "<li><label><input class='check_authorizedSite' type='checkbox' name='" . $authorizedSiteIns['authorizedSiteID'] . "' id='" . $authorizedSiteIns['authorizedSiteID'] . "' value='" . $authorizedSiteIns['authorizedSiteID'] . "' />   " . $authorizedSiteIns['shortName'] . "</label></li>\n";
									}
									
								}
								echo "</ul>";
							}
							?>
						</div>

						<div class="col">
							<label for='authorizedSiteID'><b><?php echo _("Administering Site(s)");?></b></label>
								<?php
								if (is_array($administeringSiteArray) && count($administeringSiteArray) > 0) {
									echo "<ul class='unstyled'>";
									foreach ($administeringSiteArray as $administeringSiteIns){
										
										if (in_array($administeringSiteIns['administeringSiteID'],$resourceAdministeringSiteArray)){
											echo "<li><label><input class='check_administeringSite' type='checkbox' name='" . $administeringSiteIns['administeringSiteID'] . "' id='" . $administeringSiteIns['administeringSiteID'] . "' value='" . $administeringSiteIns['administeringSiteID'] . "' checked />   " . $administeringSiteIns['shortName'] . "</label></li>\n";
										}else{
											echo "<td><label><input class='check_administeringSite' type='checkbox' name='" . $administeringSiteIns['administeringSiteID'] . "' id='" . $administeringSiteIns['administeringSiteID'] . "' value='" . $administeringSiteIns['administeringSiteID'] . "' />   " . $administeringSiteIns['shortName'] . "</label></td>\n";
										}
									}
									echo "</ul>";

								}
								?>
						</div>
					</div>
		
		<p class='actions'>
			<input type='submit' value='<?php echo _("submit");?>' name='submitAccessChanges' id ='submitAccessChanges' class='submit-button primary'>
			<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='cancel-button secondary'>
		</p>


		<script type="text/javascript" src="js/forms/accessForm.js?random=<?php echo rand(); ?>"></script>

