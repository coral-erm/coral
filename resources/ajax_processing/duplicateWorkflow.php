<?php

$sourceID = $_GET['id'];

if ($sourceID) {
    $workflowObj = new Workflow();
    $newWorkflow = $workflowObj->cloneFrom($sourceID);

} else {
    echo ("Unable to duplicate workflow: source workflow is missing");
}

?>
