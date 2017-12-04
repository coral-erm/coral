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
$titleId = filter_input(INPUT_POST,'titleId', FILTER_SANITIZE_NUMBER_INT);
$packageId = filter_input(INPUT_POST,'packageId', FILTER_SANITIZE_NUMBER_INT);
$vendorId = filter_input(INPUT_POST,'vendorId', FILTER_SANITIZE_NUMBER_INT);
$importType = filter_input(INPUT_POST,'importType', FILTER_SANITIZE_STRING);
$resourceStatus = filter_input(INPUT_POST,'status', FILTER_SANITIZE_STRING);
$organizationId = filter_input(INPUT_POST,'organizationId', FILTER_SANITIZE_NUMBER_INT);
$providerOption = filter_input(INPUT_POST,'providerOption', FILTER_SANITIZE_STRING);
$parentOrChild = filter_input(INPUT_POST,'providerParentOrChild', FILTER_SANITIZE_STRING);
$resourceFormatId = filter_input(INPUT_POST,'resourceFormatId', FILTER_SANITIZE_NUMBER_INT);
$acquisitionTypeId = filter_input(INPUT_POST,'acquisitionTypeId', FILTER_SANITIZE_NUMBER_INT);
$resourceTypeId = filter_input(INPUT_POST,'resourceTypeId', FILTER_SANITIZE_NUMBER_INT);
$noteText = filter_input(INPUT_POST,'noteText', FILTER_SANITIZE_STRING);
$providerText = trim(filter_input(INPUT_POST,'providerText', FILTER_SANITIZE_STRING));
$titleFilter = filter_input(INPUT_POST,'titleFilter', FILTER_SANITIZE_STRING);
$aliasTypeId = filter_input(INPUT_POST,'aliasTypeId', FILTER_SANITIZE_NUMBER_INT);
$workflowOption = filter_input(INPUT_POST, 'workflowOption', FILTER_SANITIZE_STRING);

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
$subjectCache = [];

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
    echo json_encode(['error' => $errors]);
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

if($importType == 'package'){
    // Is the package id set
    if(empty($packageId)) {
        $errors[] = create_error('general', 'No package ID found');
    }
    // Is the vendor id set
    if(empty($vendorId)) {
        $errors[] = create_error('general', 'No vendor ID found');
    }
    // can we access the package via Ebsco KB
    try {
        $package = $ebscoKb->getPackage($vendorId, $packageId);
    } catch (Exception $e) {
        $errors[] = create_error('general', 'Could not get package from ebsco', $e->getMessage());
    }

    // Is the providerOption set
    if(empty($organizationId) && empty($providerOption)){
        $errors[] = create_error('providerOption', 'Please select a provider import option');
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
    if(empty($titleId)) {
        $errors[] = create_error('general', 'No title ID found');
    }
    // can we access the package via Ebsco KB
    try {
        $title = $ebscoKb->getTitle($titleId);
    } catch (Exception $e) {
        $errors[] = create_error('general', 'Could not get title from EBSCO Kb', $e->getMessage());
    }
}

// Send errors to be rendered
if(!empty($errors)){
    send_errors($errors);
}

/* ------ Setup organization --------- */
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

/* ------ Title import --------- */
if($importType == 'title'){
    $title = $ebscoKb->getTitle($titleId);
    $newWorkflow = true;
    $resource = importTitle($title);
    echo $resource->primaryKey;
    exit;
}

/* ------ Package import --------- */
if($importType == 'package' && isset($package)) {

    $resource = importPackage($package);
    $count = EbscoKbService::$defaultSearchParameters['count'];
    switch($titleFilter){
        case 'all':
            $totalTitles = $package->selectedCount;
            $selection = 0;
            break;
        case 'selected':
            $totalTitles = $package->titleCount;
            $selection = 1;
            break;
        default:
            echo $resource->primaryKey;
            exit;
    }

    $newWorkflow = $workflowOption == 'all' ? true : false;
    for($i = 1; $i <= ceil($totalTitles / $count); $i++){
        $ebscoKb->createQuery([
            'vendorId' => $package->vendorId,
            'packageId' => $package->packageId,
            'count' => $count,
            'selection' => $selection,
            'type' => 'titles'
        ]);
        $ebscoKb->execute();
        $packageTitles = $ebscoKb->results();
        foreach($packageTitles as $title){
            $title = $ebscoKb->getTitle($title->titleId);
            importTitle($title, $resource->primaryKey);
        }
    }
    echo $resource->primaryKey;
    exit;
}



/* --------------------
    Functions
*/

function dd($item, $title){
    echo "<h1>$title</h1>";
    echo '<pre>';
    echo print_r($item);
    echo '</pre>';
}

function importPackage($package){

    global $loginID,
           $statusId,
           $acquisitionTypeId,
           $resourceFormatId,
           $resourceTypeId,
           $providerText;

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

    addProvider($resource);
    addNotes($resource);

    if(empty($resource->getCurrentWorkflowID())){
        $resource->enterNewWorkflow();
    }
    return $resource;

}

function importTitle($title, $parentId = null){

    global $loginID,
           $statusId,
           $newWorkflow,
           $acquisitionTypeId,
           $resourceFormatId,
           $providerText;

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
    $resource->coverageText = implode('; ', $title->coverageTextArray);
    $resource->ebscoKbID = $title->titleId;

    $urlsByCoverage = $title->sortUrlsByCoverage();
    $resource->resourceURL = empty($urlsByCoverage[0]) ? '' : $urlsByCoverage[0]['url'];
    $resource->resourceAltURL = empty($urlsByCoverage[1]) ? '' : $urlsByCoverage[1]['url'];

    try {
        $resource->save();
    } catch (Exception $e) {
        send_errors([create_error('general', 'Could not import title', $e->getMessage())]);
    }
    $resource->setIsbnOrIssn($title->isxns);

    addProvider($resource);
    addNotes($resource);

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
    if ($newWorkflow && empty($resource->getCurrentWorkflowID())){
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

function addNotes(Resource $resource){

    global $loginID, $resourceStatus, $noteText, $providerText, $organizationId;
    //add notes
    if (($noteText) || (($providerText) && (!$organizationId))){

        $noteText = $resourceStatus == 'progress' ? "Provider:  $providerText\n\n$noteText" : $noteText;
        //first, remove existing notes in case this was saved before
        $existingNotes = $resource->getNotes();

        // If the note text doesn't already exist, add it
        if(!in_array($noteText, array_map(function($note){
            return $note['noteText'];
        }, $existingNotes))) {

            //this is just to figure out what the creator entered note type ID is
            $noteType = new NoteType();

            $resourceNote = new ResourceNote();
            $resourceNote->resourceNoteID = '';
            $resourceNote->updateLoginID = $loginID;
            $resourceNote->updateDate = date( 'Y-m-d' );
            $resourceNote->noteTypeID = $noteType->getInitialNoteTypeID();
            $resourceNote->tabName = 'Product';
            $resourceNote->resourceID = $resource->primaryKey;
            $resourceNote->noteText = $noteText;
            try {
                $resourceNote->save();
            } catch (Exception $e) {
                send_errors([create_error('general', 'Could not add resource note', $e->getMessage())]);
            }
        }

    }
}

function addSubjects($resource, $subjects){
    // TODO
}

function getSubjectId($subject){

    // TODO, would be used by add subjects

    global $subjectCache;

    // Search for the cached key
    $cachedKey = array_search($subject, $subjectCache);
    if($cachedKey) {
        return $cachedKey;
    }

    // If it doesn't exist, create or get the subject id
    // TODO: should it be detailed or general?
    $detailedSubject = new DetailedSubject();
    $detailedSubjectId = $detailedSubject->getResourceTypeIDByName($subject);
    if(empty($detailedSubjectId)){
        // create a new resource type
        $detailedSubject->shortName = $subject;
        $detailedSubject->save();
        $detailedSubjectId = $detailedSubject->primaryKey;
    }
    // add the key and name to the cache
    $resourceTypeCache[$detailedSubjectId] = $subject;
    return $detailedSubjectId;
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
    global $loginID, $config, $orgModule;


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

