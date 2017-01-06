<?php
require 'Flight/flight/Flight.php';

include_once '../directory.php';
include_once '../admin/classes/common/NamedArguments.php';
include_once '../admin/classes/common/Object.php';
include_once '../admin/classes/common/DynamicObject.php';
include_once '../admin/classes/common/Utility.php';
include_once '../admin/classes/common/Configuration.php';
include_once '../admin/classes/common/DBService.php';
include_once '../admin/classes/common/DatabaseObject.php';
include_once '../admin/classes/common/Email.php';
include_once '../admin/classes/domain/Resource.php';
include_once '../admin/classes/domain/ResourceType.php';
include_once '../admin/classes/domain/AcquisitionType.php';
include_once '../admin/classes/domain/ResourceFormat.php';
include_once '../admin/classes/domain/NoteType.php';
include_once '../admin/classes/domain/ResourceNote.php';
include_once '../admin/classes/domain/ResourcePayment.php';
include_once '../admin/classes/domain/AdministeringSite.php';
include_once '../admin/classes/domain/ResourceAdministeringSiteLink.php';
include_once '../admin/classes/domain/Status.php';
include_once '../admin/classes/domain/User.php';
include_once '../admin/classes/domain/Workflow.php';
include_once '../admin/classes/domain/Step.php';
include_once '../admin/classes/domain/ResourceStep.php';
include_once '../admin/classes/domain/UserGroup.php';
include_once '../admin/classes/domain/Fund.php';

if (!isAllowed()) {
    header('HTTP/1.0 403 Forbidden');
    echo "Unauthorized IP: " . $_SERVER['REMOTE_ADDR'];
    die();
}

Flight::route('/proposeResource/', function(){

    $user = userExists(Flight::request()->data['user']) ? Flight::request()->data['user'] : 'API';
    $status = new Status();
    $resource = new Resource();
    $resource->createDate = date( 'Y-m-d' );
    $resource->createLoginID = $user;
    $resource->statusID = $status->getIDFromName('progress');
    $resource->updateDate                   = '';
    $resource->updateLoginID                = '';
    $resource->orderNumber                  = '';
    $resource->systemNumber                 = '';
    $resource->userLimitID                  = '';
    $resource->authenticationUserName       = '';
    $resource->authenticationPassword       = '';
    $resource->storageLocationID            = '';
    $resource->registeredIPAddresses        = '';
    $resource->coverageText                 = '';
    $resource->archiveDate                  = '';
    $resource->archiveLoginID               = '';
    $resource->workflowRestartDate          = '';
    $resource->workflowRestartLoginID       = '';
    $resource->currentStartDate             = '';
    $resource->currentEndDate               = '';
    $resource->subscriptionAlertEnabledInd  = '';
    $resource->authenticationTypeID         = '';
    $resource->accessMethodID               = '';
    $resource->recordSetIdentifier          = '';
    $resource->hasOclcHoldings              = '';
    $resource->numberRecordsAvailable       = '';
    $resource->numberRecordsLoaded          = '';
    $resource->bibSourceURL                 = '';
    $resource->catalogingTypeID             = '';
    $resource->catalogingStatusID           = '';
    $resource->mandatoryResource            = '';
    $resource->resourceID                   = null;

    $fieldNames = array("titleText", "descriptionText", "providerText", "resourceURL", "resourceAltURL", "noteText", "resourceTypeID", "resourceFormatID", "acquisitionTypeID");
    foreach ($fieldNames as $fieldName) {
        $resource->$fieldName = Flight::request()->data->$fieldName;
    }
    try {
        $resource->save();
        $resourceID = $resource->primaryKey;
        $resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));

        //add administering site
        if (Flight::request()->data['administeringSiteID']) {
            foreach (Flight::request()->data['administeringSiteID'] as $administeringSiteID) {
                $resourceAdministeringSiteLink = new ResourceAdministeringSiteLink();
                $resourceAdministeringSiteLink->resourceAdministeringSiteLinkID = '';
                $resourceAdministeringSiteLink->resourceID = $resourceID;
                $resourceAdministeringSiteLink->administeringSiteID = $administeringSiteID;
                try {
                    $resourceAdministeringSiteLink->save();
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }
        }

        // add home location
        foreach (array("homeLocationNote" => "Home Location") as $key => $value) {
            if (Flight::request()->data[$key]) {
                $noteType = new NoteType();
                $noteTypeID = $value ? createNoteType($value) : $noteType->getInitialNoteTypeID();
                $resourceNote = new ResourceNote();
                $resourceNote->resourceNoteID   = '';
                $resourceNote->updateLoginID    = $user;
                $resourceNote->updateDate       = date( 'Y-m-d' );
                $resourceNote->noteTypeID       = $noteTypeID;
                $resourceNote->tabName          = 'Product';
                $resourceNote->resourceID       = $resourceID;
                $resourceNote->noteText         = Flight::request()->data[$key];
                $resourceNote->save();
            }
        }

        // General notes
        $noteText = '';
        foreach (array("noteText" => "Note", "providerText" => "Provider", "publicationYear" => "Publication Year or order start date", "edition" => "Edition", "holdLocation" => "Hold location", "patronHold" => "Patron hold") as $key => $value) {
            if (Flight::request()->data[$key]) {
                $noteText .= $value . ": " . Flight::request()->data[$key] . "\n";
            }

        }
        if ($noteText) {
            $noteType = new NoteType();
            $noteTypeID = $noteType->getInitialNoteTypeID();
            $resourceNote = new ResourceNote();
            $resourceNote->resourceNoteID   = '';
            $resourceNote->updateLoginID    = $user;
            $resourceNote->updateDate       = date( 'Y-m-d' );
            $resourceNote->noteTypeID       = $noteTypeID;
            $resourceNote->tabName          = 'Product';
            $resourceNote->resourceID       = $resourceID;
            $resourceNote->noteText         = $noteText;
            $resourceNote->save();
        }

        // add existing license and/or license required
        $noteText = '';
        foreach (array("licenseRequired" => "License required?", "existingLicense" => "Existing License?") as $key => $value) {
            $noteText .= $value . " " . Flight::request()->data[$key] . "\n";
        }
        if ($noteText) {
            $noteTypeID = createNoteType("License Type");
            $resourceNote = new ResourceNote();
            $resourceNote->resourceNoteID   = '';
            $resourceNote->updateLoginID    = $user;
            $resourceNote->updateDate       = date( 'Y-m-d' );
            $resourceNote->noteTypeID       = $noteTypeID;
            $resourceNote->tabName          = 'Acquisitions';
            $resourceNote->resourceID       = $resourceID;
            $resourceNote->noteText         = $noteText;
            $resourceNote->save();
        }

        // add CM Ranking
        foreach (array("CMRanking" => "CM Ranking") as $key => $value) {
            if (Flight::request()->data[$key]) {
                $noteTypeID = createNoteType("CM Ranking");
                $resourceNote = new ResourceNote();
                $resourceNote->resourceNoteID   = '';
                $resourceNote->updateLoginID    = $user;
                $resourceNote->updateDate       = date( 'Y-m-d' );
                $resourceNote->noteTypeID       = $noteTypeID;
                $resourceNote->tabName          = 'Product';
                $resourceNote->resourceID       = $resourceID;
                $resourceNote->noteText         = $value . ": " . Flight::request()->data[$key];
                $resourceNote->save();
            }
        }

        // add CM Important Factor
        $noteText = '';
        foreach (array("ripCode" => "RIP code", "subjectCoverage" => "Subject coverage", "audience" => "Audience", "frequency" => "Frequency and language", "access" => "Access via indexes", "contributingFactors" => "Contributing factors") as $key => $value) {
            if (Flight::request()->data[$key]) {
                $noteText .= $value . ": " . Flight::request()->data[$key] . "\n";
            }
        }
        if ($noteText) {
            $noteTypeID = createNoteType("CM Important Factor");
            $resourceNote = new ResourceNote();
            $resourceNote->resourceNoteID   = '';
            $resourceNote->updateLoginID    = $user;
            $resourceNote->updateDate       = date( 'Y-m-d' );
            $resourceNote->noteTypeID       = $noteTypeID;
            $resourceNote->tabName          = 'Product';
            $resourceNote->resourceID       = $resourceID;
            $resourceNote->noteText         = $noteText;
            $resourceNote->save();
        }

        // add fund and cost
        if (Flight::request()->data['cost'] || Flight::request()->data['fund']) {
            $rp = new ResourcePayment();
            $rp->resourcePaymentID = ''; 
            $rp->selectorLoginID = $user; 
            $rp->year = ''; 
            $rp->subscriptionStartDate = ''; 
            $rp->subscriptionEndDate = ''; 
            $rp->costDetailsID = ''; 
            $rp->costNote = ''; 
            $rp->invoiceNum = ''; 
            $rp->resourceID = $resourceID;
            $rp->paymentAmount = cost_to_integer(Flight::request()->data['cost']);
            $rp->currencyCode = 'USD';
            $rp->orderTypeID = 2;
            $rp->priceTaxExcluded = '';
            $rp->priceTaxIncluded = '';
            $rp->taxRate = '';
            $fundCode = Flight::request()->data['fund'];
			// Check if the fund already exists
            
			$fundObj = new Fund();
			$fundID = $fundObj->getFundIDFromFundCode($fundCode);

			// Add it if not
			if (!$fundID) {
               $fundObj->fundID = '';
			   $fundObj->fundCode = $fundCode;
			   $fundObj->shortName = $fundCode;
               $fundObj->archived = null;
			   $fundObj->save();
			}
			// Create the resourcePayment
			$rp->fundID = $fundID;
            $rp->save();
        }
        

      $resource->enterNewWorkflow();


    } catch (Exception $e) {
        Flight::json(array('error' => $e->getMessage()));
    }
    Flight::json(array('resourceID' => $resourceID));

});

Flight::route('/version/', function() {
    Flight::json(array('API' => 'v1'));
});

Flight::route('/getResourceTypes/', function() {
    $rt = new ResourceType();
    $resourceTypeArray = $rt->allAsArray();
    Flight::json($resourceTypeArray);
});

Flight::route('/getResourceType/@id', function($id) {
   $resourceTypeObj = new ResourceType(new NamedArguments(array('primaryKey' => $id)));
    Flight::json($resourceTypeObj->shortName);
});

Flight::route('/getAcquisitionTypes/', function() {
    $acquisitionTypeObj = new AcquisitionType();
    $acquisitionTypeArray = $acquisitionTypeObj->sortedArray();
    Flight::json($acquisitionTypeArray);
});

Flight::route('/getAcquisitionType/@id', function($id) {
    $acquisitionTypeObj = new AcquisitionType(new NamedArguments(array('primaryKey' => $id)));
    Flight::json($acquisitionTypeObj->shortName);
});

Flight::route('/getResourceFormats/', function() {
   $resourceFormatObj = new ResourceFormat();
   $resourceFormatArray = $resourceFormatObj->sortedArray();
    Flight::json($resourceFormatArray);
});

Flight::route('/getResourceFormat/@id', function($id) {
   $resourceFormatObj = new ResourceFormat(new NamedArguments(array('primaryKey' => $id)));
    Flight::json($resourceFormatObj->shortName);
});

Flight::route('/getAdministeringSites/', function() {
   $as = new AdministeringSite();
   $asArray = $as->allAsArray();
    Flight::json($asArray);
});

Flight::route('/getAdministeringSite/@id', function($id) {
   $as = new AdministeringSite(new NamedArguments(array('primaryKey' => $id)));
    Flight::json($as->shortName);
});


Flight::start();

function isAllowed() {
    $config = new Configuration();

    // If apiAuthorizedIP is not set, don't allow
    if (!$config->settings->apiAuthorizedIP) { return 0; }

    // If apiAuthorizedIP could not be parsed, don't allow
    $authorizedIP = explode(',', $config->settings->apiAuthorizedIP);
    if (!$authorizedIP) { return 0; }

    // If a matching IP has been found, allow
    if (array_filter($authorizedIP, "IpFilter")) { return 1; } 

    return 0;
}

// A matching IP is either a complete IP or the start of one (allowing IP range)
function IpFilter($var) {
    $pos = strpos($_SERVER['REMOTE_ADDR'], $var);
    return $pos === false ? false : true;
}

// Create a note type if it doesn't exist
// Return noteTypeID
function createNoteType($name) {
    $noteType = new NoteType();
    $noteTypeID = $noteType->getNoteTypeIDByName($name);
    if ($noteTypeID) return $noteTypeID;

    $noteType->shortName = $name;
    $noteType->noteTypeID = '';
    $noteType->save();
    return $noteType->noteTypeID;
}

function userExists($user) {
    $createUser = new User(new NamedArguments(array('primaryKey' => $user)));
    return $createUser->loginID ? true : false;
}
?>
