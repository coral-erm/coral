<?php
$titleId = filter_input(INPUT_GET, 'titleId', FILTER_SANITIZE_STRING);
$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT);

if(empty($titleId)){
    echo '<p>No title ID provided</p>';
    exit;
}

$ebscoKb = EbscoKbService::getInstance();
$title = $ebscoKb->getTitle($titleId);

$title->loadResource();
?>
<div id="div_ebscoKbTitleDetails" class="ebsco-layout" style="width:745px;">

    <div class="formTitle" style="margin-bottom:5px;position:relative;"><span class="headerText"><?php echo _("EBSCO Kb Title Details");?></span></div>

    <div class="container">
        <div class="row">
            <div class="col-8"><h1 style="line-height: 1em;"><?php echo $title->titleName; ?></h1></div>
            <div class="col-4" style="text-align: right;">KbID: <?php echo $title->titleId; ?></div>
        </div>
        <div class="row" style="margin-top: 1em;">
            <div class="col-12">
                <p style="font-size: 1rem;"><?php echo $title->description; ?></p>
            </div>
        </div>
        <div class="row" style="margin-top: 1em;">
            <div class="col-12">
                <h2>Title details</h2>
                <div class="row">
                    <div class="col-6">
                        <dl>
                            <dt><?php echo _("Publication Type"); ?></dt>
                            <dd><?php echo $title->pubType; ?></dd>

                            <?php if(!empty($title->edition)): ?>
                                <dt><?php echo _("Edition"); ?></dt>
                                <dd><?php echo $title->edition; ?></dd>
                            <?php endif; ?>

                            <dt><?php echo _("Peer Reviewed"); ?></dt>
                            <dd><?php echo $title->isPeerReviewed; ?></dd>

                            <dt><?php echo _("Publisher"); ?></dt>
                            <dd><?php echo $title->publisherName; ?></dd>
                        </dl>
                    </div>
                    <div class="col-6">
                        <dl>
                            <dt><?php echo _("Subjects"); ?></dt>
                            <dd>
                                <ul>
                                    <?php foreach($title->subjects as $subject): ?>
                                        <li><?php echo $subject; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </dd>

                            <dt><?php echo _("ISXNs"); ?></dt>
                            <dd>
                                <ul style="list-style: none; ">
                                    <?php
                                    foreach($title->isxnList as $identifier){
                                        if(in_array($identifier['type'], [0,1])) {
                                            switch($identifier['subtype']){
                                                case 1:
                                                    $subtype = _(' (Print)');
                                                    break;
                                                case 2:
                                                    $subtype = _(' (Electronic)');
                                                    break;
                                                default:
                                                    $subtype = '';
                                            }
                                            echo sprintf('<li>%s%s</li>', $identifier['id'], $subtype);
                                        }
                                    }
                                    ?>
                                </ul>
                            </dd>

                            <?php if(!empty($title->contributorList)): ?>
                                <dt><?php echo _("Contributors"); ?></dt>
                                <dd><?php echo implode(', ', $title->contributorList); ?></dd>
                            <?php endif; ?>
                        </dl>
                    </div>
                </div>
                <?php if($title->resource): ?>
                    <h3 class="text-success">
                        <i class="fa fa-info"></i> <?php echo _('This title has been imported into Coral'); ?>
                    </h3>
                    <p style="margin-top: 6px;">
                        <a href="ajax_forms.php?action=getEbscoKbRemoveConfirmation&height=700&width=730&modal=true&resourceID=<?php echo $title->resource->primaryKey; ?>&fallbackTitleId=<?php echo $titleId; ?>&page=<?php echo $page; ?>"
                           class="btn thickbox">
                            <?php echo _('delete from Coral'); ?>
                        </a>
                    </p>
                <?php endif; ?>
            </div>
            <div class="col-12" style="margin-top: 1em;">
                <h2>Available in the following packages:</h2>
                <div class="row">
                    <div class="col-4">
                        <label for="showAllPackages">
                            <input type="checkbox" id="showAllPackages"> <?php echo _("Show all packages"); ?>
                        </label>
                    </div>
                </div>
                <div class="row">
                    <?php foreach($title->customerResourcesList as $resource): ?>
                    <?php
                        $package = $ebscoKb->getPackage($resource->vendorId, $resource->packageId);
                        $package->loadResource();
                        $callback = "myDialog('ajax_htmldata.php?action=getEbscoKbTitleDetails&height=700&width=730&modal=true&titleId=$titleId',700,730)";
                    ?>
                    <div class="col-12 packageOption <?php echo $package->isSelected ? 'selectedPackage' : ''; ?>">
                        <div class="card" style="margin-top: 1em;">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-8">
                                        <h3>
                                            <?php echo $resource->packageName; ?>
                                        </h3>
                                        <?php if($package->resource): ?>
                                            <?php if($package->isSelected): ?>
                                                <span class="text-success">
                                                        <i class="fa fa-check"></i> <?php echo _('Package is Selected & Imported'); ?>
                                                    </span>
                                                <a href="resource.php?resourceID=<?php echo $package->resource->primaryKey; ?>" target="_blank">
                                                    (<?php echo _('view in Coral'); ?>)
                                                </a>
                                            <?php else: ?>
                                                <span class="text-danger">
                                                        <i class="fa fa-ban"></i> <?php echo _('Package is NOT selected in EBSCOhost'); ?>
                                                    </span>
                                                <a href="ajax_forms.php?action=getEbscoKbRemoveConfirmation&height=700&width=730&modal=true&resourceID=<?php echo $package->resource->primaryKey; ?>&fallbackTitleId=<?php echo $titleId; ?>"
                                                   class="thickbox">
                                                    (<?php echo _('delete from Coral'); ?>)
                                                </a>
                                            <?php endif; ?>
                                        <?php elseif ($package->selectedCount): ?>
                                            <span class="text-warning">
                                                    <i class="fa fa-exclamation-triangle"></i> <?php echo _('Package is Selected but not Imported '); ?>
                                                </span>
                                            <a href="ajax_forms.php?action=getEbscoKbPackageImportForm&height=700&width=730&modal=true&vendorId=<?php echo $package->vendorId; ?>&packageId=<?php echo $package->packageId; ?>&fallbackTitleId=<?php echo $titleId; ?>"
                                               class="thickbox">
                                                (<?php echo _('import package'); ?>)
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-4" style="text-align: right">
                                        <a href="javascript:void(0);"
                                                class="setPackage btn btn-primary"
                                                style="margin: 6px 0;"
                                                onclick="myCloseDialog();"
                                                data-vendor-id="<?php echo $resource->vendorId; ?>"
                                                data-package-id="<?php echo $resource->packageId; ?>"
                                                data-package-name="<?php echo $resource->packageName; ?>">
                                            <?php echo _("view package titles"); ?>
                                        </a>
                                        <p>
                                            <?php
                                                if($package->isSelected) {
                                                    echo _("$package->selectedCount of $package->titleCount titles selected");
                                                }
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-9">
                                        <p><?php echo _("Vendor"); ?>: <?php echo $resource->vendorName; ?></p>
                                        <dl>
                                            <dt><?php echo _("Coverage Statement"); ?></dt>
                                            <dd><?php echo $resource->coverageStatement; ?></dd>
                                            <dt><?php echo _("Embargo"); ?></dt>
                                            <dd><?php echo $resource->embargoStatement; ?></dd>
                                            <dt><?php echo _("Resource Url"); ?></dt>
                                            <dd><a href="<?php echo $resource->url; ?>"><?php echo $resource->url; ?></a></dd>
                                        </dl>
                                    </div>
                                    <div class="col-3">
                                        <div style="text-align: center; margin-top: 1em">
                                            <p>
                                                <?php if($resource->isSelected): ?>
                                                    <strong class="text-success"><?php echo _('Title Selected'); ?></strong>
                                                <?php else: ?>
                                                    <strong><?php echo _('Title Not Selected'); ?></strong>
                                                <?php endif; ?>
                                            </p>
                                            <div style="margin-top: 6px;">
                                                <a href="javascript:void(0);"
                                                   class="btn <?php if(!$resource->isSelected) echo 'btn-primary'; ?>"
                                                   onclick="setEbscoSelection(<?php echo implode(',', [$resource->isSelected ? 'false' : 'true',$resource->vendorId, $resource->packageId, $titleId, $callback]); ?>)">
                                                    <?php echo _($resource->isSelected ? 'Deselect Title' : 'Select Title'); ?>
                                                </a>
                                            </div>
                                        </div>
                                        <div style="text-align: center; margin-top: 1em">
                                            <p><strong><?php echo _('Package Options'); ?></strong></p>
                                            <div style="margin-top: 6px;">
                                                <?php
                                                    unset($ebscoDropdownConfig);
                                                    $ebscoDropdownConfig = [
                                                        'vendorId' => $resource->vendorId,
                                                        'packageId' => $resource->packageId,
                                                        'titleId' => $resource->titleId,
                                                        'selected' => $package->isSelected,
                                                        'resourceID' => $package->resource ? $package->resource->primaryKey : null,
                                                        'callback' => $callback
                                                    ];
                                                    include BASE_DIR . '/templates/ebscoKbPackageDropdown.php';
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 1em;">
            <div class="col-12">
                <a
                    href="ajax_forms.php?action=getEbscoKbTitleImportForm&height=700&width=730&modal=true&titleId=<?php echo $title->titleId; ?>"
                    class="thickbox btn btn-primary">
                    <?php echo _('import'); ?>
                </a>
                <button onclick="myCloseDialog();updateSearch(<?php echo empty($page) ? 1 : $page; ?>)" class="btn btn-primary ml-1"><?php echo _("cancel");?></button>
            </div>
        </div>
    </div>
</div>

