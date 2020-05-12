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

class Platform extends DatabaseObject {

	protected function defineRelationships() {}

	protected function overridePrimaryKeyName() {}


	protected function defineAttributes() {
		$this->addAttribute('platformID');
		$this->addAttribute('organizationID');
		$this->addAttribute('name');
		$this->addAttribute('reportDisplayName');
		$this->addAttribute('reportDropDownInd');
	}



	//override of database object all() function
	//returns array of all platform objects - needed for proper ordering
	public function all(){

		$query = "SELECT *
					FROM Platform
					ORDER BY name;";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['platformID'])){
			$object = new Platform(new NamedArguments(array('primaryKey' => $result['platformID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new Platform(new NamedArguments(array('primaryKey' => $row['platformID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}

	//returns array of publisher platform note objects
	public function getPlatformNotes(){

		$query = "SELECT *
					FROM PlatformNote
					WHERE platformID='" . $this->platformID . "';";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['platformNoteID'])){
			$object = new PlatformNote(new NamedArguments(array('primaryKey' => $result['platformNoteID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new PlatformNote(new NamedArguments(array('primaryKey' => $row['platformNoteID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}

	//returns array of importlog objects
	public function getImportLogs(){

		$query = "SELECT *
					FROM ImportLog
					WHERE importLogID IN (select importLogID from ImportLogPlatformLink WHERE platformID ='" . $this->platformID . "')
					Order by importDateTime desc;";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['importLogID'])){
			$object = new ImportLog(new NamedArguments(array('primaryKey' => $result['importLogID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new ImportLog(new NamedArguments(array('primaryKey' => $row['importLogID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}


	//returns most recent date of the last month of imports for any titles under this platform
	public function getLastImportDate(){

		$query = "SELECT max(concat(year,'-',month,'-01')) max_month
					FROM MonthlyUsageSummary tsm INNER JOIN Title USING (titleID), PublisherPlatform pp
					WHERE pp.publisherPlatformID = tsm.publisherPlatformID
					AND pp.platformID = '" . $this->platformID . "';";

		$result = $this->db->processQuery(stripslashes($query), 'assoc');

		if (isset($result['max_month'])){
			return $result['max_month'];
		}

	}




	//returns array of import log records from user "sushi"
	public function removeFailedSushiImports(){

		//now formulate query
		$query = "DELETE FROM ImportLog
					WHERE loginID = 'sushi'
					AND importLogID IN (SELECT importLogID FROM ImportLogPlatformLink WHERE platformID = '" . $this->platformID . "')
					AND ucase(details) like '%FAIL%'";


		return $this->db->processQuery($query);

	}






	//returns array of platforms - used on publisherPlatformList.php
	public function getPlatformArray(){


		//now formulate query
		$query = "SELECT * FROM Platform ORDER BY name;";


		$result = $this->db->processQuery(stripslashes($query), 'assoc');


		$allArray = array();
		$resultArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['platformID'])){

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


	//returns array of external login objects
	public function getExternalLogins(){

		$query = "SELECT *
					FROM ExternalLogin
					WHERE platformID='" . $this->platformID . "';";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['externalLoginID'])){
			$object = new ExternalLogin(new NamedArguments(array('primaryKey' => $result['externalLoginID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new ExternalLogin(new NamedArguments(array('primaryKey' => $row['externalLoginID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}


	//returns array of titles and identifiers
  public function getTitles($type = null) {
    $query = "SELECT DISTINCT t.titleID titleID, t.title title,
					MAX(IF(ti.identifierType='DOI',identifier,null)) doi,
					MAX(IF(ti.identifierType='URI',identifier,null)) uri,
					MAX(IF(ti.identifierType='ISBN', identifier, null)) isbn,
					MAX(IF(ti.identifierType='Proprietary Identifier',identifier,null)) pi,
					MAX(IF(ti.identifierType='ISSN', concat(substr(ti.identifier,1,4), '-', substr(ti.identifier,5,4)),null)) issn,
					MAX(IF(ti.identifierType='eISSN', concat(substr(ti.identifier,1,4), '-', substr(ti.identifier,5,4)),null)) eissn
					FROM MonthlyUsageSummary mus, PublisherPlatform pp, Title t
					LEFT JOIN TitleIdentifier ti ON t.titleID = ti.titleID
					WHERE pp.publisherPlatformID = mus.publisherPlatformID
					AND mus.titleID = t.titleID
					AND pp.platformID = '" . $this->platformID . "'";
    if (!empty($type)) {
      $query .= "AND resourceType = '$type'";
    }
    $query .= "GROUP BY t.titleID, t.title ORDER BY title";

    $result = $this->db->processQuery($query, 'assoc');

    $allArray = array();
    $resultArray = array();

    //need to do this since it could be that there's only one result and this is how the dbservice returns result
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



	//returns array of publisher platform objects
	public function getPublisherPlatforms(){

		$query = "SELECT publisherPlatformID
					FROM PublisherPlatform pp, Publisher
					WHERE pp.publisherID = Publisher.publisherID
					AND platformID = '" . $this->platformID . "'
					ORDER BY Publisher.name;";


		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['publisherPlatformID'])){
			$object = new PublisherPlatform(new NamedArguments(array('primaryKey' => $result['publisherPlatformID'])));
			array_push($objects, $object);
		}else{
            $db = DBService::getInstance();
			foreach ($result as $row) {
				$object = new PublisherPlatform(new NamedArguments(array('primaryKey' => $row['publisherPlatformID'],'db'=>$db)));
				array_push($objects, $object);
			}
		}

		return $objects;
	}



	//returns array of monthly outlier records
	public function getMonthlyOutliers($resourceType, $archiveInd, $year, $month){


		//now formulate query
		$query = "select monthlyUsageSummaryID, Title, archiveInd, usageCount, overrideUsageCount, color
				from MonthlyUsageSummary tsm, Title t, Outlier o, PublisherPlatform pp
				where tsm.titleID = t.titleID
				and o.outlierID = tsm.outlierID
				and pp.publisherPlatformID = tsm.publisherPlatformID
				and platformID='" . $this->platformID . "'
				and archiveInd='" . $archiveInd . "'
				and resourceType='" . $resourceType . "'
				and year='" . $year . "'
				and month='" . $month . "' and ignoreOutlierInd = 0
				order by 1,2,3;";


		$result = $this->db->processQuery(stripslashes($query), 'assoc');


		$allArray = array();
		$resultArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['monthlyUsageSummaryID'])){

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



	//returns array of yearly override records
	public function getYearlyOverrides($resourceType, $archiveInd, $year){

		//now formulate query
		$query = "SELECT DISTINCT yearlyUsageSummaryID, Title, totalCount, ytdHTMLCount, ytdPDFCount, overrideTotalCount, overrideHTMLCount, overridePDFCount
					FROM YearlyUsageSummary tsy, MonthlyUsageSummary tsm, Title t, PublisherPlatform pp
					WHERE tsy.titleID = t.titleID
					AND tsm.publisherPlatformID = tsy.publisherPlatformID
					AND tsm.titleID = tsy.titleID
					AND tsm.year = tsy.year
					AND tsm.archiveInd = tsy.archiveInd
					AND tsm.outlierID > 0
					AND pp.publisherPlatformID = tsm.publisherPlatformID
					AND pp.platformID='" . $this->platformID . "'
					AND tsy.archiveInd='" . $archiveInd . "'
					AND tsy.year='" . $year . "'
					AND t.resourceType='" . $resourceType . "'
					AND ignoreOutlierInd = 0;";


		$result = $this->db->processQuery(stripslashes($query), 'assoc');


		$allArray = array();
		$resultArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['yearlyUsageSummaryID'])){

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



	//returns array of full statistics info for display
	public function getFullStatsDetails($resourceType = null){

		if ($resourceType){
			$addWhere = "AND t.resourceType = '" . $resourceType . "'";
		} else {
			$addWhere = "";
		}

		//now formulate query
		$query = "SELECT DISTINCT resourceType, year, GROUP_CONCAT(DISTINCT concat(month, '|', if(ignoreOutlierInd=0,ifnull(outlierID,0),0)) ORDER BY month, 1 SEPARATOR ',') months, archiveInd, MAX(month) max_month, MIN(month) min_month, MAX(IF(ignoreOutlierInd=0,outlierID,null)) outlierID
					FROM PublisherPlatform pp, MonthlyUsageSummary tsm INNER JOIN Title t USING (titleID)
					WHERE pp.platformID = '" . $this->platformID . "'
					AND pp.publisherPlatformID = tsm.publisherPlatformID " . $addWhere . "
					GROUP BY resourceType, year, archiveInd
					ORDER BY resourceType desc, year desc, archiveInd, month;";

		$result = $this->db->processQuery(stripslashes($query), 'assoc');


		$allArray = array();
		$resultArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['year'])){

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

  public function statOverview() {
    $query = "SELECT
            resourceType, year, month, archiveInd, MAX(IF(ignoreOutlierInd=0,outlierID,null))
            outlierID, l.layoutID layoutID, l.name layoutName, l.layoutCode layoutCode
					FROM PublisherPlatform pp, MonthlyUsageSummary tsm
					LEFT JOIN Layout l ON tsm.layoutID = l.layoutID
					WHERE pp.platformID = '" . $this->platformID . "'
					AND pp.publisherPlatformID = tsm.publisherPlatformID
					GROUP BY layoutID, resourceType, year, month";

    $result = $this->db->processQuery(stripslashes($query), 'assoc');


    $allArray = array();
    $resultArray = array();

    //need to do this since it could be that there's only one result and this is how the dbservice returns result
    if (isset($result['year'])){

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




	//returns array of months available for a given year
	public function getAvailableMonths($resourceType, $archiveInd, $year){

		//now formulate query
		if ($year){
			$addWhere = " AND year = '" . $year . "'";
		}
		if ($archiveInd){
			$addWhere .= " AND archiveInd = '" . $archiveInd . "'";
		}
		if ($resourceType){
			$addWhere = " AND resourceType = '" . $resourceType . "'";
		}

		$query = "SELECT DISTINCT year, month, archiveInd
					FROM MonthlyUsageSummary tsm INNER JOIN Title USING (titleID), PublisherPlatform pp
					WHERE pp.publisherPlatformID = tsm.publisherPlatformID
					AND pp.platformID = '" . $this->platformID . "'" . $addWhere . "
					ORDER BY year, archiveInd, month;";

		$result = $this->db->processQuery(stripslashes($query), 'assoc');


		$allArray = array();
		$resultArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['month'])){

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


	//remove an entire month for this platform
	public function deleteMonth($layoutID, $archiveInd, $year, $month){

		//now formulate query
		$query = "DELETE FROM MonthlyUsageSummary
					WHERE publisherPlatformID IN
						(SELECT publisherPlatformID
							FROM PublisherPlatform
							WHERE platformID = '" . $this->platformID . "')
							AND year = '"  . $year . "'
							AND month = '" . $month . "'
							AND layoutID = $layoutID
							AND archiveInd = '" . $archiveInd . "';";

		return $this->db->processQuery($query);

	}



	//returns array total stats devided by month
	public function getStatMonthlyTotals($resourceType, $archiveInd, $year){

		//now formulate query
		$query = "SELECT pp.platformID,
			SUM(IF(month='1',usageCount,null)) january,
			SUM(IF(month='2',usageCount,null)) february,
			SUM(IF(month='3',usageCount,null)) march,
			SUM(IF(month='4',usageCount,null)) april,
			SUM(IF(month='5',usageCount,null)) may,
			SUM(IF(month='6',usageCount,null)) june,
			SUM(IF(month='7',usageCount,null)) july,
			SUM(IF(month='8',usageCount,null)) august,
			SUM(IF(month='9',usageCount,null)) september,
			SUM(IF(month='10',usageCount,null)) october,
			SUM(IF(month='11',usageCount,null)) november,
			SUM(IF(month='12',usageCount,null)) december
			FROM Title t, MonthlyUsageSummary tsm, PublisherPlatform pp, Publisher p
			WHERE t.titleID = tsm.titleID
			AND tsm.publisherPlatformID = pp.publisherPlatformID
			AND pp.publisherID = p.publisherID
			AND pp.platformID = '" . $this->platformID . "'
			AND tsm.year='" . $year . "'
			AND tsm.archiveInd = '" . $archiveInd . "'
			AND t.resourceType = '" . $resourceType . "'
			GROUP BY pp.platformID;";

		$result = $this->db->processQuery(stripslashes($query), 'assoc');


		$resultArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['platformID'])){

			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

		}

		return $resultArray;


	}






	//returns array total stats devided by month
	public function getStatYearlyTotals($resourceType, $archiveInd, $year){

		//now formulate query
		$query = "SELECT pp.platformID, SUM(totalCount) totalCount, SUM(ytdHTMLCount) ytdHTMLCount, SUM(ytdPDFCount) ytdPDFCount
					FROM YearlyUsageSummary yus, PublisherPlatform pp, MonthlyUsageSummary mus INNER JOIN Title t ON (mus.titleID = t.titleID)
					WHERE pp.publisherPlatformID = yus.publisherPlatformID
					AND pp.platformID = '" . $this->platformID . "'
					AND yus.archiveInd ='" . $archiveInd . "'
					AND yus.year='" . $year . "'
					AND yus.titleID=mus.titleID
					AND mus.publisherPlatformID = yus.publisherPlatformID
					AND mus.year = '" . $year . "'
					AND mus.archiveInd = '" . $archiveInd . "'
					AND t.resourceType = '" . $resourceType . "'
					AND mus.month = '1'
					GROUP BY pp.platformID;";



		$result = $this->db->processQuery(stripslashes($query), 'assoc');


		$resultArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['platformID'])){

			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

		}

		return $resultArray;


	}




	//returns arrays of monthly statistics by title
	public function getMonthlyStats($resourceType, $archiveInd, $year){

		//now formulate query
		$query = "SELECT pp.publisherPlatformID, t.titleID, t.Title, Publisher.name Publisher, Platform.name Platform,
					MAX(IF(month='1',usageCount,null)) january,
					MAX(IF(month='2',usageCount,null)) february,
					MAX(IF(month='3',usageCount,null)) march,
					MAX(IF(month='4',usageCount,null)) april,
					MAX(IF(month='5',usageCount,null)) may,
					MAX(IF(month='6',usageCount,null)) june,
					MAX(IF(month='7',usageCount,null)) july,
					MAX(IF(month='8',usageCount,null)) august,
					MAX(IF(month='9',usageCount,null)) september,
					MAX(IF(month='10',usageCount,null)) october,
					MAX(IF(month='11',usageCount,null)) november,
					MAX(IF(month='12',usageCount,null)) december,
					MAX(IF(month='1',outlierID,0)) january_outlier,
					MAX(IF(month='2',outlierID,0)) february_outlier,
					MAX(IF(month='3',outlierID,0)) march_outlier,
					MAX(IF(month='4',outlierID,0)) april_outlier,
					MAX(IF(month='5',outlierID,0)) may_outlier,
					MAX(IF(month='6',outlierID,0)) june_outlier,
					MAX(IF(month='7',outlierID,0)) july_outlier,
					MAX(IF(month='8',outlierID,0)) august_outlier,
					MAX(IF(month='9',outlierID,0)) september_outlier,
					MAX(IF(month='10',outlierID,0)) october_outlier,
					MAX(IF(month='11',outlierID,0)) november_outlier,
					MAX(IF(month='12',outlierID,0)) december_outlier,
					MAX(mergeInd) mergeInd, activityType
					FROM Title t, MonthlyUsageSummary tsm, PublisherPlatform pp, Publisher, Platform
					WHERE t.titleID = tsm.titleID
					AND tsm.publisherPlatformID = pp.publisherPlatformID
					AND pp.publisherID = Publisher.publisherID
					AND pp.platformID = Platform.platformID
					AND pp.platformID = '" . $this->platformID . "'
					AND tsm.year='" . $year . "'
					AND tsm.archiveInd = '" . $archiveInd . "'
					AND t.resourceType = '" . $resourceType . "'
					GROUP BY t.titleID, t.Title, activityType
					ORDER BY t.Title;";


		$result = $this->db->processQuery(stripslashes($query), 'assoc');


		$allArray = array();
		$resultArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['publisherPlatformID'])){

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

	//returns arrays of monthly statistics by title
	public function getMonthlyStatsByLayout($layoutID, $year)
  {


    //now formulate query
    $query = "SELECT p.name AS platform, pub.name AS publisher, pub.counterPublisherID AS counterPublisherID,
          tsm.publisherPlatformID, tsm.year AS year, tsm.month AS month, tsm.usageCount AS usageCount, tsm.outlierID AS outlierID,
          tsm.activityType AS activityType, tsm.sectionType AS sectionType, tsm.accessType AS accessType,
          tsm.accessMethod AS accessMethod, tsm.yop AS yop, t.titleID AS titleID, t.title AS title, t.resourceType AS resourceType,
          t.publicationDate AS publicationDate, t.articleVersion AS articleVersion, t.authors AS authors,
          t.parentID AS parentID, t.componentID AS componentID
					FROM Platform p
					INNER JOIN PublisherPlatform pp ON p.platformID = pp.platformID
					INNER JOIN Publisher pub ON pp.publisherID = pub.publisherID
					INNER JOIN MonthlyUsageSummary tsm ON pp.publisherPlatformID = tsm.publisherPlatformID
					INNER JOIN Title t ON tsm.titleID = t.titleID
					WHERE p.platformID = '" . $this->platformID . "'
					AND tsm.year='" . $year . "'
					AND tsm.layoutID = '".$layoutID."'";


    $result = $this->db->processQuery(stripslashes($query), 'assoc');
    $allArray = array();
    $resultArray = array();

    //need to do this since it could be that there's only one result and this is how the dbservice returns result
    if (isset($result['publisherPlatformID'])){

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





	//returns platform object from name
	public function getByName($platformName){

		$query = "select platformID from Platform where upper(name) = upper('" . str_replace("'","''", $platformName) . "') LIMIT 1;";

		$result = $this->db->processQuery($query, 'assoc');

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['platformID'])){
			$obj = new Platform(new NamedArguments(array('primaryKey' => $result['platformID'])));
			return $obj;
		}else{
			return false;
		}

	}



	//returns array of month counts available for a given year
	public function getTotalMonths($resourceType, $archiveInd, $year){

		//now formulate query
		$query = "SELECT COUNT(month) count_months, MIN(month) min_month, MAX(month) max_month
					FROM MonthlyUsageSummary mus INNER JOIN Title t USING (titleID), PublisherPlatform pp
					WHERE mus.publisherPlatformID = pp.publisherPlatformID
					AND year = '" . $year . "'
					AND pp.PlatformID = '" . $this->platformID . "'
					AND resourceType = '" . $resourceType . "'
					AND archiveInd=$archiveInd;";

		$result = $this->db->processQuery(stripslashes($query), 'assoc');


		$resultArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['count_months'])){

			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

		}

		return $resultArray;


	}








	//search used for the autocomplete
	public function searchOrganizations($q){
		$config = new Configuration;

		$q = str_replace("+", " ",$q);
		$q = str_replace("%", "&",$q);

		$orgArray = array();

		//if the org module is installed get the org names from org database
		if ($config->settings->organizationsModule == 'Y'){

			$dbName = $config->settings->organizationsDatabaseName;


			$query = "SELECT CONCAT(A.name, ' (', O.name, ')') name, O.organizationID
									FROM " . $dbName . ".Alias A, " . $dbName . ".Organization O
									WHERE A.organizationID=O.organizationID
									AND upper(A.name) like upper('%" . $q . "%')
									UNION
									SELECT name, organizationID
									FROM " . $dbName . ".Organization
									WHERE upper(name) like upper('%" . $q . "%')
									ORDER BY 1;";

			$result = mysqli_query($this->db->getDatabase(), $query);

			while ($row = mysqli_fetch_assoc($result)){
				$orgArray[] = $row['organizationID'] . "|" . $row['name'];
			}

		}


		return $orgArray;
	}




	//search used index page drop down
	public function getOrganizationList(){
		$config = new Configuration;

		$orgArray = array();

		//if the org module is installed get the org names from org database
		if ($config->settings->organizationsModule == 'Y'){
			$dbName = $config->settings->organizationsDatabaseName;
			$query = "SELECT name, organizationID FROM " . $dbName . ".Organization ORDER BY 1;";
		}


		$result = $this->db->processQuery($query, 'assoc');

		$resultArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['organizationID'])){

			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

			array_push($orgArray, $resultArray);
		}else{
			foreach ($result as $row) {
				$resultArray = array();
				foreach (array_keys($row) as $attributeName) {
					$resultArray[$attributeName] = $row[$attributeName];
				}
				array_push($orgArray, $resultArray);
			}
		}

		return $orgArray;

	}



	//go to organizations and get the org name for this platform
	public function getOrganizationName(){
		$config = new Configuration;

		//if the org module is installed get the org name from org database
		if ($config->settings->organizationsModule == 'Y'){
			$dbName = $config->settings->organizationsDatabaseName;

			$orgArray = array();
			$query = "SELECT name FROM " . $dbName . ".Organization WHERE organizationID = " . $this->organizationID;

			if ($result = mysqli_query($this->db->getDatabase(), $query)){

				while ($row = mysqli_fetch_assoc($result)){
					return $row['name'];
				}
			}
		}
	}


	//returns array of external login data
	public function getOrganizationExternalLogins(){
		$config = new Configuration;

		//if the org module is installed get the org info from org database
		if (($config->settings->organizationsModule == 'Y') && ($this->organizationID)){
			$dbName = $config->settings->organizationsDatabaseName;

			$query = "SELECT externalLoginID, ELT.shortName externalLoginType, updateDate, loginURL, emailAddress, username, password, noteText
						FROM " . $dbName . ".ExternalLogin EL, " . $dbName . ".ExternalLoginType ELT
						WHERE  EL.externalLoginTypeID = ELT.externalLoginTypeID
						AND organizationID = '" . $this->organizationID . "'
						ORDER BY ELT.shortName";

			$result = $this->db->processQuery($query, 'assoc');

			$allArray = array();
			$resultArray = array();

			//need to do this since it could be that there's only one result and this is how the dbservice returns result
			if (isset($result['externalLoginID'])){

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

	}




	//used for A-Z on search (index)
	public function getAlphabeticalList(){
		$allArray = array();
		$result = $this->db->processQuery("SELECT DISTINCT UPPER(SUBSTR(TRIM(LEADING 'The ' FROM name),1,1)) letter, COUNT(SUBSTR(TRIM(LEADING 'The ' FROM name),1,1)) letter_count
								FROM Platform
								GROUP BY SUBSTR(TRIM(LEADING 'The ' FROM name),1,1)
								ORDER BY 1;", "assoc");



		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['letter'])){
			$allArray = $result;
		}else{
			foreach ($result as $row) {
				$allArray[$row['letter']] = $row['letter_count'];
			}
		}

		return $allArray;
	}


	//returns array based on search
	public function search($whereAdd, $orderBy, $limit){

		if (count($whereAdd) > 0){
			$whereStatement = " WHERE " . implode(" AND ", $whereAdd);
		}else{
			$whereStatement = "";
		}

		if ($limit != ""){
			$limitStatement = " LIMIT " . $limit;
		}else{
			$limitStatement = "";
		}


		//now actually execute query
		$query = "SELECT P.platformID, P.name, P.reportDisplayName,
						GROUP_CONCAT(DISTINCT PP.publisherPlatformID ORDER BY PP.reportDisplayName DESC SEPARATOR ':') publishers,
						date(importDateTime) last_import,
						loginID,
						details,
						if(serviceDayOfMonth > day(now()), str_to_date(concat(EXTRACT(YEAR_MONTH FROM NOW()), lpad(serviceDayOfMonth,2,'0')), '%Y%m%d'), str_to_date(concat(EXTRACT(YEAR_MONTH FROM NOW()) + 1, lpad(serviceDayOfMonth,2,'0')), '%Y%m%d') ) next_import
								FROM
									Platform P
									LEFT JOIN (PublisherPlatform PP
										INNER JOIN Publisher USING (publisherID))
									ON P.PlatformID = PP.PlatformID
									LEFT JOIN (SELECT platformID, mil.importLogID, max(importDateTime) importDateTime, loginID, details FROM ImportLog mil INNER JOIN ImportLogPlatformLink mipl USING (ImportLogID) GROUP BY platformID) mil ON P.platformID = mil.platformID
									LEFT JOIN SushiService SS ON P.PlatformID = SS.PlatformID
									" . $whereStatement . "
								GROUP By P.platformID
								ORDER BY " . $orderBy . $limitStatement;


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

	public function deleteStats() {
    foreach($this->getPublisherPlatforms() as $publisherPlatform) {
      $publisherPlatform->deleteStats();
    }
  }

	public function delete() {

    // Platform notes
    foreach($this->getPlatformNotes() as $note) {
      $note->delete();
    }

	  // Sushi Files & Config
    require_once 'SushiService.php';
    $sushiService = new SushiService();
    $sushiService->getByPlatformID($this->platformID);

    if ($sushiService->platformID != ''){
      $deleteSushiServiceQuery = "DELETE FROM SushiService WHERE platformID = ".$this->platformID;
      $this->db->processQuery($deleteSushiServiceQuery);
    }

    // Delete counterstore files
    $globname = implode('_', explode(' ', $this->name));
    $dir = __DIR__ . "/../../../counterstore/*$globname*.xml";
    foreach (glob($dir) as $filename) {
      unlink($filename);
    }

    // Logs
    $importLogArray = $this->getImportLogs();
    $deletableLogs = array();
    if (count($importLogArray) > 0 ) {
      foreach($importLogArray as $importLog) {
        $importLogPlatforms = $importLog->getPlatforms();
        if (count($importLogPlatforms) == 1 ) {
          $importLog->delete();
        }
      }
    }

    // PublisherPlatforms
    foreach($this->getPublisherPlatforms() as $publisherPlatform) {
      $publisherPlatform->delete();
    }

    // this
    parent::delete();
  }


}


?>
