<?php
$resourceStepID = $_POST['resourceStepID'];
$userGroupID = $_POST['userGroupID'];
$note = $_POST['note'];
$applyToAll = ($_POST['applyToAll'] == "true")? true:false;
$userGroupIDChanged = $_POST['userGroupIDChanged'];


if($resourceStepID != ''){
    $step = new ResourceStep(new NamedArguments(array('primaryKey' => $resourceStepID)));

    //business logic
    $step->note = $note;
    $step->save();

    // If the step has been reassigned
    if ($userGroupIDChanged) {
        $step->userGroupID = $userGroupID;
        //if apply to all selected, we need to cycle through later steps.
        try {
            $step->restartReassignedStep();

            if ($applyToAll){
                //get later open steps and restart those.
                $laterSteps = $step->getLaterOpenSteps();
                if (count($laterSteps) > 0){
                    foreach($laterSteps as $laterStep){
                        $laterStep->userGroupID = $userGroupID;
                        $laterStep->restartReassignedStep();
                    }
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}else{
    //do something for empty result
    echo "There was an error. Invalid or missing step.";
}
