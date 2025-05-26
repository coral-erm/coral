<?php

/*
**************************************************************************************************************************
** CORAL Organizations Module
**
** Copyright (c) 2010 University of Notre Dame
**
** This file is part of CORAL.
**
** CORAL is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
**
** CORAL is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License along with CORAL.  If not, see <http://www.gnu.org/licenses/>.
**
**************************************************************************************************************************
*/


include_once 'directory.php';
include_once 'user.php';


switch ($_GET['action']) {

    case 'getOrganizationForm':
    	if (isset($_GET['organizationID'])) $organizationID = $_GET['organizationID']; else $organizationID = '';
    	$organization = new Organization(new NamedArguments(array('primaryKey' => $organizationID)));

		//get parent organizations
		$sanitizedInstance = array();
		$instance = new Organization();
		$parentOrganizationArray = array();
		foreach ($organization->getParentOrganizations() as $instance) {
			foreach (array_keys($instance->attributeNames) as $attributeName) {
				$sanitizedInstance[$attributeName] = $instance->$attributeName;
			}

			$sanitizedInstance[$instance->primaryKeyName] = $instance->primaryKey;

			array_push($parentOrganizationArray, $sanitizedInstance);
		}


		//get organization roles
		$sanitizedInstance = array();
		$instance = new OrganizationRole();
		$organizationRoleProfileArray = array();
		foreach ($organization->getOrganizationRoles() as $instance) {
			$organizationRoleProfileArray[] = $instance->organizationRoleID;
		}


		//get all org roles for output in drop down
		$organizationRoleArray = array();
		$organizationRoleObj = new OrganizationRole();
		$organizationRoleArray = $organizationRoleObj->allAsArray();

		?>
		<div id='div_organizationForm'>
		<form id='organizationForm'>
		<input type='hidden' name='editOrganizationID' id='editOrganizationID' value='<?php echo $organizationID; ?>'>

		<h2><?php if ($organizationID != "") { echo _("Edit Organization"); }else{ echo _("Add New Organization"); } ?></h2>
		
		<div class="form-grid">
		<label for='organizationName'><?php echo _("Name:");?></label>
		<input type='text' id='organizationName' name='organizationName' value = "<?php echo htmlentities($organization->name); ?>" <?php if ($organization->isLinkedToILS()) echo "disabled='disabled'" ?> /> 
		<p id='span_errors' class='error'></p>
		
		<?php if (is_array($parentOrganizationArray) && count($parentOrganizationArray) > 0) { ?>
			
			<label for='parentOrganization'><?php echo _("Parent:");?></label>
		
			<?php
			foreach ($parentOrganizationArray as $parentOrganization){
				echo "<input type='text' id='parentOrganization' name='parentOrganization'  value = \"" . $parentOrganization['name'] . "\" />";
				echo "<input type='hidden' id='parentOrganizationID' name='parentOrganizationID' value=\"" . $parentOrganization['organizationID'] . "\">";
			}
			?>
		<?php
		}else{
		?>
			
			<label for='parentOrganization'><?php echo _("Parent:");?></label>
		
			<input type='text' id='parentOrganization' name='parentOrganization' value = ''>
			<input type='hidden' id='parentOrganizationID' name='parentOrganizationID' value=''>
		<?php } ?>

		<label for='companyURL'><?php echo _("Company URL:");?></label>
		<input type='text' id='companyURL' name='companyURL' value = '<?php if (!$organizationID) { echo "http://"; } else { echo $organization->companyURL; } ?>' <?php if ($organization->isLinkedToILS()) echo "disabled='disabled'" ?>>
		
		<label for='orgRoles'><?php echo _("Role(s):");?></label>
		<ul class="unstyled columns">
			<?php
			$i=0;
			if (is_array($organizationRoleArray) && count($organizationRoleArray) > 0) {
				foreach ($organizationRoleArray as $organizationRoleIns){
					
					$ilsClass = ($config->ils && $organizationRoleIns['shortName'] == $config->ils->ilsVendorRole) ? ' ils_role' : '';
					if (in_array($organizationRoleIns['organizationRoleID'],$organizationRoleProfileArray)){
						echo "<li><input class='check_roles$ilsClass' type='checkbox' name='" . $organizationRoleIns['organizationRoleID'] . "' id='" . $organizationRoleIns['organizationRoleID'] . "' value='" . $organizationRoleIns['organizationRoleID'] . "' checked> <label for='" . $organizationRoleIns['organizationRoleID'] . "'>" . $organizationRoleIns['shortName'] . "</label></li>\n";
					}else{
						echo "<li><input class='check_roles$ilsClass' type='checkbox' name='" . $organizationRoleIns['organizationRoleID'] . "' id='" . $organizationRoleIns['organizationRoleID'] . "' value='" . $organizationRoleIns['organizationRoleID'] . "' /> <label for='" . $organizationRoleIns['organizationRoleID'] . "'>" . $organizationRoleIns['shortName'] . "</label></li>\n";
					}
				}
			}
			?>
		</ul>

		
		<label for='accountDetailText'><?php echo _("Account Details:");?></label>
		<textarea rows='3' id='accountDetailText' name='accountDetailText' <?php if ($organization->isLinkedToILS()) echo "disabled='disabled'" ?>><?php echo $organization->accountDetailText; ?></textarea>
		
		<label for='noteText'><?php echo _("Notes:");?></label>
		<textarea rows='3' id='noteText' name='noteText' <?php if ($organization->isLinkedToILS()) echo "disabled='disabled'" ?>><?php echo $organization->noteText; ?></textarea>
		
		<p class="actions">
			<input type='button' value='<?php echo _("submit");?>' name='submitOrganizationChanges' id ='submitOrganizationChanges' class='submit-button primary'>
			<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='cancel-button secondary'>
		</p>

		</div>

		</form>
		</div>

		<script type="text/javascript" src="js/forms/organizationSubmitForm.js?random=<?php echo rand(); ?>"></script>

		<?php

        break;




    case 'getAliasForm':
    	$organizationID = $_GET['organizationID'];
    	if (isset($_GET['aliasID'])) $aliasID = $_GET['aliasID']; else $aliasID = '';
    	$alias = new Alias(new NamedArguments(array('primaryKey' => $aliasID)));

		//get all alias types for output in drop down
		$aliasTypeArray = array();
		$aliasTypeObj = new AliasType();
		$aliasTypeArray = $aliasTypeObj->allAsArray();

		?>
		<div id='div_aliasForm'>
		<form id='aliasForm'>
		<input type='hidden' name='organizationID' id='organizationID' value='<?php echo $organizationID; ?>'>
		<input type='hidden' name='editOrganizationID' id='editOrganizationID' value='<?php echo $organizationID; ?>'>
		<input type='hidden' name='editAliasID' id='editAliasID' value='<?php echo $aliasID; ?>'>
		<input type='hidden' name='showTab' id='showTab' value='alias'>


		<h2><?php if ($aliasID){ echo _("Edit Alias"); } else { echo _("Add Alias"); } ?></h2>
		<p id='span_errors' class='error'></p>
		
		<div class="grid-form">

		<label for='aliasTypeID'><?php echo _("Alias Type");?></label>
		<select name='aliasTypeID' id='aliasTypeID'>
		<?php
		foreach ($aliasTypeArray as $aliasType){
			if (!(trim(strval($aliasType['aliasTypeID'])) != trim(strval($alias->aliasTypeID)))){
				echo "<option value='" . $aliasType['aliasTypeID'] . "' selected>" . $aliasType['shortName'] . "</option>\n";
			}else{
				echo "<option value='" . $aliasType['aliasTypeID'] . "'>" . $aliasType['shortName'] . "</option>\n";
			}
		}
		?>
		</select>
		
		<label for='aliasName'><?php echo _("Name");?></label>
		<input type='text' id='aliasName' name='aliasName' value = "<?php echo $alias->name; ?>" aria-describedby="span_error_aliasName">
		<p id='span_error_aliasName' class="error"></p>

		<p class="actions">
			<input type='submit' value='<?php echo _("submit");?>' name='submitAliasForm' id ='submitAliasForm' class='submit-button primary'></p>
			<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='cancel-button secondary'>
		</p>
		
		</div>
		</form>
		</div>

		<script type="text/javascript" src="js/forms/aliasSubmitForm.js?random=<?php echo rand(); ?>"></script>

		<?php

        break;



    case 'getInlineContactForm':
		//get all contact roles for output in drop down
		$contactRoleArray = array();
		$contactRoleObj = new ContactRole();
		$contactRoleArray = $contactRoleObj->allAsArray();
?>
		<div class="form-inline">
			<p class="form-element">
				<label for="contactAddName"><?php echo _("Name"); ?></label>
				<input type='text' id='contactAddName' name='contactName' aria-describedby="span_error_contactAddName">
				<span id='span_error_contactAddName' class='error'></span>
			</p>
			<p class="form-element">
				<label for="emailAddress"><?php echo _("Email"); ?></label>
				<input type='text' id='emailAddress' name='emailAddress' aria-describedby="span_error_contactEmailAddress">
				<span id='span_error_contactEmailAddress' class='error'></span>
			</p>
		</div>
<?php
		if (is_array($contactRoleArray) && count($contactRoleArray) > 0) {
			echo '<h3>Roles</h3>';
			foreach ($contactRoleArray as $contactRoleIns){
				echo "<p class='checkbox'>
						<input class='check_roles' type='checkbox' name='" . $contactRoleIns['contactRoleID'] . "' id='" . $contactRoleIns['contactRoleID'] . "' value='" . $contactRoleIns['contactRoleID'] . "' />
						<label for='" . $contactRoleIns['contactRoleID'] . "'>" . $contactRoleIns['shortName'] . "</label>
					 </p>";
			}
			echo '<input type="button" id="createContact" class="primary" value="Create" />';
		}
	break;
    case 'getContactForm':
    	$organizationID = $_GET['organizationID'];
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
		<input type='hidden' name='editOrganizationID' id='editOrganizationID' value='<?php echo $organizationID; ?>'>
		<input type='hidden' name='editContactID' id='editContactID' value='<?php echo $contactID; ?>'>

		<h2><?php if ($contactID){ echo _("Edit Contact"); } else { echo _("Add Contact"); } ?></h2>
		<p id='span_errors' class="error"></p>

		<div class="form-grid">

		<label for='contactName'><?php echo _("Name:");?></label>
		<input type='text' id='contactName' name='contactName' value = "<?php echo $contact->name; ?>" aria-describedby="span_error_contactName">
		<p id='span_error_contactName' class="error"></p>
		
		<label for='contactTitle'><?php echo _("Title:");?></label>
		<input type='text' id='contactTitle' name='contactTitle' value = '<?php echo $contact->title; ?>'>

		<label for='phoneNumber'><?php echo _("Phone:");?></label>
		<input type='text' id='phoneNumber' name='phoneNumber' value = '<?php echo $contact->phoneNumber; ?>'>

		<label for='altPhoneNumber'><?php echo _("Alt Phone:");?></label>
		<input type='text' id='altPhoneNumber' name='altPhoneNumber' value = '<?php echo $contact->altPhoneNumber; ?>'>

		<label for='faxNumber'><?php echo _("Fax:");?></label>
		<input type='text' id='faxNumber' name='faxNumber' value = '<?php echo $contact->faxNumber; ?>'>
		
		<label for='emailAddress'><?php echo _("Email:");?></label>
		<input type='text' id='emailAddress' name='emailAddress' value = '<?php echo $contact->emailAddress; ?>'>
		
		<p class="checkbox indent">
			<input type='checkbox' id='invalidInd' name='invalidInd' <?php echo $invalidChecked; ?>>
			<label for='invalidInd'><?php echo _("Archived");?></label>	
		</p>
		
		<label for='addressText'><?php echo _("Address:");?></label>
		<textarea rows='3' id='addressText' name='addressText'><?php echo $contact->addressText; ?></textarea>
		
		<fieldset class="subgrid">
			<legend><?php echo _("Role(s):");?></legend>
			<div class="form-group">
				<ul class="unstyled columns">
				<?php
				if (is_array($contactRoleArray) && count($contactRoleArray) > 0) {
					foreach ($contactRoleArray as $contactRoleIns){
						$checked = '';
						if (in_array($contactRoleIns['contactRoleID'],$contactRoleProfileArray)){
							$checked = ' checked ';
						}
						echo "<li class='checkbox'>";
						echo "<input class='check_roles' type='checkbox' name='" . $contactRoleIns['contactRoleID'] . "' id='" . $contactRoleIns['contactRoleID'] . "' value='" . $contactRoleIns['contactRoleID'] . "' aria-describedby='span_error_contactRole' ".$checked.">";
						echo "<label for='" . $contactRoleIns['contactRoleID'] . "'>" . $contactRoleIns['shortName'] . "</label>";
						echo "</li>\n";
					}

				}
				?>
			</ul>
			<p id='span_error_contactRole' class='error'></p>
		</div>

		</fieldset>

		<label for='noteText'><?php echo _("Notes:");?></label>
		<textarea rows='6' id='noteText' name='noteText'><?php echo $contact->noteText; ?></textarea>
		
		<p class="actions">
			<input type='submit' value='<?php echo _("submit");?>' name='submitContactForm' id ='submitContactForm' class='submit-button primary'>
			<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='cancel-button secondary'>
		</p>
		</div>

		</form>
		</div>


		<script type="text/javascript" src="js/forms/contactSubmitForm.js?random=<?php echo rand(); ?>"></script>

		<?php

        break;






    case 'getAccountForm':
    	$organizationID = $_GET['organizationID'];
    	if (isset($_GET['externalLoginID'])) $externalLoginID = $_GET['externalLoginID']; else $externalLoginID = '';
    	$externalLogin = new ExternalLogin(new NamedArguments(array('primaryKey' => $externalLoginID)));


		//get all contact roles for output in drop down
		$externalLoginTypeArray = array();
		$externalLoginTypeObj = new ExternalLoginType();
		$externalLoginTypeArray = $externalLoginTypeObj->allAsArray();

		?>
		<div id='div_organizationForm'>
		<form id='organizationForm'>
		<input type='hidden' name='editOrganizationID' id='editOrganizationID' value='<?php echo $organizationID; ?>'>
		<input type='hidden' name='editExternalLoginID' id='editExternalLoginID' value='<?php echo $externalLoginID; ?>'>

		<h2><?php if ($externalLoginID){ echo _("Edit Login"); } else { echo _("Add Login"); } ?></h2>
		<p id='span_errors' class='error'></p>
		
		<div class="form-grid">
		<label for='externalLoginTypeID'><?php echo _("Login Type:");?></label>
		<select name='externalLoginTypeID' id='externalLoginTypeID'>
		<?php
		foreach ($externalLoginTypeArray as $externalLoginType){
			if ($externalLoginType['externalLoginTypeID'] == $externalLogin->externalLoginTypeID){
				echo "<option value='" . $externalLoginType['externalLoginTypeID'] . "' selected>" . $externalLoginType['shortName'] . "</option>\n";
			}else{
				echo "<option value='" . $externalLoginType['externalLoginTypeID'] . "'>" . $externalLoginType['shortName'] . "</option>\n";
			}
		}
		?>
		</select>
		
		<label for='loginURL'><?php echo _("URL:");?></label>
		<input type='url' id='loginURL' name='loginURL' value = '<?php echo $externalLogin->loginURL; ?>'>
		
		<label for='emailAddress'><?php echo _("Local Account Email:");?></label>
		<input type='text' id='emailAddress' name='emailAddress' value = '<?php echo $externalLogin->emailAddress; ?>'>
		
		<label for='username'><?php echo _("Username:");?></label>
		<input type='text' id='username' name='username' value = '<?php echo $externalLogin->username; ?>'>
		
		<label for='password'><?php echo _("Password:");?></label>
		<input type='text' id='password' name='password' value = '<?php echo $externalLogin->password; ?>'>
		
		<label for='noteText'><?php echo _("Notes:");?></label>
		<textarea rows='3' id='noteText' name='noteText'><?php echo $externalLogin->noteText; ?></textarea>
		
		<p class="actions">
			<input type='button' value='<?php echo _("submit");?>' name='submitExternalLoginForm' id ='submitExternalLoginForm' class='submit-button primary'></td>
			<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='cancel-button secondary'></td>
		</p>
		</div>

		</form>
		</div>


		<script type="text/javascript" src="js/forms/externalLoginSubmitForm.js?random=<?php echo rand(); ?>"></script>

		<?php

        break;

	case 'getCloseResourceIssueForm':
		$issueID = $_GET['issueID'];
?>
		<div id="closeIssue">
			<form>
				<input type="hidden" id="issueID" name="issueID" value="<?php echo $issueID; ?>">
				
				<h2><?php echo _('Issue Resolution'); ?></h2>
				
				<label for="resolutionText"><?php echo _('Resolution:'); ?></label>
				<textarea id="resolutionText" name="resolutionText"></textarea>
				
				<p class="actions">
					<input type="submit" value="submit" name="submitCloseResourceIssue" id="submitCloseResourceIssue" class='submit-button primary'>
					<input type='button' value='cancel' onclick="myCloseDialog();" class='cancel-button secondary'>
				</p>

			</form>
		</div>
<?php
	break;
	case 'getNewIssueForm':
		$organizationID = $_GET["organizationID"];

		$organization = new Organization(new NamedArguments(array('primaryKey' => $organizationID)));
		$organizationContactsArray = $organization->getUnarchivedContacts();
		$organizationResourcesArray = $organization->getResources(5);
?>

<form id='newIssueForm'>
	<input type="hidden" id="sourceOrganizationID" name="sourceOrganizationID" value="<?php echo $organizationID;?>" />
	
		<h2><?php echo _("Report New Problem");?></h2>
		<p class="required"><?php echo _("* required fields");?></p>
			
		<div class="form-grid">
			
				<p class="fw-bold"><?php echo _("Organization:");?> <span class='required'>*</span></p>
				<p><?php echo $organization->name; ?></p>
				<p id='span_error_organizationId' class='error'></p>
			
				<label for="contactIDs"><?php echo _("Contact:");?> <span class='required'>*</span></label>
				<select multiple required type='text' id='contactIDs' name='contactIDs[]' aria-describedby="span_error_contactName">
<?php

		foreach ($organizationContactsArray as $contact) {
			$contactname = ($contact->name);
				if (!empty($contactname)) {
					echo "		<option value=\"{$contact->contactID}\">{$contact->name}</option>";
				} else {
					echo "		<option value=\"{$contact->contactID}\">{$contact->emailAddress}</option>";
				}
		}

?>
				</select>
				<p id='span_error_contactName' class='error'></p>
				
				<a id="getCreateContactForm" href="#"><?php echo _("add contact");?></a>
				<div id="inlineContact"></div>
			
				<p class="checkbox">
					<input type='checkbox' id='ccCreator' name='ccCreator' class='changeInput' aria-describedby="span_error_ccCreator">
					<label for="ccCreator"><?php echo _("CC myself:");?></label>
					<span id='span_error_ccCreator' class='error'></span>
				</p>
		
				<label for="inputEmail"><?php echo _("CC:");?></label>
				<div>
					<input type="text" id="inputEmail" name="inputEmail">
					<input type="button" id="addEmail" name="addEmail" value="<?php echo _('Add');?>" class="secondary">
				</div>
				<!-- TODO: check this display -->
				<h3><?php echo _("Current CCs: ");?></h3>
				<p id="currentEmails"></p>

				<input type="hidden" id='ccEmails' name='ccEmails' value='' class='changeInput' aria-describedby="span_error_contactIDs">
				<span id='span_error_contactIDs' class='error'></span>
			
				<label for="subjectText"><?php echo _("Subject:");?> <span class='required'>*</span></label>
				<input required type='text' id='subjectText' name='issue[subjectText]' value='' class='changeInput' aria-describedby="span_error_subjectText">
				<span id='span_error_subjectText' class='error'></span>
			
				<label for="bodyText"><?php echo _("Body:");?> <span class='required'>*</span></label>
				<textarea required id='bodyText' name='issue[bodyText]' aria-describedby="span_error_bodyText"></textarea>
				<span id='span_error_bodyText' class='error'></span>
			
				<h3><?php echo _("Applies to:");?> <span class='required'>*</span></h3>
				<!-- TODO: a11y: use the same name attribute for these checkboxes and add required attribute to both -->
				<p class="checkbox">
					<input type="checkbox" class="issueResources" id="organizationID" name="organizationID" value="<?php echo $organization->organizationID;?>"> 
					<label for="organizationID"><?php printf(_("Applies to all %s resources"), $organization->name);?></label>
				</p>
				<p class="checkbox">
					<input type="checkbox" class="issueResources" id="otherResources">
					<label for="otherResources"> <?php printf(_("Applies to selected %s resources"), $organization->name);?></label>
				</p>
				<select multiple id="resourceIDs" name="resourceIDs[]" aria-label="<?php echo _('Select resources');?>" aria-describedby="span_error_entities">
<?php
		if (!empty($organizationResourcesArray)) {
			foreach ($organizationResourcesArray as $resource) {
				echo "		<option value=\"{$resource['resourceID']}\">{$resource['titleText']}</option>";
			}
		}
?>
				</select>
				<p id='span_error_entities' class='error'></p>
			
	<!-- TODO: i18n - no sentence-style forms! Remove aria-describedby span when fixed. -->
	<label for="issueReminderInterval"><?php echo _("Send me a reminder every");?></label>
	<div>
		<input type="number" min="1" max="31" value="7" name="issue[reminderInterval]" id="issueReminderInterval" aria-describedby="days">
		<span id="days"><?php echo _("day(s)");?></span>
	</div>

	<p class="actions">
		<input type='button' value='<?php echo _("submit");?>' name='submitNewResourceIssue' id='submitNewResourceIssue' class='submit-button primary'>
		<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog();" class='cancel-button secondary'>
	</p>

</form>

<?php
	break;
		case 'getResolveDowntimeForm':
			$downtimeID = is_numeric($_GET['downtimeID']) ? $_GET['downtimeID']:null;

			if ($downtimeID) {
				$downtime = new Downtime(new NamedArguments(array('primaryKey' => $downtimeID)));

?>
<form id="resolveDowntimeForm">
	<input name="downtimeID" type="hidden" value="<?php echo $downtime->downtimeID;?>" />
	<h2><?php echo _('Resolve Downtime'); ?></h2>
	
		<h3><?php echo _('Downtime Resolution:'); ?></h3>
		<!-- TODO: check layout -->
		<p>
			<label for="endDate"><?php echo _("Date");?></label>
			<input class="date-pick" type="text" name="endDate" id="endDate" aria-describedby="span_error_endDate">
			<span id='span_error_endDate' class='error updateDowntimeError'></span>
		</p>
		<p>
			<!-- TODO: replace 3-part time form with native <input>? -->
			<b><?php echo _("Time");?></b>
					<?php
					echo buildTimeForm("endTime");
					?>
					<span id='span_error_endDate' class='error updateDowntimeError'></span>
			</p>
			
			<label for="note"><?php echo _("Note");?></label>
			<textarea name="note" id="note"><?php echo $downtime->note;?></textarea>
			
			<p class="actions">
				<input type='submit' value='submit' name='submitUpdatedDowntime' id='submitUpdatedDowntime' class="submit-button primary">
				<input type='button' value='cancel' onclick="myCloseDialog();" class="cancel-button secondary">
			</p>
			
</form>
<?php
			} else {
?>
		<p>
			<?php echo _("Unable to retrieve Downtime.");?>
		</p>
		<p class="actions">
			<input type='button' value='cancel' onclick="myCloseDialog();" class="cancel-button secondary">
		</p>
<?php
			}
	break;
		case 'getNewDowntimeForm':

	$organizationID = $_GET["organizationID"];
	$organization = new Organization(new NamedArguments(array('primaryKey' => $organizationID)));

	$issueID = $_GET['issueID'];

	$issues = $organization->getIssues();

	$downtimeObj = new Downtime();
	$downtimeTypeNames = $downtimeObj->getDowntimeTypesArray();

?>

<form id='newDowntimeForm' class="form-grid">
	<input type="hidden" name="sourceOrganizationID" value="<?php echo $organization->organizationID;?>" />
	<h2 class='headerText'> <?php echo _("Resource Downtime Report");?></h2>
	
	<h3 class="wide"><?php echo _("Downtime Start:");?></h3>
	
	<label for="startDate"><?php echo _("Date");?></label>
	<input class="date-pick" type="text" name="startDate" id="startDate" aria-describedby="span_error_startDate" placeholder='mm/dd/yyyy' />
	<span id='span_error_startDate' class='error addDowntimeError'></span>

	<fieldset class="subgrid">
		<legend><?php echo _("Time");?></legend>
		<div class="form-group">
			<?php
			echo buildTimeForm("startTime");
			?>
			<span id='span_error_startTime' class='error addDowntimeError'></span>
		</div>
	</fieldset>	

	<h3 class="wide"><?php echo _("Downtime Resolution:");?></h3>
	
	<label for="endDate"><?php echo _("Date");?></label>
	<div class="form-group">
		<input class="date-pick" type="text" name="endDate" id="endDate" aria-describedby="span_error_endDate" placeholder='mm/dd/yyyy' />
		<span id='span_error_endDate' class='error addDowntimeError'></span>
	</div>
				
	<fieldset class="subgrid">
		<legend><?php echo _("Time");?></legend>
		<div class="form-group">
			<?php
			echo buildTimeForm("endTime");
			?>
			<span id='span_error_endTime' class='error addDowntimeError'></span>
		</div>
	</fieldset>
	
	<label for="downtimeType"><?php echo _("Problem Type:");?></label>
	<select class="downtimeType" name="downtimeType" id="downtimeType">
	<?php
			foreach ($downtimeTypeNames as $downtimeType) {
				echo "<option value=".$downtimeType["downtimeTypeID"].">".$downtimeType["shortName"]."</option>";
			}
		?>
	</select>
				
	<?php
	if ($issues) {
	?>
		<label for="issueID"><?php echo _("Link to open issue:");?></label>
		<select class="issueID" name="issueID">
			<option value=""><?php echo _("none");?></option>
			<?php
				foreach ($issues as $issue) {
					echo "<option".(($issueID == $issue->issueID) ? ' selected':'')." value=".$issue->issueID.">".$issue->subjectText."</option>";
				}
			?>
		</select>
	<?php
	}
	?>

	<label for="note"><?php echo _("Note:");?></label>
	<textarea name="note" id="note"></textarea>

	<p class="actions">
		<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog();" class='cancel-button secondary'>
	</p>
			
</form>

<?php

	break;
    case 'getIssueLogForm':
    	$organizationID = $_GET['organizationID'];
    	if (isset($_GET['issueLogID'])) $issueLogID = $_GET['issueLogID']; else $issueLogID = '';
    	$issueLog = new IssueLog(new NamedArguments(array('primaryKey' => $issueLogID)));

		if (($issueLog->issueStartDate) && ($issueLog->issueStartDate != "0000-00-00")) {
			$issueStartDate=format_date($issueLog->issueStartDate);
		}else{
			$issueStartDate='';
		}

    if (($issueLog->issueEndDate) && ($issueLog->issueEndDate != "0000-00-00")) {
			$issueEndDate=format_date($issueLog->issueEndDate);
		}else{
			$issueEndDate='';
		}

    $issueLogTypeObj = new IssueLogType();
    $issueLogTypeArray = $issueLogTypeObj->allAsArray();


		?>
		<div id='div_issueForm'>
		<form id='issueForm'>
		<input type='hidden' name='editOrganizationID' id='editOrganizationID' value='<?php echo $organizationID; ?>'>
		<input type='hidden' name='editIssueLogID' id='editIssueLogID' value='<?php echo $issueLogID; ?>'>
	
		<h2 class="headerText"><?php if ($issueLogID){ echo _("Edit Issue"); } else { echo _("Add Issue"); } ?></h2>
		<p id='span_errors' class='error'></p>
		
		<div class="form-grid">

		<label for='issueLogTypeID'><?php echo _("Type:");?></label>
		<select name='issueLogTypeID' id='issueLogTypeID'>
      <option value=''></option>
      <?php
      foreach ($issueLogTypeArray as $issueLogType){
        if (!(trim(strval($issueLogType['issueLogTypeID'])) != trim(strval($issueLog->issueLogTypeID)))){
          echo "<option value='" . $issueLogType['issueLogTypeID'] . "' selected>" . $issueLogType['shortName'] . "</option>\n";
        }else{
          echo "<option value='" . $issueLogType['issueLogTypeID'] . "'>" . $issueLogType['shortName'] . "</option>\n";
        }
      }
      ?>
      </select>
   
		<label for='issueStartDate'><?php echo _("Start date:");?></label>
		<div class="form-group">
			<input class='date-pick' id='issueStartDate' name='issueStartDate' value='<?php echo $issueStartDate; ?>' placeholder='mm/dd/yyyy' />
		</div>

		<label for='issueEndDate'><?php echo _("End date:");?></label>
		<div class="form-group">
			<input class='date-pick' id='issueEndDate' name='issueEndDate' value='<?php echo $issueEndDate; ?>' placeholder='mm/dd/yyyy' />
		</div>
		
		<label for='noteText'><?php echo _("Notes:");?></label>
		<textarea rows='3' id='noteText' name='noteText'><?php echo $issueLog->noteText; ?></textarea></td>
		
			<p class="actions">
				<input type='button' value='<?php echo _("submit");?>' name='submitIssueLogForm' id ='submitIssueLogForm' class='submit-button primary'>
				<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='cancel-button secondary'>
			</p>
		</form>
		</div>

		<script type="text/javascript" src="js/forms/issueLogSubmitForm.js?random=<?php echo rand(); ?>"></script>

		<?php

        break;





	case 'getAdminDisplay':
		$className = $_GET['className'];


		$instanceArray = array();
		$obj = new $className();

		$instanceArray = $obj->allAsArray();



		if (is_array($instanceArray) && count($instanceArray) > 0) {
			?>
			<table class='dataTable table-border'>
				<?php

				foreach($instanceArray as $instance) {
					echo "<tr>";
					echo "<th scope='row'>" . $instance['shortName'] . "</th>";
					echo "<td class='actions'><button type='button' class='btn' onclick='myDialog(\"ajax_forms.php?action=getAdminUpdateForm&className=" . $className . "&updateId=" . $instance[lcfirst($className) . 'ID'] . "&height=130&width=250&modal=true\",180,350)' class='thickbox' id='expression'><img id='Edit' class='editIcon' src='images/edit.gif' title= '".sprintf(_("Edit %s"), $instance['shortName'])."' /></button>";
					echo "<button type='button' class='btn' onclick='deleteData(\"" . $className . "\",\"" . $instance[lcfirst($className) . 'ID'] . "\")'><img id='Remove' class='removeIcon' src='images/cross.gif' title= '".sprintf(_("Remove %s"), $instance['shortName'])."' /></button></td>";
					echo "</tr>";
				}

				?>
			</table>
			<?php

		}else{
			echo _("(none found)");
		}

		break;




	case 'getAdminUpdateForm':
		$className = $_GET['className'];
		$updateId = $_GET['updateId'];

		$instance = new $className(new NamedArguments(array('primaryKey' => $updateId)));

		?>
		<div id='div_updateForm' class="block-form">
		<h2 class='headerText'><?php echo _("Edit");?></h2>
		<p id='span_errors' class="error"></p>

		<p>
			<input type='text' id='updateVal' name='updateVal' value='<?php echo $instance->shortName ?>' aria-label='<?php echo _('Name') ?>'/>
		</p>

		<p class="actions">
			<input type='submit' onclick='updateData("<?php echo $className; ?>", "<?php echo $updateId; ?>")' id='updateButton' value="<?php echo _("Update") ?>" />	
			<input type="button" onclick='myCloseDialog()' id='closeButton' class='cancel-button secondary' value="<?php echo _("Cancel");?>" />
		</p>

		</div>

		<?php

		break;






	case 'getAdminUserForm':
		$instanceArray = array();
		$user = new User();
		$tempArray = array();

		foreach ($user->allAsArray() as $tempArray) {

			$privilege = new Privilege(new NamedArguments(array('primaryKey' => $tempArray['privilegeID'])));

			$tempArray['priv'] = $privilege->shortName;

			array_push($instanceArray, $tempArray);
		}



		if (is_array($instanceArray) && count($instanceArray) > 0) {
			?>
			<table class='dataTable table-border table-striped'>
				<thead>
				<tr>
				<th scope="col"><?php echo _("Login ID");?></th>
				<th scope="col"><?php echo _("First Name");?></th>
				<th scope="col"><?php echo _("Last Name");?></th>
				<th scope="col"><?php echo _("Privilege");?></th>
				<th scope="col" class="actions"><?php echo _("Actions");?></th>
				</tr>
				</thead>
				<tbody>
				<?php

				foreach($instanceArray as $instance) {
					echo "<tr>";
					echo "<td>" . $instance['loginID'] . "</td>";
					echo "<td>" . $instance['firstName'] . "</td>";
					echo "<td>" . $instance['lastName'] . "</td>";
					echo "<td>" . $instance['priv'] . "</td>";
					echo "<td class='actions'><button type='button' class='btn' onclick='myDialog(\"ajax_forms.php?action=getAdminUserUpdateForm&loginID=" . $instance['loginID'] . "&height=185&width=250&modal=true\",250,350)' class='thickbox' id='expression'><img id='Edit' src='images/edit.gif' title= '"._("Edit")."' /></button>";
					echo "<button type='button' class='btn' onclick='deleteUser(\"" . $instance['loginID'] . "\")'><img id='Remove'  src='images/cross.gif' title= '"._("Remove")."' /></a></td>";
					echo "</tr>";
				}

				?>
			</tbody>
			</table>
			<?php

		}else{
			echo _("(none found)");
		}

		break;



	case 'getAdminUserUpdateForm':
		if (isset($_GET['loginID'])) $loginID = $_GET['loginID']; else $loginID = '';

		if ($loginID){
			$update = _('Edit User');
		}else{
			$update = _('Add User');
		}
		$user = new User(new NamedArguments(array('primaryKey' => $loginID)));

		//get all roles for output in drop down
		$privilegeArray = array();
		$privilegeObj = new Privilege();
		$privilegeArray = $privilegeObj->allAsArray();



		?>
		<div id='div_updateForm'>
		<h2 class='headerText'><?php echo $update; ?></h2>
		
		<div class="form-grid">
		<label for='loginID'><?php echo _("Login ID");?></label>
		<?php if (!$loginID) { ?><input type='text' id='loginID' name='loginID' value='<?php echo $loginID; ?>'/> 
		<?php } else { echo $loginID; } ?>
		
		<label for='firstName'><?php echo _("First Name");?></label>
		<input type='text' id='firstName' name='firstName' value="<?php echo $user->firstName; ?>" />
	
		<label for='lastName'><?php echo _("Last Name");?></label>
		<input type='text' id='lastName' name='lastName' value="<?php echo $user->lastName; ?>"/>
		
		<label for='privilegeID'><?php echo _("Privilege");?></label>
		<select name='privilegeID' id='privilegeID'>
			<option value=''></option>
			<?php

			foreach ($privilegeArray as $privilege){
				if ($privilege['privilegeID'] == $user->privilegeID){
					echo "<option value='" . $privilege['privilegeID'] . "' selected>" . $privilege['shortName'] . "</option>\n";
				}else{
					echo "<option value='" . $privilege['privilegeID'] . "'>" . $privilege['shortName'] . "</option>\n";
				}
			}

			?>
		</select>

		<p class="actions">
			<input type='submit' value='<?php echo $update; ?>' onclick='window.parent.submitUserData("<?php echo $loginID; ?>");' class='submit-button primary' />
			<input type='button' value='<?php echo _("Close");?>' onclick="myCloseDialog(); return false" class='cancel-button secondary' />
		</p>

		</div>
		</div>

		<?php

		break;





	default:
			if (empty($action))
        return;
      printf(_("Action %s not set up!"), $action);
      break;


}


?>
