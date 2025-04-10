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


session_start();

include_once 'directory.php';


$organizationID = $_GET['organizationID'];
$organization = new Organization(new NamedArguments(array('primaryKey' => $organizationID)));
$pageTitle=$organization->name;

//as long as organization is valid...
if ($organization->name){

	//if the licensing module is installed display licensing info
	$config = new Configuration;

	$showLicensing='N';
	if ($config->settings->licensingModule == 'Y'){
		$showLicensing = 'Y';
		$numLicenses = count($organization->getLicenses());
	}

	include_once('templates/header.php');
	?>
<main id="main-content">
	<article class='printContent'>
	
	
		<?php


		//fix company url in case http is missing
		if ($organization->companyURL){
			if (strpos($organization->companyURL, '://') === false){
				$companyURL = "http://" . $organization->companyURL;
			}else{
				$companyURL = $organization->companyURL;
			}

		}

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


		//get children organizations
		$sanitizedInstance = array();
		$instance = new Organization();
		$childOrganizationArray = array();
		foreach ($organization->getChildOrganizations() as $instance) {
			foreach (array_keys($instance->attributeNames) as $attributeName) {
				$sanitizedInstance[$attributeName] = $instance->$attributeName;
			}

			$sanitizedInstance[$instance->primaryKeyName] = $instance->primaryKey;

			array_push($childOrganizationArray, $sanitizedInstance);
		}


		//get roles
		$sanitizedInstance = array();
		$instance = new OrganizationRole();
		$organizationRoleArray = array();
		foreach ($organization->getOrganizationRoles() as $instance) {
			$organizationRoleArray[]=$instance->shortName;
		}

		?>
		<table class='table-border'>
		<tr>
		<th colspan='2' scope="col">
			<?php echo $organization->name; ?>
		</th>
		</tr>

		<?php if (is_array($parentOrganizationArray) && count($parentOrganizationArray) > 0) { ?>
			<tr>
			<th scope="row"><?php echo _("Parent Organization:");?></th>
			<td>
			<?php
			foreach ($parentOrganizationArray as $parentOrganization){
				echo $parentOrganization['name'] . "&nbsp;&nbsp;";
				echo "<a href='orgDetail.php?organizationID=" . $parentOrganization['organizationID'] . "'><img src='images/arrow-up-right.gif' alt='"._("view organization")."' title='"._("View")."' style='vertical-align:top;'></a><br />";
			}
			?>
			</td>
			</tr>
		<?php
		}


		if (is_array($childOrganizationArray) && count($childOrganizationArray) > 0) { ?>
			<tr>
			<th scope="row"><?php echo _("Child Organizations:");?></th>
			<td>
			<?php
			foreach ($childOrganizationArray as $childOrganization){
				echo $childOrganization['name'] . "&nbsp;&nbsp;";
				echo "<a href='orgDetail.php?organizationID=" . $childOrganization['organizationID'] . "'><img src='images/arrow-up-right.gif' alt='"._("view organization")."' title='"._("View")."' style='vertical-align:top;'></a><br />";
			}
			?>
			</td>
			</tr>
		<?php
		}


		if ($organization->companyURL){ ?>
			<tr>
			<th scope="row"><?php echo _("Company URL:");?></th>
			<td class="url"><a href='<?php echo $companyURL; ?>' <?php echo getTarget(); ?>><?php echo $organization->companyURL; ?></a></td>
			</tr>
		<?php
		}

		if (is_array($organizationRoleArray) && count($organizationRoleArray) > 0) { ?>
			<tr>
			<th scope="row"><?php echo _("Role(s):");?></th>
			<td><?php echo implode(", ", $organizationRoleArray); ?></td>
			</tr>
		<?php
		}

		if ($organization->accountDetailText){ ?>
			<tr>
			<th scope="row"><?php echo _("Account Details:");?></th>
			<td><?php echo nl2br($organization->accountDetailText); ?></td>
			</tr>
		<?php
		}

		if ($organization->noteText){ ?>
			<tr>
			<th scope="row"><?php echo _("Notes:");?></th>
			<td><?php echo nl2br($organization->noteText); ?></td>
			</tr>
		<?php
		}

		?>
		</table>

<p>
		<?php

	   	

		$createUser = new User(new NamedArguments(array('primaryKey' => $organization->createLoginID)));
		$updateUser = new User(new NamedArguments(array('primaryKey' => $organization->updateLoginID)));

		echo "<i>".sprintf(_("Created: %s"), format_date($organization->createDate));
		//since organizations can be created by other modules the user may or may not be set and may or may not have a user entry in this db
		if ($createUser->primaryKey){
			echo _(" by ");
			if ($createUser->firstName){
				echo $createUser->firstName . " " . $createUser->lastName;
			}else{
				echo $createUser->primaryKey;
			}
		}

		?>

		</i>
	</p>

<p>
		<?php
		if (($organization->updateDate) && ($organization->updateDate != '0000-00-00')){
			echo "<i>"._("Last Update: ") . format_date($organization->updateDate)._(" by "); ?><?php echo $updateUser->firstName . " " . $updateUser->lastName . "</i>";
		}



		//get aliases
		$sanitizedInstance = array();
		$instance = new Alias();
		$aliasArray = array();
		foreach ($organization->getAliases() as $instance) {
			foreach (array_keys($instance->attributeNames) as $attributeName) {
				$sanitizedInstance[$attributeName] = $instance->$attributeName;
			}

			$sanitizedInstance[$instance->primaryKeyName] = $instance->primaryKey;

			$aliasType = new AliasType(new NamedArguments(array('primaryKey' => $instance->aliasTypeID)));
			$sanitizedInstance['aliasTypeShortName'] = $aliasType->shortName;

			array_push($aliasArray, $sanitizedInstance);
		}


		?>
		</p>

		<table class='table-border'>
		<tr>
		<th colspan="2" scope="col"><?php echo _("Aliases");?></th>
		</tr>

		<?php
		foreach ($aliasArray as $organizationAlias){
			echo "<tr>\n";
			echo "<th scope='row'>" . $organizationAlias['name'] . "</th>\n";
			echo "<td>" . $organizationAlias['aliasTypeShortName'] . "</td>\n";
			echo "</tr>\n";
		}

		if (count($aliasArray) < 1){
			echo "<tr><td colspan='2'><i>"._("No aliases defined")."</i></td></tr>";
		}

		?>

		</table>

<p>
		<?php

    	if (isset($_GET['archiveInd'])) $archiveInd = $_GET['archiveInd']; else $archiveInd='';
    	if (isset($_GET['showArchivesInd'])) $showArchivesInd = $_GET['showArchivesInd']; else $showArchivesInd='';


 		//get contacts
 		$sanitizedInstance = array();
 		$contactArray = array();
 		$contactObjArray = array();
 		if ((isset($archiveInd)) && ($archiveInd == "1")){
 			//if we want archives to be displayed
 			if ($showArchivesInd == "1"){
 				if (is_array($organization->getArchivedContacts()) && count($organization->getArchivedContacts()) > 0) {
 					echo "<i><b>"._("The following are archived contacts:")."</b></i>";
 				}
 				$contactObjArray = $organization->getArchivedContacts();
 			}
 		}else{
 			$contactObjArray = $organization->getUnarchivedContacts();
 		}


 		foreach ($contactObjArray as $contact) {
 			foreach (array_keys($contact->attributeNames) as $attributeName) {
 				$sanitizedInstance[$attributeName] = $contact->$attributeName;
 			}

 			$sanitizedInstance[$contact->primaryKeyName] = $contact->primaryKey;

			//get all of this contacts roles
			$contactRoleObj = new ContactRole();
			$contactRoleArray = array();
			foreach ($contact->getContactRoles() as $contactRoleObj) {
				$contactRoleArray[]=$contactRoleObj->shortName;
			}

 			$sanitizedInstance['contactRoles'] = implode("<br />", $contactRoleArray);

 			array_push($contactArray, $sanitizedInstance);
		}

		?>
	</p>
		<table class='table-border' >
		<tr>
		<th colspan="2" scope="col"><?php echo _("Contacts");?></th>
		</tr>

		<?php
		foreach ($contactArray as $contact){
		?>
			
			<tr>
			<th scope="row"><?php echo $contact['contactRoles']; ?></th>
			<td>
			<?php

			if ($contact['name']){
				echo $contact['name'];
			}

			?>
			</td>
			</tr>

			<?php if (($contact['archiveDate'] != '0000-00-00') && ($contact['archiveDate'])) { ?>
			<tr class="archived">
			<th scope="row"><?php echo _("No longer valid:");?></th>
			<td><i><?php echo format_date($contact['archiveDate']); ?></i></td>
			</tr>
			<?php
			}

			if ($contact['title']) { ?>
			<tr>
			<th scope="row"><?php echo _("Title:");?></th>
			<td><?php echo $contact['title']; ?></td>
			</tr>
			<?php
			}

			if ($contact['addressText']) { ?>
			<tr>
			<th scope="row"><?php echo _("Address:");?></th>
			<td><?php echo nl2br($contact['addressText']); ?></td>
			</tr>
			<?php
			}

			if ($contact['phoneNumber']) { ?>
			<tr>
			<th scope="row"><?php echo _("Phone:");?></th>
			<td><?php echo $contact['phoneNumber']; ?></td>
			</tr>
			<?php
			}

			if ($contact['altPhoneNumber']) { ?>
			<tr>
			<th scope="row"><?php echo _("Alt Phone:");?></th>
			<td><?php echo $contact['altPhoneNumber']; ?></td>
			</tr>
			<?php
			}

			if ($contact['faxNumber']) { ?>
			<tr>
			<th scope="row"><?php echo _("Fax:");?></th>
			<td><?php echo $contact['faxNumber']; ?></td>
			</tr>
			<?php
			}

			if ($contact['emailAddress']) { ?>
			<tr>
			<th scope="row"><?php echo _("Email:");?></th>
			<td class="url"><a href='mailto:<?php echo $contact['emailAddress']; ?>'><?php echo $contact['emailAddress']; ?></a></td>
			</tr>
			<?php
			}

			if ($contact['noteText']) { ?>
			<tr>
			<th scope="row"><?php echo _("Notes:");?></th>
			<td><?php echo nl2br($contact['noteText']); ?></td>
			</tr>
			<?php
			}

			if ($contact['lastUpdateDate']) { ?>
			<tr>
			<th scope="row"><?php echo _("Last Updated:");?></th>
			<td><i><?php echo format_date($contact['lastUpdateDate']); ?></i></td>
			</tr>
			<?php
			}
			?>

		<?php
		}
		if (count($contactArray) < 1){
			if (($archiveInd != 1) && ($showArchivesInd != 1)){
				echo "<tr><td colspan='2'><i>"._("No unarchived contacts")."</i></td></tr>";
			}
		}
		echo "</table>";


 		//get external logins
 		$sanitizedInstance = array();
 		$externalLoginArray = array();
 		foreach ($organization->getExternalLogins() as $instance) {
 			foreach (array_keys($instance->attributeNames) as $attributeName) {
 				$sanitizedInstance[$attributeName] = $instance->$attributeName;
 			}

 			$sanitizedInstance[$instance->primaryKeyName] = $instance->primaryKey;

 			$externalLoginType = new ExternalLoginType(new NamedArguments(array('primaryKey' => $instance->externalLoginTypeID)));
 			$sanitizedInstance['externalLoginTypeShortName'] = $externalLoginType->shortName;

 			array_push($externalLoginArray, $sanitizedInstance);
		}

		?>

		<table class='table-border'>
		<tr>
			<th colspan="3"><?php echo _("External Logins");?></th>
		</tr>

		<?php
			foreach ($externalLoginArray as $externalLogin){
			?>
			
			<tr>
			<th scope="row" colspan="2"><?php echo $externalLogin['externalLoginTypeShortName']; ?></th>
			</tr>

			<?php if ($externalLogin['loginURL']) { ?>
			<tr>
			<th scope="row"><?php echo _("Login URL:");?></th>
			<td class="url"><?php echo $externalLogin['loginURL'];
				if (strpos($externalLogin['loginURL'], '://') === false) {
					$externalLogin['loginURL'] = "http://" . $externalLogin['loginURL'];
				}
			?> 
				<!-- TODO: replace image -->
				<a href='<?php echo $externalLogin['loginURL']; ?>' <?php echo getTarget(); ?>><img src='images/arrow-up-right.gif' alt='<?php echo _("Visit Login URL");?>' title='<?php echo _("Visit Login URL");?>' style='vertical-align:top;'></a></td>
			</tr>
			<?php
			}

			if ($externalLogin['emailAddress']) { ?>
			<tr>
			<th scope="row"><?php echo _("Local email on account:");?></th>
			<td><?php echo $externalLogin['emailAddress']; ?></td>
			</tr>
			<?php
			}

			if ($externalLogin['username']) { ?>
			<tr>
			<th scope="row"><?php echo _("User Name:");?></th>
			<td><?php echo $externalLogin['username']; ?></td>
			</tr>
			<?php
			}

			if ($externalLogin['password']) { ?>
			<tr>
			<th scope="row"><?php echo _("Password:");?></th>
			<td><?php echo $externalLogin['password']; ?></td>
			</tr>
			<?php
			}

			if ($externalLogin['updateDate']) { ?>
			<tr>
			<th scope="row"><?php echo _("Last Updated:");?></th>
			<td><i><?php echo format_date($externalLogin['updateDate']); ?></i></td>
			</tr>
			<?php
			}

			if ($externalLogin['noteText']) { ?>
			<tr>
			<th scope="row"><?php echo _("Notes:");?></th>
			<td><?php echo nl2br($externalLogin['noteText']); ?></td>
			</tr>
			<?php
			}
			?>

			<?php
			}
		if (count($externalLoginArray) < 1){
			echo "<tr><td colspan='2'><i>"._("No external logins added")."</i></td></tr>";
		}
		echo "</table>";

 		//get issues
 		$sanitizedInstance = array();
 		$issueLogArray = array();
 		foreach ($organization->getIssueLog() as $instance) {
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

 			array_push($issueLogArray, $sanitizedInstance);
		}

		$charsToRemove = array("*", "_");

		?>

		<table class='table-border'>
		<tr>
			<th colspan="3"><?php echo _("Issues");?></th>
		</tr>

		<?php

		if (is_array($issueLogArray) && count($issueLogArray) > 0) {
		?>
		
		<tr>
		<th><?php echo _("Date Added");?></th>
		<th><?php echo _("Issue Date");?></th>
		<th><?php echo _("Notes");?></th>
		</tr>

		<?php foreach ($issueLogArray as $issueLog){
			if (($issueLog['issueDate']) && ($issueLog['issueDate'] != "0000-00-00")) {
				$issueDate= format_date($issueLog['issueDate']);
			}else{
				$issueDate='';
			}
			?>
			<tr>
			<td><?php printf(_("%s<br> by <i>%s</i>"), format_date($issueLog['updateDate'], $issueLog['updateUser']));?></td>
			<td><?php echo $issueDate ?></td>
			<td><?php echo nl2br(str_replace($charsToRemove, "", $issueLog['noteText'])); ?></td>
			</tr>
		<?php } ?>

		<br />
		<?php
		} else {
			echo "<tr><td colspan='3'><i>"._("No issues reported")."</i></td></tr>";
		}

		echo "</table>";

		if ($showLicensing == "Y") {

			//get licenses
			$sanitizedInstance = array();
			$instance = new Alias();
			$licenseArray = array();

			try {
				$licenseArray = $organization->getLicenses();
				?>

				<br />
				<table class='table-border'>
				<tr>
					<th colspan="3"><?php echo _("Licenses");?></th>
				</tr>


				<?php				
				if (is_array($licenseArray) && count($licenseArray) > 0) { ?>
					<tr>
					<th>&nbsp;</th>
					<th><?php echo _("Consortium");?></th>
					<th><?php echo _("Status");?></th>
					</tr>

					<?php
					$licensingPath = $util->getLicensingURL();

					foreach ($licenseArray as $license){
						echo "<tr>\n";
						echo "<td>" . $license['licenseName'] . "</td>\n";
						echo "<td>" . $license['consortiumName'] . "</td>\n";
						echo "<td>" . $license['status'] . "</td>\n";
						echo "</tr>\n";
					}
					?>

					</table>
					<br />
				<?php
				} else {
					echo "<tr><td colspan='3'><i>"._("No licenses set up for this organization")."</i></td></tr>";
				}

				echo "</table";

			}catch(Exception $e){
				echo "<p class='error'>"._("Unable to access the licensing database.  Make sure the configuration.ini is pointing to the correct place and that the database and associated tables have been set up.")."</p>";
			}

		}
		?>

	<input type='hidden' name='organizationID' id='organizationID' value='<?php echo $organizationID; ?>'>

	</article>
</main>

	<?php
//end if organization valid
}else{
	echo _("invalid organization");
}

?>
</body>
</html>