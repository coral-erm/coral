<?php

/*
**************************************************************************************************************************
** CORAL Resources Module v. 1.0
**
** Copyright (c) 2010 University of Notre Dame
**
** This file is part of CORAL.
**
** CORAL is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
**
** CORAL is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License along with CORAL.  If not, see <http://www.gnu.org/licenses/>.
**
**************************************************************************************************************************
*/


class ResourceStep extends DatabaseObject {

	protected function defineRelationships() {}

	protected function overridePrimaryKeyName() {}

	//comes from ajax_processing when an approved user marks a step complete
	//marks step complete and starts the next step(s)
	//if this is the last step, marks the entire resource workflow complete
	//send notification to creator and master email that the entire workflow is complete
	public function completeStep(){

		//mark this step complete
		$this->stepEndDate = date( 'Y-m-d' );
		$this->endLoginID = CoralSession::get('loginID');
		$this->save;

        $this->startNextStepsOrComplete();

	}

    public function startNextStepsOrComplete(){
        //if there are next steps, start them
        $nextStepArray = $this->getNextSteps();

        if (count($nextStepArray) > 0){
            foreach ($nextStepArray as $nextResourceStep){

                $nextResourceStep->startStep();

            }
        }

        //check if this branch is complete or if there are still other steps open
        if ($this->getNumberOfOpenSteps() == 0){

            //if there are no more steps then we can mark the resource complete
            $resource = new Resource(new NamedArguments(array('primaryKey' => $this->resourceID)));
            $resource->completeWorkflow();

        }

    }

	public function startStep(){

		//start this step
		$this->stepStartDate = date( 'Y-m-d' );
		$this->save();

		//send notifications
		$this->sendApprovalNotification();

	}

    public function restartReassignedStep(){
        //restart step if it's active
		if (!is_null($this->stepStartDate)){
			$this->stepStartDate = date( 'Y-m-d' );

			$this->sendReassignedStepNotification();
		}
        $this->save();
    }

	//returns array of resource step objects
	public function getNextSteps(){

		$query = "SELECT * FROM ResourceStep
					WHERE priorStepID = '" . $this->stepID . "'
					AND resourceID = '" . $this->resourceID . "'
					ORDER BY resourceStepID";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceStepID'])){
			$object = new ResourceStep(new NamedArguments(array('primaryKey' => $result['resourceStepID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new ResourceStep(new NamedArguments(array('primaryKey' => $row['resourceStepID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}

	//returns prior resource step object (resource step can only have one prior step)
	public function getPriorStep(){

		$query = "SELECT * FROM ResourceStep
					WHERE stepID = '" . $this->priorStepID . "'
					AND resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query, 'assoc');


		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceStepID'])){
			$rs = new ResourceStep(new NamedArguments(array('primaryKey' => $result['resourceStepID'])));
		}

		return $rs;
	}


    public function isComplete() {
        return $this->stepEndDate ? true : false;
    }


	//returns array of resource step objects
	public function getNumberOfOpenSteps(){

		$query = "SELECT count(*) countSteps FROM ResourceStep
					WHERE resourceID = '" . $this->resourceID . "'
					AND (stepEndDate IS NULL OR stepEndDate = '0000-00-00')";

		$result = $this->db->processQuery($query, 'assoc');

		if (isset($result['countSteps'])){
			return $result['countSteps'];
		}


	}



	//returns an array of later open step objects
	public function getLaterOpenSteps(){
		$query = "SELECT * FROM ResourceStep
					WHERE resourceID = '" . $this->resourceID . "'
					AND displayOrderSequence > " . $this->displayOrderSequence . "
					AND (stepEndDate IS NULL OR stepEndDate = '0000-00-00')
					ORDER BY resourceStepID";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceStepID'])){
			$object = new ResourceStep(new NamedArguments(array('primaryKey' => $result['resourceStepID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new ResourceStep(new NamedArguments(array('primaryKey' => $row['resourceStepID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}



	//sends email to the approval user group for this step
	public function sendApprovalNotification(){

		$util = new Utility();

		$userGroup = new UserGroup(new NamedArguments(array('primaryKey' => $this->userGroupID)));
		$resource = new Resource(new NamedArguments(array('primaryKey' => $this->resourceID)));

		//only send if there is an email address set up for this group
		if ($userGroup->emailAddress){
			if (($this->priorStepID) && ($this->priorStepID != '0')){
				$priorResourceStep = $this->getPriorStep();
				$priorStepName = $priorResourceStep->stepName;
                if ($priorResourceStep){
                    $priorStepName = $priorResourceStep->stepName;
                    $messageType='ResourceQueue';
                }else{
                    $messageType='DeletedPriorStep';
                }

			}else{
				$messageType='NewResource';
				$priorStepName = '';
			}



			//formulate emil to be sent
			$email = new Email();
			$email->message = $util->createMessageFromTemplate($messageType, $this->resourceID, $resource->titleText, $priorStepName, '','');
			$email->to 			= $userGroup->emailAddress;
			$email->subject		= "CORAL Alert: " . $resource->titleText;

			$email->send();
		}


	}

    //sends email to the reassigned user group
    public function sendReassignedStepNotification(){

        $util = new Utility();

        $userGroup = new UserGroup(new NamedArguments(array('primaryKey' => $this->userGroupID)));
        $resource = new Resource(new NamedArguments(array('primaryKey' => $this->resourceID)));

        //only send if there is an email address set up for this group
        if ($userGroup->emailAddress){

            //formulate emil to be sent
            $email = new Email();
            $email->message = $util->createMessageFromTemplate('ReassignedStep', $this->resourceID, $resource->titleText, $this->stepName, '','');
            $email->to 			= $userGroup->emailAddress;
            $email->subject		= "CORAL Alert: " . $resource->titleText;

            $email->send();
        }


    }

}

?>
