<?php
if(empty($ebscoDropdownConfig)){
    exit;
}

## $ebscoDropdownConfig
# 'vendorId' => the ebsco kb vendor id
# 'packageId' => the ebsco kb package id
# 'titleId' => the ebsco kb title id
# 'selected' => boolean, whether or not the resource is selected
# 'resourceID' => the associated resource ID
# 'page' => the page from the ebscokb search results (for callback purposes)
# 'callback' => the callback function to run

extract($ebscoDropdownConfig);

if(empty($page)) {
    $page = 1;
}
if(empty($callback)) {
    $callback = "updateSearch.bind(null, $page, myCloseDialog)";
}
$jsVars = [$vendorId, $packageId, 'null', "$callback"];

$baseParams = [
    'height' => '700',
    'width' => '730',
    'modal' => 'true'
];
$deleteParams = http_build_query(
    array_merge(
        $baseParams,
        [
            'action' => 'getEbscoKbRemoveConfirmation',
            'vendorId' => $vendorId,
            'packageId' => $packageId,
            'titleId' => null,
            'resourceID' => $resourceID,
            'page' => $page,
            'fallbackTitleId' => $titleId ? $titleId : null
        ]
    )
);

$importParams = http_build_query(
    array_merge(
        $baseParams,
        [
            'action' => 'getEbscoKbPackageImportForm',
            'vendorId' => $vendorId,
            'packageId' => $packageId,
            'resourceID' => $resourceID,
            'select' => 'true'
        ]
    )
);

$selectClass = '';
$selectText = _('Not Selected');
if ($selected) {
    $selectClass = 'btn-success';
    $selectText = '<i class="fa fa-check"></i> '._('Selected');
}

?>
<div class="ebsco-select-dropdown">
    <button type="button" class="btn dd-btn <?php echo $selectClass; ?>" onclick="toggleEbscoSelectDropdown('#<?php echo $packageId; ?>-dropdown')">
        <?php echo $selectText; ?> &#x25BE;
    </button>
    <div class="dd-content" id="<?php echo $packageId; ?>-dropdown">
        <?php if($selected): ?>
            <button type="button" class="btn" 
                onclick="setEbscoSelection(false,<?php echo implode(',', $jsVars); ?>)">
                <?php echo _("Deselect Package"); ?>
            </button>
            <?php if($resourceID): ?>
                <a href="ajax_forms.php?<?php echo $deleteParams; ?>"
                   class="thickbox">
                    <?php echo _("Deselect Package & Delete from Coral"); ?>
                </a>
            <?php endif; ?>
        <?php else: ?>
            <button type="button" class="btn" 
                onclick="setEbscoSelection(true,<?php echo implode(',', $jsVars); ?>)">
                <?php echo _("Select Package"); ?>
            </button>
            <?php if(!$resourceID): ?>
                <a href="ajax_forms.php?<?php echo $importParams; ?>"
                   class="thickbox">
                    <?php echo _("Select & Import Package"); ?>
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
