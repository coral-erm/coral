<?php

// Begin with some sanity checks...

if (!array_key_exists("id", $_POST) || !array_key_exists("enabled", $_POST)) {
  echo "Fail: need id and enabled to update.";
  exit();
}

$id = $_POST['id'];
$enabled = $_POST['enabled'];

try {
  $id = (int)$id;
  $enabled = (int)$enabled;
  if ($enabled != 0 && $enabled != 1) {
    throw new Exception("`Enabled` is an integer but needs to be 0/1.", 1);
  }
  $enabled;
} catch (Exception $e) {
    echo "Fail: id needs to be an integer and enabled needs to be a boolean (i.e. 0/1).";
    exit();
}

/* Okay, so we could also check that the id is in the db
 * but at this point, if you're trying to set a non-existing
 * id, you're wasting your time.
 */

try {
  $exportConfig = new ExportConfig();
  $exportConfig->updateConfiguration($id, $enabled);
} catch (Exception $e) {
  // Note, this only ends up as a response message. It doesn't appear
  // to the user or in console.log.
  echo $e->getMessage();
}

?>
