<?php

$enabled = filter_input(INPUT_POST, 'enabled', FILTER_VALIDATE_BOOLEAN);
$customerId = filter_input(INPUT_POST, 'customerId', FILTER_SANITIZE_STRING);
$apiKey = filter_input(INPUT_POST, 'apiKey', FILTER_SANITIZE_STRING);
$ini_file = BASE_DIR . "/admin/configuration.ini";
require_once BASE_DIR."../common/write_php_ini.php";

$ini_array = parse_ini_file($ini_file, true);

$ini_array['settings']['ebscoKbEnabled'] = empty($enabled) ? 'N' : 'Y';
$ini_array['settings']['ebscoKbCustomerId'] = $customerId;
$ini_array['settings']['ebscoKbApiKey'] = $apiKey;

try {
    write_php_ini($ini_file, $ini_array);
} catch (Exception $e) {
    http_response_code(500);
    echo "<span class='error'>";
    echo $e->getMessage();
    echo "</span>";
}


