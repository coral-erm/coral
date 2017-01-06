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
		<form id='resourceForm'>
		<input type='hidden' name='editResourceID' id='editResourceID' value='<?php echo $resourceID; ?>'>

		<div class='formTitle' style='width:715px; margin-bottom:5px;position:relative;'><span class='headerText'><?php echo _("Edit Resource");?></span></div>

		<span class='smallDarkRedText' id='span_errors'></span>

		<table class='noBorder' style='width:100%;'>
		<tr style='vertical-align:top;'>
		<td style='vertical-align:top;position:relative;' colspan='2'>


			<span class='surroundBoxTitle'>&nbsp;&nbsp;<label for='resourceFormatID'><b><?php echo _("Product");?></b></label>&nbsp;&nbsp;</span>

			<table class='surroundBox' style='width:710px;'>
			<tr>
			<td>
				<table class='noBorder' style='width:670px; margin:15px 20px 10px 20px;'>
				<tr>
				<td style='width:360px;'>
					<table id="general-resource-info">
					<tr>
					<td style='vertical-align:top;text-align:left;font-weight:bold;'><label for='titleText'><?php echo _("Name:");?></label></td>
					<td><input type='text' id='titleText' name='titleText' value = "<?php echo $resource->titleText; ?>" style='width:260px;' class='changeInput' /><span id='span_error_titleText' class='smallDarkRedText'></span></td>
					</tr>

					<tr>
					<td style='vertical-align:top;text-align:left;font-weight:bold;'><label for='descriptionText'><?php echo _("Description:");?></label></td>
					<td><textarea rows='4' id='descriptionText' name='descriptionText' style='width:260px' class='changeInput' ><?php echo $resource->descriptionText; ?></textarea></td>
					</tr>

					<tr>
					<td style='vertical-align:top;text-align:left;font-weight:bold;'><label for='resourceURL'><?php echo _("URL:");?></label></td>
					<td><input type='text' id='resourceURL' name='resourceURL' value = '<?php echo $resource->resourceURL; ?>' style='width:260px;' class='changeInput'  /></td>
					</tr>

					<tr>
					<td style='vertical-align:top;text-align:left;font-weight:bold;'><label for='resourceAltURL'><?php echo _("Alt URL:");?></label></td>
					<td><input type='text' id='resourceAltURL' name='resourceAltURL' value = '<?php echo $resource->resourceAltURL; ?>' style='width:260px;' class='changeInput'  /></td>
					</tr>

					</table>

				</td>
				<td>
					<table>


					<tr>
          <td style='vertical-align:top;text-align:left;font-weight:bold;'><label for='titleText'><?php echo _("Parents:");?></label></td>
					<td>

           <span id="newParent">
           <div class="oneParent">
           <input type='text' class='parentResource parentResource_new' name='parentResourceName' value='' style='width:140px;' class='changeInput'  /><input type='hidden' class='parentResource parentResource_new' name='parentResourceID' value='' /><span id='span_error_parentResourceName' class='smallDarkRedText'></span>
           <a href='#'><input class='addParent add-button' title='<?php echo _("add Parent Resource");?>' type='button' value='<?php echo _("Add");?>'/></a><br />
          </div>
           </span>

          <span id="existingParent"> 
          <?php
           $i = 1;
           foreach ($parentResourceArray as $parentResource) {
$parentResourceObj = new Resource(new NamedArguments(array('primaryKey' => $parentResource['relatedResourceID'])));
             ?>
              <div class="oneParent">
              <input type='text' name='parentResourceName' disabled='disabled' value = '<?php echo $parentResourceObj->titleText; ?>' style='width:180px;' class='changeInput'  />
              <input type='hidden' name='parentResourceID' value = '<?php echo $parentResourceObj->resourceID; ?>' />
              <a href='javascript:void();'><img src='images/cross.gif' alt='<?php echo _("remove parent");?>' title='<?php echo _("remove parent");?>' class='removeParent' /></a>
            </div>
<?php
             $i++;
           }
          ?>
          </span> 
					</td>
					</tr>

					<tr>
					<td style='vertical-align:top;text-align:left;font-weight:bold;'><label for='isbnOrISSN'><?php echo _("ISSN / ISBN:");?></label></td>
<td>
          <span id="newIsbn">
          	<div class="oneIssnIsbn">
           <input type='text' class='isbnOrISSN isbnOrISSN_new' name='isbnOrISSN' value = "" style='width:97px;' class='changeInput'  /><span id='span_errors_isbnOrISSN' class='smallDarkRedText'></span>
           <a href='javascript:void(0);'><input class='addIsbn add-button' title='<?php echo _("add Isbn");?>' type='button' value='<?php echo _("Add");?>'/></a><br />
       </div>
           </span>
           <span id="existingIsbn">
          <?php
           $isbnOrIssns = $resource->getIsbnOrIssn();
           $i = 1;
           foreach ($isbnOrIssns as $isbnOrIssn) {
             ?>
            <div class="oneIssnIsbn">
             	<input type='text' class='isbnOrISSN' name='isbnOrISSN' value = '<?php echo $isbnOrIssn->isbnOrIssn; ?>' style='width:97px;' class='changeInput'  />
				<a href='javascript:void();'><img src='images/cross.gif' alt='<?php echo _("remove Issn/Isbn");?>' title='<?php echo _("remove Issn/Isbn");?>' class='removeIssnIsbn' /></a>
            </div>
            <?php
             $i++;
           }
          ?>
          </span>
          </td>
					</tr>


					<tr>
					<td style='vertical-align:top;text-align:left;font-weight:bold;'><label for='resourceFormatID'><?php echo _("Format:");?></label></td>
					<td>
					<select name='resourceFormatID' id='resourceFormatID' style='width:100px;' class='changeSelect'>
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
					</td>
					</tr>


					<tr>
					<td style='vertical-align:top;text-align:left;font-weight:bold;'><label for='resourceTypeID'><?php echo _("Type:");?></label></td>
					<td>
					<select name='resourceTypeID' id='resourceTypeID' style='width:100px;' class='changeSelect' >
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

					</td>
					</tr>

					<tr>
					<td style='text-align:left'><label for='archiveInd'><b><?php echo _("Archived:");?></b></label></td>
					<td>
					<input type='checkbox' id='archiveInd' name='archiveInd' <?php echo $archiveChecked; ?> />
					</td>
					</tr>

					</table>
				</td>
				</tr>
				</table>
			</td>
			</tr>
			</table>

			<div style='height:10px;'>&nbsp;</div>

			</td>
			</tr>
			<tr style='vertical-align:top;'>
			<td>

			<span class='surroundBoxTitle'>&nbsp;&nbsp;<label for='resourceFormatID'><b>Organizations</b></label>&nbsp;&nbsp;</span>

			<table class='surroundBox' style='width:380px;'>
			<tr>
			<td>

				<table class='noBorder smallPadding newOrganizationTable' style='width:330px;  margin:15px 20px 0px 20px;'>
				<tr>
					<td style='vertical-align:top;text-align:left;font-weight:bold;width:103px;'><?php echo _("Role:");?></td>
					<td style='vertical-align:top;text-align:left;font-weight:bold;width:160px;'><?php echo _("Organization:");?></td>
					<td>&nbsp;</td>
				</tr>

				<tr class='newOrganizationTR'>
				<td style='vertical-align:top;text-align:left;'>
					<select style='width:100px; background:#f5f8fa;' class='changeSelect organizationRoleID'>
					<option value=''></option>
					<?php
					foreach ($organizationRoleArray as $organizationRoleID => $organizationRoleShortName){
						echo "<option value='" . $organizationRoleID . "'>" . $organizationRoleShortName . "</option>\n";
					}
					?>
					</select>
				</td>

				<td style='vertical-align:top;text-align:left;'>
				<input type='text' value = '' style='width:160px;background:#f5f8fa;' class='changeAutocomplete organizationName' />
				<input type='hidden' class='organizationID' value = '' />
				</td>

				<td style='vertical-align:top;text-align:left;width:40px;'>
				<a href='javascript:void();'><input class='addOrganization add-button' title='<?php echo _("add organization");?>' type='button' value='<?php echo _("Add");?>'/></a>
				</td>
				</tr>
				</table>
				<div class='smallDarkRedText' id='div_errorOrganization' style='margin:0px 20px 7px 26px;'></div>

				<table class='noBorder smallPadding organizationTable' style='width:330px;margin:0px 20px 10px 20px;'>
				<tr>
				<td colspan='3'>
					<hr style='width:310px;margin:0px 0px 5px 5px;' />
				</td>
				</tr>

				<?php
				if (count($orgArray) > 0){

					foreach ($orgArray as $organization){
					?>
						<tr>
						<td style='vertical-align:top;text-align:left;'>
						<select style='width:100px;' class='organizationRoleID changeSelect'>
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
						</td>

						<td style='vertical-align:top;text-align:left;'>
						<input type='text' class='changeInput organizationName' value = '<?php echo $organization['organization']; ?>' style='width:160px;' class='changeInput' />
						<input type='hidden' class='organizationID' value = '<?php echo $organization['organizationID']; ?>' />
						</td>

						<td style='vertical-align:top;text-align:left;width:40px;'>
							<a href='javascript:void();'><img src='images/cross.gif' alt="<?php echo _("remove organization");?>" title='<?php echo _("remove ").$resourceOrganization['shortName']._("organization"); ?>' class='remove' /></a>
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

		</td>
		<td>

			<span class='surroundBoxTitle'>&nbsp;&nbsp;<label for='resourceFormatID'><b><?php echo _("Aliases");?></b></label>&nbsp;&nbsp;</span>

			<table class='surroundBox' style='width:300px;'>
			<tr>
			<td>

				<table class='noBorder smallPadding newAliasTable' style='width:260px; margin:15px 20px 0px 20px;'>
				<tr>
					<td style='vertical-align:top;text-align:left;font-weight:bold;width:98px;'><?php echo _("Type:");?></td>
					<td style='vertical-align:top;text-align:left;font-weight:bold;width:125px;'><?php echo _("Alias:");?></td>
					<td>&nbsp;</td>
				</tr>


				<tr class='newAliasTR'>
				<td style='vertical-align:top;text-align:left;'>
					<select style='width:98px; background:#f5f8fa;' class='changeSelect aliasTypeID'>
					<option value='' selected></option>
					<?php
					foreach ($aliasTypeArray as $aliasType){
						echo "<option value='" . $aliasType['aliasTypeID'] . "' class='changeSelect'>" . $aliasType['shortName'] . "</option>\n";
					}
					?>
					</select>
				</td>

				<td style='vertical-align:top;text-align:left;'>
				<input type='text' value = '' style='width:125px;' class='changeDefault aliasName' />
				</td>

				<td style='vertical-align:center;text-align:left;width:37px;'>
				<a href='javascript:void();'><input class='addAlias add-button' title='<?php echo _("add alias");?>' type='button' value='<?php echo _("Add");?>'/></a>
				</td>
				</tr>
				</table>
				<div class='smallDarkRedText' id='div_errorAlias' style='margin:0px 20px 7px 26px;'></div>


				<table class='noBorder smallPadding aliasTable' style='width:260px; margin:0px 20px 10px 20px;'>
				<tr>
				<td colspan='3'>
				<hr style='width:240px;margin:0px 0px 5px 5px;' />
				</td>
				</tr>

				<?php
				if (count($aliasArray) > 0){

					foreach ($aliasArray as $resourceAlias){
					?>
						<tr>
						<td style='vertical-align:top;text-align:left;'>
						<select style='width:98px;' class='changeSelect aliasTypeID'>
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

						<td style='vertical-align:top;text-align:left;'>

						<input type='text' value = '<?php echo htmlentities($resourceAlias['shortName'], ENT_QUOTES); ?>' style='width:125px;' class='changeInput aliasName' />
						</td>

						<td style='vertical-align:top;text-align:left;width:37px;'>
							<a href='javascript:void();'><img src='images/cross.gif' alt='<?php echo _("remove this alias");?>' title='<?php echo _("remove this alias");?>' class='remove' /></a>
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

		</td>
		</tr>
		</table>


		<hr style='width:715px;margin:15px 0px 10px 0px;' />

		<table class='noBorderTable' style='width:125px;'>
			<tr>
				<td style='text-align:left'><input type='button' value='<?php echo _("submit");?>' name='submitProductChanges' id ='submitProductChanges' class='submit-button'></td>
				<td style='text-align:right'><input type='button' value='<?php echo _("cancel");?>' onclick="kill(); tb_remove();" class='cancel-button'></td>
			</tr>
		</table>
		<script type="text/javascript" src="js/forms/resourceUpdateForm.js?random=<?php echo rand(); ?>"></script>

