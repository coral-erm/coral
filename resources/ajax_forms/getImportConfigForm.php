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
<div id='importConfigColumns'>
	<div id='importConfigColumnsLeft'>
		<div id='ic-left-column'>
			<fieldset>
				<legend>
					<?php echo _("General Resource Fields");?>
				</legend>
				<p><span class="ic-label"><?php echo _("Resource Title");?></span><span><input id="resource_titleCol" class="ic-column" value="<?php echo $configuration["title"]?>" /></span></p>
				<p><span class="ic-label"><?php echo _("Description");?></span><span><input id="resource_descCol" class="ic-column" value="<?php echo $configuration["description"]?>" /></span></p>
				<p><span class="ic-label"><?php echo _("Resource URL");?></span><span><input id='resource_urlCol' class="ic-column" value="<?php echo $configuration["url"]?>" /></span></p>
				<p><span class="ic-label"><?php echo _("Alternate URL");?></span><span><input id='resource_altUrlCol' class="ic-column" value="<?php echo $configuration["altUrl"]?>" /></span></p>
				<p><span class="ic-label"><?php echo _("Resource Format");?></span><span><input id="resource_format" class="ic-column" value="<?php echo $configuration["resourceFormat"]?>" /></span></p>
				<p><span class="ic-label"><?php echo _("Resource Type");?></span><span><input id="resource_type" class="ic-column" value="<?php echo $configuration["resourceType"]?>" /></span></p>
			</fieldset>
			<fieldset><legend><?php echo _("Alias Sets");?></legend><div id='resource_alias'>
				<?php
					if(count($configuration["alias"]) > 0) {
						foreach($configuration["alias"] as $alias) {
							echo "<div class='alias-record'><p><span class='ic-label'>" . _("Alias") . "</span><span><input class='ic-column' value='".$alias["column"]."' /></span></p>";
							echo "<p><span class='ic-label'>" . _('Alias Type') . "</span><span><select class='ic-dropdown'>";
							foreach($aliasTypeArray as $aliasType) {
								echo "<option value='" . $aliasType['aliasTypeID'] . "'";
								if($alias['aliasType'] == $aliasType['aliasTypeID']) {
									echo " selected";
								}
								echo ">" . $aliasType['shortName'] . "</option>";
							}
							echo "</select></span></p>";
							echo "<p><span class='ic-label'>" . _("If delimited, delimited by") . "</span><span><input class='ic-delimiter' value='" . $alias["delimiter"]. "' /></span></p></div>";
						}
					}
					else {
						echo "<div class='alias-record'><p><span class='ic-label'>" . _("Alias") . "</span><span><input class='ic-column' value='' /></span></p>";
						echo "<p><span class='ic-label'>" . _('Alias Type') . "</span><span><select class='ic-dropdown'>";
						foreach($aliasTypeArray as $aliasType) {
							echo "<option value='" . $aliasType['aliasTypeID'] . "'>" . $aliasType['shortName'] . "</option>";
						}
						echo "</select></span></p>";
						echo "<p><span class='ic-label'>" . _("If delimited, delimited by") . "</span><span><input class='ic-delimiter' value='' /></span></p></div>";
					}
				?>
			</div><p><a id='add_alias' href='#'><?php echo _("+ Add another alias set");?></a></p></fieldset>

			<fieldset><legend><?php echo _("Resource Parents");?></legend><div id='resource_parent'>
				<?php
					if(count($configuration["parent"]) > 0) {
						foreach($configuration["parent"] as $parent) {
							echo "<p><span class='ic-label'>" . _("Parent Resource") . "</span><span><input class='ic-column' value='" . $parent . "' /></span></p>";
						}
					}
					else {
						echo "<p><span class='ic-label'>" . _("Parent Resource") . "</span><span><input class='ic-column' value='' /></span></p>";
					}
				?>
			</div><p><a id='add_parent' href='#'><?php echo _("+ Add another parent resource")?></a></p></fieldset>


				<fieldset>
					<legend>
						<?php echo _("ISBN/ISSN Sets");?>
					</legend>
					<div id='resource_isbnOrIssn'>
					<?php
						if(count($configuration["isbnOrIssn"]) > 0) {
							foreach($configuration["isbnOrIssn"] as $isbnOrIssn) {
								echo "<div class='isbnOrIssn-record'><p><span class='ic-label'>" . _("ISBN or ISSN") . "</span><span><input class='ic-column' value='" . $isbnOrIssn['column'] . "' /></span></p>";
								echo "<p><span class='ic-label'>" . _("If delimited, delimited by") . "</span><span><input class='ic-delimiter' value='" . $isbnOrIssn["delimiter"]. "' /></span></p>";
								echo "<p><span class='ic-dedupe'><input class='ic-dedupe' type='checkbox'" . (($isbnOrIssn['dedupe'])?' checked':'') . " /><span>" . _("Dedupe on this column") . "</span></p></div>";
							}
						}
						else {
							echo "<div class='isbnOrIssn-record'><p><span class='ic-label'>" . _("ISBN or ISSN") . "</span><span><input class='ic-column' value='' /></span></p>";
							echo "<p><span class='ic-label'>" . _("If delimited, delimited by") . "</span><span><input class='ic-delimiter' value='' /></span></p>";
							echo "<p><span class='ic-dedupe'><input class='ic-dedupe' type='checkbox' /><span>" . _("Dedupe on this column") . "</span></p></div>";
						}
					?>
				</div><p><a id='add_isbnorissn' href='#'><?php echo _("+ Add another ISBN or ISSN set");?></a></p>
			</fieldset>



				<fieldset>
					<legend>
						<?php echo _("Subject Sets");?>
					</legend>
					<div id='resource_subject'>
					<?php
						if(count($configuration["subject"]) > 0) {
							foreach($configuration["subject"] as $subject) {
								echo "<div class='subject-record'><p><span class='ic-label'>" . _("Subject") . "</span><span><input class='ic-column' value='" . $subject['column'] . "' /></span></p>";
								echo "<p><span class='ic-label'>" . _("If delimited, delimited by") . "</span><input class='ic-delimiter' value='" . $subject['delimiter'] . "' /></span></p></div>";
							}
						}
						else {
							echo "<div class='subject-record'><p><span class='ic-label'>" . _("Subject") . "</span><span><input class='ic-column' value='' /></span></p>";
							echo "<p><span class='ic-label'>" . _("If delimited, delimited by") . "</span><input class='ic-delimiter' value='' /></span></p></div>";
						}
					?>
				</div><p><a id='add_subject' href='#'><?php echo _("+ Add another subject set");?></a></p>
			</fieldset>



				<fieldset>
					<legend>
						<?php echo _("Note Sets");?>
					</legend><div id='resource_note'>
					<?php
						if(count($configuration["note"]) > 0) {
							foreach($configuration["note"] as $note) {
								echo "<div class='note-record'><p><span class='ic-label'>" . _("Note") . "</span><span><input class='ic-column' value='" . $note['column'] . "' /></span></p>";
								echo "<p><span class='ic-label'>" . _('Note Type') . "</span><span><select class='ic-dropdown'>";
								foreach($noteTypeArray as $noteType) {
									echo "<option value='" . $noteType['noteTypeID'] . "'";
									if($note['noteType'] == $noteType['noteTypeID']) {
										echo " selected";
									}
									echo ">" . $noteType['shortName'] . "</option>";
								}
								echo "</select></span></p>";
								echo "<p><span class='ic-label'>" . _('If delimited, delimited by') . "</span><span><input class='ic-delimiter' value='" . $note['delimiter'] . "' /></span></p></div>";
							}
						}
						else {
							echo "<div class='note-record'><p><span class='ic-label'>" . _("Note") . "</span><span><input class='ic-column' value='' /></span></p>";
							echo "<p><span class='ic-label'>" . _('Note Type') . "</span><span><select class='ic-dropdown'>";
							foreach($noteTypeArray as $noteType) {
								echo "<option value='" . $noteType['noteTypeID'] . "'>" . $noteType['shortName'] . "</option>";
							}
							echo "</select></span></p>";
							echo "<p><span class='ic-label'>" . _('If delimited, delimited by') . "</span><span><input class='ic-delimiter' value='' /></span></p></div>";
						}
					?>
				</div><p><a id='add_note' href='#'><?php echo _("+ Add another note set");?></a></p></fieldset>


		</div>
	</div>
	<div id='importConfigColumnsRight'>
		<div id='ic-right-column'>

				<fieldset>
					<legend><?php echo _("Organization Sets");?></legend><div id='resource_organization'>
					<?php
						if(count($configuration["organization"]) > 0) {
							foreach($configuration["organization"] as $organization) {
								echo "<div class='organization-record'><p><span class='ic-label'>" . _("Organization") . "</span><span><input class='ic-column' value='".$organization['column']."' /></span></p><p><span class='ic-label'>" . _('Organization Role') . "</span><span><select class='ic-dropdown'>";
								foreach($organizationRoleArray as $organizationRoleID => $organizationRoleShortName) {
									echo "<option value='" . $organizationRoleID . "'";
									if($organization["organizationRole"] == $organizationRoleID) {
										echo " selected";
									}
									echo ">" . $organizationRoleShortName . "</option>";
								}
								echo "</select></span></p></div>";
							}
						}
						else {
							echo "<div class='organization-record'><p><span class='ic-label'>" . _("Organization") . "</span><span><input class='ic-column' value='' /></span></p><p><span class='ic-label'>" . _('Organization Role') . "</span><span><select class='ic-dropdown'>";
							foreach($organizationRoleArray as $organizationRoleID => $organizationRoleShortName) {
								echo "<option value='" . $organizationRoleID . "'>" . $organizationRoleShortName . "</option>";
							}
							echo "</select></span></p></div>";
						}
					?>
				</div><p><a id='add_organization' href='#'><?php echo _("+ Add another organization set");?></a></p></fieldset>



			<fieldset>
			<legend><?php echo _("Organization Name Mapping");?></legend><div id='resource_organization'>
			<p><?php echo _("Use these mappings to normalize different variations of an organization’s name to a single value. For example, you could have a publisher with three variations of their name across your import file: PublisherHouse, PublisherH, and PH. You could create a mapping for each one and normalize them all to PublisherHouse, to prevent duplicate organizations from being created. Each column that is added to an Organization set above is checked against the complete list of mappings that you create. ") . "<a id='regexLink' href='https://en.wikipedia.org/wiki/Perl_Compatible_Regular_Expressions' target='_blank'>" . _("PCRE regular expressions") . "</a>" . _(" are supported for these mappings.");?></p>
			<div id='importConfigOrgMapping'>
				<table id='org_mapping_table' >
					<tr>
						<th><?php echo _("Organization Name");?></th>
						<th><?php echo _("Will Be Mapped To");?></th>
						<th></th>
						<th></th>
					</tr>
					<?php
						if (isset($orgMappingsNamed) && $orgMappingsImported) {
							for ($i = 0; $i < count($orgMappingsNamed); $i++) {
								if ( $orgMappingsNamed[$i] && $orgMappingsImported[$i]) {
									echo "<tr><td><input class='ic-org-imported' value='" . $orgMappingsImported[$i] . "' /></td>";
									echo "<td><input class='ic-org-mapped' value='" . $orgMappingsNamed[$i] . "' /></td>";
									echo "<td><img class='remove' src='images/cross.gif' /></td></tr>";
								}
							}
						} elseif (isset($orgMappings) ? count($orgMappings)>0 : '') {
							foreach($orgMappings as $orgMapping) {
								echo "<tr><td><input class='ic-org-imported' value='" . $orgMapping->orgNameImported . "' /></td>";
								echo "<td><input class='ic-org-mapped' value='" . $orgMapping->orgNameMapped . "' /></td>";
								echo "<td><img class='remove' src='images/cross.gif' /></td></tr>";
							}
						}
						else {
							echo "<tr><td><input class='ic-org-imported' /></td><td><input class='ic-org-mapped' /></td><td><img class='remove' src='images/cross.gif' /></td></tr>";
						}
					?>
				</table>
				<a id='add_mapping' href='#'><?php echo _("+ Add another mapping")?></a>
			</div>
		</fieldset>
		</div>
	</div>
	<div style='clear: both;'></div>
</div>
<script type='text/javascript'>
	$(".remove").live('click', function () {
	    $(this).parent().parent().fadeTo(400, 0, function () {
			$(this).remove();
	    });
	});
   $('#add_alias').click(function (e) {
   		e.preventDefault();
   		$('#resource_alias').append(
   			"<div class='alias-record'><p><span class='ic-label'><?php echo _('Alias');?></span><span><input class='ic-column' value='' /></span></p><p><span class='ic-label'><?php echo _('Alias Type');?></span><span><select class='ic-dropdown'><?php echo $aliasOptions?></select></span></p><p><span class='ic-label'><?php echo _('If delimited, delimited by');?></span><span><input class='ic-delimiter' value='' /></span></p></div>"
   		);
   });
   $('#add_parent').click(function (e) {
   		e.preventDefault();
   		$('#resource_parent').append (
   			"<p><span class='ic-label'><?php echo _('Parent Resource');?></span><span><input class='ic-column' value='' /></span></p>"
   		);
   });
   $('#add_isbnorissn').click(function (e) {
   		e.preventDefault();
   		$('#resource_isbnOrIssn').append (
   			"<div class='isbnOrIssn-record'><p><span class='ic-label'><?php echo _('ISBN or ISSN');?></span><span><input class='ic-column' value='' /></span></p><p><span class='ic-label'><?php echo _('If delimited, delimited by');?></span><input class='ic-delimiter' value='' /></span></p><p><span class='ic-dedupe'><input class='ic-dedupe' type='checkbox' /><span><?php echo _('Dedupe on this column');?></span></p></div>"
   		);
   });
   $('#add_subject').click(function (e) {
   		e.preventDefault();
   		$('#resource_subject').append(
   			"<div class='subject-record'><p><span class='ic-label'><?php echo _('Subject');?></span><span><input class='ic-column' value='' /></span></p><p><span class='ic-label'><?php echo _('If delimited, delimited by');?></span><input class='ic-delimiter' value='' /></span></p></div>"
   		);
   });
   $('#add_note').click(function (e) {
   		e.preventDefault();
   		$('#resource_note').append (
			"<div class='note-record'><p><span class='ic-label'><?php echo _('Note');?></span><span><input class='ic-column' value='' /></span></p><p><span class='ic-label'><?php echo _('Note Type');?></span><span><select class='ic-dropdown'><?php echo $noteOptions?></select></span></p><p><span class='ic-label'><?php echo _('If delimited, delimited by');?></span><span><input class='ic-delimiter' value='' /></span></p></div>"
   		);
   });
   $('#add_organization').click(function (e) {
   		e.preventDefault();
   		$('#resource_organization').append (
			"<div class='organization-record'><p><span class='ic-label'><?php echo _('Organization');?></span><span><input class='ic-column' value='' /></span></p><p><span class='ic-label'><?php echo _('Organization Role');?></span><span><select class='ic-dropdown'><?php echo $organizationOptions?></select></span></p></div>"
   		);
   });
   $('#add_mapping').click(function (e) {
   		e.preventDefault();
   		$('#org_mapping_table').append (
   			"<tr><td><input class='ic-org-imported' /></td><td><input class='ic-org-mapped' /></td><td><img class='remove' src='images/cross.gif' /></td></tr>"
   		);
   });
</script>
