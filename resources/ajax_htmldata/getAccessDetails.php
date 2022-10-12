<?php
	$resourceID = $_GET['resourceID'];
	$resourceAcquisitionID = $_GET['resourceAcquisitionID'];
	$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));
	$resourceAcquisition = new ResourceAcquisition(new NamedArguments(array('primaryKey' => $resourceAcquisitionID)));

	$userLimit = new UserLimit(new NamedArguments(array('primaryKey' => $resourceAcquisition->userLimitID)));
	$storageLocation = new StorageLocation(new NamedArguments(array('primaryKey' => $resourceAcquisition->storageLocationID)));
	$accessMethod = new AccessMethod(new NamedArguments(array('primaryKey' => $resourceAcquisition->accessMethodID)));
	$authenticationType = new AuthenticationType(new NamedArguments(array('primaryKey' => $resourceAcquisition->authenticationTypeID)));

		//get administering sites
		$sanitizedInstance = array();
		$instance = new AdministeringSite();
		$administeringSiteArray = array();
		foreach ($resourceAcquisition->getAdministeringSites() as $instance) {
			$administeringSiteArray[]=$instance->shortName;
		}



		//get authorized sites
		$sanitizedInstance = array();
		$instance = new PurchaseSite();
		$authorizedSiteArray = array();
		foreach ($resourceAcquisition->getAuthorizedSites() as $instance) {
			$authorizedSiteArray[]=$instance->shortName;
		}
?>
			<table class='linedFormTable'>
			<tr>
			<th colspan='2'>
			<span style='float:left;vertical-align:bottom;'><?php echo _("Access Information");?></span>


			<?php if ($user->canEdit()){ ?>
				<span style='float:right;vertical-align:bottom;'><a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getAccessForm&resourceID=<?php echo $resourceID; ?>&resourceAcquisitionID=<?php echo $resourceAcquisitionID; ?>",500,800)' class='thickbox' id='editAccess'><img src='images/edit.gif' alt='<?php echo _("edit");?>' title='<?php echo _("edit resource");?>'></a></span>
			<?php } ?>

			</th>
			</tr>

			<?php if (count($administeringSiteArray) > 0) { ?>
				<tr>
				<td style='vertical-align:top;width:150px;'><?php echo _("Administering Sites:");?></td>
				<td style='width:310px;'><?php echo implode(", ", $administeringSiteArray); ?></td>
				</tr>
			<?php } ?>

			<?php if (count($authorizedSiteArray) > 0) { ?>
				<tr>
				<td style='vertical-align:top;width:150px;'><?php echo _("Authorized Sites:");?></td>
				<td style='width:310px;'><?php echo implode(", ", $authorizedSiteArray); ?></td>
				</tr>
			<?php } ?>

			<?php if ($authenticationType->shortName) { ?>
				<tr>
				<td style='vertical-align:top;width:150px;'><?php echo _("Authentication Type:");?></td>
				<td style='width:310px;'><?php echo $authenticationType->shortName; ?></td>
				</tr>
			<?php } ?>


			<?php if (($resourceAcquisition->authenticationUserName) || ($resourceAcquisition->authenticationPassword)) { ?>
				<tr>
				<td style='vertical-align:top;width:150px;'><?php echo _("Username / Password:");?></td>
				<td style='width:310px;'><?php echo $resourceAcquisition->authenticationUserName . " / " . $resourceAcquisition->authenticationPassword; ?></td>
				</tr>
			<?php } ?>

			<?php if ($userLimit->shortName) { ?>
				<tr>
				<td style='vertical-align:top;width:150px;'><?php echo _("Simultaneous User Limit:");?></td>
				<td style='width:310px;'><?php echo $userLimit->shortName; ?></td>
				</tr>
			<?php } ?>


			<?php if ($resourceAcquisition->registeredIPAddressException){ ?>
				<tr>
				<td style='vertical-align:top;width:150px;'><?php echo _("Registered IP Address:");?></td>
				<td style='width:310px;'><?php echo $resourceAcquisition->registeredIPAddressException; ?></td>
				</tr>
			<?php } ?>


			<?php if ($storageLocation->shortName) { ?>
				<tr>
				<td style='vertical-align:top;width:150px;'><?php echo _("Storage Location:");?></td>
				<td style='width:310px;'><?php echo $storageLocation->shortName; ?></td>
				</tr>
			<?php } ?>

			<?php if ($resourceAcquisition->coverageText) { ?>
				<tr>
				<td style='vertical-align:top;width:150px;'><?php echo _("Coverage:");?></td>
				<td style='width:310px;'><?php echo $resourceAcquisition->coverageText; ?></td>
				</tr>
			<?php } ?>

			<?php if ($accessMethod->shortName) { ?>
				<tr>
				<td style='vertical-align:top;width:150px;'><?php echo _("Access Method:");?></td>
				<td style='width:310px;'><?php echo $accessMethod->shortName; ?></td>
				</tr>
			<?php
			}

			if ((count($administeringSiteArray) == 0) && (!$authenticationType->shortName) && (!$resourceAcquisition->coverageText) && (!$resourceAcquisition->authenticationUserName) && (!$resourceAcquisition->authenticationPassword) && (!$userLimit->shortName) && (!$resourceAcquisition->registeredIPAddressException) && (!$storageLocation->shortName) && (!$accessMethod->shortName)){
				echo "<tr><td colspan='2'><i>"._("No access information available").".</i></td></tr>";
			}

			?>
			</table>

			<?php if ($user->canEdit()){ ?>
				<a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getAccessForm&resourceID=<?php echo $resourceID; ?>&resourceAcquisitionID=<?php echo $resourceAcquisitionID; ?>",450,800)' class='thickbox' id='editAccess'><?php echo _("edit access information");?></a>
			<?php } ?>

			<br /><br /><br />



		<?php


		//get notes for this tab
		$sanitizedInstance = array();
		$noteArray = array();
		foreach ($resourceAcquisition->getNotes('Access') as $instance) {
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

				<?php if ($user->canEdit()){?>
			 <a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getNoteForm&tab=Access&entityID=<?php echo $resourceAcquisitionID; ?>&resourceNoteID=",300,500)' class='thickbox'><?php echo "<div class= 'addIconTab'><img id='Add' class='addIcon' src='images/plus.gif' title= '"._("Add")."' /></div>";?></a> 
				<?php }?>
				</th>
				</tr>
				<?php foreach ($noteArray as $resourceNote){ ?>
					<tr>
					<td style='width:150px;'><?php echo $resourceNote['noteTypeName']; ?><br />
							<a  href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getNoteForm&tab=Access&entityID=<?php echo $resourceAcquisitionID; ?>&resourceNoteID=<?php echo $resourceNote['resourceNoteID']; ?>",300, 500)' class='thickbox'><img src='images/edit.gif' alt='<?php echo _("edit");?>' title='<?php echo _("edit note");?>'></a>
							<a  href='javascript:void(0);' class='removeNote' id='<?php echo $resourceNote['resourceNoteID']; ?>' tab='Access'><img src='images/cross.gif' alt='<?php echo _("remove note");?>' title='<?php echo _("remove note");?>'></a>
					</td>
					<td><?php echo nl2br($resourceNote['noteText']); ?><br /><i><?php echo format_date($resourceNote['updateDate']) . _(" by ") . $resourceNote['updateUser']; ?></i></td>
					</tr>
				<?php } ?>
			</table>
		<?php
		}else{
			if ($user->canEdit()){
			?>
				<a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getNoteForm&tab=Access&entityID=<?php echo $resourceAcquisitionID; ?>&resourceNoteID=", 300,500)' class='thickbox'><?php echo _("add note");?></a>
			<?php
			}
		}

?>
