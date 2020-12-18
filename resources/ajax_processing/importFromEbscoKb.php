<?php

/* ------ Class Imports --------- */
/*
 * TODO: Use namespaces
 * This importer need the Organization classes to create new Orgs from EBSCO Kb.
 * But the current classes aren't namespaced and trying to include the classes manually creats conflicts with __autoload()
 * function in directory.php
 *
 * Right now it's using sql inserts instead of the class methods.
 *
 * When the classes are namespaced, the following code can be cleaned up:
 * Either call the namespaced classes directly:
 *
 *      $organization = new \Organization\Admin\Classes\Domain\Organization
 *
 * OR utilize php's "use" function and then call the class directly, new Organization.
 *
 *      use \Organization\Admin\Classes\Domain\Organization
 *      $organization = new Organization;
 *
 * Then use the class methods to deal with organizations
 */


/* ------ Filter inputs --------- */
// TODO:: switch this to filter_input_array instead
// All
$importType = filter_input(INPUT_POST,'importType', FILTER_SANITIZE_STRING);
$resourceStatus = filter_input(INPUT_POST,'status', FILTER_SANITIZE_STRING);
$organizationId = filter_input(INPUT_POST,'organizationId', FILTER_SANITIZE_NUMBER_INT);
$resourceFormatId = filter_input(INPUT_POST,'resourceFormatId', FILTER_SANITIZE_NUMBER_INT);
$acquisitionTypeId = filter_input(INPUT_POST,'acquisitionTypeId', FILTER_SANITIZE_NUMBER_INT);
$noteText = filter_input(INPUT_POST,'noteText', FILTER_SANITIZE_STRING);
$providerText = trim(filter_input(INPUT_POST,'providerText', FILTER_SANITIZE_STRING));
$setAsSelected = filter_input(INPUT_POST,'setAsSelected', FILTER_VALIDATE_BOOLEAN);

// Package
$packageId = filter_input(INPUT_POST,'packageId', FILTER_SANITIZE_NUMBER_INT);
$vendorId = filter_input(INPUT_POST,'vendorId', FILTER_SANITIZE_NUMBER_INT);
$providerOption = filter_input(INPUT_POST,'providerOption', FILTER_SANITIZE_STRING);
$parentOrChild = filter_input(INPUT_POST,'providerParentOrChild', FILTER_SANITIZE_STRING);
$resourceTypeId = filter_input(INPUT_POST,'resourceTypeId', FILTER_SANITIZE_NUMBER_INT);
$titleFilter = filter_input(INPUT_POST,'titleFilter', FILTER_SANITIZE_STRING);
$aliasTypeId = filter_input(INPUT_POST,'aliasTypeId', FILTER_SANITIZE_NUMBER_INT);
$workflowOption = filter_input(INPUT_POST, 'workflowOption', FILTER_SANITIZE_STRING);

// Title
$titleId = filter_input(INPUT_POST,'titleId', FILTER_SANITIZE_NUMBER_INT);

// Batch
$selection = filter_input(INPUT_POST,'selection', FILTER_SANITIZE_NUMBER_INT);
$newWorkflow = filter_input(INPUT_POST,'newWorkflow', FILTER_VALIDATE_BOOLEAN);
$offset = filter_input(INPUT_POST, 'offset', FILTER_SANITIZE_NUMBER_INT);
$parentId = filter_input(INPUT_POST, 'parentId', FILTER_SANITIZE_NUMBER_INT);


/* ------ Additional setup --------- */
// Is the org module used
$config = new Configuration;
$orgModule = $config->settings->organizationsModule == 'Y';


//determine status id for all the imports
$status = new Status();
$statusId = $status->getIDFromName($resourceStatus);

// Set the organization role as provider
$organizationRole = new OrganizationRole();
$organizationRoleId = $organizationRole->getProviderID();

// cache for subjects so we don't have to keep pinging the DB to check if an org exists
$generalSubjectCache = [];
$generalDetailIdCache = [];

// cache for resource types so we don't have to keep pinging the DB to check if an org exists
$resourceTypeCache = [];

// Setup the ebsco connection
$ebscoKb = EbscoKbService::getInstance();

/* ------ Check user input errors --------- */
$errors = [];
function create_error($target, $text, $context = ''){
    return ['target' => $target, 'text' => _($text), 'context' => $context];
}
function send_errors($errors){
    header('Content-type: application/json');
    echo json_encode(['type' => 'error', 'error' => $errors]);
    exit;
}

if(empty($resourceFormatId)) {
    $errors[] = create_error('resourceTypeId', 'No Resource Type selected');
}
if(empty($acquisitionTypeId)) {
    $errors[] = create_error('acquisitionTypeId', 'No Acquisition Type selected');
}
if(empty($importType)){
    $errors[] = create_error('general', 'No import type set');
}
if(empty($statusId)) {
    $errors[] = create_error('general', 'Status not found');
}

if($importType == 'batch'){

    // Is the package id set
    if(!isset($packageId)) {
        $errors[] = create_error('general', 'No package ID found');
    }
    // Is the vendor id set
    if(!isset($vendorId)) {
        $errors[] = create_error('general', 'No vendor ID found');
    }
    // Is the selection set
    if(!isset($selection)) {
        $errors[] = create_error('general', 'Selection not identified');
    }
    // Is the offset set
    if(!isset($offset)){
        $errors[] = create_error('general', 'No offset array provided');
    }
    // Make sure the parent exists
    if(!empty($parentId)){
        try {
            new Resource(new NamedArguments(['primaryKey' => $parentId]));
        } catch (Exception $e){
            $errors[] = create_error('general', 'Parent package does not exist');
        }
    }
}

if($importType == 'package'){
    // Is the package id set
    if(!isset($packageId)) {
        $errors[] = create_error('general', 'No package ID found');
    }
    // Is the vendor id set
    if(!isset($vendorId)) {
        $errors[] = create_error('general', 'No vendor ID found');
    }
    // Is the providerOption set
    if(empty($organizationId) && empty($providerOption)){
        $errors[] = create_error('providerOption', 'Please select a provider import option');
    }
    // Is the alias type option set
    if($providerOption == 'alias' && empty($aliasTypeId)){
        $errors[] = create_error('aliasType', 'Please select an alias type');
    }
    // Is the organization ID set if adding an alias or parent/child relationship
    if(($providerOption == 'parentChild' || $providerOption == 'alias') && empty($organizationId)){
        $errors[] = create_error('organization', 'You must select an organization');
    }
    // alias & parent/child require the org module
    if(($providerOption == 'parentChild' || $providerOption == 'alias') && !$orgModule){
        $errors[] = create_error('organization', 'The organization module is not in use. You cannot import an alias or parent child relationship');
    }
    // If the provider option is parentChild, is the option selected
    if($providerOption == 'parentChild' && empty($parentOrChild)){
        $errors[] = create_error('parentOrChild', 'You must select either a parent or child relationship');
    }
    // Is the title filter set
    if(empty($titleFilter)){
        $errors[] = create_error('titleFilter', 'You must select which set of titles to import');
    }
    // Is the workflow option set
    if($titleFilter != 'none' && empty($workflowOption)){
        $errors[] = create_error('workflowOption', 'You must select if you want to start a workflow for all titles or only the package');
    }
}

if($importType == 'title'){
    // Is the title id set
    if(!isset($titleId)) {
        $errors[] = create_error('general', 'No title ID found');
    }
}

// Send errors to be rendered
if(!empty($errors)){
    send_errors($errors);
}

/* ------ Batch Import --------- */
if($importType == 'batch'){
    $count = EbscoKbService::$defaultSearchParameters['count'];
    $ebscoKb->createQuery([
        'selection' => $selection,
        'count' => $count,
        'offset' => $offset,
        'type' => 'titles',
        'vendorId' => $vendorId,
        'packageId' => $packageId,
    ]);

    // can we access the packageTitles via Ebsco KB
    // using attempts because sometimes kb times out
    $total_attempts = 5;
    $attempts = 0;
    $attempt_error = '';
    $packageTitles = [];
    do {
        $ebscoKb->execute();
        if(!empty($ebscoKb->error)){
            $attempts++;
            $attempt_error = $ebscoKb->error;
            sleep(1);
            continue;
        }
        $packageTitles = $ebscoKb->results();
        break;
    } while($attempts < $total_attempts);
    if(empty($packageTitles)){
        send_errors([create_error('general', 'Could not load package titles', $attempt_error)]);
    }

    // load the full title info from ebsco
    $titleErrors = [];
    foreach($packageTitles as $title){
        try{
            $title = $ebscoKb->getTitle($title->titleId);
            importTitle($title, $parentId);
        } catch (Exception $e) {
            $titleErrors[] = create_error('general', 'Error importing title '.$title->titleName.' (KbID #'.$title->titleId.') for batch import', $e->getMessage());
        }
    }

    header('Content-type: application/json');
    echo json_encode([
        'complete' => true,
        'titleErrors' => $titleErrors,
    ]);
    exit;

}

/* ------ Title import --------- */
if($importType == 'title'){
    // can we access the package via Ebsco KB
    $title = $ebscoKb->getTitle($titleId);
    if(!empty($ebscoKb->error)){
        send_errors([create_error('general', 'Could not get title from ebsco', $ebscoKb->error)]);
    }
    if($setAsSelected) {
        $ebscoKb->setTitle($vendorId, $packageId, $titleId, $selected);
    }
    $newWorkflow = true;
    $resource = importTitle($title);
    header('Content-type: application/json');
    echo json_encode([
        'type' => 'redirect',
        'status' => $newWorkflow ? 'progress' : 'save',
        'resourceId' => $resource->primaryKey
    ]);
    exit;
}

/* ------ Package import --------- */
if($importType == 'package') {

    // can we access the package via Ebsco KB
    $package = $ebscoKb->getPackage($vendorId, $packageId);
    if(!empty($ebscoKb->error)){
        send_errors([create_error('general', 'Could not get package from ebsco', $ebscoKb->error)]);
    }
    if($setAsSelected) {
        $ebscoKb->setPackage($vendorId, $packageId, $selected);
    }

    // setup organization
    switch($providerOption){
        case 'alias':
            addOrganizationAlias($organizationId, $aliasTypeId, $package->vendorId, $package->vendorName);
            break;
        case 'parentChild':
            // create a record to attached it to the provided organization ID
            $providedOrganizationId = $organizationId;
            $ebscoOrganizationId = createOrUpdateOrganization($package->vendorId, $package->vendorName);
            // Set which is parent/child
            $parentOrganizationId = $parentOrChild == 'parent' ? $ebscoOrganizationId : $providedOrganizationId;
            $childOrganizationId = $parentOrChild == 'parent' ? $providedOrganizationId : $ebscoOrganizationId;
            addOrganizationRelationship($parentOrganizationId, $childOrganizationId);
            // Set the import id to the ebsco kb vendor
            $organizationId = $ebscoOrganizationId;
            break;
        case 'import':
            $organizationId = createOrUpdateOrganization($package->vendorId, $package->vendorName);
            break;
        default:
            break;
    }

    $resource = importPackage($package);
    $count = EbscoKbService::$defaultSearchParameters['count'];
    switch($titleFilter){
        case 'all':
            $totalTitles = $package->titleCount;
            $selection = 0;
            break;
        case 'selected':
            $totalTitles = $package->selectedCount;
            $selection = 1;
            break;
        default:
            header('Content-type: application/json');
            echo json_encode([
                'type' => 'redirect',
                'status' => 'progress',
                'resourceId' => $resource->primaryKey
            ]);
            exit;
    }

    $newWorkflow = $workflowOption == 'all' ? true : false;

    // generate section batchers
    $batchAmount = $count;
    while(ceil($totalTitles/$batchAmount) > 10){
        $batchAmount += $count;
    }
    $batchers = generateBatchers($resource->primaryKey, $totalTitles,$batchAmount,$count);

    header('Content-type: application/json');
    echo json_encode(['type' => 'batchers', 'batchers' => $batchers, 'resourceId' => $resource->primaryKey]);
    exit;
}

/* ------ Functions --------- */

function generateBatchers($parentId, $totalTitles, $batchAmount, $maxReturn){
    global $newWorkflow, $organizationId, $resourceFormatId, $acquisitionTypeId, $selection,
           $package, $resourceStatus;

    $batchers = [];
    $totalBatchersNeeded = ceil($totalTitles/$batchAmount);
    $inc = ceil($batchAmount / $maxReturn);

    for($i=1; $i<=$totalBatchersNeeded; $i++){
        $x = ($i*$inc)-($inc-1);
        $y = $x+($inc-1);
        while($y*$maxReturn >= $totalTitles + $maxReturn){
            $y--;
        }
        $batchers[] = [
            'batchNumber' => $i,
            'batchStart' => ($x * $maxReturn) - $maxReturn + 1,
            'batchEnd' => $y * $maxReturn > $totalTitles ? $totalTitles : $y * $maxReturn,
            'importType' => 'batch',
            'batchAmount' => $batchAmount,
            'offsets' => range($x,$y),
            'parentId' => $parentId,
            'newWorkflow' => $newWorkflow,
            'organizationId' => $organizationId,
            'resourceFormatId' => $resourceFormatId,
            'acquisitionTypeId' => $acquisitionTypeId,
            'selection' => $selection,
            'packageId' => $package->packageId,
            'vendorId' => $package->vendorId,
            'resourceStatus' => $resourceStatus,
        ];
    }
    return $batchers;
}

function importPackage($package){

    global $loginID,
           $statusId,
           $acquisitionTypeId,
           $resourceFormatId,
           $resourceTypeId,
           $providerText,
           $noteText,
           $ebscoKb;

    $resource = new Resource();
    $existingResource = $resource->getResourceByEbscoKbId($package->packageId);
    if ($existingResource){
        //get this resource
        $resource = $existingResource;
    } else {
        //set up new resource
        $resource->createLoginID = $loginID;
        $resource->createDate = date( 'Y-m-d' );
        $resource->updateLoginID = '';
        $resource->updateDate = '';
    }
    if($resourceTypeId == '-1'){
        $resource->resourceTypeID = getResourceTypeId($package->contentType);
    } else {
        $resource->resourceTypeID = $resourceTypeId;
    }
    $resource->resourceFormatID = $resourceFormatId;
    $resource->acquisitionTypeID = $acquisitionTypeId;
    $resource->titleText = $package->packageName;
    $resource->statusID	= $statusId;
    $resource->orderNumber = '';
    $resource->systemNumber = '';
    $resource->userLimitID = '';
    $resource->authenticationUserName = '';
    $resource->authenticationPassword = '';
    $resource->storageLocationID = '';
    $resource->registeredIPAddresses = '';
    $resource->providerText	= $providerText;
    $resource->ebscoKbID = $package->packageId;
    try {
        $resource->save();
    } catch (Exception $e) {
        send_errors([create_error('general', 'Could not import package', $e->getMessage())]);
    }

    addResourceAcquisition($resource);
    addProvider($resource);
    if(!empty($noteText)){
        addNote($resource, 'Product', $noteText);
    }

    if(empty($resource->getCurrentWorkflowID())){
        // Create the default order
        $resource->enterNewWorkflow();
    }
    $ebscoKb->setPackage($package->vendorId, $package->packageId, true);
    return $resource;

}

function importTitle($title, $parentId = null){

    global $loginID,
           $statusId,
           $organizationId,
           $resourceStatus,
           $newWorkflow,
           $acquisitionTypeId,
           $resourceFormatId,
           $providerText,
           $noteText;

    $resource = new Resource();
    $existingResource = $resource->getResourceByEbscoKbId($title->titleId);
    if ($existingResource){
        //get this resource
        $resource = $existingResource;
    } else {
        //set up new resource
        $resource->createLoginID = $loginID;
        $resource->createDate = date( 'Y-m-d' );
        $resource->updateLoginID = '';
        $resource->updateDate = '';
    }

    $resource->resourceTypeID = getResourceTypeId($title->pubType);
    $resource->resourceFormatID = $resourceFormatId;
    $resource->acquisitionTypeID = $acquisitionTypeId;
    $resource->titleText = $title->titleName;
    $resource->descriptionText = $title->description;
    $resource->statusID	= $statusId;
    $resource->orderNumber = '';
    $resource->systemNumber = '';
    $resource->userLimitID = '';
    $resource->authenticationUserName = '';
    $resource->authenticationPassword = '';
    $resource->storageLocationID = '';
    $resource->registeredIPAddresses = '';
    $resource->providerText	= $providerText;
    $resource->ebscoKbID = $title->titleId;

    $urlsByCoverage = $title->sortUrlsByCoverage();
    $resource->resourceURL = empty($urlsByCoverage[0]) ? '' : $urlsByCoverage[0]['url'];
    $resource->resourceAltURL = empty($urlsByCoverage[1]) ? '' : $urlsByCoverage[1]['url'];
    // If any additional urls, add to the notes field
    $additionalUrls = null;
    if(!empty($urlsByCoverage[2])){
        $additionalUrls = "Additional Urls\n\n".implode("\n\n", array_map(function($u){
            return $u['url'];
        }, array_slice($urlsByCoverage,2)));
    }

    try {
        $resource->save();
    } catch (Exception $e) {
        send_errors([create_error('general', 'Could not import title', $e->getMessage())]);
    }

    $resourceAcquisition = addResourceAcquisition($resource, implode('; ', $title->coverageTextArray));
    $resource->setIsbnOrIssn($title->isxns);
    addProvider($resource);
    addSubjects($resource, $title->subjects);

    //add notes
    if ($providerText && !$organizationId && $resourceStatus == 'progress') {
        addNote($resource, 'Product', "Provider:  $providerText");
    }
    if(isset($additionalUrls)) {
        addNote($resourceAcquisition, 'Access', $additionalUrls);
    }
    if(!empty($noteText)){
        addNote($resource, 'Product', $noteText);
    }

    if(!empty($parentId)){
        $parents = $resource->getParentResources();
        $parentIds = array_map(function($parent){
            return $parent->relatedResourceID;
        }, $parents);
        if(!in_array($parentId, $parentIds)){
            $resourceRelationship = new ResourceRelationship();
            $resourceRelationship->resourceID = $resource->primaryKey;
            $resourceRelationship->relatedResourceID = $parentId;
            $resourceRelationship->relationshipTypeID = '1';  //hardcoded because we're only allowing parent relationships
            try {
                $resourceRelationship->save();
            } catch (Exception $e) {
                send_errors([create_error('general', 'Could not import resource relationship', $e->getMessage())]);
            }
        }
    }

    // Workflow
    if ($newWorkflow && empty($resource->getCurrentWorkflowID())){
        // Create the default order
        $resource->enterNewWorkflow();
    }
    return $resource;

}

function addProvider(Resource $resource){
    global $organizationId, $organizationRoleId;

    if ($organizationId && $organizationRoleId){

        // create an original list of organzational links
        $linkedOrganizations = array_map(function($org){
            return ['organizationId' => $org['organizationID'], 'organizationRoleId' => $org['organizationRoleID']];
        }, $resource->getOrganizationArray());
        $linkedOrganizations[] = ['organizationId' => $organizationId, 'organizationRoleId' => $organizationRoleId];
        $linkedOrganizations = array_map("unserialize", array_unique(array_map("serialize", $linkedOrganizations)));
        // Remove old links
        $resource->removeResourceOrganizations();
        foreach($linkedOrganizations as $link){
            $resourceOrganizationLink = new ResourceOrganizationLink();
            $resourceOrganizationLink->resourceID = $resource->primaryKey;
            $resourceOrganizationLink->organizationID = $link['organizationId'];
            $resourceOrganizationLink->organizationRoleID = $link['organizationRoleId'];
            try {
                $resourceOrganizationLink->save();
            } catch (Exception $e) {
                send_errors([create_error('general', 'Could not add resource provider', $e->getMessage())]);
            }
        }
    }
}

function addNote($entity, $tab = 'Product', $text = ''){

    global $loginID;

    // check if note exists
    if ($tab === 'Product') {
        $existingNotes = $entity->getNotes();
    } else {
        $existingNotes = $entity->getNotes($tab);
    }
    $existingNoteText =  array_map(function($note){ return $note->noteText; }, $existingNotes);

    if(!in_array($text, $existingNoteText)) {
        $noteType = new NoteType();
        $resourceNote = new ResourceNote();
        $resourceNote->resourceNoteID = '';
        $resourceNote->updateLoginID = $loginID;
        $resourceNote->updateDate = date('Y-m-d');
        $resourceNote->noteTypeID = $noteType->getInitialNoteTypeID();
        $resourceNote->tabName = $tab;
        $resourceNote->entityID = $entity->primaryKey;
        $resourceNote->noteText = $text;
        try {
            $resourceNote->save();
        } catch (Exception $e) {
            send_errors([create_error('general', 'Could not add resource note', $e->getMessage())]);
        }
    }
}

function addResourceAcquisition($resource, $coverageText = ''){
    $resourceAcquisition = new ResourceAcquisition();
    $resourceAcquisition->resourceID = $resource->primaryKey;
    $resourceAcquisition->subscriptionStartDate = date("Y-m-d");
    $resourceAcquisition->subscriptionEndDate = date("Y-m-d");
    $resourceAcquisition->coverageText = $coverageText;
    $resourceAcquisition->save();
    return $resourceAcquisition;
}

function addSubjects($resource, $subjects){
    foreach($subjects as $subject){
        $generalSubjectId = getGeneralSubjectId($subject);
        if(empty($generalSubjectId)){
            continue;
        }

        $generalDetailId = getGeneralDetailId($generalSubjectId);
        if(empty($generalDetailId)){
            continue;
        }
        $resourceSubject = new ResourceSubject();
        $resourceSubject->resourceID = $resource->primaryKey;
        $resourceSubject->generalDetailSubjectLinkID = $generalDetailId;

        // Check to see if the subject has already been associated with the resouce.  If not then save.
        if ($resourceSubject->duplicateCheck($resource->primaryKey, $generalDetailId) == 0) {
            $resourceSubject->save();
        }
    }
}

function getGeneralDetailId($generalSubjectId){
    global $generalDetailIdCache;

    // Search for the cached key
    $cachedKey = array_search($generalSubjectId, $generalDetailIdCache);
    if($cachedKey) {
        return $cachedKey;
    }

    // If it doesn't exist, create or get the generalDetailId
    $generalDetail = new GeneralDetailSubjectLink();
    $generalDetailId = $generalDetail->getGeneralDetailID($generalSubjectId, -1);
    if(empty($generalDetailId)){
        // create a new resource type
        $generalDetail->generalSubjectID = $generalSubjectId;
        $generalDetail->detailedSubjectID = -1;
        $generalDetail->save();
        $generalDetailId = $generalDetail->primaryKey;
    }
    // add the key and name to the cache
    $generalDetailIdCache[$generalDetailId] = $generalSubjectId;
    return $generalDetailId;
}

function getGeneralSubjectId($subject){
    global $generalSubjectCache;

    // Search for the cached key
    $cachedKey = array_search($subject, $generalSubjectCache);
    if($cachedKey) {
        return $cachedKey;
    }

    // If it doesn't exist, create or get the subject id
    $generalSubject = new GeneralSubject();
    try{
        $generalSubjectId = $generalSubject->getGeneralSubjectIDByName($subject);
    } catch(Exception $e){
        send_errors([create_error('general', 'Error checking subject'.$subject, $e->getMessage())]);
    }
    if(empty($generalSubjectId)){
        // create a new resource type
        $generalSubject->shortName = $subject;
        try{
            $generalSubjectId = $generalSubject->getGeneralSubjectIDByName($subject);
        } catch(Exception $e){
            send_errors([create_error('general', 'Error saving subject', $e->getMessage())]);
        }
        $generalSubject->save();
        $generalSubjectId = $generalSubject->primaryKey;
    }
    // add the key and name to the cache
    $generalSubjectCache[$generalSubjectId] = $subject;
    return $generalSubjectId;
}

function getResourceTypeId($typeName){

    global $resourceTypeCache;

    // Search for the cached key
    $cachedKey = array_search($typeName, $resourceTypeCache);
    if($cachedKey) {
        return $cachedKey;
    }

    // If it doesn't exist, create or get the resource type id
    $resourceType = new ResourceType();
    $resourceTypeId = $resourceType->getResourceTypeIDByName($typeName);
    if(empty($resourceTypeId)){
        // create a new resource type
        $resourceType->shortName = $typeName;
        try {
            $resourceType->save();
        } catch (Exception $e) {
            send_errors([create_error('general', 'Could not save new resource type', $e->getMessage())]);
        }
        $resourceTypeId = $resourceType->primaryKey;
    }
    // add the key and name to the cache
    $resourceTypeCache[$resourceTypeId] = $typeName;
    return $resourceTypeId;
}


// TODO: Update to use Organization domain classes instead of sql calls, see note above
function createOrUpdateOrganization($ebscoKbId, $organizationName){
    global $loginID, $config, $orgModule, $dbService;


    if($orgModule){
        $orgDbName = $config->settings->organizationsDatabaseName;
        $dbService = new DBService;

        // search for existing matches
        $selectSql = "SELECT organizationID
			FROM $orgDbName.Organization
			WHERE ebscoKbID = $ebscoKbId
			OR `name` = '$organizationName'
			LIMIT 0,1";
        try {
            $result = $dbService->query($selectSql);
        } catch (Exception $e) {
            send_errors([create_error('general', 'DB Error when searching for Organization matches via EBSCO Kb ID', $e->getMessage())]);
        }
        $result = $result->fetch_assoc();


        if(empty($result)){
            $now = date( 'Y-m-d H:i:s' );
            $insert = "INSERT INTO $orgDbName.Organization
              (createDate, createLoginID, updateDate, updateLoginID, `name`, ebscoKbID)
              VALUES('$now','$loginID','','','$organizationName',$ebscoKbId)";
            try {
                $dbService->query($insert);
            } catch (Exception $e) {
                send_errors([create_error('general', 'Could not create new organization', $e->getMessage())]);
            }
            return $dbService->db->insert_id;
        } else {
            return $result['organizationID'];
        }
    } else {
        $organization = new Organization;
        $existingOrg = $organization->getOrganizationByEbscoKbId($ebscoKbId);

        // Search for a matching resource
        if ($existingOrg){
            //get this resource
            $organization = $existingOrg;
        } else {
            $existingOrg = $organization->getOrganizationIDByName($organizationName);
            if($existingOrg){
                $organization = new Organization(new NamedArguments(array('primaryKey' => $existingOrg)));
            } else {
                //set up new resource
                $organization->createLoginID 		= $loginID;
                $organization->createDate			= date( 'Y-m-d H:i:s' );
                $organization->updateLoginID 		= '';
                $organization->updateDate			= '';
            }
        }
        $organization->ebscoKbID = $ebscoKbId;
        $organization->name = $organizationName;
        try {
            $organization->save();
        } catch (Exception $e) {
            send_errors([create_error('general', 'Could not create or update organization', $e->getMessage())]);
        }
        return $organization->primaryKey;
    }

}

// TODO: Update to use Organization domain classes instead of sql calls, see note above
function addOrganizationAlias($organizationId, $aliasTypeId, $ebscoKbId, $alias){
    global $config;
    $orgDbName = $config->settings->organizationsDatabaseName;
    $dbService = new DBService;

    // Check for matching aliases first
    $selectSql = "SELECT * 
      FROM $orgDbName.Alias 
      WHERE organizationID = $organizationId 
      AND aliasTypeID = $aliasTypeId
      AND `name` = '$alias'";
    try {
        $result = $dbService->query($selectSql);
    } catch (Exception $e) {
        send_errors([create_error('general', 'DB Error when searching for Organization alias matches', $e->getMessage())]);
    }

    $result = $result->fetch_assoc();
    if(!empty($matches)){
        return;
    }

    // Insert the alias
    $insert = "INSERT INTO $orgDbName.Alias
      (organizationID, aliasTypeID, `name`)
      VALUES ($organizationId, $aliasTypeId, '$alias')";
    try {
        $dbService->query($insert);
    } catch (Exception $e) {
        send_errors([create_error('general', 'Could not save organization alias', $e->getMessage())]);
    }

    // update with the ebscoKbId
    $update = "UPDATE $orgDbName.Organization SET ebscoKbID = $ebscoKbId WHERE organizationID = $organizationId";
    try {
        $dbService->query($update);
    } catch (Exception $e) {
        send_errors([create_error('general', 'Could not update the organization with the EBSCO Kb ID', $e->getMessage())]);
    }
}

// TODO: Update to use Organization domain classes instead of sql calls, see note above
function addOrganizationRelationship($parentOrganizationId, $childOrganizationId){
    global $config;
    $orgDbName = $config->settings->organizationsDatabaseName;
    $dbService = new DBService;

    // Delete any existing parents from the child
    $deleteParentSql = "DELETE FROM $orgDbName.OrganizationHierarchy WHERE organizationID = $childOrganizationId";
    try {
        $dbService->query($deleteParentSql);
    } catch (Exception $e) {
        send_errors([create_error('general', 'Could not delete existing parents', $e->getMessage())]);
    }

    // Insert the new relationship
    $insert = $insert = "INSERT INTO $orgDbName.OrganizationHierarchy
          (organizationID, parentOrganizationID)
          VALUES ($childOrganizationId, $parentOrganizationId)";
    try {
        $dbService->query($insert);
    } catch (Exception $e) {
        send_errors([create_error('general', 'Could not set new organization relationship', $e->getMessage())]);
    }
}
