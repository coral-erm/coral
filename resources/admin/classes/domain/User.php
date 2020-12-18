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

class User extends DatabaseObject {

	protected function defineRelationships() {}

	protected function overridePrimaryKeyName() {
		$this->primaryKeyName = 'loginID';
	}




	//used to formulate display name in case firstname/last name isn't set up or user doesn't exist
	//format: firstname {space} lastname
	public function getDisplayName(){

		if ($this->firstName){
			return $this->firstName . " " . $this->lastName;
		}else if ($this->loginID){
			return $this->loginID;
		}else{
			return false;
		}
	}


	//used to formulate display name in case firstname/last name isn't set up or user doesn't exist for drop down
	//format lastname {comma} firstname
	public function getDDDisplayName(){

		if ($this->firstName){
			return $this->lastName . ", " . $this->firstName;
		}else if ($this->loginID){
			return $this->loginID;
		}
	}


	//used only for allowing access to admin page
	public function isAdmin(){
		$privilege = new Privilege(new NamedArguments(array('primaryKey' => $this->privilegeID)));
		if (mb_strtoupper($privilege->shortName) == 'ADMIN'){
			return true;
		}else{
			return false;
		}

	}

	//used for displaying add/update/delete links
	public function canEdit(){
		$privilege = new Privilege(new NamedArguments(array('primaryKey' => $this->privilegeID)));

		if ((mb_strtoupper($privilege->shortName) == 'ADD/EDIT') || (mb_strtoupper($privilege->shortName) == 'ADMIN')){
			return true;
		}else{
			return false;
		}

	}



	public function allAsArray() {
		$query = "SELECT * FROM User ORDER BY lastName, loginID";
		$result = $this->db->processQuery($query, 'assoc');

		$resultArray = array();
		$rowArray = array();

		if (isset($result['loginID'])){
			foreach (array_keys($result) as $attributeName) {
				$rowArray[$attributeName] = $result[$attributeName];
			}
			array_push($resultArray, $rowArray);
		}else{
			foreach ($result as $row) {
				foreach (array_keys($this->attributeNames) as $attributeName) {
					$rowArray[$attributeName] = $row[$attributeName];
				}
				array_push($resultArray, $rowArray);
			}
		}

		return $resultArray;
	}


	//returns array of resource records that are saved/submitted for this user
	public function getResourcesInQueue($statusName){

		$status = new Status();
		$statusID = $status->getIDFromName($statusName);

		$query = "SELECT resourceID, date_format(createDate, '%c/%e/%Y') createDate, titleText, statusID
			FROM Resource
			WHERE statusID = '" . $statusID . "'
			AND createLoginID = '" . $this->loginID . "'
			ORDER BY 1 desc LIMIT 0,25";

		$result = $this->db->processQuery($query, 'assoc');

		$resourceArray = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceID'])){

			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

			array_push($resourceArray, $resultArray);

		}else{
			foreach ($result as $row) {
				$resultArray = array();
				foreach (array_keys($row) as $attributeName) {
					$resultArray[$attributeName] = $row[$attributeName];
				}

				array_push($resourceArray, $resultArray);
			}
		}


		return $resourceArray;
	}






	//returns array of resource arrays that are in the outstanding queue for this user
	public function getOutstandingTasks(){

		$status = new Status();
		$excludeStatus =  Array();
		$excludeStatus[]=$status->getIDFromName('complete');
		$excludeStatus[]=$status->getIDFromName('archive');

		if (count($excludeStatus) > 1){
			$whereAdd = "AND R.statusID NOT IN (" . implode(",", $excludeStatus) . ")";
		}else if (count($excludeStatus) == 1){
			$whereAdd = "AND R.statusID != '" . implode("", $excludeStatus) . "'";
		}else{
			$whereAdd = "";
		}

		$query = "SELECT DISTINCT R.resourceID, date_format(createDate, '%c/%e/%Y') createDate, titleText, statusID, 
            RA.resourceAcquisitionID, RA.acquisitionTypeID,
            date_format(subscriptionStartDate, '%c/%e/%Y') subscriptionStartDate,
            date_format(subscriptionEndDate, '%c/%e/%Y') subscriptionEndDate
			FROM Resource R, ResourceAcquisition RA, ResourceStep RS, UserGroupLink UGL
			WHERE R.resourceID = RA.resourceID
            AND RA.resourceAcquisitionID = RS.resourceAcquisitionID
			AND RS.userGroupID = UGL.userGroupID
			AND UGL.loginID = '" . $this->loginID . "'
			AND (RS.stepEndDate IS NULL OR RS.stepEndDate = '0000-00-00')
			AND (RS.stepStartDate IS NOT NULL AND RS.stepStartDate != '0000-00-00')
			" . $whereAdd . "
			ORDER BY 1 desc";

		$result = $this->db->processQuery($query, 'assoc');

		$resourceArray = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceID'])){

			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

			array_push($resourceArray, $resultArray);

		}else{
			foreach ($result as $row) {
				$resultArray = array();
				foreach (array_keys($row) as $attributeName) {
					$resultArray[$attributeName] = $row[$attributeName];
				}

				array_push($resourceArray, $resultArray);
			}
		}


		return $resourceArray;
	}






	//returns array of tasks that are in the outstanding queue for this resource and user
	public function getOutstandingTasksByResource($outstandingResourceID){

		$status = new Status();
		$excludeStatus =  Array();
		$excludeStatus[]=$status->getIDFromName('complete');
		$excludeStatus[]=$status->getIDFromName('archive');

		if (count($excludeStatus) > 1){
			$whereAdd = "AND R.statusID NOT IN (" . implode(",", $excludeStatus) . ")";
		}else if (count($excludeStatus) == 1){
			$whereAdd = "AND R.statusID != '" . implode("", $excludeStatus) . "'";
		}else{
			$whereAdd = "";
		}

		$query = "SELECT DISTINCT RS.stepName, date_format(stepStartDate, '%c/%e/%Y') startDate
			FROM Resource R, ResourceAcquisition RA, ResourceStep RS, UserGroupLink UGL
			WHERE R.resourceID = RA.resourceID
            AND RA.resourceAcquisitionID = RS.resourceAcquisitionID
			AND RS.userGroupID = UGL.userGroupID
			AND UGL.loginID = '" . $this->loginID . "'
			AND R.resourceID = '" . $outstandingResourceID . "'
			AND (RS.stepEndDate IS NULL OR RS.stepEndDate = '0000-00-00')
			AND (RS.stepStartDate IS NOT NULL AND RS.stepStartDate != '0000-00-00')
			" . $whereAdd . "
			ORDER BY 1 desc LIMIT 0,25";

		$result = $this->db->processQuery($query, 'assoc');

		$resourceArray = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['stepName'])){

			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

			array_push($resourceArray, $resultArray);

		}else{
			foreach ($result as $row) {
				$resultArray = array();
				foreach (array_keys($row) as $attributeName) {
					$resultArray[$attributeName] = $row[$attributeName];
				}

				array_push($resourceArray, $resultArray);
			}
		}


		return $resourceArray;
	}




	public function isInGroup($groupID) {
		$query = "SELECT DISTINCT userGroupID FROM UserGroupLink WHERE loginID = '" . $this->loginID . "' AND userGroupID='" . $groupID . "'";
		$result = $this->db->processQuery($query, 'assoc');

		if (isset($result['userGroupID'])){
			return true;
		}else{
			return false;
		}

	}



	public function hasOpenSession() {
		$util = new Utility();
		$config = new Configuration();

		$dbName = $config->settings->authDatabaseName;
		$sessionID = $util->getSessionCookie();


		$query = "SELECT DISTINCT sessionID FROM " . $dbName . ".Session WHERE loginID = '" . $this->loginID . "' AND sessionID='" . $sessionID . "'";
		$result = $this->db->processQuery($query, 'assoc');

		if (isset($result['sessionID'])){
			return true;
		}else{
			return false;
		}

	}





}

?>
