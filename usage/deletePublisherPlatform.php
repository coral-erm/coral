<?php

include_once 'directory.php';
include "common.php";

if (isset($_GET['publisherPlatformID'])){
  $publisherPlatformID = $_GET['publisherPlatformID'];
  $platformID = '';
}

if (isset($_GET['platformID'])){
  $platformID = $_GET['platformID'];
  $publisherPlatformID = '';
}


if (empty($platformID) && empty($publisherPlatformID)){
  header( 'Location: publisherPlatformList.php?error=1' );
  exit;
}

$config = new Configuration();

if ($publisherPlatformID != '') {
  $obj = new PublisherPlatform(new NamedArguments(array('primaryKey' => $publisherPlatformID)));
}else if ($platformID != ''){
  $obj = new Platform(new NamedArguments(array('primaryKey' => $platformID)));
}

if (isset($_GET['statsOnly'])) {
  $statsOnly = $_GET['statsOnly'];
} else {
  $statsOnly = false;
}

if($statsOnly) {
  $obj->deleteStats();
} else {
  $obj->delete();
}

header( 'Location: index.php' );
