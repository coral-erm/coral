<?php
			$resourceID = $_GET['resourceID'];
			$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));
			$resourceFormat = new ResourceFormat(new NamedArguments(array('primaryKey' => $resource->resourceFormatID)));
			$resourceType = new ResourceType(new NamedArguments(array('primaryKey' => $resource->resourceTypeID)));
			$acquisitionType = new AcquisitionType(new NamedArguments(array('primaryKey' => $resource->acquisitionTypeID)));
			$status = new Status(new NamedArguments(array('primaryKey' => $resource->statusID)));

			$createUser = new User(new NamedArguments(array('primaryKey' => $resource->createLoginID)));
			$updateUser = new User(new NamedArguments(array('primaryKey' => $resource->updateLoginID)));
			$archiveUser = new User(new NamedArguments(array('primaryKey' => $resource->archiveLoginID)));

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

			//get children resources
			$childResourceArray = array();
			foreach ($resource->getChildResources() as $instance) {
				foreach (array_keys($instance->attributeNames) as $attributeName) {
					$sanitizedInstance[$attributeName] = $instance->$attributeName;
				}

				$sanitizedInstance[$instance->primaryKeyName] = $instance->primaryKey;

				array_push($childResourceArray, $sanitizedInstance);
			}


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

			//get organizations (already returned in an array)
			$orgArray = $resource->getOrganizationArray();


		?>
		
					<p>
						
						<span class="editElement">
							<?php if ($user->canEdit()) { ?>
								<a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getUpdateProductForm&resourceID=<?php echo $resource->resourceID; ?>", 800,830)'
									class='thickbox'>

									<img src='images/edit.gif'
										alt='<?php echo _("edit");?>'
										title='<?php echo _("edit resource");?>' /></a>
									<?php } ?>
									<?php if ($user->isAdmin) { ?>
										<a href='javascript:void(0);'
											class='removeResource'
											id='<?php echo $resourceID; ?>'>

											<img src='images/cross.gif'
												alt='<?php echo _("remove resource");?>'
												title='<?php echo _("remove resource");?>' /></a>
										<a href='javascript:void(0);'
											class='removeResourceAndChildren'
											id='<?php echo $resourceID; ?>'>

											<img src='images/deleteall.png'
												alt='<?php echo _("remove resource and its children");?>'
												title='<?php echo _("remove resource and its children");?>' />
										</a>
									<?php } ?>
							</span>
					</p>
					<dl class="dl-grid dl-compact">
						<?php if ($acquisitionType->shortName) { ?>
							<dt><?php echo _('Acquisition Type:'); ?></dt>
							<dd><?php echo $acquisitionType->shortName; ?></dd>
						<?php } ?>
						<?php if ($resourceFormat->shortName) { ?>
							<dt><?php echo _('Resource Format:'); ?></dt>
							<dd><?php echo $resourceFormat->shortName; ?></dd>
						<?php } ?>
						<?php if ($resourceType->shortName) { ?>
							<dt><?php echo _('Resource Type:'); ?></dt>
							<dd><?php echo $resourceType->shortName;; ?></dd>
						<?php } ?>

			<dt><?php echo _("Status:");?></dt>
			<dd><?php echo $status->shortName; ?></dd>
			
			<?php
			if (($resource->archiveDate) && ($resource->archiveDate != '0000-00-00')){
			?>

				<dt class="archived">
				<?php echo _("Archived:");?>
				</dt>
				<dd class="archived byline">
				<?php
					if ($archiveUser->getDisplayName){
						printf(_("%s by %s"), format_date($resource->createDate), $archiveUser->getDisplayName);
					}else if ($resource->archiveLoginID){
						printf(_("%s by %s"), format_date($resource->createDate), $resource->archiveLoginID);
					}
				?>
				</dd>
			<?php
			}
			?>

			<dt>
			<?php echo _("Created:");?>
			</dt>
			<dd class="byline">
				<?php
					if ($createUser->getDisplayName){
						printf(_("%s by %s"), format_date($resource->createDate), $createUser->getDisplayName);
					}else if ($resource->createLoginID){
						printf(_("%s by %s"), format_date($resource->createDate), $createUser->createLoginID);
					}
				?>
			</dd>

			<?php
			if (($resource->updateDate) && ($resource->updateDate != '0000-00-00')){
			?>

				<dt>
				<?php echo _("Last Update:");?>
				</dt>
				<dd class="byline">
				<?php
					if ($updateUser->getDisplayName){
						printf(_("%s by %s"), format_date($resource->updateDate), $updateUser->getDisplayName);
					}else if ($resource->updateLoginID){
						printf(_("%s by %s"), format_date($resource->updateDate), $resource->updateLoginID);
					}
				?>
				</dd>

			<?php
			}




      if ((count($parentResourceArray) > 0) || (count($childResourceArray) > 0)){ ?>
				<dt><?php echo _("Related Products:");?>
				</dt>
				<dd>
				<?php

        if (is_array($parentResourceArray) && count($parentResourceArray) > 0) {
					echo "<div id='parentResources'>";
					echo "<h3>" . _("Parent Resources") . "</h3>";
					echo "<ul class='unstyled'>";
          foreach ($parentResourceArray as $parentResource){
            $parentResourceObj = new Resource(new NamedArguments(array('primaryKey' => $parentResource['relatedResourceID'])));
            echo "<li><a href='resource.php?resourceID=" . $parentResourceObj->resourceID . "' ". getTarget() .">" . $parentResourceObj->titleText . "</a></li>";
          }
					echo "</ul>";
					echo "</div>";
				}

				if (is_array($childResourceArray) && count($childResourceArray) > 0) { 
					echo "<div id='childResources'>";
					echo "<h3>" . _("Child Resources") . "</h3>";
					echo "<ul class='unstyled'>";
					foreach ($childResourceArray as $childResource){
						$childResourceObj = new Resource(new NamedArguments(array('primaryKey' => $childResource['resourceID'])));
            echo "<li><a href='resource.php?resourceID=" . $childResourceObj->resourceID . "' ". getTarget() .">" . $childResourceObj->titleText . "</a></li>";
					}
					echo "</ul>";
					echo "</div>";
					?>
					</dd>

			<?php
				}
			}

      if ($isbnOrIssns = $resource->getIsbnOrIssn()) {
			?>
			<dt><?php echo _("ISSN / ISBN:");?></dt>
      <dd>
				<ul class='unstyled'>
					<?php
						foreach ($isbnOrIssns as $isbnOrIssn) {
							print  "<li>" . $isbnOrIssn->isbnOrIssn . "</li>";
						}
					?>
				</ul>	
			</dd>
			
			<?php
			}

			if (is_array($aliasArray) && count($aliasArray) > 0) {
			?>
			<dt><?php echo _("Aliases:");?></dt>
			<dd>
				<dl class="dl-grid">
			<?php
				foreach ($aliasArray as $resourceAlias){
					echo "\n<dt>" . $resourceAlias['aliasTypeShortName'] . ":</dt><dd>" . $resourceAlias['shortName'] . "</dd>";
				}
			?>
				</dl>
			</dd>
			<?php
			}


			if (is_array($orgArray) && count($orgArray) > 0) {
			?>

			<dt><?php echo _("Organizations:");?></dt>
			<dd>
				<dl class='dl-grid' id="relatedOrgs">
				<?php
				foreach ($orgArray as $organization){
					//if organizations is installed provide a link
					if ($config->settings->organizationsModule == 'Y'){
						echo "<dt>" . $organization['organizationRole'] . ":</dt><dd> <a href='" . $util->getOrganizationURL() . $organization['organizationID'] . "' " . getTarget() . ">" . $organization['organization'] . "</a></dd>";
					}else{
						echo "<dt>" . $organization['organizationRole'] . ":</dt><dd> " . $organization['organization'] . "</dd>";
					}
				}
				?>
				</dl>
			</dd>

			<?php
			}

			if ($resource->resourceURL) { ?>
				<dt><?php echo _("Resource URL:");?></dt>
				<dd><?php echo $resource->resourceURL; ?> <a href='<?php echo $resource->resourceURL; ?>' <?php echo getTarget() ?>><img src='images/arrow-up-right.gif' alt="<?php echo _("Visit Resource URL");?>" title="<?php echo _("Visit Resource URL");?>"></a></dd>
				<?php
			}

			if ($resource->resourceAltURL) { ?>
				<dt><?php echo _("Alt URL:");?></dt>
				<dd><?php echo $resource->resourceAltURL; ?> <a href='<?php echo $resource->resourceAltURL; ?>' <?php echo getTarget() ?>><img src='images/arrow-up-right.gif' alt="<?php echo _("Visit Secondary Resource URL");?>" title="<?php echo _("Visit Secondary Resource URL");?>"></a></dd>
			<?php
			}

			if ($resource->descriptionText){ ?>
				<dd><?php echo _("Description:");?></dd>
				<dd><?php echo nl2br($resource->descriptionText); ?></dd>
			<?php } ?>


		</dl>
		<?php if ($user->canEdit()){ ?>
		<p><a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getUpdateProductForm&resourceID=<?php echo $resource->resourceID; ?>", 800,830)'  class='thickbox' id='editResource'><?php echo _("edit product details");?></a></p>
		<?php } ?>

		<?php

		//get subjects for this tab
		$sanitizedInstance = array();
		$generalDetailSubjectIDArray = array();


		foreach ($resource->getGeneralDetailSubjectLinkID() as $instance) {
			foreach (array_keys($instance->attributeNames) as $attributeName) {
				$sanitizedInstance[$attributeName] = $instance->$attributeName;
			}

			$sanitizedInstance[$instance->primaryKeyName] = $instance->primaryKey;
			array_push($generalDetailSubjectIDArray, $sanitizedInstance);

		}

		if (is_array($generalDetailSubjectIDArray) && count($generalDetailSubjectIDArray) > 0) {

		?>
		<h2><?php echo _("Subjects");?></h2>
			<table class='table-border table-striped'>
			<thead>
				<tr>
				<th scope="col"><?php echo _("General Subject Name");?></th>
				<th scope="col"><?php echo _("Detail Subject Name");?></th>
				<th scope="col"><?php echo _("Actions");?></th>
				</tr>
				</thead>
				<tbody>
				<?php
					$generalSubjectID = 0;
					foreach ($generalDetailSubjectIDArray as $generalDetailSubjectID){
						$generalSubject = new GeneralSubject(new NamedArguments(array('primaryKey' => $generalDetailSubjectID['generalSubjectID'])));
						$detailedSubject = new DetailedSubject(new NamedArguments(array('primaryKey' => $generalDetailSubjectID['detailedSubjectID'])));

				?>
						<tr>
							<th scope="row">
								<?php if ($generalDetailSubjectID['generalSubjectID'] != $generalSubjectID) {
										echo $generalSubject->shortName;
											// Allow deleting of the General Subject if no Detail Subjects exist
											if (in_array($generalDetailSubjectID['generalSubjectID'], $generalDetailSubjectIDArray[0], true) > 1) {
												$canDelete = false;
											} else {
												$canDelete = true;
											}

									} else {
										echo "&nbsp;";
										$canDelete = true;
									}
								?>
							</th>

							<td>
								<?php echo $detailedSubject->shortName; ?>
							</td>

							<td class="actions">
							<?php if ($user->canEdit() && $canDelete) { ?>
								<a href='javascript:void(0);'
									tab='Product'
									class='removeResourceSubjectRelationship'
									generalDetailSubjectID='<?php echo $generalDetailSubjectID['generalDetailSubjectLinkID']; ?>'
									resourceID='<?php echo $resourceID; ?>'>

									<img src='images/cross.gif'
										alt='<?php printf(_("remove %s"), $detailedSubject->shortName ? $detailedSubject->shortName : $generalSubject->shortName);?>'
										title='<?php printf(_("remove %s"), $detailedSubject->shortName ? $detailedSubject->shortName : $generalSubject->shortName);?>' /></a>
							<?php } ?>
							</td>



						</tr>

				<?php
						$generalSubjectID = $generalDetailSubjectID['generalSubjectID'];
					}
				?>

	<?php } ?>
				</tbody>
			</table>
		<?php



		if ($user->canEdit()){
		?>
			<a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getResourceSubjectForm&tab=Product&resourceID=<?php echo $resourceID; ?>",800,500)'' class='thickbox'><?php echo _("add subject");?></a>
		<?php
		}



		?>

		<?php

		//get notes for this tab
		$sanitizedInstance = array();
		$noteArray = array();

		foreach ($resource->getNotes('Product') as $instance) {
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

		if (is_array($noteArray) && count($noteArray) > 0) {
		?>
		<h2><?php echo _("Additional Notes");?></h2>
			
		<ol class="unstyled">
				<?php foreach ($noteArray as $resourceNote){ ?>
					
					<li>
						<h3><?php echo $resourceNote['noteTypeName']; ?></h3>
						<div class="note-text">
							<?php echo nl2br($resourceNote['noteText']); ?>
						</div>
						<p class="byline">
							<?php printf(_("%s by %s"), format_date($resourceNote['updateDate']), $resourceNote['updateUser']); ?>
							<?php if ($user->canEdit()){ ?>
								<a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getNoteForm&tab=Product&entityID=<?php echo $resourceID; ?>&resourceNoteID=<?php echo $resourceNote['resourceNoteID']; ?>",250,500)' class='thickbox'><img  src='images/edit.gif' alt='<?php echo _("edit");?>' title='<?php echo _("edit note");?>'></a>
								<a href='javascript:void(0);' class='removeNote' id='<?php echo $resourceNote['resourceNoteID']; ?>' tab='Product'><img src='images/cross.gif' alt='<?php echo _("remove note");?>' title='<?php echo _("remove note");?>'></a>
							<?php } ?>
						</p>
				<?php } ?>
		</ol>
		<?php
		}
		
		if ($user->canEdit()){
		?>
			<p><a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getNoteForm&tab=Product&entityID=<?php echo $resourceID; ?>&resourceNoteID=<?php echo $resourceNote['resourceNoteID']; ?>",250,500)' class='thickbox'><?php echo _("add note");?></a></p>
		<?php
		}

?>
