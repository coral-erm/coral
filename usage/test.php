<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once 'directory.php';

$queryObject = new Title(new NamedArguments(array('primaryKey' => '3856')));
// try to find the title via title identifiers
$ytdCounts = $queryObject->getYearlyStats(null, 2020, 1700, null);
print_r($ytdCounts);
