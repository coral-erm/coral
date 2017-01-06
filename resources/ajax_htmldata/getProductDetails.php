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
		<table class='linedFormTable' style='width:460px;'>
			<tr>
				<th width="115"></th>
				<th></th>
			</tr>
			<tr>
				<th colspan='2' style='margin-top: 7px; margin-bottom: 5px;'>
					<span style='float:left; vertical-align:top; max-width:400px; margin-left:3px;'><span style='font-weight:bold;font-size:120%;margin-right:8px;'><?php echo $resource->titleText; ?></span><span style='font-weight:normal;font-size:100%;'><?php echo $acquisitionType->shortName . " " . $resourceFormat->shortName . " " . $resourceType->shortName; ?></span></span>

					<span style='float:right; vertical-align:top;'>
					<?php if ($user->canEdit()) { ?>
						<a href='ajax_forms.php?action=getUpdateProductForm&height=700&width=730&resourceID=<?php echo $resource->resourceID; ?>&modal=true'
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

			</th>
			</tr>

			<tr>
			<td style='vertical-align:top;width:115px;'><?php echo _("Status:");?></td>
			<td style='width:345px;'><?php echo $status->shortName; ?></td>
			</tr>

			<?php
			if (($resource->archiveDate) && ($resource->archiveDate != '0000-00-00')){
			?>

				<tr class='lightGrayBackground'>
				<td>
				<?php echo _("Archived:");?>
				</td>
				<td>
				<i>

				<?php
					echo format_date($resource->archiveDate);

					if ($archiveUser->getDisplayName){
						echo _(" by ") . $archiveUser->getDisplayName;
					}else if ($resource->archiveLoginID){
						echo _(" by ") . $resource->archiveLoginID;
					}
				?>

				</i>
				</td>
				</tr>

			<?php
			}
			?>

			<tr>
			<td>
			<?php echo _("Created:");?>
			</td>
			<td>
			<i>

				<?php
					echo format_date($resource->createDate);

					if ($createUser->getDisplayName){
						echo _(" by ") . $createUser->getDisplayName;
					}else if ($resource->createLoginID){
						echo _(" by ") . $resource->createLoginID;
					}
				?>

			</i>
			</td>
			</tr>

			<?php
			if (($resource->updateDate) && ($resource->updateDate != '0000-00-00')){
			?>

				<tr>
				<td>
				<?php echo _("Last Update:");?>
				</td>
				<td>
				<i>
				<?php
					echo format_date($resource->updateDate);

					if ($updateUser->getDisplayName){
						echo _(" by ") . $updateUser->getDisplayName;
					}else if ($resource->updateLoginID){
						echo _(" by ") . $resource->updateLoginID;
					}
				?>
				</i>
				</td>
				</tr>

			<?php
			}




      if ((count($parentResourceArray) > 0) || (count($childResourceArray) > 0)){ ?>
				<tr>
				<td style='vertical-align:top;width:115px;'><?php echo _("Related Products:");?>
				</td>
				<td style='width:345px;'>
				<?php

        if (count($parentResourceArray) > 0) {
           foreach ($parentResourceArray as $parentResource){
              $parentResourceObj = new Resource(new NamedArguments(array('primaryKey' => $parentResource['relatedResourceID'])));
            echo $parentResourceObj->titleText . "&nbsp;&nbsp;(Parent)&nbsp;&nbsp;<a href='resource.php?resourceID=" . $parentResourceObj->resourceID . "' target='_BLANK'><img src='images/arrow-up-right.gif' alt='"._("view resource")."' title='"._("View ") . $parentResourceObj->titleText . "' style='vertical-align:top;'></a><br />";
            }
         }

				if (count($childResourceArray) > 0) { ?>
					<?php
					foreach ($childResourceArray as $childResource){
						$childResourceObj = new Resource(new NamedArguments(array('primaryKey' => $childResource['resourceID'])));
            echo $childResourceObj->titleText . "<a href='resource.php?resourceID=" . $childResourceObj->resourceID . "' target='_BLANK'><img src='images/arrow-up-right.gif' alt='"._("view resource")."' title='"._("View ") . $childResourceObj->titleText . "' style='vertical-align:top;'></a><br />";

					}


					?>
					</td>
				</tr>

			<?php
				}
			}

      if ($isbnOrIssns = $resource->getIsbnOrIssn()) {
			?>
			<tr>
			<td style='vertical-align:top;width:115px;'><?php echo _("ISSN / ISBN:");?></td>
      <td style='width:345px;'>
      <?php 
        foreach ($isbnOrIssns as $isbnOrIssn) {
          print $isbnOrIssn->isbnOrIssn . "<br />";
        }
      ?></td>
			</tr>
			<?php
			}

			if (count($aliasArray) > 0){
			?>
			<tr>
			<td style='vertical-align:top;width:115px;'><?php echo _("Aliases:");?></td>
			<td style='width:345px;'>
			<?php
				foreach ($aliasArray as $resourceAlias){
					echo "\n<span style='float: left; width:95px;'>" . $resourceAlias['aliasTypeShortName'] . ":</span><span style='width:270px;'>" . $resourceAlias['shortName'] . "</span><br />";
				}
			?>
			</td>
			</tr>
			<?php
			}


			if (count($orgArray) > 0){
			?>

			<tr>
			<td style='vertical-align:top;width:115px;'><?php echo _("Organizations:");?></td>
			<td style='width:345px;'>

				<?php
				foreach ($orgArray as $organization){
					//if organizations is installed provide a link
					if ($config->settings->organizationsModule == 'Y'){
						echo "<span style='float:left; width:75px;'>" . $organization['organizationRole'] . ":</span><span style='width:270px;'>" . $organization['organization'] . "&nbsp;&nbsp;<a href='" . $util->getOrganizationURL() . $organization['organizationID'] . "' target='_blank'><img src='images/arrow-up-right.gif' alt='"._("View ") . $organization['organization'] . "' title='"._("View ") . $organization['organization'] . "' style='vertical-align:top;'></a></span><br />";
					}else{
						echo "<span style='float:left; width:75px;'>" . $organization['organizationRole'] . ":</span><span style='width:270px;'>" . $organization['organization'] . "</span><br />";
					}
				}
				?>
			</td>
			</tr>

			<?php
			}

			if ($resource->resourceURL) { ?>
				<tr>
				<td style='vertical-align:top;width:115px;'><?php echo _("Resource URL:");?></td>
				<td style='width:345px;'><?php echo $resource->resourceURL; ?>&nbsp;&nbsp;<a href='<?php echo $resource->resourceURL; ?>' target='_blank'><img src='images/arrow-up-right.gif' alt="<?php echo _("Visit Resource URL");?>" title="<?php echo _("Visit Resource URL");?>" style='vertical-align:top;'></a></td>
				</tr>
			<?php
			}

			if ($resource->resourceAltURL) { ?>
				<tr>
				<td style='vertical-align:top;width:115px;'><?php echo _("Alt URL:");?></td>
				<td style='width:345px;'><?php echo $resource->resourceAltURL; ?>&nbsp;&nbsp;<a href='<?php echo $resource->resourceAltURL; ?>' target='_blank'><img src='images/arrow-up-right.gif' alt="<?php echo _("Visit Secondary Resource URL");?>" title="<?php echo _("Visit Secondary Resource URL");?>" style='vertical-align:top;'></a></td>
				</tr>
			<?php
			}

			if ($resource->descriptionText){ ?>
				<tr>
				<td style='vertical-align:top;width:115px;'><?php echo _("Description:");?></td>
				<td style='width:345px;'><?php echo nl2br($resource->descriptionText); ?></td>
				</tr>
			<?php } ?>


		</table>
		<?php if ($user->canEdit()){ ?>
		<a href='ajax_forms.php?action=getUpdateProductForm&height=700&width=730&modal=true&resourceID=<?php echo $resourceID; ?>' class='thickbox' id='editResource'><?php echo _("edit product details");?></a><br />
		<?php } ?>

		<br />
		<br />

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

		if (count($generalDetailSubjectIDArray) > 0){

		?>
			<table class='linedFormTable'>
				<tr>
				<th><?php echo _("Subjects");?></th>
				<th>
				</th>
				<th>
				</th>
				</tr>
				<?php
					$generalSubjectID = 0;
					foreach ($generalDetailSubjectIDArray as $generalDetailSubjectID){
						$generalSubject = new GeneralSubject(new NamedArguments(array('primaryKey' => $generalDetailSubjectID[generalSubjectID])));
						$detailedSubject = new DetailedSubject(new NamedArguments(array('primaryKey' => $generalDetailSubjectID[detailedSubjectID])));

				?>
						<tr>
							<td>
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
							</td>

							<td>
								<?php echo $detailedSubject->shortName; ?>
							</td>

							<td style='width:50px;'>
							<?php if ($user->canEdit() && $canDelete) { ?>
								<a href='javascript:void(0);'
									tab='Product'
									class='removeResourceSubjectRelationship'
									generalDetailSubjectID='<?php echo $generalDetailSubjectID[generalDetailSubjectLinkID]; ?>'
									resourceID='<?php echo $resourceID; ?>'>

									<img src='images/cross.gif'
										alt='<?php echo _("remove subject");?>'
										title='<?php echo _("remove subject");?>' /></a>
							<?php } ?>
							</td>



						</tr>

				<?php
						$generalSubjectID = $generalDetailSubjectID['generalSubjectID'];
					}
				?>

	<?php } ?>
			</table>
		<?php



		if ($user->canEdit()){
		?>
			<a href='ajax_forms.php?action=getResourceSubjectForm&height=700&width=425&tab=Product&resourceID=<?php echo $resourceID; ?>&modal=true' class='thickbox'><?php echo _("add new subject");?></a>
		<?php
		}



		?>
		<br />
		<br />

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

		if (count($noteArray) > 0){
		?>
			<table class='linedFormTable'>
				<tr>
				<th><?php echo _("Additional Notes");?></th>
				<th>
				<?php if ($user->canEdit()){ ?>
					<a href='ajax_forms.php?action=getNoteForm&height=233&width=410&tab=Product&resourceID=<?php echo $resourceID; ?>&resourceNoteID=&modal=true' class='thickbox'><?php echo _("add new note");?></a>
				<?php } ?>
				</th>
				</tr>
				<?php foreach ($noteArray as $resourceNote){ ?>
					<tr>
					<td style='width:115px;'><?php echo $resourceNote['noteTypeName']; ?><br />
					<?php if ($user->canEdit()){ ?>
					<a href='ajax_forms.php?action=getNoteForm&height=233&width=410&tab=Product&resourceID=<?php echo $resourceID; ?>&resourceNoteID=<?php echo $resourceNote['resourceNoteID']; ?>&modal=true' class='thickbox'><img src='images/edit.gif' alt='<?php echo _("edit");?>' title='<?php echo _("edit note");?>'></a>  <a href='javascript:void(0);' class='removeNote' id='<?php echo $resourceNote['resourceNoteID']; ?>' tab='Product'><img src='images/cross.gif' alt='<?php echo _("remove note");?>' title='<?php echo _("remove note");?>'></a>
					<?php } ?>
					</td>
					<td><?php echo nl2br($resourceNote['noteText']); ?><br /><i><?php echo format_date($resourceNote['updateDate']) . _(" by ") . $resourceNote['updateUser']; ?></i></td>
					</tr>
				<?php } ?>
			</table>
		<?php
		}else{
			if ($user->canEdit()){
			?>
				<a href='ajax_forms.php?action=getNoteForm&height=233&width=410&tab=Product&resourceID=<?php echo $resourceID; ?>&resourceNoteID=&modal=true' class='thickbox'><?php echo _("add new note");?></a>
			<?php
			}
		}

?>

