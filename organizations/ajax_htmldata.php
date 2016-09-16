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

//shared html template for organization and resource issues
function generateIssueHTML($issue,$associatedEntities=null) {
	$html = "
	<div class=\"issue\">";
	if (!$issue->dateClosed) {
		$html .= "
		<a class=\"thickbox action closeResourceIssueBtn\" href=\"ajax_forms.php?action=getCloseResourceIssueForm&issueID={$issue->issueID}&height=120&width=345&modal=true\">close</a>
		<a class=\"thickbox action\" href=\"ajax_forms.php?action=getNewDowntimeForm&organizationID={$GLOBALS['organizationID']}&issueID={$issue->issueID}&height=200&width=390&modal=true\">downtime</a>";
	}
	$html .= "
	  	<dl>
	  		<dt>Date reported:</dt> 
	  		<dd>{$issue->dateCreated}</dd>";
	if ($issue->dateClosed) {
	  	
		$html .= "<dt>Date closed:</dt>
	  		<dd>{$issue->dateClosed}</dd>
	  		<dt>Resolution</dt>
	  		<dd>{$issue->resolutionText}</dd>";
	  	}
	  		
	$html .= "<dt>Contact(s):</dt> 
	  		<dd>";
	$contacts = $issue->getContacts();
	if ($contacts) {
		$html .= "<ul class=\"contactList\">";
		foreach($contacts as $contact) {
			$html .= "<li><a href=\"mailto:".urlencode($contact['emailAddress'])."?Subject=RE: {$issue->subjectText}\">{$contact['name']}</a></li>";
		}
		$html .= "</ul>";
	}


	$html .= "	</dd> 
	  		<dt>Applies to:</dt> 
	  		<dd>";
	if ($associatedEntities) {
		$temp ='';
		foreach ($associatedEntities as $entity) {
			$temp .= " {$entity['name']},";
		}
		$html .= rtrim($temp,',');
	}
	$html .= "</dd> 
	  		<dt>Subject:</dt> 
	  		<dd>{$issue->subjectText}</dd> 
	  		
	  		<dt class=\"block\">Body:</dt> 
	  		<dd>{$issue->bodyText}</dd>
	  	</dl>
	</div>";
	return $html;
}

//shared html template for organization and resource downtimes
function generateDowntimeHTML($downtime) {

	$html = "
	<div class=\"downtime\">";
	
	$html .= "
	  	<dl>
	  		<dt>Type:</dt> 
	  		<dd>{$downtime->shortName}</dd>

	  		<dt>Downtime Start:</dt> 
	  		<dd>{$downtime->startDate}</dd>

	  		<dt>Downtime Resolved:</dt> 
	  		<dd>";
	if ($downtime->endDate != null) {
		$html .= $downtime->endDate;
	} else {
		$html .= "<a class=\"thickbox\" href=\"ajax_forms.php?action=getResolveDowntimeForm&height=363&width=345&modal=true&downtimeID={$downtime->downtimeID}\">Resolve</a>";
	}
	$html .= "</dd>";

	if($downtime->subjectText) {
		$html .= "
	  		<dt>Linked issue:</dt> 
	  		<dd>{$downtime->subjectText}</dd>";
	}
	if ($downtime->note) {
		$html .= "
	  		<dt>Note:</dt> 
	  		<dd>{$downtime->note}</dd>";
	}
	$html .= "		
		</dl>
	</div>";	
	
	return $html;
}

switch ($_GET['action']) {

	case 'getOrganizationContacts':
    	$organizationID = $_GET['organizationID'];
    	$contactIDs = $_GET['contactIDs'];
    	
    	$organization = new Organization(new NamedArguments(array('primaryKey' => $organizationID)));
		$contactObjArray = $organization->getUnarchivedContacts();
		if (count($contactObjArray) > 0) {
			foreach ($contactObjArray as $contact) {
				$isSelected = (!empty($contactIDs) && in_array($contact->contactID, $contactIDs)) ? "selected" : "";
				echo "<option {$isSelected} value=\"{$contact->contactID}\">{$contact->name}</option>";	
			}
		}
	break;
    case 'getOrganizationDetails':
    	$organizationID = $_GET['organizationID'];
    	$organization = new Organization(new NamedArguments(array('primaryKey' => $organizationID)));

		$createUser = new User(new NamedArguments(array('primaryKey' => $organization->createLoginID)));
		$updateUser = new User(new NamedArguments(array('primaryKey' => $organization->updateLoginID)));

		//fix company url in case http is missing
		if ($organization->companyURL){
			if (strpos(strtolower($organization->companyURL), 'http') === false){
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
		<table class='linedFormTable' style='width:440px;'>
		<tr>
		<th colspan='2'>

			<span style='float:left; max-width:400px;'>&nbsp;<?php echo $organization->name; ?></span>
			<span style='float:right; vertical-align:top;'><?php if ($user->canEdit()){ ?><a href='ajax_forms.php?action=getOrganizationForm&height=363&width=345&modal=true&organizationID=<?php echo $organizationID; ?>' class='thickbox' id='editOrganization'><img src='images/edit.gif' alt='<?php echo _("edit organization");?>' title='<?php echo _("edit organization");?>'></a><?php } ?>  <?php if ($user->isAdmin){ ?><a href='javascript:removeOrganization(<?php echo $organizationID; ?>);'><img src='images/cross.gif' alt='<?php echo _("remove organization");?>' title='<?php echo _("remove organization");?>'></a><?php } ?></span>
		</th>
		</tr>

		<?php if (count($parentOrganizationArray) > 0){ ?>
			<tr>
			<td style='vertical-align:top;text-align:left;width:140px;'><?php echo _("Parent Organization:");?></td>
			<td style='width:320px;'>
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


		if (count($childOrganizationArray) > 0){ ?>
			<tr>
			<td style='vertical-align:top;text-align:left;width:140px;'><?php echo _("Child Organizations:");?></td>
			<td style='width:320px;'>
			<?php
			foreach ($childOrganizationArray as $childOrganization){
				echo $childOrganization['name'] . "&nbsp;&nbsp;";
				echo "<a href='orgDetail.php?organizationID=" . $childOrganization['organizationID'] . "'><img src='images/arrow-up-right.gif' alt='".("view organization")."' title='".("View")."' style='vertical-align:top;'></a><br />";
			}
			?>
			</td>
			</tr>
		<?php
		}


		if ($organization->companyURL){ ?>
			<tr>
			<td style='vertical-align:top;text-align:left;width:140px;'><?php echo _("Company URL:");?></td>
			<td style='width:320px;'><a href='<?php echo $companyURL; ?>' target='_blank'><?php echo $organization->companyURL; ?></a></td>
			</tr>
		<?php
		}

		if (count($organizationRoleArray) > 0){ ?>
			<tr>
			<td style='vertical-align:top;text-align:left;width:140px;'><?php echo _("Role(s):");?></td>
			<td style='width:320px;'><?php echo implode(", ", $organizationRoleArray); ?></td>
			</tr>
		<?php
		}

		if ($organization->accountDetailText){ ?>
			<tr>
			<td style='vertical-align:top;text-align:left;width:140px;'><?php echo _("Account Details:");?></td>
			<td style='width:320px;'><?php echo nl2br($organization->accountDetailText); ?></td>
			</tr>
		<?php
		}

		if ($organization->noteText){ ?>
			<tr>
			<td style='vertical-align:top;text-align:left;width:140px;'><?php echo _("Notes:");?></td>
			<td style='width:320px;'><?php echo nl2br($organization->noteText); ?></td>
			</tr>
		<?php
		}

		?>
		</table>


		<br />
		<i><?php echo _("Created:");?>
		<?php
			echo format_date($organization->createDate);
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
		<br />

		<?php
		if (($organization->updateDate) && ($organization->updateDate != '0000-00-00')){
			echo "<i>"._("Last Update:"). format_date($organization->updateDate)._(" by "); ?><?php echo $updateUser->firstName . " " . $updateUser->lastName . "</i>";
		}

        break;


    case 'getOrganizationName':
    	$organizationID = $_GET['organizationID'];
    	$organization = new Organization(new NamedArguments(array('primaryKey' => $organizationID)));

		echo $organization->name;
        break;



    case 'getAliasDetails':
    	$organizationID = $_GET['organizationID'];
    	$organization = new Organization(new NamedArguments(array('primaryKey' => $organizationID)));


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
		<?php if (count($aliasArray) > 0){ ?>
			<table class='linedFormTable' style='width:440px;'>
			<tr>
			<th><?php echo _("Alias");?></th>
			<th><?php echo _("Alias Type");?></th>
			</tr>

			<?php
			foreach ($aliasArray as $organizationAlias){
				echo "<tr>\n";
				echo "<td>" . $organizationAlias['name'] . "</td>\n";
				echo "<td>" . $organizationAlias['aliasTypeShortName'];
				if ($user->canEdit()){
					echo "<span style='float:right; vertical-align:top;'><a href='ajax_forms.php?action=getAliasForm&height=124&width=285&modal=true&organizationID=" .  $organizationID . "&aliasID=" . $organizationAlias['aliasID'] . "' class='thickbox'><img src='images/edit.gif' alt='"._("edit")."' title='"._("edit alias")."'></a>";
					echo "&nbsp;<a href='javascript:removeAlias(" . $organizationAlias['aliasID'] . ")'><img src='images/cross.gif' alt='"._("remove alias")."' title='"._("remove alias")."'></a>";
					echo "</span>";
				}
				echo "</td>\n</tr>\n";
			}
			?>

			</table>
			<br />
		<?php
		} else {
			echo "<i>"._("No aliases defined")."</i><br /><br />";
		}

		?>

		<?php if ($user->canEdit()){ ?>
			<a href='ajax_forms.php?action=getAliasForm&height=124&width=285&modal=true&organizationID=<?php echo $organizationID; ?>' class='thickbox' id='newAlias'><?php echo _("add a new alias");?></a>
		<?php } ?>

		<?php

        break;






    case 'getContactDetails':
    	$organizationID = $_GET['organizationID'];
    	if (isset($_GET['archiveInd'])) $archiveInd = $_GET['archiveInd']; else $archiveInd='';
    	if (isset($_GET['showArchivesInd'])) $showArchivesInd = $_GET['showArchivesInd']; else $showArchivesInd='';

    	$organization = new Organization(new NamedArguments(array('primaryKey' => $organizationID)));


 		//get contacts
 		$sanitizedInstance = array();
 		$contactArray = array();
 		$contactObjArray = array();
 		if ((isset($archiveInd)) && ($archiveInd == "1")){
 			//if we want archives to be displayed
 			if ($showArchivesInd == "1"){
 				if (count($organization->getArchivedContacts()) > 0){
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

		if (count($contactArray) > 0){
			foreach ($contactArray as $contact){
			?>
				<table class='linedFormTable' style='width:440px;'>
				<tr>
				<th style='width:150px;vertical-align:top;text-align:left'><?php echo $contact['contactRoles']; ?></th>
				<th>
				<?php

				if ($contact['name']){
					echo $contact['name'] . "&nbsp;&nbsp;";
				}

				if ($user->canEdit()){
					echo "<span style='float:right; vertical-align:top;'><a href='ajax_forms.php?action=getContactForm&height=463&width=345&modal=true&organizationID=" . $organizationID . "&contactID=" . $contact['contactID'] . "' class='thickbox'><img src='images/edit.gif' alt='"._("edit")."' title='"._("edit contact")."'></a>";
					echo "&nbsp;<a href='javascript:removeContact(" . $contact['contactID'] . ")'><img src='images/cross.gif' alt='"._("remove contact")."' title='"._("remove contact")."'></a>";
					echo "</span>";
				}

				?>
				</th>
				</tr>

				<?php if (($contact['archiveDate'] != '0000-00-00') && ($contact['archiveDate'])) { ?>
				<tr>
				<td style='vertical-align:top;text-align:left;background-color:#ebebeb'><?php echo _("No longer valid:");?></td>
				<td style='background-color:#ebebeb'><i><?php echo format_date($contact['archiveDate']); ?></i></td>
				</tr>
				<?php
				}

				if ($contact['title']) { ?>
				<tr>
				<td style='vertical-align:top;text-align:left;'><?php echo _("Title:");?></td>
				<td><?php echo $contact['title']; ?></td>
				</tr>
				<?php
				}

				if ($contact['addressText']) { ?>
				<tr>
				<td style='vertical-align:top;text-align:left;'><?php echo _("Address:");?></td>
				<td><?php echo nl2br($contact['addressText']); ?></td>
				</tr>
				<?php
				}

				if ($contact['phoneNumber']) { ?>
				<tr>
				<td style='vertical-align:top;text-align:left;'><?php echo _("Phone:");?></td>
				<td><?php echo $contact['phoneNumber']; ?></td>
				</tr>
				<?php
				}

				if ($contact['altPhoneNumber']) { ?>
				<tr>
				<td style='vertical-align:top;text-align:left;'><?php echo _("Alt Phone:");?></td>
				<td><?php echo $contact['altPhoneNumber']; ?></td>
				</tr>
				<?php
				}

				if ($contact['faxNumber']) { ?>
				<tr>
				<td style='vertical-align:top;text-align:left;'><?php echo _("Fax:");?></td>
				<td><?php echo $contact['faxNumber']; ?></td>
				</tr>
				<?php
				}

				if ($contact['emailAddress']) { ?>
				<tr>
				<td style='vertical-align:top;text-align:left;'><?php echo _("Email:");?></td>
				<td><a href='mailto:<?php echo $contact['emailAddress']; ?>'><?php echo $contact['emailAddress']; ?></a></td>
				</tr>
				<?php
				}

				if ($contact['noteText']) { ?>
				<tr>
				<td style='vertical-align:top;text-align:left;'><?php echo _("Notes:");?></td>
				<td><?php echo nl2br($contact['noteText']); ?></td>
				</tr>
				<?php
				}

				if ($contact['lastUpdateDate']) { ?>
				<tr>
				<td style='vertical-align:top;text-align:left;'><?php echo _("Last Updated:");?></td>
				<td><i><?php echo format_date($contact['lastUpdateDate']); ?></i></td>
				</tr>
				<?php
				}
				?>

				</table>
				<br />
			<?php
			}
		} else {
			if (($archiveInd != 1) && ($showArchivesInd != 1)){
				echo "<i>"._("No unarchived contacts")."</i><br /><br />";
			}
		}

		if (($showArchivesInd == "0") && ($archiveInd == "1") && (count($organization->getArchivedContacts()) > 0)){
			echo "<i>" . count($organization->getArchivedContacts()) . _(" archived contact(s) available.  ")."<a href='javascript:updateArchivedContacts(1);'>"._("show archived contacts")."</a></i><br />";
		}

		if (($showArchivesInd == "1") && ($archiveInd == "1") && (count($organization->getArchivedContacts()) > 0)){
			echo "<i><a href='javascript:updateArchivedContacts(0);'>"._("hide archived contacts")."</a></i><br />";
		}

        break;




    case 'getAccountDetails':
    	$organizationID = $_GET['organizationID'];
    	$organization = new Organization(new NamedArguments(array('primaryKey' => $organizationID)));


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

		if (count($externalLoginArray) > 0){
			foreach ($externalLoginArray as $externalLogin){
			?>
				<table class='linedFormTable' style='width:440px;max-width:440px;'>
				<tr>
				<th style='width:150px;vertical-align:top;text-align:left;'><?php echo $externalLogin['externalLoginTypeShortName']; ?></th>
				<th>
				<?php
					if ($user->canEdit()){
						echo "<span style='float:right; vertical-align:top;'><a href='ajax_forms.php?action=getAccountForm&height=254&width=342&modal=true&organizationID=" . $organizationID . "&externalLoginID=" . $externalLogin['externalLoginID'] . "' class='thickbox'><img src='images/edit.gif' alt='"._("edit")."' title='"._("edit external login")."'></a>";
						echo "&nbsp;<a href='javascript:removeExternalLogin(" . $externalLogin['externalLoginID'] . ")'><img src='images/cross.gif' alt='"._("remove external login")."' title='"._("remove external login")."'></a>";
						echo "</span>";
					}
				?>
				</th>
				</tr>

				<?php if ($externalLogin['loginURL']) { ?>
				<tr>
				<td style='vertical-align:top;text-align:left;'><?php echo _("Login URL:");?></td>
				<td><?php echo $externalLogin['loginURL']; 
					if (strpos($externalLogin['loginURL'], 'http') !== 0) {
						$externalLogin['loginURL'] = "http://" . $externalLogin['loginURL'];
					}
				?>&nbsp;&nbsp;<a href='<?php echo $externalLogin['loginURL']; ?>' target='_blank'><img src='images/arrow-up-right.gif' alt='<?php echo _("Visit Login URL");?>' title='<?php echo _("Visit Login URL");?>' style='vertical-align:top;'></a></td>
				</tr>
				<?php
				}

				if ($externalLogin['emailAddress']) { ?>
				<tr>
				<td style='vertical-align:top;text-align:left;'><?php echo _("Local email on account:");?></td>
				<td><?php echo $externalLogin['emailAddress']; ?></td>
				</tr>
				<?php
				}

				if ($externalLogin['username']) { ?>
				<tr>
				<td style='vertical-align:top;text-align:left;'><?php echo _("User Name:");?></td>
				<td><?php echo $externalLogin['username']; ?></td>
				</tr>
				<?php
				}

				if ($externalLogin['password']) { ?>
				<tr>
				<td style='vertical-align:top;text-align:left;'><?php echo _("Password:");?></td>
				<td><?php echo $externalLogin['password']; ?></td>
				</tr>
				<?php
				}

				if ($externalLogin['updateDate']) { ?>
				<tr>
				<td style='vertical-align:top;text-align:left;'><?php echo _("Last Updated:");?></td>
				<td><i><?php echo format_date($externalLogin['updateDate']); ?></i></td>
				</tr>
				<?php
				}

				if ($externalLogin['noteText']) { ?>
				<tr>
				<td style='vertical-align:top;text-align:left;'><?php echo _("Notes:");?></td>
				<td><?php echo nl2br($externalLogin['noteText']); ?></td>
				</tr>
				<?php
				}
				?>

				</table>
				<br />
				<br />
			<?php
			}
		} else {
			echo "<i>"._("No external logins added")."</i><br /><br />";
		}

		if ($user->canEdit()){
		?>
		<a href='ajax_forms.php?action=getAccountForm&height=254&width=342&modal=true&organizationID=<?php echo $organizationID; ?>' class='thickbox' id='newAlias'><?php echo _("add new external login");?></a><br />
		<?php
		}

        break;

	case 'getResourceIssueDetails':
    	$organizationID = $_GET['organizationID'];

		$getIssuesFormData = "action=getResourceIssuesList&organizationID=".$organizationID;
		$getDowntimeFormData = "action=getDowntimeList&organizationID=".$organizationID;
		$exportIssueUrl = "export_resourceissues.php?organizationID={$organizationID}";
		$exportDowntimeUrl = "export_downtimes.php?organizationID={$organizationID}";

?>
		<table class='linedFormTable issueTabTable'>
			<tr>
				<th><?php echo _("Issues/Problems");?></th>
			</tr>
			<tr>
				<td><a id="createIssueBtn" class="thickbox" href="ajax_forms.php?action=getNewIssueForm&organizationID=<?php echo $organizationID; ?>&modal=true"><?php echo _("report new issue");?></a></td>
			</tr>
			<tr>
				<td>
					<a href="<?php echo $getIssuesFormData; ?>" class="issuesBtn" id="openIssuesBtn"><?php echo _("view open issues");?></a> 
					<a target="_blank" href="<?php echo $exportIssueUrl;?>"><img src="images/xls.gif" /></a>
					<div class="issueList" id="openIssues" style="display:none;"></div>
				</td>
			</tr>
			<tr>
				<td>
					<a href="<?php echo $getIssuesFormData."&archived=1"; ?>" class="issuesBtn" id="archivedIssuesBtn"><?php echo _("view archived issues");?></a> 
					<a target="_blank" href="<?php echo $exportIssueUrl;?>&archived=1"><img src="images/xls.gif" /></a>
					<div class="issueList" id="archivedIssues"></div>
				</td>
			</tr>
		</table>

		<table id="downTimeTable" class='linedFormTable issueTabTable'>
			<tr>
				<th><?php echo _("Downtime");?></th>
			</tr>
			<tr>
				<td><a id="createDowntimeBtn" class="thickbox" href="ajax_forms.php?action=getNewDowntimeForm&organizationID=<?php echo $_GET['organizationID']; ?>&height=264&width=390&modal=true"><?php echo _("report new Downtime");?></a></td>
			</tr>
			<tr>
				<td>
					<a href="<?php echo $getDowntimeFormData; ?>" class="downtimeBtn" id="openDowntimeBtn"><?php echo _("view current/upcoming downtime");?></a> 
					<a target="_blank" href="<?php echo $exportDowntimeUrl;?>"><img src="images/xls.gif" /></a>
					<div class="downtimeList" id="currentDowntime" style="display:none;"></div>
				</td>
			</tr>
			<tr>
				<td>
					<a href="<?php echo $getDowntimeFormData."&archived=1"; ?>" class="downtimeBtn" id="archiveddowntimeBtn"><?php echo _("view archived downtime");?></a> 
					<a target="_blank" href="<?php echo $exportDowntimeUrl;?>&archived=1"><img src="images/xls.gif" /></a>
					<div class="downtimeList" id="archivedDowntime"></div>
				</td>
			</tr>
		</table>
<?php
	break;
	case 'getResourceIssuesList':
    	$organizationID = $_GET['organizationID'];
		$archivedFlag = (!empty($_GET['archived']) && $_GET['archived'] == 1) ? true:false;
		$organization = new Organization(new NamedArguments(array('primaryKey' => $organizationID)));
		$orgIssues = $organization->getIssues($archivedFlag);

		if(count($orgIssues) > 0) {
			foreach ($orgIssues as $issue) {
				echo generateIssueHTML($issue,array(array("name"=>$organization->name,"id"=>$organization->organizationID,"entityType"=>1)));
			}
		} else {
			echo "<br><p>" . _("There are no organization level issues.") . "</p><br>";
		}

	break;
	case 'getDowntimeList':
		$organizationID = $_GET['organizationID'];
		$archivedFlag = (!empty($_GET['archived']) && $_GET['archived'] == 1) ? true:false;
		$organization = new Organization(new NamedArguments(array('primaryKey' => $organizationID)));
		$orgDowntime = $organization->getDowntime($archivedFlag);

		if(count($orgDowntime) > 0) {
			foreach ($orgDowntime as $downtime) {
				echo generateDowntimeHTML($downtime);
			}
		} else {
			echo "<br><p>" . _("There are no organization level downtimes.") . "</p><br>";
		}
	break;
    case 'getIssueDetails':
    	$organizationID = $_GET['organizationID'];
    	$organization = new Organization(new NamedArguments(array('primaryKey' => $organizationID)));


 		//get external logins
 		$sanitizedInstance = array();
 		$issueLogArray = array();
 		foreach ($organization->getIssueLog() as $instance) {
 			foreach (array_keys($instance->attributeNames) as $attributeName) {
 				$sanitizedInstance[$attributeName] = $instance->$attributeName;
 			}

 			$sanitizedInstance['issueLogType'] = $instance->getTypeShortName();

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

		if (count($issueLogArray) > 0){
		?>
		<table class='linedFormTable' style='width:440px;'>
		<tr>
		<th><?php echo _("Added");?></th>
		<th><?php echo _("Date");?></th>
		<th><?php echo _("Type");?></th>
		<th><?php echo _("Notes");?></th>
		</tr>

		<?php foreach ($issueLogArray as $issueLog){
			if (($issueLog['issueStartDate'] != '') && ($issueLog['issueStartDate'] != "0000-00-00")) {
				$issueStartDate= format_date($issueLog['issueStartDate']);
			}else{
				$issueStartDate='';
			}
      if (($issueLog['issueEndDate'] != '') && ($issueLog['issueEndDate'] != "0000-00-00")) {
				$issueEndDate= format_date($issueLog['issueEndDate']);
			}else{
				$issueEndDate='';
			}

			?>
			<tr>
			<td style='width:80px;'><?php echo format_date($issueLog['updateDate']); ?><br /><?php echo _("by ");?><i><?php echo $issueLog['updateUser']; ?></i></td>
			<td><?php 
        if ($issueStartDate && $issueEndDate) {
          echo $issueStartDate._(" to ").$issueEndDate;
        } elseif ($issueStartDate) {
          echo _("start: ").$issueStartDate;
        } elseif ($issueEndDate) {
          echo _("end: ").$issueEndDate;
        }
      ?>
      </td>
      <td><?php echo $issueLog['issueLogType'] ?></td>
			<td style='width:360px;'><?php echo nl2br(str_replace($charsToRemove, "", $issueLog['noteText'])); ?>
			<?php 
			if ($user->canEdit()){
				echo "<span style='float:right; vertical-align:top;'><a href='ajax_forms.php?action=getIssueLogForm&height=250&width=265&modal=true&organizationID=" . $organizationID . "&issueLogID=" . $issueLog['issueLogID'] . "' class='thickbox'><img src='images/edit.gif' alt='"._("edit")."' title='"._("edit issue")."'></a>";
				echo "&nbsp;<a href='javascript:removeIssueLog(" . $issueLog['issueLogID'] . ")'><img src='images/cross.gif' alt='"._("remove issue")."' title='"._("remove issue")."'></a>";
				echo "</span>";
			} 
			?>
			</td></tr>
		<?php } ?>

		</table>
		<br />
		<?php
		} else {
			echo "<i>"._("No issues reported")."</i><br /><br />";
		}

		if ($user->canEdit()){
		?>
			<a href='ajax_forms.php?action=getIssueLogForm&height=250&width=265&modal=true&organizationID=<?php echo $organizationID; ?>' class='thickbox' id='newIssue'><?php echo _("add new issue");?></a> - 
		<?php
		}
    ?>
      <a href='issues_export.php?organizationID=<?php echo $organizationID; ?>'><?php echo _("export these issues");?></a> - <a href='issues_export.php'><?php echo _("export all issues");?></a>
    <?php
        break;



    case 'getLicenseDetails':
    	$organizationID = $_GET['organizationID'];
    	$organization = new Organization(new NamedArguments(array('primaryKey' => $organizationID)));

		//if the licensing module is installed get license info for this organization
		$config = new Configuration;
		$util = new Utility();

		if ($config->settings->licensingModule == 'Y'){

			//get licenses
			$sanitizedInstance = array();
			$instance = new Alias();
			$licenseArray = array();

			try {
				$licenseArray = $organization->getLicenses();

				if (count($licenseArray) > 0){ ?>
					<table class='linedFormTable' style='width:440px;'>
					<tr>
					<th><?php echo _("License");?></th>
					<th><?php echo _("Consortium");?></th>
					<th><?php echo _("Status");?></th>
					</tr>

					<?php
					$licensingPath = $util->getLicensingURL();

					foreach ($licenseArray as $license){
						echo "<tr>\n";
						echo "<td><a href='" . $licensingPath . $license['licenseID'] . "' target='_blank'>" . $license['licenseName'] . "</a></td>\n";
						echo "<td>" . $license['consortiumName'] . "</td>\n";
						echo "<td>" . $license['status'] . "</td>\n";
						echo "</tr>\n";
					}
					?>

					</table>
					<br />
				<?php
				} else {
					echo "<i>"._("No licenses set up for this organization")."</i>";
				}

			}catch(Exception $e){
				echo "<span style='color:red;'>"._("Unable to access the licensing database.  Make sure the configuration.ini is pointing to the correct place and that the database and associated tables have been set up.")."</span>";
			}
		}


        break;





	case 'getSearchOrganizations':

		$pageStart = $_GET['pageStart'];
		$numberOfRecords = $_GET['numberOfRecords'];
		$whereAdd = array();

		//get where statements together (and escape single quotes)
		if ($_GET['organizationName']) $whereAdd[] = "(UPPER(O.name) LIKE UPPER('%" . str_replace("'","''",$_GET['organizationName']) . "%') OR UPPER(Alias.name) LIKE UPPER('%" . str_replace("'","''",$_GET['organizationName']) . "%'))";
		if ($_GET['organizationRoleID']) $whereAdd[] = "O.organizationID in (select OrganizationRoleProfile.organizationID from OrganizationRoleProfile WHERE OrganizationRoleProfile.organizationRoleID = '" . $_GET['organizationRoleID'] . "')";
		if ($_GET['contactName']) $whereAdd[] = "UPPER(C.name) LIKE UPPER('%" . str_replace("'","''",$_GET['contactName']) . "%')";
		if ($_GET['startWith']) $whereAdd[] = "TRIM(LEADING 'THE ' FROM UPPER(O.name)) LIKE UPPER('" . $_GET['startWith'] . "%')";

		$orderBy = $_GET['orderBy'];
		$limit = ($pageStart-1) . ", " . $numberOfRecords;

		//get total number of records to print out and calculate page selectors
		$totalOrgObj = new Organization();
		$totalRecords = count($totalOrgObj->search($whereAdd, $orderBy, ""));

		//reset pagestart to 1 - happens when a new search is run but it kept the old page start
		if ($totalRecords < $pageStart){
			$pageStart=1;
		}

		$limit = ($pageStart-1) . ", " . $numberOfRecords;

		$organizationObj = new Organization();
		$organizationArray = array();
		$organizationArray = $organizationObj->search($whereAdd, $orderBy, $limit);

		if (count($organizationArray) == 0){
			echo "<br /><br /><i>"._("Sorry, no requests fit your query")."</i>";
			$i=0;
		}else{
			$thisPageNum = count($organizationArray) + $pageStart - 1;
			echo "<span style='font-weight:bold;'>"._("Displaying ") . $pageStart . _(" to ") . $thisPageNum . _(" of ") . $totalRecords . _(" Organization Records")."</span><br />";

			//print out page selectors
			if ($totalRecords > $numberOfRecords){
					echo "<div id='pagination-div'>";
				if ($pageStart == "1"){
					echo "<span class='smallerText'><i class='fa fa-backward'></i></span>&nbsp;";
				}else{
					echo "<a href='javascript:setPageStart(1);' class='smallLink'><i class='fa fa-backward'></i></a>&nbsp;";
				}

				//don't want to print out too many page selectors!!
				$maxDisplay=41;
				if ((($totalRecords/$numberOfRecords)+1) < $maxDisplay){
					$maxDisplay = ($totalRecords/$numberOfRecords)+1;
				}

				for ($i=1; $i<$maxDisplay; $i++){

					$nextPageStarts = ($i-1) * $numberOfRecords + 1;
					if ($nextPageStarts == "0") $nextPageStarts = 1;


					if ($pageStart == $nextPageStarts){
						echo "<span class='smallerText'>" . $i . "</span>&nbsp;";
					}else{
						echo "<a href='javascript:setPageStart(" . $nextPageStarts  .");' class='smallLink'>" . $i . "</a>&nbsp;";
					}
				}

				if ($pageStart == $nextPageStarts){
					echo "<span class='smallerText'><i class='fa fa-forward'></i></span>&nbsp;";
				}else{
					echo "<a href='javascript:setPageStart(" . $nextPageStarts  .");' class='smallLink'><i class='fa fa-forward'></i></a>&nbsp;";
				}
				echo "</div>";
			}else{
				echo "<div id='pagination-div'></div>";
			}


			?>
			<table class='dataTable' style='width:840px'>
			<tr>
			<?php if ($_GET['contactName']) { ?>
				<th><table class='noBorderTable'><tr><td><?php echo _("Contact Name(s)");?></td><td class='arrow'><a href='javascript:setOrder("C.name","asc");'><img src='images/arrowup.png' border=0></a>&nbsp;<a href='javascript:setOrder("C.name","desc");'><img src='images/arrowdown.png' border=0></a></td></tr></table></th>
				<th><table class='noBorderTable'><tr><td><?php echo _("Contact Role(s)");?></td><td class='arrow'><a href='javascript:setOrder("O.name","asc");'><img src='images/arrowup.png' border=0></a>&nbsp;<a href='javascript:setOrder("O.name","desc");'><img src='images/arrowdown.png' border=0></a></td></tr></table></th>
				<th><table class='noBorderTable'><tr><td><?php echo _("Organization Name");?></td><td class='arrow'><a href='javascript:setOrder("O.name","asc");'><img src='images/arrowup.png' border=0></a>&nbsp;<a href='javascript:setOrder("O.name","desc");'><img src='images/arrowdown.png' border=0></a></td></tr></table></th>
				<th><table class='noBorderTable'><tr><td><?php echo _("Parent Organization");?></td><td class='arrow'><a href='javascript:setOrder("OP.name","asc");'><img src='images/arrowup.png' border=0></a>&nbsp;<a href='javascript:setOrder("OP.name","desc");'><img src='images/arrowdown.png' border=0></a></td></tr></table></th>
				<th><table class='noBorderTable'><tr><td><?php echo _("Organization Role(s)");?></td><td class='arrow'><a href='javascript:setOrder("orgRoles","asc");'><img src='images/arrowup.png' border=0></a>&nbsp;<a href='javascript:setOrder("orgRoles","desc");'><img src='images/arrowdown.png' border=0></a></td></tr></table></th>

			<?php } else{ ?>
				<th><table class='noBorderTable'><tr><td><?php echo _("Organization Name");?></td><td class='arrow'><a href='javascript:setOrder("O.name","asc");'><img src='images/arrowup.png' border=0></a>&nbsp;<a href='javascript:setOrder("O.name","desc");'><img src='images/arrowdown.png' border=0></a></td></tr></table></th>
				<th><table class='noBorderTable'><tr><td><?php echo _("Alias");?></td><td class='arrow'><a href='javascript:setOrder("Aliases","asc");'><img src='images/arrowup.png' border=0></a>&nbsp;<a href='javascript:setOrder("Aliases","desc");'><img src='images/arrowdown.png' border=0></a></td></tr></table></th>
				<th><table class='noBorderTable'><tr><td><?php echo _("Parent Organization");?></td><td class='arrow'><a href='javascript:setOrder("OP.name","asc");'><img src='images/arrowup.png' border=0></a>&nbsp;<a href='javascript:setOrder("OP.name","desc");'><img src='images/arrowdown.png' border=0></a></td></tr></table></th>
				<th><table class='noBorderTable'><tr><td><?php echo _("Role(s)");?></td><td class='arrow'><a href='javascript:setOrder("orgRoles","asc");'><img src='images/arrowup.png' border=0></a>&nbsp;<a href='javascript:setOrder("orgRoles","desc");'><img src='images/arrowdown.png' border=0></a></td></tr></table></th>
			<?php } ?>
			</tr>

			<?php

			$i=0;
			foreach ($organizationArray as $organization){
				$i++;
				if ($i % 2 == 0){
					$classAdd="";
				}else{
					$classAdd="class='alt'";
				}
				echo "<tr>";
				if ($_GET['contactName']) {
					echo "<td $classAdd>" . $organization['contacts'] . "</td>";
					echo "<td $classAdd>" . $organization['contactroles'] . "</td>";
					echo "<td $classAdd><a href='orgDetail.php?showTab=contacts&organizationID=" . $organization['organizationID'] . "'>" . $organization['name'] . "</a></td>";
				}else{
					echo "<td $classAdd><a href='orgDetail.php?organizationID=" . $organization['organizationID'] . "'>" . $organization['name'] . "</a></td>";
					echo "<td $classAdd>" . $organization['aliases'] . "</td>";
				}

				if ($organization['parentOrganizationID'] && $organization['parentOrganizationID']){
					echo "<td $classAdd><a href='orgDetail.php?organizationID=" . $organization['parentOrganizationID'] . "'>" . $organization['parentOrganizationName'] . "</a></td>";
				}else{
					echo "<td $classAdd>&nbsp;</td>";
				}

				echo "<td $classAdd>" . $organization['orgRoles'] . "</td>";
				echo "</tr>";
			}

			?>
			</table>

			<table style='width:100%;margin-top:4px'>
			<tr>
			<td style='text-align:left'>
			<?php
			//print out page selectors
			if ($totalRecords > $numberOfRecords){
				if ($pageStart == "1"){
					echo "<span class='smallerText'><i class='fa fa-backward'></i></span>&nbsp;";
				}else{
					echo "<a href='javascript:setPageStart(1);' class='smallLink'><i class='fa fa-backward'></i></a>&nbsp;";
				}

				$maxDisplay=41;
				if ((($totalRecords/$numberOfRecords)+1) < $maxDisplay){
					$maxDisplay = ($totalRecords/$numberOfRecords)+1;
				}

				for ($i=1; $i<$maxDisplay; $i++){

					$nextPageStarts = ($i-1) * $numberOfRecords + 1;
					if ($nextPageStarts == "0") $nextPageStarts = 1;


					if ($pageStart == $nextPageStarts){
						echo "<span class='smallerText'>" . $i . "</span>&nbsp;";
					}else{
						echo "<a href='javascript:setPageStart(" . $nextPageStarts  .");' class='smallLink'>" . $i . "</a>&nbsp;";
					}
				}

				if ($pageStart == $nextPageStarts){
					echo "<span class='smallerText'><i class='fa fa-forward'></i></span>&nbsp;";
				}else{
					echo "<a href='javascript:setPageStart(" . $nextPageStarts  .");' class='smallLink'><i class='fa fa-forward'></i></a>&nbsp;";
				}
			}
			?>
			</td>
			</tr>
			<tr>
			<td style="text-align:left;padding-top:10px;">
			<select id='numberOfRecords' name='numberOfRecords' onchange='javascript:setNumberOfRecords();' style='width:50px;'>
				<?php
				for ($i=5; $i<=50; $i=$i+5){
					if ($i == $numberOfRecords){
						echo "<option value='" . $i . "' selected>" . $i . "</option>";
					}else{
						echo "<option value='" . $i . "'>" . $i . "</option>";
					}
				}
				?>
			</select>
			<span class='smallText'><?php echo _("records per page");?></span>
			</td>
			</tr>
			</table>

			<?php
		}

		//set everything in sessions to make form "sticky"
		$_SESSION['org_pageStart'] = $_GET['pageStart'];
		$_SESSION['org_numberOfRecords'] = $_GET['numberOfRecords'];
		$_SESSION['org_organizationName'] = $_GET['organizationName'];
		$_SESSION['org_organizationRoleID'] = $_GET['organizationRoleID'];
		$_SESSION['org_contactName'] = $_GET['contactName'];
		$_SESSION['org_startWith'] = $_GET['startWith'];
		$_SESSION['org_orderBy'] = $_GET['orderBy'];

		break;



	default:
       echo _("Action ") . $action . _(" not set up!");
       break;


}


?>

