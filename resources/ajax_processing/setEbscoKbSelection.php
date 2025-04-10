<?php

$packageId = filter_input(INPUT_GET, 'packageId', FILTER_SANITIZE_STRING);
$vendorId = filter_input(INPUT_GET, 'vendorId', FILTER_SANITIZE_STRING);
$titleId = filter_input(INPUT_GET, 'titleId', FILTER_SANITIZE_STRING);
$selected = filter_input(INPUT_GET, 'selected', FILTER_VALIDATE_BOOLEAN);

var_dump($_GET);

if(!isset($packageId) || !isset($vendorId)){
    echo '<span class="error">' . _('Missing Package or Vendor ID') . '</span>';
    exit;
}

$ebscoKb = EbscoKbService::getInstance();
if (!empty($titleId)) {
    $ebscoKb->setTitle($vendorId, $packageId, $titleId, $selected);
} else {
    $ebscoKb->setPackage($vendorId, $packageId, $selected);
}




