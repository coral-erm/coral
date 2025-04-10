<?php
	if (isset($_POST['jsonData'])) {
		$jsonData = $_POST['jsonData'];
		$configuration = json_decode($jsonData, true);
		$orgMappingsNamed = explode(":::", $_POST['orgNamesMapped']);
		$orgMappingsImported = explode(":::", $_POST['orgNamesImported']);
	} elseif (isset($_POST['configID'])) {
		$configID = $_POST['configID'];
		$instance = new ImportConfig(new NamedArguments(array('primaryKey' => $configID)));
		$orgMappingInstance = new OrgNameMapping();
		$orgMappings=$orgMappingInstance->getOrgNameMappingByImportConfigID($configID);
		$configuration=json_decode($instance->configuration,true);
	}

	//get all alias types for output in drop-down menu
	$aliasTypeArray = array();
	$aliasTypeObj = new AliasType();
	$aliasTypeArray = $aliasTypeObj->allAsArray();
	$aliasOptions = "";
	foreach($aliasTypeArray as $aliasType) {
		$aliasOptions .= "<option value='" . $aliasType['aliasTypeID'] . "'>" . $aliasType['shortName'] . "</option>";
	}


	//get all note types for output in drop-down menu
	$noteTypeArray = array();
	$noteTypeObj = new NoteType();
	$noteTypeArray = $noteTypeObj->allAsArrayForDD();
	$noteOptions = "";
	foreach($noteTypeArray as $noteType) {
		$noteOptions .= "<option value='" . $noteType['noteTypeID'] . "'>" . $noteType['shortName'] . "</option>";
	}

	//get all organization roles for output in drop-down menu
	$organizationRoleArray = array();
	$organizationRoleObj = new OrganizationRole();
	$organizationRoleArray = $organizationRoleObj->getArray();
	$organizationOptions = "";
	foreach($organizationRoleArray as $organizationRoleID => $organizationRoleShortName) {
		$organizationOptions .= "<option value='" . $organizationRoleID . "'>" . $organizationRoleShortName . "</option>";
	}
?>
<div id='importConfigColumns' class="flex flex-auto">
<div>
	<fieldset class="border">
		<legend><?php echo _("General Resource Fields");?></legend>
		<p>
			<label for="resource_titleCol"><?php echo _("Resource Title");?></label>
			<input id="resource_titleCol" type="text" inputmode="numeric" size="4" pattern="[0-9]" size="4" value="<?php echo $configuration["title"]?>" />
		</p>
		<p>
			<label for="resource_descCol"><?php echo _("Description");?></label>
			<input id="resource_descCol" type="text" inputmode="numeric" size="4" pattern="[0-9]" size="4" value="<?php echo $configuration["description"]?>" />
		</p>
		<p>
			<label for="resource_urlCol"><?php echo _("Resource URL");?></label>
			<input id='resource_urlCol' type="text" inputmode="numeric" size="4" pattern="[0-9]" size="4" value="<?php echo $configuration["url"]?>" />
		</p>
		<p>
			<label for="resource_altUrlCol"><?php echo _("Alternate URL");?></label>
			<input id='resource_altUrlCol' type="text" inputmode="numeric" size="4" pattern="[0-9]" size="4" value="<?php echo $configuration["altUrl"]?>" />
		</p>
		<p>
			<label for="resource_format"><?php echo _("Resource Format");?></label>
			<input id="resource_format" type="text" inputmode="numeric" size="4" pattern="[0-9]" size="4" value="<?php echo $configuration["resourceFormat"]?>" />
		</p>
		<p>
			<label for="resource_type"><?php echo _("Resource Type");?></label>
			<input id="resource_type" type="text" inputmode="numeric" size="4" pattern="[0-9]" size="4" value="<?php echo $configuration["resourceType"]?>" />
		</p>
		<p>
			<label for="acquisition_type"><?php echo _("Acquisition Type");?></label>
			<input id="acquisition_type" type="text" inputmode="numeric" size="4" pattern="[0-9]" size="4" value="<?php echo $configuration["acquisitionType"]?>" />
		</p>
	</fieldset>

	<fieldset class="border">
		<legend><?php echo _("Alias Sets");?></legend>
		<div id='resource_alias'>
		<?php
			$counter = 0;
			if (is_array($configuration) && is_array($configuration["alias"])) {
				
				foreach($configuration["alias"] as $alias) { 
					$counter++; 
					?>
					<p>
						<label for="column-<?php echo intval($counter) ?>"><?php echo _("Alias") ?></label>
						<input id="column-<?php echo intval($counter) ?>" type="text" inputmode="numeric" size="4" pattern="[0-9]" size="4" value='<?php echo $alias["column"] ?>' />
					</p>
					<p>
						<label for="alias-type-<?php echo intval($counter) ?>"><?php echo _("Alias Type") ?></label>
						<select id="alias-type-<?php echo intval($counter) ?>" class="span-2">
							<?php
							foreach($aliasTypeArray as $aliasType) {
								echo "<option value='" . $aliasType['aliasTypeID'] . "'";
								if($alias['aliasType'] == $aliasType['aliasTypeID']) {
									echo " selected";
								}
								echo ">" . $aliasType['shortName'] . "</option>";
							}
						?>
						</select>
					</p>
					<p>
						<label for="alias-delimiter-<?php echo intval($counter) ?>"><?php echo _("Delimiter") ?></label>
						<input id="alias-delimiter-<?php echo intval($counter) ?>" type="text" size="2" value='<?php echo $alias["delimiter"] ?>' />
					</p>
					<?php
					}
				}
				else { ?>
					<p>
						<label for="column-<?php echo intval($counter) ?>"><?php echo _("Alias") ?></label>
						<input id="column-<?php echo intval($counter) ?>" type="text" inputmode="numeric" size="4" pattern="[0-9]" size="4" value='' />
					</p>
					<p>
						<label for="alias-type-<?php echo intval($counter) ?>"><?php echo _('Alias Type') ?></label>
						<select id="alias-type-<?php echo intval($counter) ?>">
							<?php
							foreach($aliasTypeArray as $aliasType) {
								echo "<option value='" . $aliasType['aliasTypeID'] . "'>" . $aliasType['shortName'] . "</option>";
							}
							?>
						</select>
					</p>

					<p>
						<label for="alias-delimiter-<?php echo intval($counter) ?>"><?php echo _("Delimiter") ?></label>
						<input id="alias-delimiter-<?php echo intval($counter) ?>" type="text" size="2" value='' />
					</p>
					<?php
					}
			?>
		<p class="end"><a id='add_alias' href='#'><?php echo _("+ Add another alias set");?></a></p>
		</div>
	</fieldset>

	<fieldset class="border">
		<legend><?php echo _("Resource Parents");?></legend>
		<div id='resource_parent'>
		<?php
			$counter = 0;
			if (is_array($configuration) && is_array($configuration["parent"])) {
				$counter++;
				foreach($configuration["parent"] as $parent) { ?>
					<p>
						<label for="parent-<?php echo intval($counter) ?>"><?php echo _("Parent Resource") ?></label>
						<input id="parent-<?php echo intval($counter) ?>" type="text" inputmode="numeric" size="4" pattern="[0-9]" size="4" value="<?php echo $parent ?>" />
					</p>
				<?php
				}
			}
			else { ?>
				<p>
					<label for="parent-<?php echo intval($counter) ?>"><?php echo _("Parent Resource") ?></label>
					<input id="parent-<?php echo intval($counter) ?>" type="text" inputmode="numeric" size="4" pattern="[0-9]" size="4" value='' />
				</p>
			<?php
			}
		?>
		</div>
		<p class="end"><a id='add_parent' href='#'><?php echo _("+ Add another parent resource")?></a></p>
	
	</fieldset>

	<fieldset class="border">
		<legend><?php echo _("ISBN/ISSN Sets");?></legend>
		<div id='resource_isbnOrIssn'>
			<?php
				$counter = 0;
				if (is_array($configuration) && is_array($configuration["isbnOrIssn"])) {
					foreach($configuration["isbnOrIssn"] as $isbnOrIssn) { 
						$counter++;
						?>
						<div class='isbnOrIssn-record'>
							<p>
								<label for="isbn-<?php echo intval($counter) ?>"><?php echo _("ISBN or ISSN") ?>
								<input id="isbn-<?php echo intval($counter) ?>" type="text" inputmode="numeric" size="4" pattern="[0-9]" size="4" value="<?php echo $isbnOrIssn['column'] ?>" />
							</p>
						
							<p>
								<label for="isbn-delimiter-<?php echo intval($counter) ?>"><?php echo _("Delimiter") ?></label>
								<input id="isbn-delimiter-<?php echo intval($counter) ?>" type="text" size="2" value="<?php echo $isbnOrIssn["delimiter"] ?>" />
							</p>

							<p class="checkbox">
								<input id="isbn-dedupe-<?php echo intval($counter) ?>" type='checkbox' <?php echo $isbnOrIssn['dedupe'] ? ' checked' : '' ?> />
								<label for="isbn-dedupe-<?php echo intval($counter) ?>"><?php echo _("Dedupe on this column") ?></label>
							</p>
						</div>
					<?php
					}
				}
				else { ?>
					<div class='isbnOrIssn-record'>
						<p>
							<label for="isbn-<?php echo intval($counter) ?>"><?php echo _("ISBN or ISSN") ?></label>
							<input id="isbn-<?php echo intval($counter) ?>" type="text" inputmode="numeric" size="4" pattern="[0-9]" size="4" value='' />
						</p>
					
						<p>
							<label for="isbn-delimiter-<?php echo intval($counter) ?>"><?php echo _("Delimiter") ?></label>
							<input id="isbn-delimiter-<?php echo intval($counter) ?>" type="text" size="2" value='' />
						</p>
						
						<p class="checkbox">
							<input id="isbn-dedupe-<?php echo intval($counter) ?>" type='checkbox' />
							<label for="isbn-dedupe-<?php echo intval($counter) ?>"><?php echo _("Dedupe on this column") ?></label>
						</p>
					</div>
				<?php
				}
			?>
		</div>
		<p class="end"><a id='add_isbnorissn' href='#'><?php echo _("+ Add another ISBN or ISSN set");?></a></p>
	</fieldset>

	<fieldset class="border">
			<legend><?php echo _("Subject Sets");?></legend>
			<div id='resource_subject'>
			<?php
				$counter = 0;
				if (is_array($configuration) && is_array($configuration["subject"])) {
					foreach($configuration["subject"] as $subject) {
						$counter++;
						?>
						<p>
							<label for="subject-<?php echo intval($counter) ?>"><?php echo _("Subject") ?></label>
							<input id="subject-<?php echo intval($counter) ?>" type="text" inputmode="numeric" size="4" pattern="[0-9]" size="4" value="<?php echo $subject['column'] ?>" />
						</p>
					
						<p>
							<label for="subject-delimiter-<?php echo intval($counter) ?>"><?php echo _("Delimiter") ?></label>
							<input id="subject-delimiter-<?php echo intval($counter) ?>" type="text" size="2" value="<?php echo $subject['delimiter'] ?>" />
						</p>
					<?php
					}
				}
				else {
					?>
					<p>
						<label for="subject-<?php echo intval($counter) ?>"><?php echo _("Subject") ?></label>
						<input id="subject-<?php echo intval($counter) ?>" type="text" inputmode="numeric" size="4" pattern="[0-9]" size="4" value='' />
					</p>
					
					<p>
						<label for="subject-delimiter-<?php echo intval($counter) ?>"><?php echo _("Delimiter") ?></label>
						<input id="subject-delimiter-<?php echo intval($counter) ?>" type="text" size="2" value="" />
					</p>
				<?php
				}
			?>
		</div>
		<p class="end"><a id='add_subject' href='#'><?php echo _("+ Add another subject set");?></a></p>
	</fieldset>

	<fieldset class="border">
		<legend><?php echo _("Note Sets");?></legend>
		<div id='resource_note'>
		<?php
			$counter = 0;
			if (is_array($configuration) && is_array($configuration["note"])) {
				foreach($configuration["note"] as $note) {
					$counter++;
					?>
					<p>
						<label for="note-<?php echo intval($counter) ?>"><?php echo _("Note") ?></label>
						<input id="note-<?php echo intval($counter) ?>" type="text" inputmode="numeric" size="4" pattern="[0-9]" size="4" value="<?php echo $note['column'] ?>" />
					</p>
				
					<p>
						<label for="note-type-<?php echo intval($counter) ?>"><?php echo _('Note Type') ?></label>
						<select id="note-type-<?php echo intval($counter) ?>">
							<?php
							foreach($noteTypeArray as $noteType) {
								echo "<option value='" . $noteType['noteTypeID'] . "'";
								if($note['noteType'] == $noteType['noteTypeID']) {
									echo " selected";
								}
								echo ">" . $noteType['shortName'] . "</option>";
							}
							?>
						</select>
					</p>
				
					<p>
						<label for="note-delimiter-<?php echo intval($counter) ?>"><?php echo _('Delimiter') ?></label>
						<input id="note-delimiter-<?php echo intval($counter) ?>" type="text" size="2" value="<?php echo $note['delimiter'] ?>" />
					</p>
					<?php
				}
			}
			else {
				?>
				<p>
					<label for="note-<?php echo intval($counter) ?>"><?php echo _("Note") ?></label>
					<input id="note-<?php echo intval($counter) ?>" type="text" inputmode="numeric" size="4" pattern="[0-9]" size="4" value='' />
				</p>
				
				<p>
					<label for="note-type-<?php echo intval($counter) ?>"><?php echo _('Note Type') ?></label>
					<select id="note-type-<?php echo intval($counter) ?>">
						<?php
						foreach($noteTypeArray as $noteType) {
							echo "<option value='" . $noteType['noteTypeID'] . "'>" . $noteType['shortName'] . "</option>";
						}
						?>
					</select>
				</p>

				<p>
					<label for="note-delimiter-<?php echo intval($counter) ?>"><?php echo _('Delimiter') ?></label>
					<input id="note-delimiter-<?php echo intval($counter) ?>" type="text" size="2" value="" />
				</p>
			<?php
			}
		?>
		</div>
		<p class="end"><a id='add_note' href='#'><?php echo _("+ Add another note set");?></a></p>
	</fieldset>
</div>
<div>
	<fieldset class="border block-form">
		<legend><?php echo _("Organization Sets");?></legend>
		<div id='resource_organization' class="flex flex-auto">
			<?php
				$counter = 0;
				if (is_array($configuration) && is_array($configuration["organization"])) {
					foreach($configuration["organization"] as $organization) {
						$counter++;
						?>
							<p>
								<label for="org-<?php echo intval($counter) ?>"><?php echo _("Organization") ?></label>
								<input id="org-<?php echo intval($counter) ?>" type="text" value="<?php echo $organization['column'] ?>" />
							</p>
							<p>
								<label for="org-role-<?php echo intval($counter) ?>"><?php echo _('Organization Role') ?></label>
								<select id="org-role-<?php echo intval($counter) ?>">
									<?php
									foreach ($organizationRoleArray as $organizationRoleID => $organizationRoleShortName) {
										echo "<option value='" . $organizationRoleID . "'";
										if($organization["organizationRole"] == $organizationRoleID) {
											echo " selected";
										}
										echo ">" . $organizationRoleShortName . "</option>";
									}
									?>
								</select>
							</p>
						<?php
					}
				}
				else {
					?>
						<p>
							<label for="org-<?php echo intval($counter) ?>"><?php echo _("Organization") ?></label>
							<input id="org-<?php echo intval($counter) ?>" type="text" value='' />
						</p>
						<p>
							<label for="org-role-<?php echo intval($counter) ?>"><?php echo _('Organization Role') ?></label>
							<select id="org-role-<?php echo intval($counter) ?>">
								<?php
								foreach($organizationRoleArray as $organizationRoleID => $organizationRoleShortName) {
									echo "<option value='" . $organizationRoleID . "'>" . $organizationRoleShortName . "</option>";
								}
								?>
							</select>
						</p>
					<?php
				}
			?>
		</div>
		<p class="end"><a id='add_organization' href='#'><?php echo _("+ Add another organization set");?></a></p>
	</fieldset>

	<fieldset class="border">
		<legend><?php echo _("Organization Name Mapping");?></legend>
		<div id='resource_organization'>
			<p>
				<?php echo _("Use these mappings to normalize different variations of an organization’s 
						name to a single value. For example, you could have a publisher with three variations 
						of their name across your import file: PublisherHouse, PublisherH, and PH. You could 
						create a mapping for each one and normalize them all to PublisherHouse, to prevent 
						duplicate organizations from being created. Each column that is added to an Organization 
						set above is checked against the complete list of mappings that you create. ") 
						. 
						"<a id='regexLink' href='https://en.wikipedia.org/wiki/Perl_Compatible_Regular_Expressions' " . getTarget() . ">"
						. 
						_("PCRE regular expressions") . "</a>" . _(" are supported for these mappings.");?>
			</p>
			<div id='importConfigOrgMapping'>
				<table id='org_mapping_table' class="wide">
					<thead>
						<tr>
							<th id="org_mapping_name"><?php echo _("Organization Name");?></th>
							<th id="org_mapping_to"><?php echo _("Will Be Mapped To");?></th>
							<th class="actions"><?php echo _("Remove") ?></th>
						</tr>
					</thead>
					<tbody>
					<?php
						if (isset($orgMappingsNamed) && $orgMappingsImported) {
							for ($i = 0; $i < count($orgMappingsNamed); $i++) {
								if ( $orgMappingsNamed[$i] && $orgMappingsImported[$i]) {
									echo "<tr><td><input type='text' value='" . $orgMappingsImported[$i] . "' aria-labelledby='org_mapping_name' /></td>";
									echo "<td><input type='text' value='" . $orgMappingsNamed[$i] . "'  aria-labelledby='org_mapping_to' /></td>";
									echo "<td class='actions'><img class='remove' src='images/cross.gif' alt='" . sprintf(_("Remove mapping %d"), $i + 1) . "' /></td></tr>";
								}
							}
						} elseif (isset($orgMappings) ? count($orgMappings)>0 : '') {
							foreach($orgMappings as $orgMapping) {
								echo "<tr><td><input type='text' value='" . $orgMapping->orgNameImported . "'  aria-labelledby='org_mapping_name' /></td>";
								echo "<td><input type='text' value='" . $orgMapping->orgNameMapped . "'  aria-labelledby='org_mapping_to' /></td>";
								echo "<td class='actions'><img class='remove' src='images/cross.gif' alt='" . sprintf(_("Remove mapping %d"), $i + 1) . "' /></td></tr>";
							}
						}
						else {
							echo "<tr><td><input type='text' aria-labelledby='org_mapping_name' /></td><td><input type='text' aria-labelledby='org_mapping_to' /></td><td class='actions'><img class='remove' src='images/cross.gif' alt='" . sprintf(_("Remove mapping %d"), $i + 1) . "'/></td></tr>";
						}
					?>
					</tbody>
				</table>
				<p class="end"><a id='add_mapping' href='#'><?php echo _("+ Add another mapping")?></a></p>
			</div>
		</div>
	</fieldset>

	<fieldset class="border">
		<legend><?php echo _("Acquisitions"); ?></legend>
		<div id="resource_acquisitions">
			<p>
				<label for="fundCode"><?php echo _("Fund Code");?></label>
				<input id="fundCode" class="ic-column" type="text" inputmode="numeric" size="4" pattern="[0-9]" value="<?php echo $configuration["fundCode"]?>" />
			</p>

			<p>
				<label for="cost"><?php echo _("Cost");?></label>
				<input id="cost" class="ic-column" type="text" inputmode="numeric" size="4" pattern="[0-9]" value="<?php echo $configuration["cost"]?>" />
			</p>

			<p>
				<label class="ic-label" for="orderType"><?php echo _("Order Type");?></label>
				<select id="orderType" name="orderType">
					<?php
					$orderTypeObj = new OrderType();
					foreach ($orderTypeObj->allAsArray() as $orderType) {
						echo ('<option value="' . $orderType['orderTypeID'] . '"');
						if ($configuration['orderTypeID'] == $orderType['orderTypeID']) {
							echo (' selected="selected"');
						}
						echo ('>' . $orderType['shortName'] . '</option>');
					}
					?>
				</select>
			</p>
			
			<p>
				<label class="ic-label" for="currency"><?php echo _("Currency");?></label>
				<select id="currency" name="currency">
					<?php
					$currencyObj = new Currency();
					foreach ($currencyObj->allAsArray() as $currency) {
						echo ('<option value="' . $currency['currencyCode'] . '"');
						if ($configuration['currencyCode'] == $currency['currencyCode']) {
							echo (' selected="selected"');
						}
						echo ('>' . $currency['shortName'] . ' (' . $currency['currencyCode'] . ')</option>');
					}
					?>
				</select>
			</p>
		</div>
	</fieldset>

	<fieldset class="border">
		<legend><?php echo _("Workflows"); ?></legend>
		<div id="resource_workflows">
			<p class="checkbox"><input type="checkbox" name="sendemails" id="sendemails"<?php if ($configuration['sendemails']) echo ' checked="checked"'; ?>> <label for="sendemails"><?php echo _("Send emails when starting workflows?"); ?></label></p>
		</div>
	</fieldset>
</div>
</div>
<script type='text/javascript'>
	$(".remove").click(function () {
	    $(this).parent().parent().fadeTo(400, 0, function () {
			$(this).remove();
	    });
	});
   $('#add_alias').click(function (e) {
   		e.preventDefault();
   		$('#resource_alias').append(
   			"<div class='alias-record'><p><label><?php echo _('Alias');?><input class='ic-column' value='' /></label></p><p><label><?php echo _('Alias Type');?><select class='ic-dropdown'><?php echo $aliasOptions?></select></label></p><p><label><?php echo _('Delimiter');?><input type='text' size='2' value='' /></label></p></div>"
   		);
   });
   $('#add_parent').click(function (e) {
   		e.preventDefault();
   		$('#resource_parent').append (
   			"<p><label><?php echo _('Parent Resource');?><input class='ic-column' value='' /></label></p>"
   		);
   });
   $('#add_isbnorissn').click(function (e) {
   		e.preventDefault();
   		$('#resource_isbnOrIssn').append (
   			"<div class='isbnOrIssn-record'><p><label><?php echo _('ISBN or ISSN');?><input class='ic-column' value='' /></label></p><p><label><?php echo _('Delimiter');?><input class='ic-delimiter' size='2' value='' /></label></p><p><label class='ic-dedupe'><input class='ic-dedupe' type='checkbox' /><?php echo _('Dedupe on this column');?></label></p></div>"
   		);
   });
   $('#add_subject').click(function (e) {
   		e.preventDefault();
   		$('#resource_subject').append(
   			"<div class='subject-record'><p><label><?php echo _('Subject');?><input class='ic-column' value='' /></label></p><p><label><?php echo _('Delimiter');?><input class='ic-delimiter' size='2' value='' /></label></p></div>"
   		);
   });
   $('#add_note').click(function (e) {
   		e.preventDefault();
   		$('#resource_note').append (
			"<div class='note-record'><p><label><?php echo _('Note');?><input class='ic-column' value='' /></label></p><p><label><?php echo _('Note Type');?><select class='ic-dropdown'><?php echo $noteOptions?></select></label></p><p><label><?php echo _('Delimiter');?><input type='text' size='2' value='' /></label></p></div>"
   		);
   });
   $('#add_organization').click(function (e) {
   		e.preventDefault();
   		$('#resource_organization').append (
			"<div class='organization-record'><p><label><?php echo _('Organization');?><input class='ic-column' value='' /></label></p><p><label><?php echo _('Organization Role');?><select class='ic-dropdown'><?php echo $organizationOptions?></select></label></p></div>"
   		);
   });
   $('#add_mapping').click(function (e) {
   		e.preventDefault();
   		$('#org_mapping_table').append (
   			"<tr><td><input class='ic-org-imported' aria-labelledby='org_mapping_name' /></td><td><input class='ic-org-mapped' aria-labelledby='org_mapping_to' /></td><td><img class='remove' src='images/cross.gif' /></td></tr>"
   		);
   });
</script>
