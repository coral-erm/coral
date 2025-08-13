<?php
		$postInput = $_POST['formInput'];
		$workflowID = intval($postInput['workflowID']);
		$workflowValue = ($workflowID != "") ? new NamedArguments(array('primaryKey' => $workflowID)) : NULL;
		$workflow = new Workflow($workflowValue);

		$workflow->workflowName = '';
		$workflow->acquisitionTypeIDValue = $postInput['acquisitionTypeID'];
		$workflow->resourceFormatIDValue = $postInput['resourceFormatID'];
		$workflow->resourceTypeIDValue = $postInput['resourceTypeID'];


		try {
			$workflow->save();
			$workflowID = $workflow->primaryKey;

			$steps = $postInput['steps'];

			//We'll want to insert all the new steps, update the remaining steps, and then remove any existing steps that weren't included in this.
			$priorStepKeyArray= [];
			foreach($steps as $step){
				//all "new" steps have 'new' as a prefix in the id.
				$isNewStep = (strpos($step['id'], 'new') !== FALSE);
				$stepID = ($isNewStep) ? NULL : new NamedArguments(array('primaryKey' => $step['id']));
				$stepInsert = new Step($stepID);
				$stepInsert->workflowID = $workflowID;
				$stepInsert->stepName = $step['stepName'];
				$stepInsert->userGroupID = $step['groupID'];
				$stepInsert->priorStepID = '';
				$stepInsert->displayOrderSequence = $step['order'];
				try {
					$stepInsert->save();
					$priorStepKeyArray["{$step['id']}"] = $stepInsert->primaryKey;
				} catch (Exception $e) {
					echo "<span class='error'>";
					echo $e->getMessage();
					echo "</span>";
				}
			}
			//Now go through and update the Prior Steps.
			foreach($steps as $step){
				if($step['priorStep'] == ""){continue;}
				$stepID = $step['id'];
				$priorStepID = $step['priorStep'];
				$updatedStepID = new NamedArguments(array('primaryKey' => $priorStepKeyArray[$stepID]));
				$updatedPriorStepID = $priorStepKeyArray[$priorStepID];
				$stepInsert = new Step($updatedStepID);
				$stepInsert->priorStepID = $updatedPriorStepID;
				try {
					$stepInsert->save();
				} catch (Exception $e) {
					echo "<span class='error'>";
					echo $e->getMessage();
					echo "</span>";
				}
			}

			//Now delete any steps that weren't included as new or existing steps.
			$validSteps = array_values($priorStepKeyArray);
			$stepsToKeep = implode(",", $validSteps);
			$workflow->removeAllStepsExcept($stepsToKeep);
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

?>
