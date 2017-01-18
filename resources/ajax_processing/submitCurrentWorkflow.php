<?php

		$resourceID = $_POST['resourceID'];

        $resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));

		try {

			$stepNameArray = array();
			$stepNameArray = explode(':::',$_POST['stepNames']);
			$userGroupArray = array();
			$userGroupArray = explode(':::',$_POST['userGroups']);
			$priorStepArray = array();
			$priorStepArray = explode(':::',$_POST['priorSteps']);
			$stepIDArray = array();
			$stepIDPriorArray = array();
			$oldStepIDArray = array();
            $oldStepIDArray = explode(':::',$_POST['stepIDs']);
			$seqOrderArray = array();
			$seqOrderArray = explode(':::',$_POST['seqOrders']);
			$actionArray = array();
            $actionArray = explode(':::',$_POST['actions']);
			$mailReminderDelayArray = array();
            $mailReminderDelayArray = explode(':::',$_POST['mailReminderDelays']);



			foreach ($stepNameArray as $key => $value){
				if (trim($value)){

                    if ($actionArray[$key] == "delete") {
                        if ($oldStepIDArray[$key] != -1 ) {
                            $oldStep = new ResourceStep(new NamedArguments(array('primaryKey' => $oldStepIDArray[$key])));
                            $oldStep->delete();
                        }
                        continue;
                    } 

					$rstep = new ResourceStep();

                    // Getting previous information if available
                    if ($oldStepIDArray[$key] != -1) {
                        $oldStep = new ResourceStep(new NamedArguments(array('primaryKey' => $oldStepIDArray[$key])));
                        $rstep->stepStartDate = $oldStep->stepStartDate;
                        $rstep->stepEndDate = $oldStep->stepEndDate;
                        $rstep->displayOrderSequence = $oldStep->displayOrderSequence;
                        $rstep->stepID = $oldStep->stepID;
                        $rstep->priorStepID = $oldStep->priorStepID;
                        $rstep->mailReminderDelay = $oldStep->mailReminderDelay;
                        $oldStep->delete();
                        unset($oldStep);
                    }

					$rstep->stepName = trim($value);

                    // Also save in the Step table with workflowID=0
                    // (so we can set priorStepID)
                    if (!$rstep->stepID) {
                        $step = new Step();
                        $step->stepName = $rstep->stepName;
                        try {
                            $step->save();
                            $stepID = $step->stepID;
                            $rstep->stepID = $step->stepID;
                        } catch (Exception $e) {
                            echo $e->getMessage();
                        }
                    }
  
					$rstep->displayOrderSequence = $seqOrderArray[$key];
					$rstep->resourceID = $resourceID;
					$rstep->userGroupID = $userGroupArray[$key];
					$rstep->mailReminderDelay = $mailReminderDelayArray[$key];
                    if ($rstep->priorStepID == null) {
                        $rstep->priorStepID = $priorStepArray[$key];
                    }

					try {
						$rstep->save();
						$rstepID = $rstep->primaryKey;

					} catch (Exception $e) {
						echo $e->getMessage();
					}

                  
                }
			}


		} catch (Exception $e) {
			echo $e->getMessage();
		}
?>
