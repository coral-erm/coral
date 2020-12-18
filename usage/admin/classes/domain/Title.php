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

class Title extends DatabaseObject {

	protected function defineRelationships() {}

	protected function overridePrimaryKeyName() {}

	protected function defineAttributes() {
		$this->addAttribute('titleID');
		$this->addAttribute('title');
		$this->addAttribute('resourceType');
    $this->addAttribute('publicationDate');
    $this->addAttribute('articleVersion');
    $this->addAttribute('authors');
    $this->addAttribute('parentID');
    $this->addAttribute('componentID');
	}

	//returns identifier (issn, isbn, etc) only of the first print identifier found for this title
	public function getIdentifier($idType){
		$query = "SELECT * FROM TitleIdentifier ti
					WHERE ti.titleID = '" . $this->titleID . "'
					AND identifierType='" . $idType . "'
					ORDER BY 1
					LIMIT 1;";

		$result = $this->db->processQuery($query, 'assoc');

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['titleIdentifierID'])){
			$identifier =  $result['identifier'];
			return substr($identifier, 0, 4) . "-" . substr($identifier, 4, 4);
		}else{
			return;
		}


	}



	//find out if there is an existing identifier for a title
	public function getExistingIdentifier($identifier){

		$query = "SELECT distinct identifier
					FROM TitleIdentifier ti
					WHERE ti.titleID = '" . $this->titleID . "'
					AND identifier = '" . $identifier . "'
					ORDER BY identifierType DESC
					LIMIT 1;";

		$result = $this->db->processQuery($query, 'assoc');

		//only one identifier will be returned
		if (isset($result['identifier'])){
			return true;
		}else{
			return false;
		}


	}


	//returns array of identifier objects
	public function getIdentifiers($idType = null){

		if($idType){
			$addWhere = " AND identifierType='" . $idType . "'";
		}

		$query = "SELECT *
					FROM TitleIdentifier ti
					WHERE ti.titleID = '" . $this->titleID . "' " . $addWhere . "
					ORDER BY identifierType DESC;";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['titleIdentifierID'])){
			$object = new TitleIdentifier(new NamedArguments(array('primaryKey' => $result['titleIdentifierID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new TitleIdentifier(new NamedArguments(array('primaryKey' => $row['titleIdentifierID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}


	//returns array of title objects
	public function getRelatedTitles(){

		$query = "SELECT DISTINCT t.titleID
					FROM TitleIdentifier ti, Title t, TitleIdentifier ti2
					WHERE ti.titleID = t.titleID
					AND ti.identifier = ti2.identifier
					AND ti.identifierType = ti2.identifierType
					AND ti2.titleID = '" . $this->titleID . "';";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['titleID'])){
			$object = new Title(new NamedArguments(array('primaryKey' => $result['titleID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new Title(new NamedArguments(array('primaryKey' => $row['titleID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}




	//returns array of yearly stats for this title
	public function getYearlyStats($archiveInd, $year, $publisherPlatformID, $activityType){

		$addWhere = '';
		if ($activityType){
			$addWhere=" AND activityType='" . $activityType . "'";
		}
    if ($archiveInd){
      $addWhere=" AND archiveInd='" . $archiveInd . "'";
    }
		$query = "SELECT titleID, totalCount, ytdHTMLCount, ytdPDFCount
					FROM YearlyUsageSummary
					WHERE titleID = '" . $this->titleID . "'
					AND year='" . $year . "'" . $addWhere . "
					AND publisherPlatformID = '" . $publisherPlatformID . "';";

		$result = $this->db->processQuery($query, 'assoc');

		$allArray = array();
		$resultArray = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['titleID'])){
			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

			array_push($allArray, $resultArray);
		}else{
			foreach ($result as $row) {
				$resultArray = array();
				foreach (array_keys($row) as $attributeName) {
					$resultArray[$attributeName] = $row[$attributeName];
				}
				array_push($allArray, $resultArray);
			}
		}

		return $allArray;
	}

	public function getTitleIdByTitleIdentifier($identifier, $identifierType, $resourceType) {
    $query = "SELECT DISTINCT ti.titleID as titleID FROM TitleIdentifier ti INNER JOIN Title t USING (titleID) WHERE identifierType = '$identifierType' AND identifier = '$identifier' AND t.resourceType = '$resourceType' LIMIT 1;";
    $result = $this->db->processQuery($query, 'assoc');

    //need to do this since it could be that there's only one request and this is how the dbservice returns result
    if (!empty($result['titleID'])){
      return $result['titleID'];
    }else{
      return false;
    }
  }


	//returns array of the first listed identifier objects
	public function getByTitle($resourceType, $resourceTitle, $publisherPlatformID, $publicationDate = null, $authors = null, $articleVersion = null, $parentID = null, $componentID = null){

	  // Never retrieve an Item type using these identifiers, they should only be found with URIs or DOIs
	  if($resourceType == 'Item') {
      $query = "SELECT DISTINCT titleID FROM Title WHERE ucase(title) = ucase('$resourceTitle') AND resourceType = '$resourceType'";
      if (!empty($publicationDate)) {
        $query .= " AND ucase(publicationDate) = ucase('$publicationDate')";
      }
      if (!empty($authors)) {
        $escaped_authors = str_replace("'", "\'");
        $query .= " AND ucase(authors) = ucase('$escaped_authors')";
      }
      if (!empty($articleVersion)) {
        $query .= " AND ucase(articleVersion) = ucase('$articleVersion')";
      }
      if (!empty($parentID)) {
        $query .= " AND parentID = $parentID";
      }
      if (!empty($componentID)) {
        $query .= " AND componentID = $componentID";
      }
      $query .= ' LIMIT 1';
    // if the resource type is a platform, there are no identifiers, so just look for a matching platform
    } else if ($resourceType == 'Platform') {
      $query = "SELECT DISTINCT titleID FROM Title WHERE ucase(title) = ucase('$resourceTitle') AND resourceType = '$resourceType' LIMIT 1";
		//this is a title search so we're also searching on common platform / publisher (used for Databases probably primarily)
		} else {
			$query = "SELECT DISTINCT t.titleID as titleID FROM Title t INNER JOIN MonthlyUsageSummary mus ON (mus.titleID = t.titleID) WHERE publisherPlatformID = '" . $publisherPlatformID . "' AND ucase(title) = ucase('" . $resourceTitle . "') AND t.resourceType = '" . $resourceType . "' LIMIT 1;";
		}

		$result = $this->db->processQuery($query, 'assoc');

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['titleID'])){
			return $result['titleID'];
		}else{
			return false;
		}

	}




	//remove an entire month for this title/publisher
	public function deleteMonth($archiveInd, $year, $month, $publisherPlatformID){

		//now formulate query
		$query = "DELETE FROM MonthlyUsageSummary
					WHERE archiveInd = '" . $archiveInd . "'
					AND titleID = '" . $this->titleID . "'
					AND publisherPlatformID = '" . $publisherPlatformID . "'
					AND year = '"  . $year . "'
					AND month = '" . $month . "';";

		return $this->db->processQuery($query);

	}


	//remove an entire month for this title/publisher
	public function deleteYearlyStats($archiveInd, $year, $publisherPlatformID, $activityType){

		if ($activityType){
			$addWhere = " AND activityType='" . $activityType . "'";
		} else {
			$addWhere = null;
		}

		//now formulate query
		$query = "DELETE FROM YearlyUsageSummary
					WHERE archiveInd = '" . $archiveInd . "'
					AND titleID = '" . $this->titleID . "'
					AND publisherPlatformID = '" . $publisherPlatformID . "' " . $addWhere . "
					AND year = '"  . $year . "';";

		return $this->db->processQuery($query);

	}


	//returns usage count only
	public function getUsageCountByMonth($archiveInd, $year, $month, $publisherPlatformID){

		$query = "SELECT usageCount FROM MonthlyUsageSummary
					WHERE archiveInd = '" . $archiveInd . "'
					AND titleID = '" . $this->titleID . "'
					AND publisherPlatformID = '" . $publisherPlatformID . "'
					AND year = '"  . $year . "'
					AND month = '" . $month . "';";



		$result = $this->db->processQuery($query, 'assoc');

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['usageCount'])){
			return $result['usageCount'];
		}else{
			return false;
		}

	}





	//returns array of the first listed identifier objects
	public function getTotalCountByYear($archiveInd, $year, $publisherPlatformID){

		$query = "SELECT totalCount usageCount, ytdHTMLCount, ytdPDFCount FROM YearlyUsageSummary
					WHERE archiveInd = '" . $archiveInd . "'
					AND titleID = '" . $this->titleID . "'
					AND publisherPlatformID = '" . $publisherPlatformID . "'
					AND year = '"  . $year . "';";



		$result = $this->db->processQuery($query, 'assoc');

		$resultArray = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['usageCount'])){
			 $resultArray['usageCount'] = $result['usageCount'];
			 $resultArray['ytdHTMLCount'] = $result['ytdHTMLCount'];
			 $resultArray['ytdPDFCount'] = $result['ytdPDFCount'];

			 return $resultArray;
		}else{
			return false;
		}

	}



	//returns array of yearly stats for this title
	public function get12MonthUsageCount($archiveInd, $publisherPlatformID, $layoutID, $yearAddWhere){

		$query = "SELECT usageCount FROM MonthlyUsageSummary
					WHERE archiveInd = '" . $archiveInd . "'
					AND titleID = '" . $this->titleID . "'
					AND layoutID = '" . $layoutID . "'
					AND publisherPlatformID = '" . $publisherPlatformID . "'
					AND "  . $yearAddWhere . ";";

		$result = $this->db->processQuery($query, 'assoc');

		$allArray = array();
		$resultArray = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['usageCount'])){
			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

			array_push($allArray, $resultArray);
		}else{
			if ($result){
				foreach ($result as $row) {
					$resultArray = array();
					if ($resultArray){
						foreach (array_keys($row) as $attributeName) {
							$resultArray[$attributeName] = $row[$attributeName];
						}
					}
					array_push($allArray, $resultArray);
				}
			}
		}

		return $allArray;
	}


  public function delete() {
	  foreach($this->getIdentifiers() as $identifier) {
	    $identifier->delete();
    }
	  parent::delete();
  }








}

?>
