<?php
/*
**************************************************************************************************************************
** CORAL Usage Statistics Module
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
**************************************************************************************************************************
** ajax_processing.php contains processing (adds/updates/deletes) on data sent using ajax from forms and other pages
**
** when ajax_processing.php is called through ajax, 'action' parm is required to dictate which form will be returned
**
**************************************************************************************************************************
*/


include "common.php";
include_once 'directory.php';


$action = $_REQUEST['action'];

switch ($action) {

    case 'submitLogEmailAddress':
    	//if logEmailAddressID is sent then this is an update
    	if ((isset($_GET['logEmailAddressID'])) && ($_GET['logEmailAddressID'] != '')){
 			$logEmailAddress = new LogEmailAddress(new NamedArguments(array('primaryKey' => $_GET['logEmailAddressID'])));
    	}else{
    		$logEmailAddress = new LogEmailAddress();
    	}

		$logEmailAddress->emailAddress = $_GET['emailAddress'];

		try {
			$logEmailAddress->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

        break;


    case 'deleteLogEmailAddress':

		$logEmailAddress = new LogEmailAddress(new NamedArguments(array('primaryKey' => $_GET['logEmailAddressID'])));

		try {
			$logEmailAddress->delete();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

        break;


    case 'updateOutlier':
    	$overageCount = $_GET['overageCount'];
    	$overagePercent = $_GET['overagePercent'];

    	$outlier = new Outlier(new NamedArguments(array('primaryKey' => $_GET['outlierID'])));
		$outlier->overageCount = $_GET['overageCount'];
		$outlier->overagePercent = $_GET['overagePercent'];

		try {
			$outlier->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

        break;



    case 'submitPlatformNote':

    	//if this is an update
    	if ((isset($_POST['platformNoteID'])) && ($_POST['platformNoteID'] != '')){
 			$platformNote = new PlatformNote(new NamedArguments(array('primaryKey' => $_POST['platformNoteID'])));
    	}else{
    		$platformNote = new PlatformNote();
    	}

		$platformNote->platformID = $_POST['platformID'];
		$platformNote->startYear = $_POST['startYear'];
		$platformNote->endYear = $_POST['endYear'];
		$platformNote->counterCompliantInd = $_POST['counterCompliantInd'];
		$platformNote->noteText = $_POST['noteText'];

		try {
			$platformNote->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}


        break;




    case 'addPlatform':

    	$platform = new Platform();

		$platform->name = $_POST['platformName'];
		$platform->reportDisplayName = $_POST['platformName'];

		try {
			$platform->save();
			echo $platform->primaryKey;

		} catch (Exception $e) {
			echo $e->getMessage();
		}


        break;



    case 'deletePlatformNote':

		$platformNote = new PlatformNote(new NamedArguments(array('primaryKey' => $_GET['platformNoteID'])));

		try {
			$platformNote->delete();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

        break;




    case 'submitPublisherNote':


    	//update
    	if ((isset($_POST['publisherPlatformNoteID'])) && ($_POST['publisherPlatformNoteID'] != '')){
 			$publisherPlatformNote = new PublisherPlatformNote(new NamedArguments(array('primaryKey' => $_POST['publisherPlatformNoteID'])));
    	}else{
    		$publisherPlatformNote = new PublisherPlatformNote();
    	}

		$publisherPlatformNote->publisherPlatformID = $_POST['publisherPlatformID'];
		$publisherPlatformNote->startYear = $_POST['startYear'];
		$publisherPlatformNote->endYear = $_POST['endYear'];
		$publisherPlatformNote->noteText = $_POST['noteText'];

		try {
			$publisherPlatformNote->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}


        break;


    case 'deletePublisherNote':
		$publisherPlatformNote = new PublisherPlatformNote(new NamedArguments(array('primaryKey' => $_GET['publisherPlatformNoteID'])));

		try {
			$publisherPlatformNote->delete();
		} catch (Exception $e) {
			echo $e->getMessage();
		}
        break;



    case 'submitExternalLogin':


    	//update
    	if ((isset($_POST['externalLoginID'])) && ($_POST['externalLoginID'] != '')){
 			$externalLogin = new ExternalLogin(new NamedArguments(array('primaryKey' => $_POST['externalLoginID'])));
    	}else{
    		$externalLogin = new ExternalLogin();
			$externalLogin->platformID = $_POST['platformID'];
			$externalLogin->publisherPlatformID = $_POST['publisherPlatformID'];
    	}


		$externalLogin->username = $_POST['username'];
		$externalLogin->password = $_POST['password'];
		$externalLogin->loginURL = $_POST['loginURL'];
		$externalLogin->noteText = $_POST['noteText'];


		try {
			$externalLogin->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

        break;




    case 'submitSushiService':


    	//update
    	if ((isset($_POST['sushiServiceID'])) && ($_POST['sushiServiceID'] != '')){
 			$sushiService = new SushiService(new NamedArguments(array('primaryKey' => $_POST['sushiServiceID'])));
    	}else{
    		$sushiService = new SushiService();
			$sushiService->platformID = $_POST['platformID'];
    	}


		$sushiService->serviceURL = $_POST['serviceURL'];
		$sushiService->wsdlURL = $_POST['wsdlURL'];
		$sushiService->requestorID = $_POST['requestorID'];
    $sushiService->requestorKey = $_POST['requestorKey'];
		$sushiService->customerID = $_POST['customerID'];
		$sushiService->security = $_POST['security'];
		$sushiService->reportLayouts = $_POST['reportLayouts'];
		$sushiService->releaseNumber = $_POST['releaseNumber'];
		$sushiService->login = $_POST['login'];
		$sushiService->password = $_POST['password'];
		$sushiService->serviceDayOfMonth = $_POST['serviceDayOfMonth'];
		$sushiService->noteText = $_POST['noteText'];


		try {
			$sushiService->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

        break;


    case 'runSushiService':

    	//update
    	if ((isset($_GET['sushiServiceID'])) && ($_GET['sushiServiceID'] != '')){
 			$sushiService = new SushiService(new NamedArguments(array('primaryKey' => $_GET['sushiServiceID'])));

 			//try to run!
			try {
				$sushiService->runAll();
			} catch (Exception $e) {
				echo $e->getMessage();
			}

    	}

        break;




    case 'testSushiService':

    	//update
    	if ((isset($_GET['sushiServiceID'])) && ($_GET['sushiServiceID'] != '')){
 			$sushiService = new SushiService(new NamedArguments(array('primaryKey' => $_GET['sushiServiceID'])));

 			//try to run!
			try {
				echo $sushiService->runTest();
			} catch (Exception $e) {
				echo $e->getMessage();
			}

    	}

        break;


    case 'deleteExternalLogin':
		$externalLogin = new ExternalLogin(new NamedArguments(array('primaryKey' => $_GET['externalLoginID'])));

		try {
			$externalLogin->delete();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		break;



    case 'submitOrganization':

		if (isset($_GET['publisherPlatformID']) && ($_GET['publisherPlatformID'] != '')){
			$publisherPlatformID = $_GET['publisherPlatformID'];
			$platformID = '';
			$obj = new PublisherPlatform(new NamedArguments(array('primaryKey' => $_GET['publisherPlatformID'])));
		}else{
			$publisherPlatformID = '';
			$platformID = $_GET['platformID'];
			$obj = new Platform(new NamedArguments(array('primaryKey' => $_GET['platformID'])));
		}

		if (isset($_GET['organizationID'])){
			$obj->organizationID = $_GET['organizationID'];
		}

		try {
			$obj->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

        break;




    case 'deleteMonth':

    	if ($_GET['publisherPlatformID'] && $_GET['publisherPlatformID'] != "0"){
			$obj = new PublisherPlatform(new NamedArguments(array('primaryKey' => $_GET['publisherPlatformID'])));
		}else{
			$obj = new Platform(new NamedArguments(array('primaryKey' => $_GET['platformID'])));
		}

		try {
			$obj->deleteMonth($_GET['layoutID'], $_GET['archiveInd'], $_GET['year'], $_GET['month']);
		} catch (Exception $e) {
			echo $e->getMessage();
		}

        break;



    case 'updateOverride':

		$monthlyUsageSummary = new MonthlyUsageSummary(new NamedArguments(array('primaryKey' => $_POST['monthlyUsageSummaryID'])));
		$monthlyUsageSummary->overrideUsageCount = $_POST['overrideUsageCount'];

		try {
			$monthlyUsageSummary->save();
			echo "Override has been updated";
		} catch (Exception $e) {
			echo $e->getMessage();
		}

        break;


    case 'ignoreOutlier':

		$monthlyUsageSummary = new MonthlyUsageSummary(new NamedArguments(array('primaryKey' => $_POST['monthlyUsageSummaryID'])));
		$monthlyUsageSummary->ignoreOutlierInd = '1';

		try {
			$monthlyUsageSummary->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

        break;


    case 'removeOutlier':

		$monthlyUsageSummary = new MonthlyUsageSummary(new NamedArguments(array('primaryKey' => $_POST['monthlyUsageSummaryID'])));
		$monthlyUsageSummary->outlierID = '0';

		try {
			$monthlyUsageSummary->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		break;



    case 'updateYTDOverride':

		$yearlyUsageSummary = new YearlyUsageSummary(new NamedArguments(array('primaryKey' => $_POST['yearlyUsageSummaryID'])));
		$yearlyUsageSummary->$_POST['overrideColumn'] = $_POST['overrideCount'];

		try {
			$yearlyUsageSummary->save();
			echo _("Override has been updated");
		} catch (Exception $e) {
			echo $e->getMessage();
		}

        break;



    case 'addIdentifier':

		$titleIdentifier = new TitleIdentifier();
		$titleIdentifier->titleID = $_POST['titleID'];
		$titleIdentifier->identifier = trim(str_replace ('-','',$_POST['identifier']));
		$titleIdentifier->identifierType = $_POST['identifierType'];


		try {
			$titleIdentifier->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

        break;



    case 'removeIdentifier':

		$titleIdentifier = new TitleIdentifier(new NamedArguments(array('primaryKey' => $_GET['titleIdentifierID'])));

		try {
			$titleIdentifier->delete();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

        break;


    case 'updatePlatformDisplay':

		$platform = new Platform(new NamedArguments(array('primaryKey' => $_GET['updateID'])));
		$platform->reportDisplayName = $_GET['reportDisplayName'];

		try {
			$platform->save();
			echo _("Platform Reporting Display Name has been updated");
		} catch (Exception $e) {
			echo $e->getMessage();
		}

        break;



    case 'updatePlatformDropDown':

		$platform = new Platform(new NamedArguments(array('primaryKey' => $_GET['platformID'])));
		$platform->reportDropDownInd = $_GET['dropDownInd'];

		try {
			$platform->save();
			echo _("Default display list has been updated");
		} catch (Exception $e) {
			echo $e->getMessage();
		}

        break;



    case 'updatePublisherDisplay':

		$publisherPlatform = new PublisherPlatform(new NamedArguments(array('primaryKey' => $_GET['updateID'])));
		$publisherPlatform->reportDisplayName = $_GET['reportDisplayName'];

		try {
			$publisherPlatform->save();
			echo _("Publisher Reporting Display Name has been updated");
		} catch (Exception $e) {
			echo $e->getMessage();
		}

        break;


    case 'updatePublisherDropDown':


		$publisherPlatform = new PublisherPlatform(new NamedArguments(array('primaryKey' => $_GET['publisherPlatformID'])));
		$publisherPlatform->reportDropDownInd = $_GET['dropDownInd'];

		try {
			$publisherPlatform->save();
			echo _("Default display list has been updated");
		} catch (Exception $e) {
			echo $e->getMessage();
		}


        break;


     case 'submitUserData':
		if ($_GET['orgLoginID']){
 			$user = new User(new NamedArguments(array('primaryKey' => $_GET['orgLoginID'])));
		}else{
  			$user = new User();
		}

		$user->loginID		= $_GET['loginID'];
		$user->firstName 	= $_GET['firstName'];
		$user->lastName		= $_GET['lastName'];
		$user->privilegeID	= $_GET['privilegeID'];

		try {
			$user->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}


 		break;



     case 'deleteImportLog':

 		$importLogID = $_GET['importLogID'];

		$importLog = new ImportLog(new NamedArguments(array('primaryKey' => $importLogID)));

		echo "<font color='red'>";
		try {
			$importLog->delete();
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		echo "</font>";

 		break;




     case 'deleteUser':

 		$loginID = $_GET['loginID'];

		$user = new User(new NamedArguments(array('primaryKey' => $loginID)));

		echo "<font color='red'>";
		try {
			$user->delete();
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		echo "</font>";

 		break;


	//used for autocomplete of provider names (from organizations module)
    case 'getOrganizations':

		if (isset($_GET['searchMode'])) $searchMode = $_GET['searchMode']; else $searchMode='';
		if (isset($_GET['limit'])) $limit = $_GET['limit']; else $limit = '';

		$q = $_GET['q'];
		$q = str_replace(" ", "+",$q);
		$q = str_replace("&", "%",$q);

		$platform = new Platform();
		$orgArray = $platform->searchOrganizations($q);

		echo implode("\n", $orgArray);

		break;


	//used to verify organization name isn't already being used as it's added
	case 'getExistingOrganizationName':
		$shortName = $_GET['shortName'];


		$platform = new Platform();
		$orgArray = array();

		$exists = 0;

		foreach ($platform->getOrganizationList() as $orgArray) {
			if (strtoupper($orgArray['name']) == strtoupper($shortName)) {
				$exists = $orgArray['organizationID'];
			}
		}

		echo $exists;

		break;


	default:
       echo _("Function ") . $_REQUEST['function'] . _(" not set up!");
       break;


}



?>
