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

$config = new Configuration();

switch ($_GET['action']) {


    case 'submitOrganization':
		//if this is an existing organization
		$organizationID=$_POST['organizationID'];
		if ($organizationID){
			$organization = new Organization(new NamedArguments(array('primaryKey' => $organizationID)));

			$organization->updateLoginID 		= $loginID;
			$organization->updateDate			= date( 'Y-m-d H:i:s' );
		}else{
			//set up new organization
			$organization = new Organization();

			$organization->createLoginID 		= $loginID;
			$organization->createDate			= date( 'Y-m-d H:i:s' );

			$organization->updateLoginID 		= '';
			$organization->updateDate			= '';
		}

		$organization->name = trim($_POST['name']);
		if ($_POST['companyURL'] != 'http://'){
			$organization->companyURL = $_POST['companyURL'];
		}else{
			$organization->companyURL = '';
		}

		$organization->noteText = $_POST['noteText'];
		$organization->accountDetailText = $_POST['accountDetailText'];

		try {
			$organization->save();
			if (!$organizationID){
				echo $organization->primaryKey;
				$organizationID=$organization->primaryKey;
			}

			//first remove all orgs, then we'll add them back
			$organization->removeOrganizationRoles();

			foreach (explode(',', $_POST['organizationRoles']) as $id){
				if ($id){
					$organizationRoleProfile = new OrganizationRoleProfile();
					$organizationRoleProfile->organizationID = $organizationID;
					$organizationRoleProfile->organizationRoleID = $id;
					$organizationRoleProfile->save();
				}
			}

			//next, add organization hierarchy if added
			if ($_POST['parentOrganizationID'] != ""){
				//first remove all parents, then we'll add them back
				$organization->removeParentOrganizations();

				if ($_POST['parentOrganization']){
					$organizationHierarchy = new OrganizationHierarchy();
					$organizationHierarchy->organizationID = $organizationID;
					$organizationHierarchy->parentOrganizationID = $_POST['parentOrganizationID'];
					$organizationHierarchy->save();
				}


			}

            if ($organization->hasILSVendorRole()) {
                $ilsClient = (new ILSClientSelector())->select();
                // Create vendor in ILS
                if ($organization->ilsID == null) {
                    $ilsID = $ilsClient->addVendor(array(
                                                "name" => $organization->name,
                                                "companyURL" => $organization->companyURL,
                                                "noteText" => $organization->noteText,
                                                "accountDetailText" => $organization->accountDetailText,
                                                )
                                            );
                    if ($ilsID) {
                        $organization->ilsID = $ilsID;
                    }

                } else {
                    // Retrieve organization data from ILS
                    $ilsVendor = $ilsClient->getVendor($organization->ilsID);
                    $organization->name = $ilsVendor['name'];
                    $organization->companyURL = $ilsVendor['companyURL'];
                    $organization->noteText = $ilsVendor['noteText'];
                    $organization->accountDetailText = $ilsVendor['accountDetailText'];
                }
                $organization->save();
            }

		} catch (Exception $e) {
			echo $e->getMessage();
		}

        break;



    case 'submitAlias':
		//if this is an existing alias
		$aliasID=$_POST['aliasID'];

		if ($aliasID){
			$alias = new Alias(new NamedArguments(array('primaryKey' => $aliasID)));
		}else{
			//set up new alias
			$alias = new Alias();
		}

		$alias->aliasTypeID = $_POST['aliasTypeID'];
		$alias->organizationID = $_POST['organizationID'];
		$alias->name = $_POST['name'];

		try {
			$alias->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

        break;



    case 'submitContact':
		//if this is an existing contact
		if (isset($_POST['contactID'])) $contactID=$_POST['contactID']; else $contactID='';

		if ($contactID){
			$contact = new Contact(new NamedArguments(array('primaryKey' => $contactID)));
		}else{
			//set up new contact
			$contact = new Contact();
		}

		$contact->lastUpdateDate		= date( 'Y-m-d' );
		$contact->organizationID 		= $_POST['organizationID'];
		$contact->name 					= $_POST['name'];
		$contact->title 				= $_POST['title'];
		$contact->addressText			= $_POST['addressText'];
		$contact->phoneNumber			= $_POST['phoneNumber'];
		$contact->altPhoneNumber		= $_POST['altPhoneNumber'];
		$contact->faxNumber				= $_POST['faxNumber'];
		$contact->emailAddress			= $_POST['emailAddress'];
		$contact->noteText				= $_POST['noteText'];

		if (((!$contact->archiveDate) || ($contact->archiveDate == '0000-00-00')) && ($_POST['archiveInd'] == "1")){
			$contact->archiveDate = date( 'Y-m-d' );
		}else if ($_POST['archiveInd'] == "0"){
			$contact->archiveDate = '';
		}

		try {
			$contact->save();

			if (!$contactID){
				$contactID=$contact->primaryKey;
			}

			//first remove all orgs, then we'll add them back
			$contact->removeContactRoles();

			foreach (explode(',', $_POST['contactRoles']) as $id){
				if ($id){
					$contactRoleProfile = new ContactRoleProfile();
					$contactRoleProfile->contactID = $contactID;
					$contactRoleProfile->contactRoleID = $id;
					$contactRoleProfile->save();
				}
			}

			echo $contact->contactID;

		} catch (Exception $e) {
			echo $e->getMessage();
		}

        break;





    case 'submitExternalLogin':
		//if this is an existing contact
		$externalLoginID=$_POST['externalLoginID'];

		if ($externalLoginID){
			$externalLogin = new ExternalLogin(new NamedArguments(array('primaryKey' => $externalLoginID)));
		}else{
			//set up new external login
			$externalLogin = new ExternalLogin();
		}

		$externalLogin->updateDate				= date( 'Y-m-d H:i:s' );
		$externalLogin->externalLoginTypeID 	= $_POST['externalLoginTypeID'];
		$externalLogin->organizationID 			= $_POST['organizationID'];
		$externalLogin->loginURL				= $_POST['loginURL'];
		$externalLogin->username				= $_POST['username'];
		$externalLogin->emailAddress			= $_POST['emailAddress'];
		$externalLogin->password				= $_POST['password'];
		$externalLogin->noteText				= $_POST['noteText'];

		try {
			$externalLogin->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

        break;
	case 'submitCloseResourceIssue':
		if (!empty($_POST['issueID'])){
			$issueID = $_POST['issueID'];
			$issue = new Issue(new NamedArguments(array('primaryKey' => $issueID)));
			$issue->resolutionText = $_POST['resolutionText'];
			$issue->dateClosed = date("Y-m-d H:i:s");
			try {
				$issue->save();
			} catch (Exception $e) {
				echo $e->getMessage();
			}
		}
	break;
	case 'insertResourceIssue':
		$formDataArray = $_POST["issue"];
		$resourceIDArray = $_POST["resourceIDs"];
		$contactIDs = $_POST['contactIDs'];
		$organizationID = $_POST['organizationID'];

		$sourceOrganizationID = $_POST['sourceOrganizationID'];

		$sourceOrganization = new Organization(new NamedArguments(array('primaryKey' => $sourceOrganizationID)));


		$issueEmails = array();

		if (!empty($_POST["ccEmails"])) {
			$issueEmails = explode(',',$_POST["ccEmails"]);
		}

		$newIssue = new Issue();

		$newIssue->creatorID = $user->loginID;
		$newIssue->dateCreated = date( 'Y-m-d H:i:s');

		if($_POST["ccCreator"]) {
			$issueEmails[] = $user->emailAddress;
		}

		if(!is_numeric($formDataArray["reminderInterval"])) {
			$formDataArray["reminderInterval"] = 0;
		}

		foreach($formDataArray as $key => $value) {
			$newIssue->$key = $value;
		}

		$newIssue->save();

		//start building the email body
		$emailMessage = "Subject: {$newIssue->subjectText}\r\n\r\n
					Body: {$newIssue->bodyText}\r\n\r\n
					Applies To: ";

		if ($organizationID) {
			$newIssueRelationship = new IssueRelationship();
			$newIssueRelationship->issueID = $newIssue->primaryKey;
			$newIssueRelationship->entityID = $organizationID;
			$newIssueRelationship->entityTypeID = 1;
			$newIssueRelationship->save();

			$emailMessage .= "{$organization->name}\r\n";
		} else {
			$orgResourcesArray = $sourceOrganization->getResources(5);
			$orgResourcesIndexed = array();
			foreach ($orgResources as $resource) {
				$orgResourcesIndexed[$resource['resourceID']] = $resource;
			}

			foreach($resourceIDArray as $resourceID) {
				$newIssueRelationship = new IssueRelationship();
				$newIssueRelationship->issueID = $newIssue->primaryKey;
				$newIssueRelationship->entityID = $resourceID;
				$newIssueRelationship->entityTypeID = 2;
				$newIssueRelationship->save();
				unset($newIssueRelationship);

				$emailMessage .= "{$orgResourcesIndexed[$resourceID]->titleText}\r\n";
			}
		}

		if (count($issueEmails) > 0) {
			foreach ($issueEmails as $email) {
				if ($email) {
					$newIssueEmail = new IssueEmail();
					$newIssueEmail->issueID = $newIssue->primaryKey;
					$newIssueEmail->email = $email;
					$newIssueEmail->save();
					unset($newIssueEmail);
				}
			}
		}

		if (count($contactIDs)) {
			foreach ($contactIDs as $contactID) {
				$newIssueContact = new IssueContact();
				$newIssueContact->issueID = $newIssue->primaryKey;
				$newIssueContact->contactID = $contactID;
				$newIssueContact->save();
				unset($newIssueContact);
			}
			$organizationContacts = $sourceOrganization->getContacts();
			$emailMessage .= "\r\n\r\nContacts: \r\n\r\n";
			foreach ($organizationContacts as $contact) {
				if (in_array($contact->contactID,$contactIDs)) {
					$emailMessage .= "{$contact->name} ({$contact->emailAddress})\r\n";
				}
			}
			//send emails to contacts
			foreach ($organizationContacts as $contactData) {
				if (in_array($contactData->contactID,$contactIDs)) {
					mail($email, "New Issue: {$newIssue->subjectText}",$emailMessage);
				}
			}
		}

		if (count($issueEmails) > 0) {
			//send emails to CCs
			foreach ($issueEmails as $email) {
				mail($email, "New Issue: {$newIssue->subjectText}",$emailMessage);
			}
		}

	break;
	case 'updateDowntime':
		if (is_numeric($_POST['downtimeID'])) {
			$downtime = new Downtime(new NamedArguments(array('primaryKey' => $_POST['downtimeID'])));
			$downtime->endDate = ($_POST['endDate']) ?  date('Y-m-d H:i:s', strtotime(create_date_from_js_format($_POST['endDate'])->format('Y-m-d')." ".$_POST['endTime']['hour'].":".$_POST['endTime']['minute'].$_POST['endTime']['meridian'])) : null;
			$downtime->note = ($_POST['note']) ? $_POST['note']:null;
			$downtime->save();
		}
	break;
	case 'insertDowntime':
		$newDowntime = new Downtime();
		$newDowntime->entityID = $_POST['sourceOrganizationID'];
		$newDowntime->creatorID = $user->loginID;
		$newDowntime->downtimeTypeID = $_POST['downtimeType'];
		$newDowntime->issueID = $_POST['issueID'];

		$newDowntime->startDate = date('Y-m-d H:i:s', strtotime(create_date_from_js_format($_POST['startDate'])->format('Y-m-d')." ".$_POST['startTime']['hour'].":".$_POST['startTime']['minute'].$_POST['startTime']['meridian']));
		$newDowntime->endDate = ($_POST['endDate']) ?  date('Y-m-d H:i:s', strtotime(create_date_from_js_format($_POST['endDate'])->format('Y-m-d')." ".$_POST['endTime']['hour'].":".$_POST['endTime']['minute'].$_POST['endTime']['meridian'])):null;

		$newDowntime->dateCreated = date( 'Y-m-d H:i:s');
		$newDowntime->entityTypeID = 1;
		$newDowntime->note = ($_POST['note']) ? $_POST['note']:null;

		$newDowntime->save();
	break;

    case 'submitIssueLog':
		//if this is an existing issue
		$issueLogID=$_POST['issueLogID'];

		if ($issueLogID){
			$issueLog = new IssueLog(new NamedArguments(array('primaryKey' => $issueLogID)));
		}else{
			//set up new external login
			$issueLog = new IssueLog();
		}

		if ($_POST['issueStartDate']){
			$issueLog->issueStartDate = create_date_from_js_format($_POST['issueStartDate'])->format('Y-m-d');
		}else{
			$issueLog->issueStartDate = '';
		}

    if ($_POST['issueEndDate']){
			$issueLog->issueEndDate = create_date_from_js_format($_POST['issueEndDate'])->format('Y-m-d');
		}else{
			$issueLog->issueEndDate = '';
		}

		$issueLog->organizationID 		= $_POST['organizationID'];
		$issueLog->issueLogTypeID 		= $_POST['issueLogTypeID'];
		$issueLog->updateLoginID 		= $loginID;
		$issueLog->updateDate			= date( 'Y-m-d H:i:s' );
		$issueLog->noteText				= $_POST['noteText'];

		try {
			$issueLog->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

        break;


	case 'deleteInstance':

 		$className = $_GET['class'];
 		$deleteID = $_GET['id'];

		//since we're using MyISAM which doesn't support FKs, must verify that there are no records of children or they could disappear
		$instance = new $className(new NamedArguments(array('primaryKey' => $deleteID)));
		$numberOfChildren = $instance->getNumberOfChildren();

		echo "<font color='red'>";

		if ($numberOfChildren > 0){
			//print out a friendly message...
			echo _("Unable to delete  - this ") . strtolower(ereg_replace("[A-Z]", " \\0" , lcfirst($className))) . _(" is in use.  Please make sure no organizations are set up with this information.");
		}else{
			try {
				$instance->delete();
			} catch (Exception $e) {
				//print out a friendly message...
				echo _("Unable to delete.  Please make sure no organizations are set up with this information.");
			}
		}
		echo "</font>";

		break;

	case 'deleteOrganization':
		$organizationID = $_GET['organizationID'];
		$organization = new Organization(new NamedArguments(array('primaryKey' => $organizationID)));

		try {
			$organization->removeOrganization();
			echo _("Organization successfully deleted.");
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		break;


     case 'updateData':
 		$className = $_GET['className'];
 		$updateID = $_GET['updateId'];
 		$shortName = $_GET['shortName'];

		$instance = new $className(new NamedArguments(array('primaryKey' => $updateID)));
		$instance->shortName = $shortName;

		try {
			$instance->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

 		break;


     case 'submitUserData':

 		$orgloginID = $_GET['orgloginID'];

		if ($orgloginID){
			$user = new User(new NamedArguments(array('primaryKey' => $orgloginID)));
		}else{
			$user = new User();
			$user->loginID=$_GET['loginID'];
		}


		$user->firstName=$_GET['firstName'];
		$user->lastName=$_GET['lastName'];
		$user->privilegeID=$_GET['privilegeID'];


		try {
			$user->save();
			echo _("User successfully saved.");
		} catch (Exception $e) {
			echo $e->getMessage();
		}

 		break;


     case 'addData':

 		$className = $_GET['className'];
 		$shortName = $_GET['shortName'];

		$instance = new $className();
		$instance->shortName = $shortName;

		try {
			$instance->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

 		break;



    case 'getOrganizationList':

		if (isset($_GET['searchMode'])) $searchMode = $_GET['searchMode']; else $searchMode='';
		if (isset($_GET['limit'])) $v = $_GET['limit']; else $limit='';

		$q = $_GET['q'];
		$q = str_replace(" ", "+",$q);
		$q = str_replace("&", "%",$q);

		$organization = new Organization();
		$orgArray = $organization->autocompleteSearch($q);

		echo implode("\n", $orgArray);

		break;


    case 'getILSVendorInfos':
        if ($config->ils->ilsConnector) {
            $ilsClient = (new ILSClientSelector())->select();
            $vendor = $ilsClient->getVendorByName($_GET['name']);
            print json_encode($vendor[0]);
        }

        break;

    case 'getExistingOrganization':
        $name = $_GET['name'];
        if (isset($_GET['organizationID'])) $organizationID = $_GET['organizationID']; else $organizationID = '';


        $organization = new Organization();
        $orgArray = array();

        $exists = 0;

        foreach ($organization->allAsArray() as $orgArray) {
            if ((strtoupper($orgArray['name']) == strtoupper($name)) && ($orgArray['organizationID'] != $organizationID)) {
                $exists = $orgArray['organizationID']; break;
            }
        }
        echo $exists;
        break;

    case 'getExistingVendor':
        $name = $_GET['name'];
        $exists = -1;
        if ($config->ils->ilsConnector) {
            $ilsClient = (new ILSClientSelector())->select();
            if ($name) {
                echo $ilsClient->vendorExists($name);
            }
        }
        break;

    default:
       echo _("Action ") . $action . _(" not set up!");
       break;


}

?>
