<?php
	$resourceID = "";
    if(isset($_GET['resourceID'])){
		$resourceID = $_GET['resourceID'];
	}
	
    $resourceAcquisitionID = isset($_GET['resourceAcquisitionID']) ? $_GET['resourceAcquisitionID'] : null;
	if (isset($_GET['contactID'])) $contactID = $_GET['contactID']; else $contactID = '';
	$contact = new Contact(new NamedArguments(array('primaryKey' => $contactID)));

		if (($contact->archiveDate) && ($contact->archiveDate != '0000-00-00')){
			$invalidChecked = 'checked';
		}else{
			$invalidChecked = '';
		}

		//get contact roles
		$sanitizedInstance = array();
		$instance = new ContactRole();
		$contactRoleProfileArray = array();
		foreach ($contact->getContactRoles() as $instance) {
			$contactRoleProfileArray[] = $instance->contactRoleID;
		}

		//get all contact roles for output in drop down
		$contactRoleArray = array();
		$contactRoleObj = new ContactRole();
		$contactRoleArray = $contactRoleObj->allAsArray();
?>
		<div id='div_contactForm'>
		<form id='contactForm'>
		<input type='hidden' name='editResourceID' id='editResourceID' value='<?php echo $resourceID; ?>'>
		<input type='hidden' name='editResourceAcquisitionID' id='editResourceAcquisitionID' value='<?php echo $resourceAcquisitionID; ?>'>
		<input type='hidden' name='editContactID' id='editContactID' value='<?php echo $contactID; ?>'>

		<div class='formTitle'><h2 class='headerText'><?php if ($contactID){ echo _("Edit Contact"); } else { echo _("Add Contact"); } ?></h2></div>

		<span class='error' id='span_errors'></span>

		<div class="form-grid">
		
		<label for='contactName'><?php echo _("Name:");?></label>
		<input type='text' id='contactName' name='contactName' value = '<?php echo $contact->name; ?>' class='changeInput' aria-describedby='span_error_contactName' />
		<p id='span_error_contactName' class='error'></p>
		
		<label for='contactTitle'><?php echo _("Title:");?></label>
		<input type='text' id='contactTitle' name='contactTitle' value = '<?php echo $contact->title; ?>' class='changeInput' />
		
		<label for='phoneNumber'><?php echo _("Phone:");?></label>
		<input type='text' id='phoneNumber' name='phoneNumber' value = '<?php echo $contact->phoneNumber; ?>' class='changeInput' />
		
		<label for='altPhoneNumber'><?php echo _("Alt Phone:");?></label>
		<input type='text' id='altPhoneNumber' name='altPhoneNumber' value = '<?php echo $contact->altPhoneNumber; ?>' class='changeInput' />
		
		<label for='faxNumber'><?php echo _("Fax:");?></label>
		<input type='text' id='faxNumber' name='faxNumber' value = '<?php echo $contact->faxNumber; ?>' class='changeInput' />
		
		<label for='emailAddress'><?php echo _("Email:");?></label>
		<input type='text' id='emailAddress' name='emailAddress' value = '<?php echo $contact->emailAddress; ?>' class='changeInput' />
		
		<label for='addressText'><?php echo _("Address:");?></label>
		<textarea rows='3' id='addressText'><?php echo $contact->addressText; ?></textarea>
		
		<p class="checkbox indent">
			<input type='checkbox' id='invalidInd' name='invalidInd' <?php echo $invalidChecked; ?> />
			<label for='invalidInd'><?php echo _("Archived");?></label>
		</p>

		<fieldset class="subgrid">
			<legend><?php echo _("Role(s):");?></legend>
			<div class="form-group">
			<?php
			if (is_array($contactRoleArray) && count($contactRoleArray) > 0) {
				echo "<ul class='unstyled columns'>";
				foreach ($contactRoleArray as $contactRoleIns){
					
					if (in_array($contactRoleIns['contactRoleID'],$contactRoleProfileArray)){
						echo "<li class='checkbox'><label><input class='check_roles' type='checkbox' name='" . $contactRoleIns['contactRoleID'] . "' id='" . $contactRoleIns['contactRoleID'] . "' value='" . $contactRoleIns['contactRoleID'] . "' checked />   " . $contactRoleIns['shortName'] . "</label></li>\n";
					}else{
						echo "<li class='checkbox'><label><input class='check_roles' type='checkbox' name='" . $contactRoleIns['contactRoleID'] . "' id='" . $contactRoleIns['contactRoleID'] . "' value='" . $contactRoleIns['contactRoleID'] . "' />   " . $contactRoleIns['shortName'] . "</label></li>\n";
					}
				}
				echo "</ul>";
			}
			?>
			
			<p id='span_error_contactRole' class='error'></p>
			</div>
		</fieldset>

			<label for='noteText'><?php echo _("Notes:");?></label>
			<textarea rows='3' id='noteText'><?php echo $contact->noteText; ?></textarea>
			
			<p class='actions'>
				<input type='submit' value='<?php echo _("submit");?>' name='submitContactForm' id ='submitContactForm' class='submit-button primary'>
				<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='cancel-button secondary'>
			</p>

		</div>

		</form>
		</div>


		<script type="text/javascript" src="js/forms/contactForm.js?random=<?php echo rand(); ?>"></script>

