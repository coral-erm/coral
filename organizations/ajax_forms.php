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

		<table class="thickboxTable" style="width:100%;">
		<tr>
		<td colspan='2' id='org-title'><span class='headerText'><?php if ($organizationID != "") { echo _("Update Organization"); }else{ echo _("Add New Organization"); } ?></span>
		<br /></td>
		</tr>

		<tr>
		<td style='vertical-align:top;text-align:right;padding-top:10px;'><label for='organizationName'><b><?php echo _("Name:");?></b></label></td>
		<td style='vertical-align:top;padding-top:10px;'><input type='text' id='organizationName' name='organizationName' value = "<?php echo htmlentities($organization->name); ?>" style='width:220px;' /><span id='span_errors' style='color:red'></span></td>
		</tr>

		<?php if (count($parentOrganizationArray) > 0){ ?>
			<tr>
			<td style='vertical-align:top;text-align:right;'><label for='parentOrganization'><b><?php echo _("Parent:");?></b></label></td>
			<td>
			<?php
			foreach ($parentOrganizationArray as $parentOrganization){
				echo "<input type='text' id='parentOrganization' name='parentOrganization'  value = \"" . $parentOrganization['name'] . "\" style='width:220px;' />";
				echo "<input type='hidden' id='parentOrganizationID' name='parentOrganizationID' value=\"" . $parentOrganization['organizationID'] . "\">";
			}
			?>
			</td>
			</tr>
		<?php
		}else{
		?>
			<tr>
			<td style='vertical-align:top;text-align:right;'><label for='parentOrganization'><b><?php echo _("Parent:");?></b></label></td>
			<td>
			<input type='text' id='parentOrganization' name='parentOrganization' value = '' style='width:220px;' />
			<input type='hidden' id='parentOrganizationID' name='parentOrganizationID' value=''>
			</td>
			</tr>
		<?php } ?>

		<tr>
		<td style='vertical-align:top;text-align:right;'><label for='companyURL'><b><?php echo _("Company URL:");?></b></label></td>
		<td><input type='text' id='companyURL' name='companyURL' value = '<?php if (!$organizationID) { echo "http://"; } else { echo $organization->companyURL; } ?>' style='width:220px' />
		</td>
		</tr>

		<tr>
		<td style='vertical-align:top;text-align:right;'><label for='orgRoles'><b><?php echo _("Role(s):");?></b></label></td>
		<td>

			<table>
			<?php
			$i=0;
			if (count($organizationRoleArray) > 0){
				foreach ($organizationRoleArray as $organizationRoleIns){
					$i++;
					if(($i % 2)==1){
						echo "<tr>\n";
					}
					if (in_array($organizationRoleIns['organizationRoleID'],$organizationRoleProfileArray)){
						echo "<td><input class='check_roles' type='checkbox' name='" . $organizationRoleIns['organizationRoleID'] . "' id='" . $organizationRoleIns['organizationRoleID'] . "' value='" . $organizationRoleIns['organizationRoleID'] . "' checked />   " . $organizationRoleIns['shortName'] . "</td>\n";
					}else{
						echo "<td><input class='check_roles' type='checkbox' name='" . $organizationRoleIns['organizationRoleID'] . "' id='" . $organizationRoleIns['organizationRoleID'] . "' value='" . $organizationRoleIns['organizationRoleID'] . "' />   " . $organizationRoleIns['shortName'] . "</td>\n";
					}
					if(($i % 2)==0){
						echo "</tr>\n";
					}
				}

				if(($i % 2)==1){
					echo "<td>&nbsp;</td></tr>\n";
				}
			}
			?>
			</table>
		</td>
		</tr>

		<tr>
		<td style='vertical-align:top;text-align:right;'><label for='accountDetailText'><b><?php echo _("Account Details:");?></b></label></td>
		<td><textarea rows='3' id='accountDetailText' name='accountDetailText' style='width:220px'><?php echo $organization->accountDetailText; ?></textarea></td>
		</tr>

		<tr>
		<td style='vertical-align:top;text-align:right;'><label for='noteText'><b><?php echo _("Notes:");?></b></label></td>
		<td><textarea rows='3' id='noteText' name='noteText' style='width:220px'><?php echo $organization->noteText; ?></textarea></td>
		</tr>


		<tr style="vertical-align:middle;">
		<td style="padding-top:8px;text-align:right;">&nbsp;</td>
		<td style="padding-top:8px;padding-right:8px;text-align:left;">
			<table class='noBorderTable' style='width:100%;'>
				<tr>
					<td style='width:60px'><input type='button' value='<?php echo _("submit");?>' name='submitOrganizationChanges' id ='submitOrganizationChanges' class='submit-button'></td>
					<td><input type='button' value='<?php echo _("cancel");?>' onclick="tb_remove()" class='cancel-button'></td>
				</tr>
			</table>
		</td>
		</tr>

		</table>

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


		<table class="thickboxTable" style="background-image:url('images/title.gif');background-repeat:no-repeat;width:280px;">
		<tr>
		<td colspan='2'><span class='headerText'><?php if ($aliasID){ echo _("Update Alias"); } else { echo _("Add Alias"); } ?></span>
		<span id='span_errors' style='color:red;'></span><br />
		</td>
		</tr>

		<tr>
		<td><label for='aliasTypeID'><b><?php echo _("Alias Type");?></b></label></td>
		<td>
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
		</td>
		</tr>

		<tr>
		<td><label for='aliasName'><b><?php echo _("Name");?></b></label></td>
		<td>
		<input type='text' id='aliasName' name='aliasName' value = "<?php echo $alias->name; ?>" style='width:195px' /><span id='span_error_aliasName'></span>
		</td>
		</tr>


		<tr style="vertical-align:middle;">
		<td style="padding-top:8px;text-align:right;">&nbsp;</td>
		<td style="padding-top:8px;padding-right:8px;text-align:left;">
			<table class='noBorderTable' style='width:100%;'>
				<tr>
					<td style='width:60px'><input type='button' value='<?php echo _("submit");?>' name='submitAliasForm' id ='submitAliasForm' class='cancel-button'></td>
					<td><input type='button' value='<?php echo _("cancel");?>' onclick="tb_remove()" class='cancel-button'></td>
				</tr>
			</table>
		</td>
		</tr>


		</table>

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
		<div style="display:inline-block;vertical-align:top;width:42%;">
			<div class="form-element">
				<label for="contactAddName">Name</label>
				<input type='text' id='contactAddName' name='contactName' /><br />
				<span id='span_error_contactAddName' class='smallDarkRedText'></span>
			</div>
			<div class="form-element">
				<label for="emailAddress">Email</label>
				<input type='text' id='emailAddress' name='emailAddress' />
				<span id='span_error_contactEmailAddress' class='smallDarkRedText'></span>
			</div>
		</div>
<?php
		if (count($contactRoleArray) > 0){
			echo '<div style="display:inline-block;vertical-align:top;width:48%;">
					<label style="display:block;" for="'.$contactRoleIns['contactRoleID'].'">Roles</label>';
			foreach ($contactRoleArray as $contactRoleIns){
				echo "<div class=\"form-element form-element-tight\">
						<input class='check_roles' type='checkbox' name='" . $contactRoleIns['contactRoleID'] . "' id='" . $contactRoleIns['contactRoleID'] . "' value='" . $contactRoleIns['contactRoleID'] . "' />   
						<span class='smallText'>" . $contactRoleIns['shortName'] . "</span>
					 </div>";
			}
			echo '</div>
				  <input type="button" id="createContact" value="Create" />';
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

		<table class="thickboxTable" style="background-image:url('images/title.gif');background-repeat:no-repeat;width:340px;">
		<tr>
		<td colspan='2'><span class='headerText'><?php if ($contactID){ echo _("Update Contact"); } else { echo _("Add Contact"); } ?></span>
		<span id='span_errors' style='color:red;'></span>
		</td>
		</tr>

		<tr>
		<td style='text-align:right'><label for='contactName'><b><?php echo _("Name:");?></b></label></td>
		<td>
            <input type='text' id='contactName' name='contactName' value = "<?php echo $contact->name; ?>" style='width:150px' /><span id='span_error_contactName' style='color:red'></span>
		</td>
		</tr>

		<tr>
		<td style='text-align:right'><label for='contactTitle'><b><?php echo _("Title:");?></b></label></td>
		<td>
		<input type='text' id='contactTitle' name='contactTitle' value = '<?php echo $contact->title; ?>' style='width:150px' />
		</td>
		</tr>

		<tr>
		<td style='text-align:right'><label for='phoneNumber'><b><?php echo _("Phone:");?></b></label></td>
		<td>
		<input type='text' id='phoneNumber' name='phoneNumber' value = '<?php echo $contact->phoneNumber; ?>' style='width:150px' />
		</td>
		</tr>

		<tr>
		<td style='text-align:right'><label for='altPhoneNumber'><b><?php echo _("Alt Phone:");?></b></label></td>
		<td>
		<input type='text' id='altPhoneNumber' name='altPhoneNumber' value = '<?php echo $contact->altPhoneNumber; ?>' style='width:150px' />
		</td>
		</tr>

		<tr>
		<td style='text-align:right'><label for='faxNumber'><b><?php echo _("Fax:");?></b></label></td>
		<td>
		<input type='text' id='faxNumber' name='faxNumber' value = '<?php echo $contact->faxNumber; ?>' style='width:150px' />
		</td>
		</tr>

		<tr>
		<td style='text-align:right'><label for='emailAddress'><b><?php echo _("Email:");?></b></label></td>
		<td>
		<input type='text' id='emailAddress' name='emailAddress' value = '<?php echo $contact->emailAddress; ?>' style='width:150px' />
		</td>
		</tr>

		<tr>
		<td style='text-align:right'><label for='invalidInd'><b><?php echo _("Archived:");?></b></label></td>
		<td>
		<input type='checkbox' id='invalidInd' name='invalidInd' <?php echo $invalidChecked; ?> />
		</td>
		</tr>


		<tr>
		<td style='vertical-align:top;text-align:right'><label for='addressText'><b><?php echo _("Address:");?></b></label></td>
		<td><textarea rows='3' id='addressText' name='addressText' style='width:150px;'><?php echo $contact->addressText; ?></textarea></td>
		</tr>



		<tr>
		<td style='vertical-align:top;text-align:right;'><label for='orgRoles'><b><?php echo _("Role(s):");?></b></label></td>
		<td>

			<table>
			<?php
			$i=0;
			if (count($contactRoleArray) > 0){
				foreach ($contactRoleArray as $contactRoleIns){
					$i++;
					if(($i % 3)==1){
						echo "<tr>\n";
					}
					if (in_array($contactRoleIns['contactRoleID'],$contactRoleProfileArray)){
						echo "<td style='vertical-align:top;text-align:left;'><input class='check_roles' type='checkbox' name='" . $contactRoleIns['contactRoleID'] . "' id='" . $contactRoleIns['contactRoleID'] . "' value='" . $contactRoleIns['contactRoleID'] . "' checked />   <span class='smallText'>" . $contactRoleIns['shortName'] . "</span></td>\n";
					}else{
						echo "<td style='vertical-align:top;text-align:left;'><input class='check_roles' type='checkbox' name='" . $contactRoleIns['contactRoleID'] . "' id='" . $contactRoleIns['contactRoleID'] . "' value='" . $contactRoleIns['contactRoleID'] . "' />   <span class='smallText'>" . $contactRoleIns['shortName'] . "</span></td>\n";
					}
					if(($i % 3)==0){
						echo "</tr>\n";
					}
				}

				if(($i % 3)==1){
					echo "<td>&nbsp;</td><td>&nbsp;</td></tr>\n";
				}else if(($i % 3)==2){
					echo "<td>&nbsp;</td></tr>\n";
				}
			}
			?>
			</table>
			<span id='span_error_contactRole' class='redText'></span>

		</td>
		</tr>


		<tr>
		<td style='vertical-align:top;text-align:right'><label for='noteText'><b><?php echo _("Notes:");?></b></label></td>
		<td><textarea cols='36' rows='6' id='noteText' name='noteText' style='width:250px;'><?php echo $contact->noteText; ?></textarea></td>
		</tr>

		<tr style="vertical-align:middle;">
		<td style="padding-left:8px;padding-top:8px;">&nbsp;</td>
		<td style="padding-top:8px;padding-right:8px;">
			<table class='noBorderTable' style='width:100%;'>
				<tr>
					<td style='width:60px'><input type='button' value='<?php echo _("submit");?>' name='submitContactForm' id ='submitContactForm' class='submit-button'></td>
					<td><input type='button' value='<?php echo _("cancel");?>' onclick="tb_remove()" class='cancel-button'></td>
				</tr>
			</table>
		</td>
		</tr>

		</table>

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

		<table class="thickboxTable" style="background-image:url('images/title.gif');background-repeat:no-repeat;width:340px;">
		<tr>
		<td colspan='2'><span class='headerText'><?php if ($externalLoginID){ echo _("Update Login"); } else { echo _("Add Login"); } ?></span>
		<span id='span_errors' style='color:red;'></span><br />
		</td>
		</tr>

		<tr>
		<td style='vertical-align:top;text-align:right;'><label for='externalLoginTypeID'><b><?php echo _("Login Type:");?></b></label></td>
		<td>
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
		</td>
		</tr>

		<tr>
		<td style='vertical-align:top;text-align:right;'><label for='loginURL'><b><?php echo _("URL:");?></b></label></td>
		<td>
		<input type='text' id='loginURL' name='loginURL' value = '<?php echo $externalLogin->loginURL; ?>' style='width:200px' />
		</td>
		</tr>

		<tr>
		<td style='vertical-align:top;text-align:right;'><label for='emailAddress'><b><?php echo _("Local Account Email:");?></b></label></td>
		<td>
		<input type='text' id='emailAddress' name='emailAddress' value = '<?php echo $externalLogin->emailAddress; ?>' style='width:200px' />
		</td>
		</tr>

		<tr>
		<td style='vertical-align:top;text-align:right;'><label for='username'><b><?php echo _("Username:");?></b></label></td>
		<td>
		<input type='text' id='username' name='username' value = '<?php echo $externalLogin->username; ?>' style='width:200px' />
		</td>
		</tr>

		<tr>
		<td style='vertical-align:top;text-align:right;'><label for='password'><b><?php echo _("Password:");?></b></label></td>
		<td>
		<input type='text' id='password' name='password' value = '<?php echo $externalLogin->password; ?>' style='width:200px' />
		</td>
		</tr>

		<tr>
		<td style='vertical-align:top;text-align:right;'><label for='noteText'><b><?php echo _("Notes:");?></b></label></td>
		<td><textarea rows='3' id='noteText' name='noteText' style='width:200px'><?php echo $externalLogin->noteText; ?></textarea></td>
		</tr>


		<tr style="vertical-align:middle;">
		<td style="padding-left:8px;padding-top:8px;">&nbsp;</td>
		<td style="padding-top:8px;padding-right:8px;">
			<table class='noBorderTable' style='width:100%;'>
				<tr>
					<td style='width:60px'><input type='button' value='<?php echo _("submit");?>' name='submitExternalLoginForm' id ='submitExternalLoginForm' class='submit-button'></td>
					<td><input type='button' value='<?php echo _("cancel");?>' onclick="tb_remove()" class='cancel-button'></td>
				</tr>
			</table>
		</td>
		</tr>

		</table>

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
				<table class="thickboxTable" style="width:98%;background-image:url('images/title.gif');background-repeat:no-repeat;">
					<tr>
						<td colspan='2'>
							<span id='headerText' class='headerText'>Issue Resolution</span><br />
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<label for="resolutionText">Resolution:</label>
							<textarea id="resolutionText" name="resolutionText"></textarea>
						</td>
					</tr>
				</table>
				<table class='noBorderTable' style='width:125px;'>
					<tr>
						<td style='width:60px'><input type="button" value="submit" name="submitCloseResourceIssue" id="submitCloseResourceIssue" class='submit-button'></td>
						<td><input type='button' value='cancel' onclick="tb_remove();" class='cancel-button'></td>
					</tr>
				</table>

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
	<table class="thickboxTable">
		<tr>
			<td colspan="2">
				<h1> <?php echo _("Report New Problem");?></h1>
				<span class='error smallDarkRedText'><?php echo _("* required fields");?></span>
			</td>
		</tr>
		<tr>
			<td><label><?php echo _("Organization:");?>&nbsp;&nbsp;<span class='bigDarkRedText'>*</span></label></td>
			<td>
				<p><?php echo $organization->name; ?></p>
				<span id='error span_error_organizationId' class='smallDarkRedText'></span>
			</td>
		</tr>
		<tr>
			<td><label><?php echo _("Contact:");?>&nbsp;&nbsp;<span class='bigDarkRedText'>*</span></label></td>
			<td>
				<select multiple style="min-height: 60px;" type='text' id='contactIDs' name='contactIDs[]'>
<?php 

		foreach ($organizationContactsArray as $contact) {
			echo "		<option value=\"{$contact->contactID}\">{$contact->name}</option>";
		}

?>
				</select>
				<span id='span_error_contactName' class='error smallDarkRedText'></span>
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<a id="getCreateContactForm" href="#"><?php echo _("add contact");?></a>
				<div id="inlineContact"></div>
			</td>
		</tr>
		<tr>
			<td><label><?php echo _("CC myself:");?></label></td>
			<td>
				<input type='checkbox' id='ccCreator' name='ccCreator' class='changeInput' />
				<span id='span_error_ccCreator' class='error smallDarkRedText'></span>
			</td>
		</tr>
		<tr>
			<td><label><?php echo _("CC:");?></label></td>
			<td>
				<input type="text" id="inputEmail" name="inputEmail" />
				<input type="button" id="addEmail" name="addEmail" value="<?php echo _('Add');?>" />
				<p>
					<?php echo _("Current CCs: ");?><span id="currentEmails"></span>
				</p>
				<input type="hidden" id='ccEmails' name='ccEmails' value='' class='changeInput' />
				<span id='span_error_contactIDs' class='error smallDarkRedText'></span>
			</td>
		</tr>
		<tr>
			<td><label><?php echo _("Subject:");?>&nbsp;&nbsp;<span class='bigDarkRedText'>*</span></label></td>
			<td>
				<input type='text' id='subjectText' name='issue[subjectText]' value='' class='changeInput' />
				<span id='span_error_subjectText' class='error smallDarkRedText'></span>
			</td>
		</tr>
		<tr>
			<td><label><?php echo _("Body:");?>&nbsp;&nbsp;<span class='bigDarkRedText'>*</span></label></td>
			<td>
				<textarea id='bodyText' name='issue[bodyText]' value='' />
				<span id='span_error_bodyText' class='error smallDarkRedText'></span>
			</td>
		</tr>
		<tr>
			<td><label><?php echo _("Applies to:");?>&nbsp;&nbsp;<span class='bigDarkRedText'>*</span></label></td>
			<td>
				<div>
					<input type="checkbox" class="issueResources" id="organizationID" name="organizationID" value="<?php echo $organization->organizationID;?>" /> <label for="allResources"><?php echo _("Applies to all ");?><?php echo $organization->name; ?> <?php echo _("resources");?></label>
				</div>
				<div>
					<input type="checkbox" class="issueResources" id="otherResources" /><label for="otherResources"> <?php echo _("Applies to selected ");?><?php echo $organization->name; ?> <?php echo _("resources");?></label>
				</div>
				<select multiple id="resourceIDs" name="resourceIDs[]">
<?php
		if (!empty($organizationResourcesArray)) {
			foreach ($organizationResourcesArray as $resource) {
				echo "		<option value=\"{$resource['resourceID']}\">{$resource['titleText']}</option>";
			}
		}
?>
				</select>
				<span id='span_error_entities' class='error smallDarkRedText'></span>
			</td>
		</tr>
	</table>

	<p> <?php echo _("Send me a reminder every");?> 
		<select name="issue[reminderInterval]">
			<?php for ($i = 1; $i <= 31; $i++) echo "<option".(($i==7) ? ' selected':'').">{$i}</option>"; ?>
		</select> <?php echo _("day(s)");?> 
	</p>

	<table class='noBorderTable' style='width:125px;'>
		<tr>
			<td style='width:60px;padding:0;'><input type='button' value='<?php echo _("submit");?>' name='submitNewResourceIssue' id='submitNewResourceIssue' class='submit-button'></td>
			<td style='padding:0;'><input type='button' value='<?php echo _("cancel");?>' onclick="tb_remove();" class='cancel-button'></td>
		</tr>
	</table>

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
	<table class="thickboxTable" style="width:98%;background-image:url('images/title.gif');background-repeat:no-repeat;">
		<tr>
			<td colspan="2">
				<h1>Resolve Downtime</h1>
			</td>
		</tr>
		<tr>
			<td>
				<label>Downtime Resolution:</label>
			</td>
			<td>
				<div>
					<div><i><?php echo _("Date");?></i></div>
					<input class="date-pick" type="text" name="endDate" id="endDate" />
					<span id='span_error_endDate' class='smallDarkRedText updateDowntimeError'></span>
				</div>
				<div style="clear:both;">
					<div><i><?php echo _("Time");?></i></div>
<?php
echo buildTimeForm("endTime");
?>
					<span id='span_error_endDate' class='smallDarkRedText updateDowntimeError'></span>
				</div>
			</td>
		</tr>
		<tr>
			<td><label>Note:</label></td>
			<td>
				<textarea name="note"><?php echo $downtime->note;?></textarea>
			</td>
		</tr>
	</table>
	<table class='noBorderTable' style='width:125px;'>
		<tr>
			<td style='text-align:left'><input type='button' value='submit' name='submitUpdatedDowntime' id='submitUpdatedDowntime'></td>
			<td style='text-align:right'><input type='button' value='cancel' onclick="tb_remove();"></td>
		</tr>
	</table>
</form>
<?php
			} else {
?>
		<div>
			Unable to retrieve Downtime.
		</div>
		<table class='noBorderTable' style='width:125px;'>
			<tr>
				<td style='text-align:right'><input type='button' value='cancel' onclick="tb_remove();"></td>
			</tr>
		</table>
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

<form id='newDowntimeForm'>
	<input type="hidden" name="sourceOrganizationID" value="<?php echo $organization->organizationID;?>" />
	<table class="thickboxTable" style="width:98%;background-image:url('images/title.gif');background-repeat:no-repeat;">
		<tr>
			<td colspan="2">
				<h1> <?php echo _("Resource Downtime Report");?></h1>
			</td>
		</tr>
		<tr>
			<td><label><?php echo _("Downtime Start:");?></label></td>
			<td>
				<div>
					<div><i><?php echo _("Date");?></i></div>
					<input class="date-pick" type="text" name="startDate" id="startDate" />
					<span id='span_error_startDate' class='smallDarkRedText addDowntimeError'></span>
				</div>
				<div style="clear:both;">
					<div><i><?php echo _("Time");?></i></div>
<?php
echo buildTimeForm("startTime");
?>
					<span id='span_error_startDate' class='smallDarkRedText addDowntimeError'></span>
				</div>
			</td>
		</tr>
		<tr>
			<td><label><?php echo _("Downtime Resolution:");?></label></td>
			<td>
				<div>
					<div><i><?php echo _("Date");?></i></div>
					<input class="date-pick" type="text" name="endDate" id="endDate" />
					<span id='span_error_endDate' class='smallDarkRedText addDowntimeError'></span>
				</div>
				<div style="clear:both;">
					<div><i><?php echo _("Time");?></i></div>
<?php
echo buildTimeForm("endTime");
?>
					<span id='span_error_endDate' class='smallDarkRedText addDowntimeError'></span>
				</div>
			</td>
		</tr>
		<tr>
			<td><label><?php echo _("Problem Type:");?></label></td>
			<td>
				<select class="downtimeType" name="downtimeType">
<?php
			foreach ($downtimeTypeNames as $downtimeType) {
				echo "<option value=".$downtimeType["downtimeTypeID"].">".$downtimeType["shortName"]."</option>";
			}
?>
				</select>
			</td>
		</tr>
<?php
if ($issues) {
?>
		<tr>
			<td><label><?php echo _("Link to open issue:");?></label></td>
			<td>
				<select class="issueID" name="issueID">
					<option value="">none</option>
<?php
			foreach ($issues as $issue) {
				echo "<option".(($issueID == $issue->issueID) ? ' selected':'')." value=".$issue->issueID.">".$issue->subjectText."</option>";
			}
?>
				</select>
			</td>
		</tr>
<?php
}
?>
		<tr>
			<td><label><?php echo _("Note:");?></label></td>
			<td>
				<textarea name="note"></textarea>
			</td>
		</tr>
	</table>

	<table class='noBorderTable' style='width:125px;'>
		<tr>
			<td style='width:60px;padding:0;'><input type='button' value='<?php echo _("submit");?>' name='submitNewDowntime' id='submitNewDowntime' class='submit-button'></td>
			<td style='padding:0;'><input type='button' value='<?php echo _("cancel");?>' onclick="tb_remove();" class='cancel-button'></td>
		</tr>
	</table>

</form>

<?php

	break;
    case 'getIssueLogForm':
    	$organizationID = $_GET['organizationID'];
    	if (isset($_GET['issueLogID'])) $issueLogID = $_GET['issueLogID']; else $issueLogID = '';
    	$issueLog = new IssueLog(new NamedArguments(array('primaryKey' => $issueLogID)));

		if (($issueLog->issueStartDate != '') && ($issueLog->issueStartDate != "0000-00-00")) {
			$issueStartDate=format_date($issueLog->issueStartDate);
		}else{
			$issueStartDate='';
		}

    if (($issueLog->issueEndDate != '') && ($issueLog->issueEndDate != "0000-00-00")) {
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

		<table class="thickboxTable" style="background-image:url('images/title.gif');background-repeat:no-repeat;width:260px;">
		<tr>
		<td colspan='2'><span class='headerText'><?php if ($issueLogID){ echo _("Update Issue"); } else { echo _("Add Issue"); } ?></span>
		<span id='span_errors' style='color:red;'></span><br />
		</td>
		</tr>
    <tr>

		<td style='vertical-align:top;text-align:right;'><label for='issueLogTypeID'><b><?php echo _("Type:");?></b></label></td>
    <td>
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
    </td>
    </tr>

		<tr>
		<td style='vertical-align:top;text-align:right;'><label for='issueStartDate'><b><?php echo _("Start date:");?></b></label></td>
		<td>
		<input class='date-pick' id='issueStartDate' name='issueStartDate' style='width:80px' value='<?php echo $issueStartDate; ?>' />
		</td>
		</tr>

    <tr>
		<td style='vertical-align:top;text-align:right;'><label for='issueDate'><b><?php echo _("End date:");?></b></label></td>
		<td>
		<input class='date-pick' id='issueEndDate' name='issueEndDate' style='width:80px' value='<?php echo $issueEndDate; ?>' />
		</td>
		</tr>


		<tr>
		<td style='vertical-align:top;text-align:right;'><label for='noteText'><b><?php echo _("Notes:");?></b></label></td>
		<td><textarea rows='3' id='noteText' name='noteText' style='width:200px'><?php echo $issueLog->noteText; ?></textarea></td>
		</tr>

		<tr style="vertical-align:middle;">
		<td style="padding-left:8px;padding-top:8px;">&nbsp;</td>
		<td style="padding-top:8px;padding-right:8px;">
			<table class='noBorderTable' style='width:100%;'>
				<tr>
					<td style='width:60px'><input type='button' value='<?php echo _("submit");?>' name='submitIssueLogForm' id ='submitIssueLogForm' class='submit-button'></td>
					<td><input type='button' value='<?php echo _("cancel");?>' onclick="tb_remove()" class='cancel-button'></td>
				</tr>
			</table>
		</td>
		</tr>

		</table>

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



		if (count($instanceArray) > 0){
			?>
			<table class='dataTable' style='width:350px'>
				<?php

				foreach($instanceArray as $instance) {
					echo "<tr>";
					echo "<td>" . $instance['shortName'] . "</td>";
					echo "<td style='width:30px'><a href='ajax_forms.php?action=getAdminUpdateForm&className=" . $className . "&updateId=" . $instance[lcfirst($className) . 'ID'] . "&height=130&width=250&modal=true' class='thickbox' id='expression'>"._("edit")."</a></td>";
					echo "<td style='width:50px'><a href='javascript:deleteData(\"" . $className . "\",\"" . $instance[lcfirst($className) . 'ID'] . "\")'>"._("remove")."</a></td>";
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
		<div id='div_updateForm'>
		<table class="thickboxTable" style="background-image:url('images/title.gif');background-repeat:no-repeat;width:200px;">
		<tr>
		<td colspan='3'><span class='headerText'><?php echo _("Update");?></span><br /><span id='span_errors' style='color:#F00;'></span><br /></td>
		</tr>
		<tr>

		</tr>
		<tr>
		
		<td>
		<?php

		echo "<input type='text' id='updateVal' name='updateVal' value='" . $instance->shortName . "' style='width:190px;'/></td><td><a href='javascript:updateData(\"" . $className . "\", \"" . $updateId . "\");' id='updateButton' class='submit-button'>"._("update")."</a>";

		?>


		</td>
		<td colspan='2'><p><a href='#' onclick='window.parent.tb_remove(); return false' id='closeButton' class='cancel-button'><?php echo _("close");?></a></td>
		</tr>
		</table>
		</div>


		<script type="text/javascript">
		   //attach enter key event to new input and call add data when hit
		   $('#updateVal').keyup(function(e) {

				   if(e.keyCode == 13) {
					   updateData("<?php echo $className; ?>", "<?php echo $updateId; ?>");
				   }
        	});

        </script>


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



		if (count($instanceArray) > 0){
			?>
			<table class='dataTable' style='width:550px'>
				<tr>
				<th align='left'><?php echo _("Login ID");?></th>
				<th align='left'><?php echo _("First Name");?></th>
				<th align='left'><?php echo _("Last Name");?></th>
				<th align='left'><?php echo _("Privilege");?></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				</tr>
				<?php

				foreach($instanceArray as $instance) {
					echo "<tr>";
					echo "<td>" . $instance['loginID'] . "</td>";
					echo "<td>" . $instance['firstName'] . "</td>";
					echo "<td>" . $instance['lastName'] . "</td>";
					echo "<td>" . $instance['priv'] . "</td>";
					echo "<td style='width:30px'><a href='ajax_forms.php?action=getAdminUserUpdateForm&loginID=" . $instance['loginID'] . "&height=185&width=250&modal=true' class='thickbox' id='expression'>"._("update")."</a></td>";
					echo "<td style='width:50px'><a href='javascript:deleteUser(\"" . $instance['loginID'] . "\")'>"._("remove")."</a></td>";
					echo "</tr>";
				}

				?>
			</table>
			<?php

		}else{
			echo _("(none found)");
		}

		break;



	case 'getAdminUserUpdateForm':
		if (isset($_GET['loginID'])) $loginID = $_GET['loginID']; else $loginID = '';

		if ($loginID){
			$update = _('Update');
		}else{
			$update = _('Add New');
		}
		$user = new User(new NamedArguments(array('primaryKey' => $loginID)));

		//get all roles for output in drop down
		$privilegeArray = array();
		$privilegeObj = new Privilege();
		$privilegeArray = $privilegeObj->allAsArray();



		?>
		<div id='div_updateForm'>
		<table class="thickboxTable" style="background-image:url('images/title.gif');background-repeat:no-repeat;width:240px;padding:2px;">
		<tr><td colspan='2'><span class='headerText'><?php echo $update._("User"); ?></span><br /><br /></td></tr>
            <tr><td><label for='loginID'><b><?php echo _("Login ID");?></b></label></td><td><?php if (!$loginID) { ?><input type='text' id='loginID' name='loginID' value='<?php echo $loginID; ?>' style='width:150px;'/> <?php } else { echo $loginID; } ?></td></tr>
            <tr><td><label for='firstName'><b><?php echo _("First Name");?></b></label></td><td><input type='text' id='firstName' name='firstName' value="<?php echo $user->firstName; ?>" style='width:150px;'/></td></tr>
            <tr><td><label for='lastName'><b><?php echo _("Last Name");?></b></label></td><td><input type='text' id='lastName' name='lastName' value="<?php echo $user->lastName; ?>" style='width:150px;'/></td></tr>
            <tr><td><label for='privilegeID'><b><?php echo _("Privilege");?></b></label></td>
		<td>
		<select name='privilegeID' id='privilegeID' style='width:155px'>
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
		</td>
		</tr>



		<tr>
		<td style='width:60px'><input type='button' value='<?php echo $update; ?>' onclick='javascript:window.parent.submitUserData("<?php echo $loginID; ?>");' class='submit-button' /></td>
		<td><input type='button' value='<?php echo _("cancel");?>' onclick="window.parent.tb_remove(); return false" class='cancel-button' /></td>
		</tr>
		</table>
		</div>


		<?php

		break;





	default:
       echo _("Action ") . $action . _(" not set up!");
       break;


}


?>


