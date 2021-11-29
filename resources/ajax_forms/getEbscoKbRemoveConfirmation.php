<?php
$resourceID = filter_input(INPUT_GET, 'resourceID', FILTER_SANITIZE_NUMBER_INT);
$vendorId = filter_input(INPUT_GET, 'vendorId', FILTER_SANITIZE_NUMBER_INT);
$packageId = filter_input(INPUT_GET, 'packageId', FILTER_SANITIZE_NUMBER_INT);
$titleId = filter_input(INPUT_GET, 'titleId', FILTER_SANITIZE_NUMBER_INT);
$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT);
$fallbackTitleId = filter_input(INPUT_GET, 'fallbackTitleId', FILTER_SANITIZE_NUMBER_INT);
$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));
$childrenCount = count($resource->getChildResources());

$page = empty($page) ? 1 : $page;

if ($fallbackTitleId) {
    $href = "ajax_htmldata.php?action=getEbscoKbTitleDetails&height=700&width=730&modal=true&titleId=$fallbackTitleId&page=$page";
    $callback = "tb_show.bind(null,null,'$href')";
    $cancelJs = "tb_show(null,'$href')";
} else {
    $callback = "updateSearch.bind(null, $page, tb_remove)";
    $cancelJs = 'myCloseDialog()';
}


$jsTitleId = empty($titleId) ? 'null' : $titleId;
$jsVars = [
    empty($resourceID) ? 'null' : $resourceID,
    empty($vendorId) ? 'null' : $vendorId,
    empty($packageId) ? 'null' : $packageId,
    $jsTitleId,
    $callback
];

$title = _('You are about to deselect the following resource from Ebsco and delete it from Coral');
$option1 = _('deselect & delete');
$option2 = _("deselect & delete, including $childrenCount child records");

if (empty($packageId)) {
    $title = _('Are you sure you want to delete the following resource from Coral?');
    $option1 = _('yes, delete resource');
    $option2 = _("yes, and delete all $childrenCount child records");
}


?>

<div id="div_ebscoKbConfirmDeletion" class="ebsco-layout" style="width:745px;">
    <div id="deleteError"></div>
    <div class="container" style="text-align: center">
        <p class="bigDarkRedText">
            <?php echo $title; ?>
        </p>
        <p style="font-size: 1.3em;"><?php echo $resource->titleText ?></p>
        <div class="row" style="padding-top: 2em;">
            <div class="col-3">
                <button class="btn btn-primary" onclick="deleteEbscoKbResource(<?php echo implode(',',$jsVars); ?>,false)">
                    <?php echo $option1; ?>
                </button>
            </div>
            <div class="col-6">
                <?php if($childrenCount > 0): ?>
                    <button class="btn btn-primary" onclick="deleteEbscoKbResource(<?php echo implode(',',$jsVars); ?>,true)">
                        <?php echo $option2; ?>
                    </button>
                <?php endif; ?>
            </div>
            <div class="col-3">
                <button class="btn btn-primary ml-1" onclick="<?php echo $cancelJs; ?>">
                    <?php echo _('cancel'); ?>
                </button>
            </div>
        </div>
    </div>
</div>
