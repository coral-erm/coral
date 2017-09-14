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

class Workflow extends DatabaseObject {

	protected function defineRelationships() {}

	protected function overridePrimaryKeyName() {}


	//returns array of step objects
	public function getSteps(){

		$query = "SELECT * FROM Step
					WHERE workflowID = '" . $this->workflowID . "'
					ORDER BY stepID";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['stepID'])){
			$object = new Step(new NamedArguments(array('primaryKey' => $result['stepID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new Step(new NamedArguments(array('primaryKey' => $row['stepID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}


	//returns array of step objects
	public function removeSteps(){

		$query = "DELETE FROM Step
					WHERE workflowID = '" . $this->workflowID . "'
					ORDER BY stepID";

		$result = $this->db->processQuery($query);

	}



	//pass in resource type, resource format, acquisition type
	//queries database to determine appropriate workflow
	public function getWorkflowID($resourceTypeID, $resourceFormatID, $acquisitionTypeID){

		//first try a match on all 3
		$query = "SELECT workflowID FROM Workflow
					WHERE resourceTypeIDValue= '" . $resourceTypeID . "'
					AND resourceFormatIDValue= '" . $resourceFormatID . "'
					AND acquisitionTypeIDValue= '" . $acquisitionTypeID . "'";

		$result = $this->db->processQuery($query, 'assoc');

		if (isset($result['workflowID'])){
			return $result['workflowID'];
		}else{
			//if match on 3 doesn't work, try match on 2
			$query = "SELECT distinct workflowID FROM Workflow
						WHERE acquisitionTypeIDValue= '" . $acquisitionTypeID . "' AND resourceFormatIDValue= '" . $resourceFormatID . "' AND ((resourceTypeIDValue= '') OR (resourceTypeIDValue IS NULL))";

			$result = $this->db->processQuery($query, 'assoc');

			if (isset($result['workflowID'])){
				return $result['workflowID'];;
			}else{
				return false;
			}
		}

	}


    public function cloneFrom($sourceID) {
        $source = new Workflow(new NamedArguments(array('primaryKey' => $sourceID)));
        $source->workflowID = null;
        $newWorkflowID = $source->saveAsNew();
        $source = new Workflow(new NamedArguments(array('primaryKey' => $sourceID)));
        $newWorkflow = new Workflow(new NamedArguments(array('primaryKey' => $newWorkflowID)));

        // Copy steps
        $stepsTable = array();
        foreach ($source->getSteps() as $step) {
            // Keeping old ids for prior steps
            $oldStepID = $step->stepID;
            $step->stepID = null;
            $newStepID = $step->saveAsNew();
            $stepsTable[$oldStepID] = $newStepID;
            $query = "UPDATE Step SET workflowID=" . $newWorkflowID . " WHERE StepID=" . $newStepID;
            $result = $this->db->processQuery($query);
        }

        // Updating prior steps
        foreach ($newWorkflow->getSteps() as $step) {
            if (isset($stepsTable[$step->priorStepID])) {
                $query = "UPDATE Step SET priorStepID=" . $stepsTable[$step->priorStepID] . " WHERE StepID=" . $step->stepID;
                $result = $this->db->processQuery($query);
            }
        }
        return $newWorkflowID;
    }



}

?>
