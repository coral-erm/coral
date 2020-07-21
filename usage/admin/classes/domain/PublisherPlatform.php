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

class PublisherPlatform extends DatabaseObject {

	protected function defineRelationships() {}

	protected function overridePrimaryKeyName() {}


	protected function defineAttributes() {
		$this->addAttribute('publisherPlatformID');
		$this->addAttribute('publisherID');
		$this->addAttribute('platformID');
		$this->addAttribute('organizationID');
		$this->addAttribute('reportDisplayName');
		$this->addAttribute('reportDropDownInd');
	}

	//returns array of publisher platform note objects
	public function getPublisherPlatformNotes(){

		$query = "SELECT *
					FROM PublisherPlatformNote
					WHERE publisherPlatformID='" . $this->publisherPlatformID . "';";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['publisherPlatformNoteID'])){
			$object = new PublisherPlatformNote(new NamedArguments(array('primaryKey' => $result['publisherPlatformNoteID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new PublisherPlatformNote(new NamedArguments(array('primaryKey' => $row['publisherPlatformNoteID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}




	//returns array of external login objects
	public function getExternalLogins(){

		$query = "SELECT *
					FROM ExternalLogin
					WHERE publisherPlatformID='" . $this->publisherPlatformID . "';";

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


	//returns most recent date of the last month of imports for any titles under this platform
	public function getLastImportDate(){

		$query = "SELECT max(concat(year,'-',month,'-01')) max_month
					FROM MonthlyUsageSummary tsm INNER JOIN Title USING (titleID), PublisherPlatform pp
					WHERE pp.publisherPlatformID = tsm.publisherPlatformID
					AND pp.publisherPlatformID = '" . $this->publisherPlatformID . "';";

		$result = $this->db->processQuery(stripslashes($query), 'assoc');

		if (isset($result['max_month'])){
			return $result['max_month'];
		}
	}

	public function getTitles($type) {
    $query = "SELECT DISTINCT t.titleID titleID, t.title title,
					MAX(IF(ti.identifierType='DOI',identifier,null)) doi,
					MAX(IF(ti.identifierType='URI',identifier,null)) uri,
					MAX(IF(ti.identifierType='ISBN', identifier, null)) isbn,
					MAX(IF(ti.identifierType='Proprietary Identifier',identifier,null)) pi,
					MAX(IF(ti.identifierType='ISSN', concat(substr(ti.identifier,1,4), '-', substr(ti.identifier,5,4)),null)) issn,
					MAX(IF(ti.identifierType='eISSN', concat(substr(ti.identifier,1,4), '-', substr(ti.identifier,5,4)),null)) eissn
					FROM MonthlyUsageSummary mus, Title t LEFT JOIN TitleIdentifier ti ON t.titleID = ti.titleID
					WHERE mus.titleID = t.titleID
					AND publisherPlatformID = '" . $this->publisherPlatformID . "'";

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




	//returns array of monthly outlier records
	public function getMonthlyOutliers($resourceType, $archiveInd, $year, $month){


		//now formulate query
		$query = "SELECT monthlyUsageSummaryID, Title, archiveInd, usageCount, overrideUsageCount, color
					FROM MonthlyUsageSummary tsm, Title t, Outlier o
					WHERE tsm.titleID = t.titleID
					AND o.outlierID = tsm.outlierID
					AND publisherPlatformID='" . $this->publisherPlatformID . "'
					AND archiveInd='" . $archiveInd . "'
					AND year='" . $year . "'
					AND month='" . $month . "'
					AND resourceType='" . $resourceType . "'
					AND ignoreOutlierInd = 0;";


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
	public function getYearlyOverrides($resourceType,$archiveInd, $year){

		//now formulate query
		$query = "SELECT DISTINCT yearlyUsageSummaryID, Title, totalCount, ytdHTMLCount, ytdPDFCount, overrideTotalCount, overrideHTMLCount, overridePDFCount
					FROM YearlyUsageSummary tsy, MonthlyUsageSummary tsm, Title t
					WHERE tsy.titleID = t.titleID
					AND tsm.publisherPlatformID = tsy.publisherPlatformID
					AND tsm.titleID = tsy.titleID
					AND tsm.year = tsy.year
					AND tsm.archiveInd = tsy.archiveInd
					AND tsm.outlierID > 0
					AND tsy.publisherPlatformID='" . $this->publisherPlatformID . "'
					AND t.resourceType='" . $resourceType . "'
					AND tsy.archiveInd='" . $archiveInd . "'
					AND tsy.year='" . $year . "' and ignoreOutlierInd = 0;";

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
		}


		//now formulate query
		$query = "SELECT DISTINCT resourceType, year, GROUP_CONCAT(DISTINCT concat(month, '|', if(ignoreOutlierInd=0,ifnull(outlierID,0),0)) ORDER BY month, 1 SEPARATOR ',') months, archiveInd, MAX(month) max_month, MIN(month) min_month, MAX(IF(ignoreOutlierInd=0,outlierID,null)) outlierID
					FROM MonthlyUsageSummary tsm INNER JOIN Title t USING (titleID)
					WHERE tsm.publisherPlatformID = '" . $this->publisherPlatformID . "'
					" . $addWhere . "
					GROUP BY year, archiveInd
					ORDER BY year desc, archiveInd, month;";

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
					FROM MonthlyUsageSummary tsm
					LEFT JOIN Layout l ON tsm.layoutID = l.layoutID
					WHERE tsm.publisherPlatformID = '" . $this->publisherPlatformID . "'
					GROUP BY layoutID, resourceType, year, month;";

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

  //returns arrays of monthly statistics by title
  public function getMonthlyStatsByLayout($layoutID, $year, $limit = 5000, $offset = 0)
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
					WHERE pp.publisherPlatformID = '" . $this->publisherPlatformID . "'
					AND tsm.year='" . $year . "'
					AND tsm.layoutID = '".$layoutID."'
					ORDER BY title
					LIMIT $limit
					OFFSET $offset";


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
			$addWhere .= " AND resourceType = '" . $resourceType . "'";
		}

		$query = "SELECT DISTINCT year, month, archiveInd
					FROM MonthlyUsageSummary INNER JOIN Title t USING (titleID)
					WHERE publisherPlatformID='" . $this->publisherPlatformID . "'" . $addWhere . "
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





	//remove an entire month for this publisher
	public function deleteMonth($layoutID, $archiveInd, $year, $month){

		//now formulate query
		$query = "DELETE FROM MonthlyUsageSummary
					WHERE publisherPlatformID = '" . $this->publisherPlatformID . "'
					AND year = '"  . $year . "'
					AND layoutID = $layoutID
					AND month = '" . $month . "';";

    if (!empty($archiveInd)) {
      $query .= " AND archiveInd = $archiveInd";
    }
		return $this->db->processQuery($query);

	}









	//returns array total stats devided by month
	public function getStatMonthlyTotals($resourceType, $archiveInd, $year){

		//now formulate query
		$query = "SELECT pp.publisherPlatformID,
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
			FROM Title t, MonthlyUsageSummary mus, PublisherPlatform pp, Publisher p
			WHERE t.titleID = mus.titleID
			AND mus.publisherPlatformID = pp.publisherPlatformID
			AND pp.publisherID = p.publisherID
			AND mus.publisherPlatformID = '" . $this->publisherPlatformID . "'
			AND mus.year='" . $year . "'
			AND mus.archiveInd = '" . $archiveInd . "'
			AND t.resourceType = '" . $resourceType . "'
			GROUP BY pp.publisherPlatformID;";

		$result = $this->db->processQuery(stripslashes($query), 'assoc');

		$resultArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['publisherPlatformID'])){

			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

		}

		return $resultArray;


	}



	//returns array total stats devided by month
	public function getStatYearlyTotals($resourceType, $archiveInd, $year){

		//now formulate query
		$query = "SELECT pp.publisherPlatformID, SUM(totalCount) totalCount, SUM(ytdHTMLCount) ytdHTMLCount, SUM(ytdPDFCount) ytdPDFCount
					FROM YearlyUsageSummary tsy INNER JOIN Title t USING (titleID), PublisherPlatform pp
					WHERE pp.publisherPlatformID = tsy.publisherPlatformID
					AND tsy.publisherPlatformID = '" . $this->publisherPlatformID . "'
					AND archiveInd ='" . $archiveInd . "'
					AND resourceType ='" . $resourceType . "'
					AND year='" . $year . "'
					GROUP BY pp.publisherPlatformID;";

		$result = $this->db->processQuery(stripslashes($query), 'assoc');

		$resultArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['publisherPlatformID'])){

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
					FROM Title t, MonthlyUsageSummary mus, PublisherPlatform pp, Publisher, Platform
					WHERE t.titleID = mus.titleID
					AND mus.publisherPlatformID = pp.publisherPlatformID
					AND pp.publisherID = Publisher.publisherID
					AND pp.platformID = Platform.platformID
					AND mus.publisherPlatformID = '" . $this->publisherPlatformID . "'
					AND mus.year='" . $year . "'
					AND mus.archiveInd = '" . $archiveInd . "'
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




	//returns array of the first listed identifier objects
	public function getPublisherPlatform($publisherID, $platformID){

		$query = "SELECT publisherPlatformID FROM PublisherPlatform WHERE publisherID='" . $publisherID . "' AND platformID = '" . $platformID . "' LIMIT 1;";

		$result = $this->db->processQuery($query, 'assoc');

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['publisherPlatformID'])){
			$obj = new PublisherPlatform(new NamedArguments(array('primaryKey' => $result['publisherPlatformID'])));
			return $obj;
		}else{
			return false;
		}

	}


	//go to organizations and get the org name for this publisher/platform
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

	// remove all stats from this publisher platform
	public function deleteAllMonthlyStats() {
      $query = "DELETE FROM MonthlyUsageSummary WHERE  publisherPlatformID = '" . $this->publisherPlatformID . "'";
      return $this->db->processQuery($query);
  }

  public function deleteAllYearlyStats() {
    $query = "DELETE FROM YearlyUsageSummary WHERE  publisherPlatformID = '" . $this->publisherPlatformID . "'";
    return $this->db->processQuery($query);
  }

  public function deleteStats() {
    // Monthly Stats
    $this->deleteAllMonthlyStats();
    // Yearly Stats
    $this->deleteAllYearlyStats();

    // Titles that are orphaned
    $findOrphanedTitlesQuery = "SELECT * FROM Title WHERE titleID NOT IN (SELECT DISTINCT titleID FROM YearlyUsageSummary) AND titleID NOT IN (SELECT DISTINCT titleID FROM MonthlyUsageSummary)";
    $orphanedTitleResults = $this->db->processQuery($findOrphanedTitlesQuery, 'assoc');
    //need to do this since it could be that there's only one request and this is how the dbservice returns result
    $orphanedTitleArray = array();
    if (!empty($orphanedTitleResults)) {
      require_once 'Title.php';
      if (isset($orphanedTitleResults['titleID'])){
        $object = new Title(new NamedArguments(array('primaryKey' => $orphanedTitleResults['titleID'])));
        $orphanedTitleArray[] = $object;
      }else{
        foreach ($orphanedTitleResults as $row) {
          $object = new Title(new NamedArguments(array('primaryKey' => $row['titleID'])));
          $orphanedTitleArray[] = $object;
        }
      }
      foreach ($orphanedTitleArray as $title) {
        $title->delete();
      }
    }
  }

  public function delete() {
    // Sushi Files & Config
    require_once 'SushiService.php';
    $sushiService = new SushiService();
    $sushiService->getByPublisherPlatformID($this->publisherPlatformID);
    if ($sushiService->publisherPlatformID != ''){
      $sushiService->delete();
    }

    // Delete counterstore files
    $globname = implode('_', explode(' ', $this->name));
    $dir = __DIR__ . "/../../../counterstore/*$globname*.xml";
    foreach (glob($dir) as $filename) {
      unlink($filename);
    }

    // Notes
    foreach($this->getPublisherPlatformNotes() as $note) {
      $note->delete();
    }

    // all stats
    $this->deleteStats();

    // this
    parent::delete();

    // Publishers that are orphaned
    $findOrphanedPublisherQuery = "SELECT * FROM Publisher WHERE publisherID NOT IN (SELECT DISTINCT publisherID FROM PublisherPlatform)";
    $orphanedPublisherResults = $this->db->processQuery($findOrphanedPublisherQuery, 'assoc');
    //need to do this since it could be that there's only one request and this is how the dbservice returns result
    $orphanedPublisherArray = array();
    if (!empty($orphanedPublisherResults)) {
      require_once 'Publisher.php';
      if (isset($orphanedPublisherResults['publisherID'])){
        $object = new Publisher(new NamedArguments(array('primaryKey' => $orphanedPublisherResults['publisherID'])));
        $orphanedPublisherArray[] = $object;
      }else{
        foreach ($orphanedPublisherResults as $row) {
          $object = new Publisher(new NamedArguments(array('primaryKey' => $row['publisherID'])));
          $orphanedPublisherArray[] = $object;
        }
      }
      foreach ($orphanedPublisherArray as $publisher) {
        $publisher->delete();
      }
    }



  }

}

?>
