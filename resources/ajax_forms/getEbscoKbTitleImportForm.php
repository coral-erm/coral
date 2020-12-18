<?php

$titleId = filter_input(INPUT_GET, 'titleId', FILTER_SANITIZE_STRING);
$setAsSelected = filter_input(INPUT_GET, 'select', FILTER_VALIDATE_BOOLEAN);
$fallbackTitleId = filter_input(INPUT_GET, 'fallbackTitleId', FILTER_SANITIZE_NUMBER_INT);

if ($fallbackTitleId) {
    $cancelJs = "tb_show(null,'ajax_htmldata.php?action=getEbscoKbTitleDetails&height=700&width=730&modal=true&titleId=$fallbackTitleId');";
} else {
    $cancelJs = 'tb_remove()';
}

if(empty($titleId)){
    echo '<p>No title ID provided</p>';
    exit;
}

$ebscoKb = EbscoKbService::getInstance();
$title = $ebscoKb->getTitle($titleId);

//get all acquisition types for output in drop down
$acquisitionTypeArray = array();
$acquisitionTypeObj = new AcquisitionType();
$acquisitionTypeArray = $acquisitionTypeObj->sortedArray();

//get all resource formats for output in drop down
$resourceFormatArray = array();
$resourceFormatObj = new ResourceFormat();
$resourceFormatArray = $resourceFormatObj->sortedArray();

?>
<div id="div_ebscoKbTitleImportForm" class="ebsco-layout" style="width:745px;">
    <div class="formTitle">
        <span class="headerText"><?php echo _('Import').' '.$title->titleName.' '._(' from EBSCO Kb'); ?></span>
    </div>
    <div class="container">
        <div class="col-12">
            <div class="smallDarkRedText">&nbsp;* <?php echo _("required fields");?></div>
        </div>
        <div class="col-12 text-danger" id="importError">
            <p><i class="fa fa-exclamation-triangle fa-lg"></i> <?php echo _('There was a problem importing this resource'); ?></p>
            <div id="importErrorText"></div>
        </div>
        <form id="ebscoKbTitleImportForm">
            <input type="hidden" id="organizationId" name="organizationId" value="" />
            <input type="hidden" id="importType" name="importType" value="title" />
            <input type="hidden" id="titleId" name="titleId" value="<?php echo $title->titleId; ?>"/>
            <?php if($setAsSelected): ?>
                <input type="hidden" id="setAsSelected" name="setAsSelected" value="true" />
            <?php endif; ?>
            <div class="row">
                <div class="col-6">

                    <!-- Provider -->
                    <div class="card mr-1 mt-1">
                        <div class="card-header">
                            <strong><?php echo _("Provider");?></strong>
                        </div>
                        <div class="card-body">
                            <label for="providerText"><?php echo _('Provider'); ?></label>
                            <input type="text" id="providerText" style="width:220px;" class="changeInput" value="" />
                            <span id="span_error_providerText" class="smallDarkRedText"></span>
                        </div>
                    </div>


                    <!-- Format -->
                    <div class="card mr-1 mt-1">
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
                </div>

                <div class="col-6">

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
                <button class="btn btn-primary" onclick="processEbscoKbImport('save','#ebscoKbTitleImportForm')">
                    <?php echo _("save");?>
                </button>
                <button class="btn btn-primary ml-1" onclick="processEbscoKbImport('progress','#ebscoKbTitleImportForm')">
                    <?php echo _("submit");?>
                </button>
                <button class="btn btn-primary ml-1" onclick="tb_remove()"><?php echo _("cancel");?></button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="js/forms/importEbscoKbForm.js?random=<?php echo rand(); ?>"></script>

