<?php

		$workflowID = $_POST['workflowID'];

		if ($workflowID!=''){
			$workflow = new Workflow(new NamedArguments(array('primaryKey' => $workflowID)));
		}else{
			$workflow = new Workflow();
		}

		$workflow->workflowName = '';
		$workflow->resourceFormatIDValue = $_POST['resourceFormatID'];
		$workflow->resourceTypeIDValue = $_POST['resourceTypeID'];
		$workflow->acquisitionTypeIDValue = $_POST['acquisitionTypeID'];

		try {
			$workflow->save();

			$workflowID=$workflow->primaryKey;

			//first remove all step records, then we'll add them back
			$workflow->removeSteps();

			$stepNameArray = array();
			$stepNameArray = explode(':::',$_POST['stepNames']);
			$userGroupArray = array();
			$userGroupArray = explode(':::',$_POST['userGroups']);
			$priorStepArray = array();
			$priorStepArray = explode(':::',$_POST['priorSteps']);
			$seqOrderArray = array();
			$seqOrderArray = explode(':::',$_POST['seqOrders']);
			$stepIDArray = array();
			$stepIDPriorArray = array();

			foreach ($stepNameArray as $key => $value){
				if (trim($value)){
					$step = new Step();
					$step->workflowID = $workflowID;
					$step->stepName = trim($value);
					$step->userGroupID = $userGroupArray[$key];
					$step->priorStepID = '';
					$step->displayOrderSequence = $seqOrderArray[$key];

					try {
						$step->save();
						$stepID = $step->primaryKey;

						//if this step has a prior step, put it in an array
						if ($priorStepArray[$key]){
							$stepIDPriorArray[$stepID] = $priorStepArray[$key];
						}


						$stepIDArray[$seqOrderArray[$key]] = $stepID;


					} catch (Exception $e) {
						echo "<span class='error'>";
						echo $e->getMessage();
						echo "</span>";
					}
				}
			}

			//now that all of the stepIDs have been set up, fix the prior step IDs
			foreach ($stepIDPriorArray as $stepID => $key){
				if ($stepID){

					$step = new Step(new NamedArguments(array('primaryKey' => $stepID)));
					$step->priorStepID = $stepIDArray[$key];

					try {
						$step->save();
					} catch (Exception $e) {
						echo "<span class='error'>";
						echo $e->getMessage();
						echo "</span>";
					}
				}
			}



		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

?>
