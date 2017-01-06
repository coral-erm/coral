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

class Fund extends DatabaseObject {

	protected function defineRelationships() {}

	public function allAsArray() {
		$query = "SELECT * FROM Fund ORDER BY 3";
		$result = $this->db->processQuery($query, 'assoc');

		$resultArray = array();
		$rowArray = array();

		if (isset($result['fundCode'])){

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

	//returns number of children for this particular contact role
	public function getNumberOfChildren(){

		$query = "SELECT count(*) childCount FROM ResourcePayment WHERE fundID = '" . $this->fundID. "';";

		$result = $this->db->processQuery($query, 'assoc');

		return $result['childCount'];
	}

    public function getFundIDFromFundCode($fundCode) {
        $query = "SELECT fundID FROM Fund WHERE fundCode = '" . $fundCode . "';";
        $result = $this->db->processQuery($query, 'assoc');
        return isset($result['fundID']) ? $result['fundID'] : null;
    }

	//returns array of archived objects
		public function getUnArchivedFunds(){

		$query = "SELECT * FROM Fund WHERE archived is null ORDER BY 3";
				$result = $this->db->processQuery($query, 'assoc');

				$resultArray = array();
				$rowArray = array();

				if (isset($result['fundCode'])){

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

		//get all unarchived ones and include the archived if it was selected previously
		public function getUnArchivedFundsForCostHistory($fundID){

				$query = "SELECT * FROM Fund WHERE fundID = ". $fundID . " OR archived is null OR archived = 0 ORDER BY 3";

				$result = $this->db->processQuery($query, 'assoc');

				$resultArray = array();
				$rowArray = array();

				if (isset($result['fundCode'])){

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
}

?>
