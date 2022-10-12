<?php

$packageId = filter_input(INPUT_GET, 'packageId', FILTER_SANITIZE_STRING);
$vendorId = filter_input(INPUT_GET, 'vendorId', FILTER_SANITIZE_STRING);
$setAsSelected = filter_input(INPUT_GET, 'select', FILTER_VALIDATE_BOOLEAN);
$fallbackTitleId = filter_input(INPUT_GET, 'fallbackTitleId', FILTER_SANITIZE_NUMBER_INT);

if ($fallbackTitleId) {
    $cancelJs = "myDialog('ajax_htmldata.php?action=getEbscoKbTitleDetails&height=700&width=730&modal=true&titleId=$fallbackTitleId',700,730);";
} else {
    $cancelJs = 'myCloseDialog()';
}

if(!isset($packageId) || !isset($vendorId)){
    echo '<p>Missing Package or Vendor ID</p>';
    exit;
}

$titleOptions = [
    [
        'text' => 'Selected titles only',
        'value' => 'selected',
        'description' => 'Import only the titles you have selected in EBSCO Kb. The package will be added as a parent resource.'
    ],
    [
        'text' => 'No titles',
        'value' => 'none',
        'description' => 'Do not import any titles.'
    ],
    [
        'text' => 'All titles',
        'value' => 'all',
        'description' => 'Import all the titles in the package. The package will be added as a parent resource.'
    ]
];

$ebsco10kLimitText = '<p class="smallDarkRedText">'.
                    _('The EBSCO Kb API only allows importing a maximum of 10,000 records. Only package information will be imported.').
                    '</p><p class="smallDarkRedText">'.
                    _('Alternatively, you may import titles individually or via a manual import.').'</p>';

$ebscoKb = EbscoKbService::getInstance();
$package = $ebscoKb->getPackage($vendorId, $packageId);

//get all acquisition types for output in drop down
$acquisitionTypeArray = array();
$acquisitionTypeObj = new AcquisitionType();
$acquisitionTypeArray = $acquisitionTypeObj->sortedArray();

//get all resource formats for output in drop down
$resourceFormatArray = array();
$resourceFormatObj = new ResourceFormat();
$resourceFormatArray = $resourceFormatObj->sortedArray();

//get all resource types for output in drop down
$resourceTypeArray = array();
$resourceTypeObj = new ResourceType();
$resourceTypeArray = $resourceTypeObj->allAsArray();

// organizations
$config = new Configuration;
$orgModule = $config->settings->organizationsModule == 'Y' ? true : false;
if ($orgModule) {
    // TODO: Once namespaces are implemented, these sql calls can be removed. Call the Orgzanization versions of these classes via their namespaces instead.
    $dbService = new DBService;
    $orgDbName = $config->settings->organizationsDatabaseName;
    $orgQuery = "SELECT organizationID, `name`
			FROM ".$orgDbName.".Organization
			WHERE ebscoKbID = $package->vendorId
			LIMIT 0,1";
    $result = $dbService->processQuery($orgQuery, 'assoc');
    $organization = isset($result['organizationID']) ? (object)['primaryKey' => $result['organizationID'], 'name' => $result['name']] : false;

    $aliasTypeQuery = "SELECT aliasTypeID, shortName
			FROM ".$orgDbName.".AliasType";
    $aliasTypeArray = $dbService->processQuery($aliasTypeQuery, 'assoc');
} else {
    $organization = new Organization;
    $organization = $organization->getOrganizationByEbscoKbId($package->vendorId);
}

?>
<div class="ebsco-layout" style="width:745px; height: 650px;">

    <div id="div_ebscoKbPackageImportForm" class="ebsco-layout">
        <div class="formTitle">
            <span class="headerText"><?php echo _('Import').' '.$package->packageName.' '._(' from EBSCO Kb'); ?></span>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="smallDarkRedText">&nbsp;* <?php echo _("required fields");?></div>
                </div>
                <div class="col-12 text-danger" id="importError">
                    <p><i class="fa fa-exclamation-triangle fa-lg"></i> <?php echo _('There was a problem importing this resource'); ?></p>
                    <div id="importErrorText"></div>
                </div>
            </div>
            <form id="ebscoKbPackageImportForm">
                <input type="hidden" id="organizationId" name="organizationId" value="<?php echo empty($organization) ? '' : $organization->primaryKey; ?>" />
                <input type="hidden" id="packageId" name="packageId" value="<?php echo $package->packageId; ?>" />
                <input type="hidden" id="vendorId" name="vendorId" value="<?php echo $package->vendorId; ?>" />
                <?php if($setAsSelected): ?>
                    <input type="hidden" id="setAsSelected" name="setAsSelected" value="true" />
                <?php endif; ?>
                <input type="hidden" id="importType" name="importType" value="package" />
                <div class="row">
                    <div class="col-6">

                        <!-- Provider Options -->
                        <div class="card mr-1 mt-1">
                            <div class="card-header">
                                <strong><?php echo _("Provider");?></strong>
                            </div>
                            <div class="card-body">
                                <?php if(empty($organization)): ?>
                                <div class="row">
                                    <p id="span_error_providerOption" class="smallDarkRedText"></p>
                                    <div class="col-6 pb-1">
                                        <label for="providerOption-import">
                                            <input type="radio" name="providerOption" value="import" id="providerOption-import" class="change-provider-option" checked> Import
                                        </label>
                                    </div>
                                    <div class="col-6 pb-1">
                                        <label for="providerOption-override">
                                            <input type="radio" name="providerOption" value="override" id="providerOption-override" class="change-provider-option"> Override
                                        </label>
                                    </div>
                                    <?php if($orgModule): ?>
                                    <div class="col-6 pb-1">
                                        <label for="providerOption-alias">
                                            <input type="radio" name="providerOption" value="alias" id="providerOption-alias" class="change-provider-option"> Alias
                                        </label>
                                    </div>
                                    <div class="col-6 pb-1">
                                        <label for="providerOption-parentChild">
                                            <input type="radio" name="providerOption" value="parentChild" id="providerOption-parentChild" class="change-provider-option"> Parent or Child
                                        </label>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div id="providerHelpText">
                                    <div id="providerOption-help-import">
                                        <p>Import the following vendor: <strong><?php echo $package->vendorName; ?></strong></p>
                                    </div>
                                    <div id="providerOption-help-override">
                                        <p>Do not import any provider information from EBSCO Kb and use the selected provider instead</p>
                                    </div>
                                    <?php if($orgModule): ?>
                                    <div id="providerOption-help-alias">
                                        <p>Import <strong><?php echo $package->vendorName; ?></strong> as an alias to the selected provider:</p>
                                        <p id="span_error_aliasType" class="smallDarkRedText"></p>
                                        <label for="aliasTypeId"><?php echo _('Alias Type'); ?></label>
                                        <select name="aliasTypeId" id="aliasTypeId">
                                            <?php foreach($aliasTypeArray as $alias): ?>
                                                <option value="<?php echo $alias['aliasTypeID']; ?>"><?php echo $alias['shortName']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div id="providerOption-help-parentChild">
                                        <p>Import <strong><?php echo $package->vendorName; ?></strong> as a parent or child of the selected provider:</p>
                                        <p id="span_error_parentOrChild" class="smallDarkRedText"></p>
                                        <label for="providerIsChild">
                                            <input type="radio" name="providerParentOrChild" value="child" id="providerIsChild" checked> Child
                                        </label>
                                        <label for="providerIsParent" class="pl-1">
                                            <input type="radio" name="providerParentOrChild" value="parent" id="providerIsParent"> Parent
                                        </label>
                                    </div>
                                    <?php endif; ?>
                                    <div id="selectProvider">
                                        <p id="span_error_organization" class="smallDarkRedText"></p>
                                        <label for="providerText">Selected Provider</label>
                                        <br>
                                        <input type="text" id="providerText" style="width:220px;" class="changeInput" value="" />
                                    </div>
                                </div>
                                <?php else: ?>
                                    <p><?php echo $organization->name; ?> <small>(matched via EBSCO Kb ID)</small></p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Package Title Options -->
                        <div class="card mr-1 mt-1">
                            <div class="card-header">
                                <strong><?php echo _("Package Titles");?><span class="bigDarkRedText">*</span></strong>
                            </div>
                            <div class="card-body">
                                <?php if ($package->selectedCount > 10000): ?>
                                    <?php echo $ebsco10kLimitText; ?>
                                    <input type="hidden" name="titleFilter" value="none">
                                <?php else: ?>
                                    <p id="span_error_titleFilter" class="smallDarkRedText"></p>
                                    <div class="row">
                                        <div class="col-12 pb-1">
                                            <p>
                                                <?php echo sprintf(ngettext('You have %d of %d title selected in EBSCO Kb', 'You have %d of %d titles selected in EBSCO Kb', $package->titleCount), $package->selectedCount, $package->titleCount); ?>
                                            </p>
                                        </div>
                                        <?php foreach($titleOptions as $option): ?>
                                            <div class="col-12 pb-1">
                                                <?php if($package->titleCount > 10000 && $option['value'] == 'all'): ?>
                                                    <p class="darkRedText"><?php echo _('All titles not available'); ?></p>
                                                    <?php echo $ebsco10kLimitText; ?>
                                                <?php else: ?>
                                                    <label for="titleFilter-<?php echo $option['value']; ?>">
                                                        <input
                                                            type="radio"
                                                            name="titleFilter"
                                                            value="<?php echo $option['value']; ?>"
                                                            id="titleFilter-<?php echo $option['value']; ?>"
                                                            <?php if($option == $titleOptions[0]){ echo 'checked'; } ?>
                                                        >
                                                        <?php echo _($option['text']); ?>
                                                        <br />
                                                        <small><?php echo _($option['description']); ?></small>
                                                    </label>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Workflow Option -->
                        <div class="card mr-1 mt-1" id="workflowOptionCard">
                            <div class="card-header">
                                <strong><?php echo _("Workflow Options");?><span class="bigDarkRedText">*</span></strong>
                            </div>
                            <div class="card-body">
                                <p id="span_error_workflowOption" class="smallDarkRedText"></p>
                                <div class="row">
                                    <div class="col-12">
                                        Do you want to start a new workflow for each title or only the package?
                                    </div>
                                    <div class="col-6 pb-1">
                                        <label for="packageOnly">
                                            <input type="radio" name="workflowOption" id="packageOnly" value="packageOnly" checked>
                                            Package only
                                        </label>
                                    </div>
                                    <div class="col-6 pb-1">
                                        <label for="allTitles">
                                            <input type="radio" name="workflowOption" id="allTitles" value="allTitles">
                                            All imported titles
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">

                        <!-- Format -->
                        <div class="card ml-1 mt-1">
                            <div class="card-header">
                                <strong><?php echo _("Format");?><span class="bigDarkRedText">*</span></strong>
                            </div>
                            <div class="card-body">
                                <p id="span_error_resourceFormatId" class="smallDarkRedText"></p>
                                <div class="row">
                                    <?php foreach ($resourceFormatArray as $resourceFormat): ?>
                                    <div class="col-6 pb-1">
                                        <label for="resourceFormat<?php echo $resourceFormat["resourceFormatID"]; ?>">
                                            <input
                                                    type="radio"
                                                    name="resourceFormatId"
                                                    id="resourceFormat<?php echo $resourceFormat["resourceFormatID"]; ?>"
                                                    value="<?php echo $resourceFormat["resourceFormatID"]; ?>"
                                                <?php if (strtoupper($resourceFormat["shortName"]) == "ELECTRONIC"){ echo 'checked'; }?>/>
                                            <?php echo $resourceFormat['shortName']; ?>
                                        </label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Acquisition Type -->
                        <div class="card ml-1 mt-1">
                            <div class="card-header">
                                <strong><?php echo _("Acquisition Type");?><span class="bigDarkRedText">*</span></strong>
                            </div>
                            <div class="card-body">
                                <p id="span_error_acquisitionTypeId" class="smallDarkRedText"></p>
                                <div class="row">
                                    <?php foreach ($acquisitionTypeArray as $acquisitionType): ?>
                                    <div class="col-6 pb-1">
                                        <label for="acquisitionType<?php echo $acquisitionType["acquisitionTypeID"]; ?>">
                                            <input type="radio"
                                                   name="acquisitionTypeId"
                                                   id="acquisitionType<?php echo $acquisitionType["acquisitionTypeID"]; ?>"
                                                   value="<?php echo $acquisitionType["acquisitionTypeID"]; ?>"
                                                <?php if (strtoupper($acquisitionType["shortName"]) == "PAID"){ echo 'checked'; }?>/>
                                            <?php echo $acquisitionType["shortName"]; ?>
                                        </label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Resource Type -->
                        <div class="card ml-1 mt-1">
                            <div class="card-header">
                                <strong><?php echo _("Package Resource Type");?><span class="bigDarkRedText">*</span></strong>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php if(!in_array(strtoupper($package->contentType), array_map(function($type){ return strtoupper($type['shortName']); }, $resourceTypeArray))): ?>
                                    <div class="col-6 pb-1">
                                        <label for="resourceTypeNew">
                                            <input type="radio" name="resourceTypeId" id="resourceTypeNew" value="-1" checked>
                                            <?php echo $package->contentType; ?>
                                        </label>
                                    </div>
                                    <?php endif; ?>
                                    <?php foreach ($resourceTypeArray as $resourceType): ?>
                                    <div class="col-6 pb-1">
                                        <label for="resourceType<?php echo $resourceType["resourceTypeID"]; ?>">
                                            <input type="radio"
                                                   name="resourceTypeId"
                                                   id="resourceType<?php echo $resourceType["resourceTypeID"]; ?>"
                                                   value="<?php echo $resourceType["resourceTypeID"]; ?>"
                                                <?php if (strtoupper($resourceType["shortName"]) == strtoupper($package->contentType)){ echo 'checked'; }?>/>
                                            <?php echo $resourceType["shortName"]; ?>
                                        </label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="card ml-1 mt-1">
                            <div class="card-header">
                                <strong><?php echo _("Notes");?></strong>
                            </div>
                            <div class="card-body">
                                <p class="smallGreyText"><?php echo _("Include any additional information");?></p>
                                <textarea rows="5" id="noteText" name="noteText" style="width: 100%;"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row mt-1">
                <div class="col-12">
                    <button class="btn btn-primary" onclick="processEbscoKbImport('progress','#ebscoKbPackageImportForm')">
                        <?php echo _("import");?>
                    </button>
                    <button class="btn btn-primary ml-1" onclick="<?php echo $cancelJs; ?>"><?php echo _("cancel");?></button>
                </div>
            </div>
        </div>
    </div>
    <div id="importOverlay">
        <div class="container mt-5">
            <div class="row">
                <div class="col-12" style="text-align: center" id="importingMessage">
                    <h1>Importing</h1>
                    <p class="mt-1">
                        <i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>
                    </p>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body" id="importLog">
                            <p>Adding package...<i class="fa fa-check-circle-o text-success" style="display: none" id="packageSuccessfullyImported"></i></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="js/forms/importEbscoKbForm.js?random=<?php echo rand(); ?>"></script>
