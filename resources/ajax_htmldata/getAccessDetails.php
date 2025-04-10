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
	<div class="header">
			<h3><?php echo _("Access Information");?></h3>
			<?php if ($user->canEdit()){ ?>
				<a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getAccessForm&resourceID=<?php echo $resourceID; ?>&resourceAcquisitionID=<?php echo $resourceAcquisitionID; ?>",500,800)' class='thickbox addElement' id='editAccess'><img src='images/edit.gif' alt='<?php echo _("edit");?>' title='<?php echo _("edit resource");?>'></a>
			<?php } ?>
		</div>

			<?php
			if ((count($administeringSiteArray) == 0) && (!$authenticationType->shortName) && (!$resourceAcquisition->coverageText) && (!$resourceAcquisition->authenticationUserName) && (!$resourceAcquisition->authenticationPassword) && (!$userLimit->shortName) && (!$resourceAcquisition->registeredIPAddressException) && (!$storageLocation->shortName) && (!$accessMethod->shortName)){
				echo "<p><i>"._("No access information available").".</i></p>";
			}
			else { ?>
			<dl class='dl-grid'>
			
			<?php if (is_array($administeringSiteArray) && count($administeringSiteArray) > 0) { ?>
				
				<dt><?php echo _("Administering Sites:");?></dt>
				<dd><?php echo implode(", ", $administeringSiteArray); ?></dd>
				
			<?php } ?>

			<?php if (is_array($authorizedSiteArray) && count($authorizedSiteArray) > 0) { ?>
				
				<dt><?php echo _("Authorized Sites:");?></dt>
				<dd><?php echo implode(", ", $authorizedSiteArray); ?></dd>
				
			<?php } ?>

			<?php if ($authenticationType->shortName) { ?>
				
				<dt><?php echo _("Authentication Type:");?></dt>
				<dd><?php echo $authenticationType->shortName; ?></dd>
				
			<?php } ?>


			<?php if (($resourceAcquisition->authenticationUserName) || ($resourceAcquisition->authenticationPassword)) { ?>
				
				<dt><?php echo _("Username / Password:");?></dt>
				<dd><?php echo $resourceAcquisition->authenticationUserName . " / " . $resourceAcquisition->authenticationPassword; ?></dd>
				
			<?php } ?>

			<?php if ($userLimit->shortName) { ?>
				
				<dt><?php echo _("Simultaneous User Limit:");?></dt>
				<dd><?php echo $userLimit->shortName; ?></dd>
				
			<?php } ?>


			<?php if ($resourceAcquisition->registeredIPAddressException){ ?>
				
				<dt><?php echo _("Registered IP Address:");?></dt>
				<dd><?php echo $resourceAcquisition->registeredIPAddressException; ?></dd>
				
			<?php } ?>


			<?php if ($storageLocation->shortName) { ?>
				
				<dt><?php echo _("Storage Location:");?></dt>
				<dd><?php echo $storageLocation->shortName; ?></dd>
				
			<?php } ?>

			<?php if ($resourceAcquisition->coverageText) { ?>
				
				<dt><?php echo _("Coverage:");?></dt>
				<dd><?php echo $resourceAcquisition->coverageText; ?></dd>
				
			<?php } ?>

			<?php if ($accessMethod->shortName) { ?>
				
				<dt><?php echo _("Access Method:");?></dt>
				<dd><?php echo $accessMethod->shortName; ?></dd>
				
			<?php
			}
			?>
			
			</dl>
<?php } ?>
			<?php if ($user->canEdit()){ ?>
				<p><a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getAccessForm&resourceID=<?php echo $resourceID; ?>&resourceAcquisitionID=<?php echo $resourceAcquisitionID; ?>",450,800)' class='thickbox' id='editAccess'><?php echo _("edit access information");?></a></p>
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

		if (is_array($noteArray) && count($noteArray) > 0) {
		?>
			<h3><?php echo _("Additional Notes");?></h3>
			<table class='linedFormTable table-border table-striped'>
				<tbody>
					<?php foreach ($noteArray as $resourceNote){ ?>
					
						<th><?php echo $resourceNote['noteTypeName']; ?></th>
						<td>
							<?php echo nl2br($resourceNote['noteText']); ?>
							<p class="byline"><?php printf(_("%s by %s"), format_date($resourceNote['updateDate']), $resourceNote['updateUser']); ?></p>
						</td>
						<td class="actions">
								<?php if ($user->canEdit()){?>
									<a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getNoteForm&tab=Access&entityID=<?php echo $resourceAcquisitionID; ?>&resourceNoteID=",300,500)' class='thickbox addElement'><?php echo "<div class= 'addIconTab'><img id='Add' class='addIcon' src='images/plus.gif' title= '"._("Add")."' /></div>";?></a> 
								<?php } ?>
								<a  href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getNoteForm&tab=Access&entityID=<?php echo $resourceAcquisitionID; ?>&resourceNoteID=<?php echo $resourceNote['resourceNoteID']; ?>",300, 500)' class='thickbox'><img src='images/edit.gif' alt='<?php echo _("edit");?>' title='<?php echo _("edit note");?>'></a>
								<a  href='javascript:void(0);' class='removeNote' id='<?php echo $resourceNote['resourceNoteID']; ?>' tab='Access'><img src='images/cross.gif' alt='<?php echo _("remove note");?>' title='<?php echo _("remove note");?>'></a>
						</td>
					<?php } ?>
				</tbody>
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
