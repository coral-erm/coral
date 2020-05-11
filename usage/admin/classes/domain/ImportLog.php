<?php

/*
**************************************************************************************************************************
** CORAL Usage Statistics Module
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

class ImportLog extends DatabaseObject {

	protected function defineRelationships() {}

	protected function overridePrimaryKeyName() {}


	static public function shortStatusFromDetails($details){
		//first, find if titles were processed
		preg_match('/[0-9]+ titles processed/i', $details, $matches);
		if (isset($matches[0])) {
			$titles = str_replace(" processed", "", $matches[0]);
		}

		if (isset($titles)){
			if (preg_match("/finished/i", $details)){
				$status = $titles . _(" (via sushi)");
			}else{
				$status = $titles;
			}
		}else{
			if (preg_match("/fail/i", $details)){
				$status = _("Failed");
			}elseif (preg_match("/finished/i", $details)){
				$status = "<i>"._("awaiting import")."</i>";
			} else {
				$status = null;
			}
		}
		return $status;
	}


	//returns array of Platform records
	public function getPlatforms(){

		//now formulate query
		$query = "SELECT platformID
					FROM ImportLogPlatformLink
					WHERE importLogID = '" . $this->importLogID . "'";


		$result = $this->db->processQuery(stripslashes($query), 'assoc');


		$searchArray = array();
		$resultArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['platformID'])){

			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

			array_push($searchArray, $resultArray);
		}else{
			foreach ($result as $row) {
				$resultArray = array();
				foreach (array_keys($row) as $attributeName) {
					$resultArray[$attributeName] = $row[$attributeName];
				}
				array_push($searchArray, $resultArray);
			}
		}

		return $searchArray;


	}



	//returns array of import log records
	public function getImportLogRecords($limit){

		if ($limit != ""){
			$limitStatement = " LIMIT " . $limit;
		}else{
			$limitStatement = "";
		}


		//now formulate query
		$query = "SELECT importLogID, loginID, importDateTime dateTime, fileName, archiveFileURL, logFileURL, details
					FROM ImportLog
					ORDER BY importDateTime DESC " . $limitStatement;


		$result = $this->db->processQuery(stripslashes($query), 'assoc');


		$searchArray = array();
		$resultArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['importLogID'])){

			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

			array_push($searchArray, $resultArray);
		}else{
			foreach ($result as $row) {
				$resultArray = array();
				foreach (array_keys($row) as $attributeName) {
					$resultArray[$attributeName] = $row[$attributeName];
				}
				array_push($searchArray, $resultArray);
			}
		}

		return $searchArray;


	}



	//returns array of import log records from user "sushi"
	public function getSushiImports(){

		//now formulate query
		$query = "SELECT importLogID, loginID, importDateTime, fileName, archiveFileURL, logFileURL, details
					FROM ImportLog
					WHERE loginID = 'sushi'
					AND fileName is not null
					AND ucase(details) not like '%FAIL%'
					ORDER BY importDateTime DESC";


		$result = $this->db->processQuery(stripslashes($query), 'assoc');


		$searchArray = array();
		$resultArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['importLogID'])){

			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

			array_push($searchArray, $resultArray);
		}else{
			foreach ($result as $row) {
				$resultArray = array();
				foreach (array_keys($row) as $attributeName) {
					$resultArray[$attributeName] = $row[$attributeName];
				}
				array_push($searchArray, $resultArray);
			}
		}

		return $searchArray;


	}

	public function delete() {
	  $deleteImportLogPlatformLinksQuery = 'DELETE FROM ImportLogPlatformLink WHERE importLogID = '.$this->importLogID;
    $this->db->processQuery($deleteImportLogPlatformLinksQuery);
	  if (!empty($this->logFileURL)) {
	    unlink(__DIR__.'/../../../'.$this->logFileURL);
    }
    if (!empty($this->archiveFileURL)) {
      unlink(__DIR__.'/../../../'.$this->archiveFileURL);
    }
    parent::delete();
  }




}

?>
