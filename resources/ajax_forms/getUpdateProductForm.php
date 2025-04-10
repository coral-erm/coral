<?php
	$resourceID = $_GET['resourceID'];
	$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));


		if (!is_null_date($resource->archiveDate)) {
			$archiveChecked = 'checked';
		}else{
			$archiveChecked = '';
		}


		//get all resource formats for output in drop down
		$resourceFormatArray = array();
		$resourceFormatObj = new ResourceFormat();
		$resourceFormatArray = $resourceFormatObj->sortedArray();

		//get all resource types for output in drop down
		$resourceTypeArray = array();
		$resourceTypeObj = new ResourceType();
		$resourceTypeArray = $resourceTypeObj->allAsArray();

    //get parents resources
    $sanitizedInstance = array();
    $instance = new Resource();
    $parentResourceArray = array();
    foreach ($resource->getParentResources() as $instance) {
      foreach (array_keys($instance->attributeNames) as $attributeName) {
        $sanitizedInstance[$attributeName] = $instance->$attributeName;
      }
      $sanitizedInstance[$instance->primaryKeyName] = $instance->primaryKey;
      array_push($parentResourceArray, $sanitizedInstance);
    }

		//get all alias types for output in drop down
		$aliasTypeArray = array();
		$aliasTypeObj = new AliasType();
		$aliasTypeArray = $aliasTypeObj->allAsArray();


		//get aliases
		$sanitizedInstance = array();
		$instance = new Alias();
		$aliasArray = array();
		foreach ($resource->getAliases() as $instance) {
			foreach (array_keys($instance->attributeNames) as $attributeName) {
				$sanitizedInstance[$attributeName] = $instance->$attributeName;
			}

			$sanitizedInstance[$instance->primaryKeyName] = $instance->primaryKey;

			$aliasType = new AliasType(new NamedArguments(array('primaryKey' => $instance->aliasTypeID)));
			$sanitizedInstance['aliasTypeShortName'] = $aliasType->shortName;

			array_push($aliasArray, $sanitizedInstance);
		}


		//get all organization roles for output in drop down
		$organizationRoleArray = array();
		$organizationRoleObj = new OrganizationRole();
		$organizationRoleArray = $organizationRoleObj->getArray();


		//get organizations (already returned in an array)
		$orgArray = $resource->getOrganizationArray();
?>
		<div id='div_resourceForm'>
		<form id='resourceForm' class="large">
		<input type='hidden' name='editResourceID' id='editResourceID' value='<?php echo $resourceID; ?>'>

		<div class='formTitle'><h2 class='headerText'><?php echo _("Edit Resource");?></h2></div>
		<span class='error' id='span_errors'></span>

		<h3><?php echo _("Product");?></h3>
	<div class="block-form">
		<p>
			<label for='titleText'><?php echo _("Name:");?></label>
			<input type='text' id='titleText' name='titleText' value = "<?php echo $resource->titleText; ?>" aria-describedby='span_error_titleText' class='changeInput' />
			<span id='span_error_titleText' class='error'></span>
		</p>
		<p>
			<label for='descriptionText'><?php echo _("Description:");?></label>
			<textarea rows='4' id='descriptionText' name='descriptionText' class='changeInput' ><?php echo $resource->descriptionText; ?></textarea>
		</p>
	</div>
	<div class="form-grid grid-columns">
		<label for='resourceURL'><?php echo _("URL:");?></label>
		<input type='url' id='resourceURL' name='resourceURL' value = '<?php echo $resource->resourceURL; ?>' class='changeInput'  />
		
		<label for='resourceAltURL'><?php echo _("Alt URL:");?></label>
		<input type='url' id='resourceAltURL' name='resourceAltURL' value = '<?php echo $resource->resourceAltURL; ?>' class='changeInput'  />

		<label for='parentResource'><?php echo _("Parents:");?></label>
		<div class="form-group">

			<p id="newParent">
				<span class="oneParent">
					<span id='span_error_parentResourceName' class='error'></span>
					<input type='text' class='parentResource parentResource_new' id='parentResource' name='parentResourceName' value='' aria-describedby='span_error_parentResourceName' class='changeInput'  />
					<input type='hidden' class='parentResource parentResource_new' name='parentResourceNewID' value='' />
					<a href='#'><input class='addParent add-button' title='<?php echo _("add Parent Resource");?>' type='button' value='<?php echo _("Add");?>'/></a>
				</span>
			</p>

			<p id="existingParent">
			<?php
				$i = 1;
				foreach ($parentResourceArray as $parentResource) {
$parentResourceObj = new Resource(new NamedArguments(array('primaryKey' => $parentResource['relatedResourceID'])));
					?>
					<span class="oneParent">
						<input type='text' name='parentResourceName' disabled='disabled' id='parentResource' value = '<?php echo $parentResourceObj->titleText; ?>' class='changeInput'  />
						<input type='hidden' name='parentResourceID' value = '<?php echo $parentResourceObj->resourceID; ?>' />
						<a href='javascript:void();'><img src='images/cross.gif' alt='<?php echo _("remove parent");?>' title='<?php echo _("remove parent");?>' class='removeParent' /></a>
					</span>
<?php
					$i++;
				}
			?>
			</p>
		</div>
			
		<label for='isbnOrISSN'><?php echo _("ISSN / ISBN:");?></label>
		<div class="form-group">
			<div id="newIsbn">
				<span class="oneIssnIsbn">
					<span id='span_errors_isbnOrISSN' class='error'></span>
					<input type='text' class='isbnOrISSN isbnOrISSN_new' name='isbnOrISSN' value = "" aria-describedby='span_errors_isbnOrISSN' class='changeInput'  />
					<a href='javascript:void(0);'><input class='addIsbn add-button' title='<?php echo _("add Isbn");?>' type='button' value='<?php echo _("Add");?>'/></a>
				</span>
			</div>
			<div id="existingIsbn">
				<?php
				$isbnOrIssns = $resource->getIsbnOrIssn();
				$i = 1;
				foreach ($isbnOrIssns as $isbnOrIssn) {
					?>
					<span class="oneIssnIsbn">
						<input type='text' class='isbnOrISSN' name='isbnOrISSN' value = '<?php echo $isbnOrIssn->isbnOrIssn; ?>' class='changeInput'  />
						<a href='javascript:void();'><img src='images/cross.gif' alt='<?php echo _("remove Issn/Isbn");?>' title='<?php echo _("remove Issn/Isbn");?>' class='removeIssnIsbn' /></a>
					</span>
					<?php
					$i++;
				}
				?>
			</div>
		</div>
	
		<label for='resourceFormatID'><?php echo _("Format:");?></label>
		<select name='resourceFormatID' id='resourceFormatID' class='changeSelect'>
			<option value=''></option>
			<?php
			foreach ($resourceFormatArray as $resourceFormat){
				if (!(trim(strval($resourceFormat['resourceFormatID'])) != trim(strval($resource->resourceFormatID)))){
					echo "<option value='" . $resourceFormat['resourceFormatID'] . "' selected>" . $resourceFormat['shortName'] . "</option>\n";
				}else{
					echo "<option value='" . $resourceFormat['resourceFormatID'] . "'>" . $resourceFormat['shortName'] . "</option>\n";
				}
			}
			?>
		</select>

		<label for='resourceTypeID'><?php echo _("Type:");?></label>
		<select name='resourceTypeID' id='resourceTypeID' class='changeSelect' >
			<option value=''></option>
			<?php
			foreach ($resourceTypeArray as $resourceType){
				if (!(trim(strval($resourceType['resourceTypeID'])) != trim(strval($resource->resourceTypeID)))){
					echo "<option value='" . $resourceType['resourceTypeID'] . "' selected>" . $resourceType['shortName'] . "</option>\n";
				}else{
					echo "<option value='" . $resourceType['resourceTypeID'] . "'>" . $resourceType['shortName'] . "</option>\n";
				}
			}
			?>
		</select>
		
		<label for='archiveInd'><?php echo _("Archived:");?></label>
		<input type='checkbox' id='archiveInd' name='archiveInd' <?php echo $archiveChecked; ?> />
		
	</div>
	<div class="flex">
	<div class="col">
		<h3><?php echo _("Organizations"); ?></h3>
		<p class='error' id='div_errorOrganization'></p>
		<!-- Note this table has two <tbody> sections -->
		<!-- Classes are used in resourceUpdateForm.js validation -->
		<table>
			<thead>
				<tr>
					<th scope="col" id="organizationRoleIDLabel"><?php echo _("Role:");?></th>
					<th scope="col" id="organizationNameLabel"><?php echo _("Organization:");?></th>
					<th scope="col"><?php echo _("Actions");?></th>
				</tr>
			</thead>
			<tbody class="newOrganizationTable">
			<tr class='newOrganizationTR'>
				<td>
					<select class='changeSelect organizationRoleID' aria-labelledby="organizationRoleIDLabel">
					<option value=''></option>
					<?php
					foreach ($organizationRoleArray as $organizationRoleID => $organizationRoleShortName){
						echo "<option value='" . $organizationRoleID . "'>" . $organizationRoleShortName . "</option>\n";
					}
					?>
					</select>
				</td>

				<td>
					<input type='text' value = '' aria-labelledby="organizationNameLabel" class='changeAutocomplete organizationName' />
					<input type='hidden' class='organizationID' value = '' />
				</td>

				<td class="actions">
				<a href='javascript:void();'><input class='addOrganization add-button' title='<?php echo _("add organization");?>' type='button' value='<?php echo _("Add");?>'/></a>
				</td>
			</tr>
			</tbody>

			<tbody class="organizationTable">
			<?php
			if (is_array($orgArray) && count($orgArray) > 0) {

				foreach ($orgArray as $organization){
				?>
					<tr>
					<th scope="row">
						<select class='organizationRoleID changeSelect' aria-labelledby="organizationRoleIDLabel">
						<option value=''></option>
						<?php
						foreach ($organizationRoleArray as $organizationRoleID => $organizationRoleShortName){
							if (!(trim(strval($organizationRoleID)) != trim(strval($organization['organizationRoleID'])))){
								echo "<option value='" . $organizationRoleID . "' selected>" . $organizationRoleShortName . "</option>\n";
							}else{
								echo "<option value='" . $organizationRoleID . "'>" . $organizationRoleShortName . "</option>\n";
							}
						}
						?>
						</select>
					</th>

					<td>
					<input type='text' class='changeInput organizationName'  aria-labelledby="organizationNameLabel" value = '<?php echo $organization['organization']; ?>' style='width:160px;' class='changeInput' />
					<input type='hidden' class='organizationID' value = '<?php echo $organization['organizationID']; ?>' />
					</td>

					<td class='actions'>
						<a href='javascript:void();'><img src='images/cross.gif' alt="<?php echo _("remove organization");?>" title="<?php echo _("remove organization");?>" class='remove' /></a>
					</td>

					</tr>

				<?php
				}
			}

			?>
			</tbody>
			</table>
		</div>
		<div class="col">
			<h3><?php echo _("Aliases");?></h3>
			<p class='error' id='div_errorAlias'></p>
			<!-- Note this table has two <tbody> sections -->
			<!-- Classes are used in resourceUpdateForm.js validation -->
			<table>
				<thead>
					<tr>
						<th scope="col" id="AliasType"><?php echo _("Type:");?></th>
						<th scope="col" id="AliasName"><?php echo _("Alias:");?></th>
						<th scope="col"><?php echo _('Actions'); ?></th>
					</tr>
				</thead>
				<tbody class="newAliasTable">
				<tr class='newAliasTR'>
				<td>
					<select class='changeSelect aliasTypeID' aria-labelledby="AliasType">
					<option value='' selected></option>
					<?php
					foreach ($aliasTypeArray as $aliasType){
						echo "<option value='" . $aliasType['aliasTypeID'] . "' class='changeSelect'>" . $aliasType['shortName'] . "</option>\n";
					}
					?>
					</select>
				</td>

				<td>
				<input type='text' value = '' aria-labelledby="AliasName" class='changeDefault aliasName' />
				</td>

				<td class="actions">
				<a href='javascript:void();'><input class='addAlias add-button' title='<?php echo _("add alias");?>' type='button' value='<?php echo _("Add");?>'/></a>
				</td>
				</tr>
				</tbody>

				<tbody class="aliasTable">
				<?php
				if (is_array($aliasArray) && count($aliasArray) > 0) {
					foreach ($aliasArray as $resourceAlias){
					?>
						<tr>
							<td>
							<select aria-labelledby="AliasType" class='changeSelect aliasTypeID'>
								<option value=''></option>
								<?php
								foreach ($aliasTypeArray as $aliasType){
									if (!(trim(strval($aliasType['aliasTypeID'])) != trim(strval($resourceAlias['aliasTypeID'])))){
										echo "<option value='" . $aliasType['aliasTypeID'] . "' selected class='changeSelect'>" . $aliasType['shortName'] . "</option>\n";
									}else{
										echo "<option value='" . $aliasType['aliasTypeID'] . "' class='changeSelect'>" . $aliasType['shortName'] . "</option>\n";
									}
								}
								?>
							</select>
						</td>

						<td>
							<input type='text' value = '<?php echo htmlentities($resourceAlias['shortName'], ENT_QUOTES); ?>' aria-labelledby="AliasName" class='changeInput aliasName' />
						</td>

						<td class="actions">
							<a href='javascript:void();'><img src='images/cross.gif' alt='<?php echo _("remove this alias");?>' title='<?php echo _("remove this alias");?>' class='remove' /></a>
						</td>
					</tr>
					<?php
					}
				}
				?>
			</tbody>
			</table>
			</div>
		</div>
		<p class='actions'>
			<input type='submit' value='<?php echo _("submit");?>' name='submitProductChanges' id ='submitProductChanges' class='submit-button primary'>
			<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog();" class='cancel-button secondary'>
		</p>
		<script type="text/javascript" src="js/forms/resourceUpdateForm.js?random=<?php echo rand(); ?>"></script>

