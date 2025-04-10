<?php
$resourceStepID = $_GET['resourceStepID'];
$resourceStep = new ResourceStep(new NamedArguments(array('primaryKey' => $resourceStepID)));

try {
    $resourceStep->delete();
    $resourceStep->startNextStepsOrComplete();

    //TODO fix the display order sequence if there are later steps


} catch (Exception $e) {
    echo "<span class='error'>";
    echo $e->getMessage();
    echo "</span>";
}
