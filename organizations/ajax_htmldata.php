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
	$html = "<div id='openIssues'>
	<div class=\"issue\">";
	if (!$issue->dateClosed) {
		$html .= "
		<a class=\"thickbox action closeResourceIssueBtn\" href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getCloseResourceIssueForm&issueID={$issue->issueID}\",140,345)'>close</a>
		<a class=\"thickbox action\" href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getNewDowntimeForm&organizationID={$GLOBALS['organizationID']}&issueID={$issue->issueID}\",220,390)'>downtime</a>";
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
			if(!empty($contact['name'])) {
				$html .= "<li><a href=\"mailto:".urlencode($contact['emailAddress'])."?Subject=RE: {$issue->subjectText}\">{$contact['name']}</a></li>";
			} else {
				$html .= "<li><a href=\"mailto:".urlencode($contact['emailAddress'])."?Subject=RE: {$issue->subjectText}\">{$contact['emailAddress']}</a></li>";
			}
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
	</div></div>";
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
		$html .= "<a class=\"thickbox\" href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getResolveDowntimeForm&downtimeID={$downtime->downtimeID}\",383,345)'>Resolve</a>";
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
		if (is_array($contactObjArray) && count($contactObjArray) > 0) {
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

		//add http if protocol is missing
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
		
		<?php if ($user->canEdit()){ ?>
			<p>
				<a href='javascript:void(0)' onclick='myDialog("ajax_forms.php?action=getOrganizationForm&organizationID=<?php echo $organizationID; ?>",383,800)' class='thickbox' id='editOrganization' aria-label='<?php echo sprintf(_('Edit %s'), $organization->name); ?>'><img src='images/edit.gif' alt='<?php echo _("edit");?>'></a><?php } ?>  
				<?php if ($user->isAdmin){ ?><a href='javascript:removeOrganization(<?php echo $organizationID; ?>)' aria-label='<?php echo sprintf(_('Delete %s'), $organization->name); ?>'><img src='images/cross.gif' alt='<?php echo _("delete organization");?>'></a>
			</p>
		<?php } ?>
		
		<dl class='dl-grid'>
		<?php if (is_array($parentOrganizationArray) && count($parentOrganizationArray) > 0) { ?>
			<dt scope="row"><?php echo _("Parent Organization:");?></dt>
			<dd>
				<ul class="unstyled">
					<?php
					foreach ($parentOrganizationArray as $parentOrganization){
						echo "<li>" . $parentOrganization['name'] . "&nbsp;&nbsp;";
						echo "<a href='orgDetail.php?organizationID=" . $parentOrganization['organizationID'] . "'><img src='images/arrow-up-right.gif' alt='"._("view organization")."' title='"._("View")."'></a></li>";
					}
					?>
				</ul>
			</dd>
		<?php
		}


		if (is_array($childOrganizationArray) && count($childOrganizationArray) > 0) { ?>
			<dt><?php echo _("Child Organizations:");?></dt>
			<dd>
				<ul class="unstyled">
					<?php
					foreach ($childOrganizationArray as $childOrganization){
						// TODO: i18n placeholders
						echo $childOrganization['name'] . "&nbsp;&nbsp;";
						echo "<a href='orgDetail.php?organizationID=" . $childOrganization['organizationID'] . "'><img src='images/arrow-up-right.gif' alt='".("view organization")."' title='".("View")."' style='vertical-align:top;'></a><br />";
					}
					?>
				</ul>
			</dd>
		<?php
		}


		if ($organization->companyURL){ ?>
			<dt><?php echo _("Company URL:");?></dt>
			<dd class="url">
					<a href='<?php echo $companyURL; ?>' <?php echo getTarget(); ?>><?php echo $organization->companyURL; ?></a>
			</dd>
			
		<?php
		}

		if (is_array($organizationRoleArray) && count($organizationRoleArray) > 0) { ?>
			<dt scope="row"><?php echo _("Role(s):");?></dt>
			<dd><?php echo implode(", ", $organizationRoleArray); ?></dd>
			
		<?php
		}

		if ($organization->accountDetailText){ ?>
			<dt><?php echo _("Account Details:");?></dt>
			<dd><?php echo nl2br($organization->accountDetailText); ?></dd>
		<?php
		}

		if ($organization->noteText){ ?>
			<dt><?php echo _("Notes:");?></dt>
			<dd><?php echo nl2br($organization->noteText); ?></dd>
		<?php
		}

        if ($organization->ilsID){
            $ilsClient = (new ILSClientSelector())->select();
        ?>
			<dt><?php echo _("ILS:");?></dt>
			<dd><a href="<?php echo $ilsClient->getVendorURL() . $organization->ilsID; ?>"><?php echo sprintf(_('Open vendor in %s'), $ilsClient->getILSName()); ?></a></dd>
		<?php
		}

		?>
		</dl>
		<p>
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
		</p>

		<?php
		if (($organization->updateDate) && ($organization->updateDate != '0000-00-00')){
			echo "<p><i>"._("Last Update:"). format_date($organization->updateDate)._(" by "); ?><?php echo $updateUser->firstName . " " . $updateUser->lastName . "</i></p>";
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
		<?php if (is_array($aliasArray) && count($aliasArray) > 0) { ?>
			<table class='table-border table-striped'>
			<thead>
			<tr>
			<th scope="col"><?php echo _("Alias");?></th>
			<th scope="col"><?php echo _("Alias Type");?></th>
			<th scope="col"><?php echo _("Actions");?></th>
			</tr>
			</thead>
			<tbody>
			<?php
			foreach ($aliasArray as $organizationAlias){
				echo "<tr>\n";
				echo "<th scope='row'>" . $organizationAlias['name'] . "</th>\n";
				echo "<td>" . $organizationAlias['aliasTypeShortName'] . "</td>\n";
				if ($user->canEdit()){
					echo "<td class='actions'><a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getAliasForm&height=124&width=285&modal=true&organizationID=" .  $organizationID . "&aliasID=" . $organizationAlias['aliasID'] . "\",250,285)' class='thickbox'><img src='images/edit.gif' alt='"._("edit")."' title='"._("edit alias")."'></a>";
					echo "<a href='javascript:removeAlias(" . $organizationAlias['aliasID'] . ")'><img src='images/cross.gif' alt='"._("remove alias")."' title='"._("remove alias")."'></a></td>";
				}
				echo "</tr>\n";
			}
			?>

			</table>
			<br />
		<?php
		} else {
			echo "<p><i>"._("No aliases defined")."</i><p>";
		}

		?>

		<?php if ($user->canEdit()){ ?>
			<a href='javascript:void(0)' onclick='myDialog("ajax_forms.php?action=getAliasForm&height=124&width=285&modal=true&organizationID=<?php echo $organizationID; ?>",250,285)' class='thickbox' id='newAlias'><?php echo _("add a new alias");?></a>
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

		if (is_array($contactArray) && count($contactArray) > 0) {
			foreach ($contactArray as $contact){
			?>
				<dl class='dl-grid contact-list'>
				<div class="header">
						<dt>
							<?php echo _("Name: ") ?>
						</dt>
						<dd>
					<?php

					if ($contact['name']){
						echo $contact['name'] . "&nbsp;&nbsp;";
					}

					if ($user->canEdit()){
						echo "<a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getContactForm&height=463&width=345&modal=true&organizationID=" . $organizationID . "&contactID=" . $contact['contactID'] . "\",500,354)' class='thickbox'><img src='images/edit.gif' alt='"._("edit")."' title='"._("edit contact")."'></a>";
						echo "&nbsp;<a href='javascript:removeContact(" . $contact['contactID'] . ")'><img src='images/cross.gif' alt='"._("remove contact")."' title='"._("remove contact")."'></a>";
					}

					?>
					</dd>
				</div>
				
				<?php if (($contact['archiveDate'] != '0000-00-00') && ($contact['archiveDate'])) { ?>
					<dt class="archived"><?php echo _("No longer valid:");?></dt>
					<dd class="archived"><?php echo format_date($contact['archiveDate']); ?></dd>
				<?php
				}

				if ($contact['title']) { ?>
					<dt><?php echo _("Title:");?></dt>
					<dd><?php echo $contact['title']; ?></dd>
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
					<dt><?php echo _("Phone:");?></dt>
					<dd><?php echo $contact['phoneNumber']; ?></dd>
				<?php
				}

				if ($contact['altPhoneNumber']) { ?>
					<dt><?php echo _("Alt Phone:");?></dt>
					<dd><?php echo $contact['altPhoneNumber']; ?></dd>
				<?php
				}

				if ($contact['faxNumber']) { ?>
					<dt><?php echo _("Fax:");?></dt>
					<dd><?php echo $contact['faxNumber']; ?></dd>
				<?php
				}

				if ($contact['emailAddress']) { ?>
					<dt><?php echo _("Email:");?></dt>
					<dd><a href='mailto:<?php echo $contact['emailAddress']; ?>'><?php echo $contact['emailAddress']; ?></a></dd>
				<?php
				}

				if ($contact['noteText']) { ?>
					<dt><?php echo _("Notes:");?></dt>
					<dd><?php echo nl2br($contact['noteText']); ?></dd>
				<?php
				}
				?>

				<dt><?php echo _('Roles: ') ?></dt>
				<dd><?php echo $contact['contactRoles']; ?></dd>

				<?php
				if ($contact['lastUpdateDate']) { ?>
					<dt><?php echo _("Last Updated:");?></dt>
					<dd><i><?php echo format_date($contact['lastUpdateDate']); ?></i></dd>
				<?php
				}
				?>

				</dl>
			<?php
			}
		} else {
			if (($archiveInd != 1) && ($showArchivesInd != 1)){
				echo "<p><i>"._("No unarchived contacts")."</i></p>";
			}
		}

		if (($showArchivesInd == "0") && ($archiveInd == "1") && (count($organization->getArchivedContacts()) > 0)){
			echo "<p><i>" . count($organization->getArchivedContacts()) . _(" archived contact(s) available.  ")."<a href='javascript:updateArchivedContacts(1);'>"._("show archived contacts")."</a></i></p>";
		}

		if (($showArchivesInd == "1") && ($archiveInd == "1") && (count($organization->getArchivedContacts()) > 0)){
			echo "<p><i><a href='javascript:updateArchivedContacts(0);'>"._("hide archived contacts")."</a></i></p>";
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

		if (is_array($externalLoginArray) && count($externalLoginArray) > 0) {
			foreach ($externalLoginArray as $externalLogin){
			?>
				<table class='table-border table-striped'>
					<thead>
				<tr>
				<th><?php echo $externalLogin['externalLoginTypeShortName']; ?></th>
				<th>
				<?php
					if ($user->canEdit()){
						echo "<a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getAccountForm&height=254&width=342&modal=true&organizationID=" . $organizationID . "&externalLoginID=" . $externalLogin['externalLoginID'] . "\",300,342)' class='thickbox'><img src='images/edit.gif' alt='"._("edit")."' title='"._("edit external login")."'></a>";
						echo "&nbsp;<a href='javascript:removeExternalLogin(" . $externalLogin['externalLoginID'] . ")'><img src='images/cross.gif' alt='"._("remove external login")."' title='"._("remove external login")."'></a>";
					}
				?>
				</th>
				</tr>
				</thead>
				<tbody>
				<?php if ($externalLogin['loginURL']) { ?>
				<tr>
				<th scope="row"><?php echo _("Login URL:");?></th>
				<td class="url"><?php echo $externalLogin['loginURL'];
					if (strpos($externalLogin['loginURL'], '://') === false) {
						$externalLogin['loginURL'] = "http://" . $externalLogin['loginURL'];
					}
				?>&nbsp;&nbsp;<a href='<?php echo $externalLogin['loginURL']; ?>' <?php echo getTarget(); ?>><img src='images/arrow-up-right.gif' alt='<?php echo _("Visit Login URL");?>' title='<?php echo _("Visit Login URL");?>' style='vertical-align:top;'></a></td>
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

				</table>
			<?php
			}
		} else {
			echo "<p><i>"._("No external logins added")."</i></p>";
		}

		if ($user->canEdit()){
		?>
		<a href='javascript:void(0)' onclick='myDialog("ajax_forms.php?action=getAccountForm&height=254&width=342&modal=true&organizationID=<?php echo $organizationID; ?>",300,342)' class='thickbox' id='newAlias'><?php echo _("add new external login");?></a><br />
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
		<div class='issueTabTable'>
			<div class="header">
				<h3><?php echo _("Issues/Problems");?></h3>
				<a id="createIssueBtn addElement" class="thickbox" href="javascript:void(0)" onclick='myDialog("ajax_forms.php?action=getNewIssueForm&organizationID=<?php echo $organizationID; ?>&modal=true",600,500)'><?php echo _("report new issue");?></a>
			</div>
				<p>
					<a href="<?php echo $getIssuesFormData; ?>" class="issuesBtn" id="openIssuesBtn"><?php echo _("view open issues");?></a>
					<a <?php echo getTarget(); ?> href="<?php echo $exportIssueUrl;?>"><img src="images/xls.gif" /></a>
					<div class="issueList" id="openIssues" style="display:none;"></div>
				</p>
				<p>
					<a href="<?php echo $getIssuesFormData."&archived=1"; ?>" class="issuesBtn" id="archivedIssuesBtn"><?php echo _("view archived issues");?></a>
					<a <?php echo getTarget(); ?> href="<?php echo $exportIssueUrl;?>&archived=1"><img src="images/xls.gif" /></a>
					<div class="issueList" id="archivedIssues"></div>
				</p>
				<div class="header">
					<h3><?php echo _("Downtime");?></h3>
					<a id="createDowntimeBtn addElement" class="thickbox" href="javascript:return false;" onclick='myDialog("ajax_forms.php?action=getNewDowntimeForm&organizationID=<?php echo $_GET['organizationID']; ?>&height=264&width=390&modal=true",300,390)'><?php echo _("report new Downtime");?></a>
				</div>
				<p>
					<a href="<?php echo $getDowntimeFormData; ?>" class="downtimeBtn" id="openDowntimeBtn"><?php echo _("view current/upcoming downtime");?></a>
					<a <?php echo getTarget(); ?> href="<?php echo $exportDowntimeUrl;?>"><img src="images/xls.gif" /></a>
					<div class="downtimeList" id="currentDowntime" style="display:none;"></div>
				</p>
				
				<p>
					<a href="<?php echo $getDowntimeFormData."&archived=1"; ?>" class="downtimeBtn" id="archiveddowntimeBtn"><?php echo _("view archived downtime");?></a>
					<a <?php echo getTarget(); ?> href="<?php echo $exportDowntimeUrl;?>&archived=1"><img src="images/xls.gif" /></a>
					<div class="downtimeList" id="archivedDowntime"></div>
				</p>
		</div>
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
			echo "<p>" . _("There are no organization level issues.") . "</p>";
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
			echo "<p>" . _("There are no organization level downtimes.") . "</p>";
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

		if (is_array($issueLogArray) && count($issueLogArray) > 0) {
		?>
		<table class='table-border table-striped'>
		<thead>
		<tr>
		<th scope="col"><?php echo _("Added");?></th>
		<th scope="col"><?php echo _("Date");?></th>
		<th scope="col"><?php echo _("Type");?></th>
		<th scope="col"><?php echo _("Notes");?></th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($issueLogArray as $issueLog){
			if (($issueLog['issueStartDate']) && ($issueLog['issueStartDate'] != "0000-00-00")) {
				$issueStartDate= format_date($issueLog['issueStartDate']);
			}else{
				$issueStartDate='';
			}
      if (($issueLog['issueEndDate']) && ($issueLog['issueEndDate'] != "0000-00-00")) {
				$issueEndDate= format_date($issueLog['issueEndDate']);
			}else{
				$issueEndDate='';
			}

			?>
			<tr>
			<th scope="row">
				<?php printf(_("%s <br />by <i>%s</i>"), format_date($issueLog['updateDate']), $issueLog['updateUser']);?>
			</th>
			<td><?php
        if ($issueStartDate && $issueEndDate) {
          printf(_("%s to %s"), $issueStartDate, $issueEndDate);
        } elseif ($issueStartDate) {
          printf(_("start: %s"), $issueStartDate);
        } elseif ($issueEndDate) {
          printf(_("end: %s"), $issueEndDate);
        }
      ?>
      </td>
      <td><?php echo $issueLog['issueLogType'] ?></td>
			<td><?php echo nl2br(str_replace($charsToRemove, "", $issueLog['noteText'])); ?>
			<?php
			if ($user->canEdit()){
				echo "<a href='javascript:void(0)' onclick='myDialog(\"ajax_forms.php?action=getIssueLogForm&height=250&width=265&modal=true&organizationID=" . $organizationID . "&issueLogID=" . $issueLog['issueLogID'] . "\",300,265)' class='thickbox'><img src='images/edit.gif' alt='"._("edit")."' title='"._("edit issue")."'></a>";
				echo "&nbsp;<a href='javascript:removeIssueLog(" . $issueLog['issueLogID'] . ")'><img src='images/cross.gif' alt='"._("remove issue")."' title='"._("remove issue")."'></a>";
			}
			?>
			</td></tr>
		<?php } ?>

		</table>
		<br />
		<?php
		} else {
			echo "<p><i>"._("No issues reported")."</i></p>";
		}

		if ($user->canEdit()){
		?>
			<a href='javascript:void(0)' onclick='myDialog("ajax_forms.php?action=getIssueLogForm&height=250&width=265&modal=true&organizationID=<?php echo $organizationID; ?>",300,265)' class='thickbox' id='newIssue'><?php echo _("add new issue");?></a> -
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

				if (is_array($licenseArray) && count($licenseArray) > 0) { ?>
					<table class='table-border table-striped'>
					<thead>
					<tr>
					<th scope="col"><?php echo _("License");?></th>
					<th scope="col"><?php echo _("Consortium");?></th>
					<th scope="col"><?php echo _("Status");?></th>
					</tr>
					</thead>
					<tbody>
					<?php
					$licensingPath = $util->getLicensingURL();

					foreach ($licenseArray as $license){
						echo "<tr>\n";
						echo "<th scope='row'><a href='" . $licensingPath . $license['licenseID'] . "' " . getTarget() . ">" . $license['licenseName'] . "</a></th>\n";
						echo "<td>" . $license['consortiumName'] . "</td>\n";
						echo "<td>" . $license['status'] . "</td>\n";
						echo "</tr>\n";
					}
					?>

					</table>
				<?php
				} else {
					echo "<p><i>"._("No licenses set up for this organization")."</i></p>";
				}

			}catch(Exception $e){
				echo "<p class='error'>"._("Unable to access the licensing database.  Make sure the configuration.ini is pointing to the correct place and that the database and associated tables have been set up.")."</p>";
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
		$pagination = '';

		if (count($organizationArray) == 0){
			echo "<p><i>"._("Sorry, no requests fit your query")."</i></p>";
			$i=0;
		}else{
			$thisPageNum = count($organizationArray) + $pageStart - 1;
			echo "<h2>".sprintf(_("Displaying %1\$d to %2\$d of %3\$d organization records"), $pageStart, $thisPageNum, $totalRecords)."</h2>";

			//print out page selectors
			if ($totalRecords > $numberOfRecords){
				$pagination .= "<nav class='pagination' id='pagination-div' aria-label='"._('Records per page')."'><ul>";
				if ($pageStart == "1"){
					$pagination .=  "<li class='first' aria-hidden='true'><span class='smallerText'><i class='fa fa-backward'></i></span></li>";
				}else{
					$pagination .=  "<li class='first'><a href='javascript:setPageStart(1);' class='smallLink' aria-label='" . sprintf(_('First page, page %d'), $i ? $i : 1) . "'><i class='fa fa-backward'></i></a></li>";
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
						$pagination .=  "<li aria-current='page'><span>" . $i . "</span></li>";
					}else{
						$pagination .=  "<li><a href='javascript:setPageStart(" . $nextPageStarts  .");' class='smallLink' aria-label='" . sprintf(_('Page %d'), $i) . "'>" . $i . "</a></li>";
					}
				}

				if ($pageStart == $nextPageStarts){
					$pagination .=  "<li class='last' aria-hidden='true'><span><i class='fa fa-forward'></i></span></li>";
				}else{
					$pagination .=  "<li class='last'><a href='javascript:setPageStart(" . $nextPageStarts  .");' class='smallLink' aria-label='" . sprintf(_('Last page, page %d'), $i - 1) . "'><i class='fa fa-forward'></i></a></li>";
				}
				$pagination .=  "</ul></nav>";
			}
			
			echo $pagination;


			?>
			<table class='dataTable table-border table-striped'>
				<thead>
			<tr>
			<?php if ($_GET['contactName']) { ?>
				<th scope="col"><span class="sortable"><?php echo _("Contact Name(s)");?><span class='arrows'><a href='javascript:setOrder("C.name","asc");'><img src='images/arrowup.png' alt='<?php echo _('Sort by name, ascending'); ?>'></a><a href='javascript:setOrder("C.name","desc");'><img src='images/arrowdown.png' alt='<?php echo _('Sort by name, descending'); ?>'></a></span></span></th>
				<th scope="col"><span class="sortable"><?php echo _("Contact Role(s)");?><span class='arrows'><a href='javascript:setOrder("O.name","asc");'><img src='images/arrowup.png' alt='<?php echo _('Sort by role, ascending'); ?>'></a><a href='javascript:setOrder("O.name","desc");'><img src='images/arrowdown.png' alt='<?php echo _('Sort by role, descending'); ?>'></a></span></span></th>
				<th scope="col"><span class="sortable"><?php echo _("Organization Name");?><span class='arrows'><a href='javascript:setOrder("O.name","asc");'><img src='images/arrowup.png' alt='<?php echo _('Sort by organization, ascending'); ?>'></a><a href='javascript:setOrder("O.name","desc");'><img src='images/arrowdown.png' alt='<?php echo _('Sort by organization, descending'); ?>'></a></span></span></th>
				<th scope="col"><span class="sortable"><?php echo _("Parent Organization");?><span class='arrows'><a href='javascript:setOrder("OP.name","asc");'><img src='images/arrowup.png' alt='<?php echo _('Sort by parent organization, ascending'); ?>'></a><a href='javascript:setOrder("OP.name","desc");'><img src='images/arrowdown.png' alt='<?php echo _('Sort by parent organization, descending'); ?>'></a></span></span></th>
				<th scope="col"><span class="sortable"><?php echo _("Organization Role(s)");?><span class='arrows'><a href='javascript:setOrder("orgRoles","asc");'><img src='images/arrowup.png' alt='<?php echo _('Sort by organization role, ascending'); ?>'></a><a href='javascript:setOrder("orgRoles","desc");'><img src='images/arrowdown.png' alt='<?php echo _('Sort by organization role, descending'); ?>'></a></span></span></th>

			<?php } else{ ?>
				<th scope="col"><span class="sortable"><?php echo _("Organization Name");?><span class='arrows'><a href='javascript:setOrder("O.name","asc");'><img src='images/arrowup.png' alt='<?php echo _('Sort by name, ascending'); ?>'></a><a href='javascript:setOrder("O.name","desc");'><img src='images/arrowdown.png' alt='<?php echo _('Sort by name, descending'); ?>'></a></span></span></th>
				<th scope="col"><span class="sortable"><?php echo _("Alias");?><span class='arrows'><a href='javascript:setOrder("Aliases","asc");'><img src='images/arrowup.png' alt='<?php echo _('Sort by alias, ascending'); ?>'></a><a href='javascript:setOrder("Aliases","desc");'><img src='images/arrowdown.png' alt='<?php echo _('Sort by alias, descending'); ?>'></a></span></span></th>
				<th scope="col"><span class="sortable"><?php echo _("Parent Organization");?><span class='arrows'><a href='javascript:setOrder("OP.name","asc");'><img src='images/arrowup.png' alt='<?php echo _('Sort by parent organzation, ascending'); ?>'></a><a href='javascript:setOrder("OP.name","desc");'><img src='images/arrowdown.png' alt='<?php echo _('Sort by parent organzation, descending'); ?>'></a></span></span></th>
				<th scope="col"><span class="sortable"><?php echo _("Role(s)");?><span class='arrows'><a href='javascript:setOrder("orgRoles","asc");'><img src='images/arrowup.png' alt='<?php echo _('Sort by organization role, ascending'); ?>'></a><a href='javascript:setOrder("orgRoles","desc");'><img src='images/arrowdown.png' alt='<?php echo _('Sort by organzation role, descending'); ?>'></a></span></span></th>
			<?php } ?>
			</tr>
			</thead>
			<?php

			$i=0;
			foreach ($organizationArray as $organization){
				echo "<tr>";
				if ($_GET['contactName']) {
					echo "<td>" . $organization['contacts'] . "</td>";
					echo "<td>" . $organization['contactroles'] . "</td>";
					echo "<td><a href='orgDetail.php?showTab=contacts&organizationID=" . $organization['organizationID'] . "'>" . $organization['name'] . "</a></td>";
				}else{
					echo "<td><a href='orgDetail.php?organizationID=" . $organization['organizationID'] . "'>" . $organization['name'] . "</a></td>";
					echo "<td>" . $organization['aliases'] . "</td>";
				}

				if ($organization['parentOrganizationID'] && $organization['parentOrganizationID']){
					echo "<td><a href='orgDetail.php?organizationID=" . $organization['parentOrganizationID'] . "'>" . $organization['parentOrganizationName'] . "</a></td>";
				}else{
					echo "<td>&nbsp;</td>";
				}

				echo "<td>" . $organization['orgRoles'] . "</td>";
				echo "</tr>";
			}

			?>
			</table>

			<?php
			echo $pagination;
			?>
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
			<label for="numberOfRecords"><?php echo _("records per page");?></label>

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
			if (empty($action))
        return;
      printf(_("Action %s not set up!"), $action);
      break;


}


?>
