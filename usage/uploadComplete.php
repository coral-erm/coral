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


ini_set("auto_detect_line_endings", true); //sometimes with macs...
include_once 'directory.php';

$util = new Utility();

$pageTitle = 'Upload Process Complete';

//read layouts ini file to get the layouts to map to columns in the database
$layoutsArray = parse_ini_file("layouts.ini", true);


$uploadedFile = $_POST['upFile'];
$uploadedFilename = explode('archive/',$uploadedFile);
$uploadedFilename = $uploadedFilename[1];
$orgFileName = $_POST['orgFileName'];
$overrideInd = $_POST['overrideInd'];
$layoutID = $_POST['layoutID'];
$importLogID = $_POST['importLogID'];
$startDate = $_POST['startDate'];
$numMonths = $_POST['numMonths'];
$startYearArr = explode("-", $startDate);
$startYear = $startYearArr[1];
$startMonthArr = explode("-", $startDate);
$startMonth = date("n",strtotime($startMonthArr[0]));
$holdStartMonth = $startMonth;
$endMonth = date("n",mktime(0,0,0,$startMonth+$numMonths-1));
if($startMonth <= $endMonth) {
	$endYear = $startYear;
	$multYear = false;//lets us know that we don't need to account for multiple years
}
else {
	$endYear = $startYear + 1;
	$multYear = true;//lets us know that we need to account for multiple years
	$holdEndMonth = $endMonth;
	$endMonth = 12;
}

if ($_POST['checkYear'] == NULL)
	$year = $startYear;
else
	$year = $_POST['checkYear'];
$pISSNArray = array();
$platformArray = array();



$layout = new Layout(new NamedArguments(array('primaryKey' => $_POST['layoutID'])));
$layoutKey = $layoutsArray['ReportTypes'][$layout->layoutCode];

$reportTypeDisplay = $layout->name;
$resourceType = $layout->resourceType;
$layoutCode = $layout->layoutCode;

$archiveInd="0";
if (strpos($reportTypeDisplay,'archive') > 1){
	$archiveInd = "1";
}

//if this came from sushi
if ($importLogID > 0){
	$file_handle = $util->utf8_fopen_read($uploadedFile, true);
	$headerline = stream_get_line($file_handle, 10000000, "\n");//This is just disregarded
}else{
	$file_handle = $util->utf8_fopen_read($uploadedFile, false);
}


$logSummary = "\n" . $orgFileName;

$topLogOutput = "";
$logOutput = "Process started on " . date('l \t\h\e jS \o\f F Y \a\t h:i A') . "<br />";
$logOutput.= "File: " . $uploadedFile . "<br /><br />";
$logOutput.= "Report Format: " . $reportTypeDisplay . "<br /><br />";
$monthlyInsert='';
$screenOutput = '';

$startFlag = "N";
$formatCorrectFlag = "N";

//determine config settings for outlier usage
$config = new Configuration();
$outlier = array();

if ($config->settings->useOutliers == "Y"){

	$logOutput.="Outlier Parameters:<br />";

	$outliers = new Outlier();
	$outlierArray = array();

	foreach($outliers->allAsArray as $outlierArray) {

		$logOutput.="Level " . $outlierArray['outlierLevel'] . ": " . $outlierArray['overageCount'] . " over plus " .  $outlierArray['overagePercent'] . "% over <br />";

		$outlier[$outlierArray['outlierID']]['overageCount'] = $outlierArray['overageCount'];
		$outlier[$outlierArray['outlierID']]['overagePercent'] = $outlierArray['overagePercent'];
		$outlier[$outlierArray['outlierID']]['outlierLevel'] = $outlierArray['outlierLevel'];
	}

}

$logOutput .="<br /><br />";

//get column values from layouts array to determine layout
$columnsToCheck = $layoutsArray[$layoutKey]['columnToCheck'];
$layoutColumns = $layoutsArray[$layoutKey]['columns'];


if ($importLogID > 0){
	$formatCorrectFlag = "Y";
	$startFlag = "Y";
	$logSummary .= " $reportTypeDisplay";
	$logSummary .= "\nfor ";
	$overrideInd="1";
}


//initialize some variables
$rownumber=0;
$holdPlatform = '';
$holdPublisher = '';
$holdPublisherPlatformID = '';
$holdYear = '';
if ($importLogID > 0) {
	$startMonth = $startMonth;
}
else {
	$startMonth = '';
}

//loop through each line of file
while (!feof($file_handle)) {

	//get each line out of the file handler
	$line = stream_get_line($file_handle, 10000000, "\n");

	//set delimiter
	if (($del) == NULL or (empty($del))) {
		if(count(explode("\t",$line)) > 5){
			$del = "\t";
		}else if (count(explode(",",$line)) > 5){
			$del = ",";
		}

	}

	//check column formats to get the year and months
	if (($formatCorrectFlag == "N") && (count(explode("\t",$line)) >= count($columnsToCheck))){
		//positive unless proven negative
		$formatCorrectFlag = "Y";
		$lineArray = explode("\t",$line);

		foreach ($columnsToCheck as $key => $colCheckName){
			$fileColName = strtolower(trim($lineArray[$key]));

			if (strpos($fileColName, strtolower($colCheckName)) === false){
				$formatCorrectFlag='N';
			}
		}
		if ($formatCorrectFlag == 'Y'){
			//at this point, $fileColName has the last column check value, Jan
			//determine the year
			list ($checkMonth,$year) = preg_split("/[-\/.]/",$fileColName);
			if ($year < 100) $year = 2000 + $year;

			$missingMonths = array();
			// determine the latest months may not all exist
			$jan_i = array_search('jan',$layoutColumns);
			for($i=$jan_i;$i<12+$jan_i;$i++){
				$month = $i - $jan_i + 1;
				$monthName = date("M", mktime(0,0,0,$month,10));
				if (strpos(strtolower($lineArray[$i]), strtolower($monthName)) === false){
					unset($layoutColumns[$i]);
				}
			}
			$layoutColumns = array_values($layoutColumns);
			$logSummary .= " $reportTypeDisplay";
			$logSummary .= "\n$year for ";
		}
	}

	//as long as the flags are set to print out then we can continue
	if (($startFlag == "Y") && ($formatCorrectFlag == "Y")  && !(strpos($line,"\t") == "0") && (substr($line,0,5) != "Total") && (count(explode("\t",$line)) > 5)) {

		$lineArray = explode("\t",$line);
		$columnValues = array();
		//match column titles in layout.ini to columns in file
		foreach ($layoutColumns as $i => $col){
			$columnValues[$col] = trim($lineArray[$i]);
		}

		$resourceTitle = $columnValues['title'];
		$platformName = $columnValues['platform'];
		$publisherName = $columnValues['publisher'];

		if (isset($columnValues['issn'])) {
			$pISSN = $columnValues['issn'];
		} else {
			$pISSN = null;
		}
		if (isset($columnValues['eissn'])) {
			$eISSN = $columnValues['eissn'];
		} else {
			$eISSN = null;
		}
		if (isset($columnValues['isbn'])) {
			$pISBN = $columnValues['isbn'];
		} else {
			$pISBN = null;
		}
		if (isset($columnValues['eisbn'])) {
			$eISBN = $columnValues['eisbn'];
		} else {
			$eISBN = null;
		}

		$doi = $columnValues['doi'];
		$pi = $columnValues['pi'];

		if (isset($columnValues['activityType'])) {
			$activityType = $columnValues['activityType'];
		} else {
			$activityType = null;
		}
		if (isset($columnValues['sectionType'])) {
			$sectionType = $columnValues['sectionType'];
		} else {
			$sectionType = null;
		}

		$ytd = $columnValues['ytd'];
		$ytdHTML = $columnValues['ytdHTML'];
		$ytdPDF = $columnValues['ytdPDF'];

		// loop through each month to assign month array
		$month=array();
		for($i=1;$i<=12;$i++){
			$monthName = date("M", mktime(0,0,0,$i,10));
			if(isset($columnValues[strtolower($monthName)])){
				$month[$i] = $columnValues[strtolower($monthName)];
			}
		}

		################################################################
		// PLATFORM
		// Query to see if the Platform already exists, if so, get the ID
		#################################################################
		//check it against the previous row - no need to do another lookup if we've already figured out the platform
		//strip out double quotes
		$platformName = trim(str_replace ('"','',$platformName));
		if ($platformName == ""){
			$platformName = $holdPlatform;
		}

		if (($platformID) == NULL || ($platformName != $holdPlatform)){
			//get the platformID if available
			$platformTestObj = new Platform();
			$platformObj = new Platform();
			$platformObj = $platformTestObj->getByName($platformName);

			if (is_object($platformObj)) $platformID = $platformObj->platformID;

			//Find the most recent month for this year / Platform that we have statistics for if override isn't set
			if (($platformID) && !($startMonth)){
				if ($overrideInd == 1){
					$logOutput .= "Override indicator set - all months will be imported.";
				}else{
					$monthArray = $platformObj->getTotalMonths($resourceType, $archiveInd, $year);
					$count_months = $monthArray['count_months'];
					$min_month = $monthArray['min_month'];
					$max_month = $monthArray['max_month'];


					if ($count_months == 12){
						$logOutput .= "Entire year already exists for this Platform / year.  No counts will be imported.";
						$startMonth = 13;
					}else if (($min_month == 1) && ($max_month < 13)) {
						$startMonth=$max_month + 1;
						$logOutput .= "Month Started at: " . $startMonth;
					}else if ($count_months == 0){
						$logOutput .= "No records exist for this Platform / year.  Import will start with month 1.";
					}else{
						$endMonth=$min_month-1;
						$logOutput .= "Partial year records exist for this Platform / year.  Import will start with month 1 and end with month $endMonth.";
					}

				}

			}
		}

		if (($startMonth) == NULL || ($startMonth == '')){
			$startMonth = 1;
		}

		if (!isset($endMonth) || ($endMonth == '')){
			$endMonth = 12;
		}


		//For log output we only want to print the 	year once
		if ($year != $holdYear) {
			$logOutput .= "<br />Year: " . $startYear;
			if ($startYear != $endYear) {
				$logOutput .= " - " . $endYear;
			}
		}

		//If Platform does not already exist, insert it and get the new ID
		if (!$platformID && $platformName){

			$platform = new Platform();
			$platform->platformID = '';
			$platform->name = $platformName;
			$platform->reportDisplayName = $platformName;
			$platform->reportDropDownInd = '0';
			$platform->organizationID = '';

			try {
				$platform->save();
				$platformID = $platform->primaryKey;
			} catch (Exception $e) {
				echo $e->getMessage();
			}

			//also insert into Platform Note
			$platformNote = new PlatformNote();
			$platformNote->platformID = $platformID;
			$platformNote->startYear = date('Y');
			$platformNote->counterCompliantInd = '';

			try {
				$platformNote->save();
			} catch (Exception $e) {
				echo $e->getMessage();
			}


			#add to output on screen
			$screenOutput .= "<br /><b>New Platform set up: " . $platformName . "   <a href='publisherPlatform.php?platformID=" . $platformID . "'>edit</a></b>";


		}

		$platformArray[] = $platformID;


		#################################################################
		// PUBLISHER
		// Query to see if the Publisher already exists, if so, get the ID
		#################################################################

		//check it against the previous row - no need to do another lookup if we've already figured out the platform
		//strip out double quotes
		$publisherName = trim(str_replace ('"','',$publisherName));
		if ($publisherName == ""){
			$publisherName = $holdPublisher;
		}

		if (($publisherID) == NULL || ($publisherName != $holdPublisher)){
			//get the publisher object
			$publisherTestObj = new Publisher();
			$publisherObj = new Publisher();
			$publisherObj = $publisherTestObj->getByName($publisherName);
			if (is_object($publisherObj)) $publisherID = $publisherObj->publisherID;
		}



		//If it does not already exist, insert it and get the new ID
		if (($publisherID == '') && ($publisherName)){

			$publisher = new Publisher();
			$publisher->publisherID = '';
			$publisher->name = $publisherName;

			try {
				$publisher->save();
			} catch (Exception $e) {
				echo $e->getMessage();
			}
			$publisherID = $publisher->primaryKey;
		}



		#################################################################
		// PUBLISHER / PLATFORM
		// Query to see if the Publisher / Platform already exists, if so, get the ID
		#################################################################
		//check it against the previous row - no need to do another lookup if we've already figured out the publisherplatformID
		if (($publisherPlatformID) == NULL || ($publisherName != $holdPublisher) || ($platformName != $holdPlatform)){
			//get the publisher platform object
			$publisherPlatformTestObj = new PublisherPlatform();
			$publisherPlatformObj = $publisherPlatformTestObj->getPublisherPlatform($publisherID, $platformID);
			if (is_object($publisherPlatformObj)) $publisherPlatformID = $publisherPlatformObj->publisherPlatformID;
		}



		//If it does not already exist, insert it and get the new ID
		if (!$publisherPlatformID && $publisherID && $platformID){

			$publisherPlatform = new PublisherPlatform();
			$publisherPlatform->publisherPlatformID = '';
			$publisherPlatform->publisherID = $publisherID;
			$publisherPlatform->platformID = $platformID;
			$publisherPlatform->organizationID = '';
			$publisherPlatform->reportDropDownInd = '0';
			$publisherPlatform->reportDisplayName = $publisherName;

			try {
				$publisherPlatform->save();
			} catch (Exception $e) {
				echo $e->getMessage();
			}

			$publisherPlatformID = $publisherPlatform->primaryKey;


			#add to log output
			$logOutput .= "<br />New Publisher / Platform set up: " . $publisherName . " / " . $platformName;

		}


		#################################################################
		// TITLE
		// Query to see if the Title already exists, if so, get the ID
		#################################################################
		//first, remove the '-' from the ISSNs
		if (isset($pISSN)) {
			$pISSN = strtoupper(trim(str_replace ('-','',$pISSN)));
			//remove blank
			$pISSN = strtoupper(trim(str_replace (' ','',$pISSN)));
			if (strpos(strtoupper($pISSN),'N/A') !== false) $pISSN = '';
			if ($pISSN == '00000000') $pISSN = '';
			if (strtoupper($pISSN) == 'XXXXXXXX') $pISSN = '';
			if (strtoupper($pISSN) == '.') $pISSN = '';
		} else {
			$pISSN = null;
		}

		if (isset($eISSN)) {
			$eISSN = strtoupper(trim(str_replace ('-','',$eISSN)));
			//remove blank
			$eISSN = strtoupper(trim(str_replace (' ','',$eISSN)));
			if (strpos(strtoupper($eISSN),'N/A') !== false) $eISSN = '';
			if ($eISSN == '00000000') $eISSN = '';
			if (strtoupper($eISSN) == 'XXXXXXXX') $eISSN = '';
			if (strtoupper($eISSN) == '.') $eISSN = '';
		} else {
			$eISSN = null;
		}

		if (isset($pISBN)) {
			$pISBN = strtoupper(trim(str_replace ('-','',$pISBN)));
			//remove blank
			$pISBN = strtoupper(trim(str_replace (' ','',$pISBN)));
			if (strpos(strtoupper($pISBN),'N/A') !== false) $pISBN = '';
			if ($pISBN == '00000000') $pISBN = '';
			if (strtoupper($pISBN) == 'XXXXXXXX') $pISBN = '';
		} else {
			$pISBN = null;
		}

		if (isset($eISBN)) {
			$eISBN = strtoupper(trim(str_replace ('-','',$eISBN)));
			//remove blank
			$eISBN = strtoupper(trim(str_replace (' ','',$eISBN)));
			if (strpos(strtoupper($eISBN),'N/A') !== false) $eISBN = '';
			if ($eISBN == '00000000') $eISBN = '';
			if (strtoupper($eISBN) == 'XXXXXXXX') $eISBN = '';
		} else {
			$eISBN = null;
		}

		if ($doi == "0") $doi = "";
		if ($pi == "0") $pi = "";

		//strip everything after parenthesis from Title
		if (strpos($resourceTitle,' (Subs') !== false) $resourceTitle = substr($resourceTitle,0,strpos($resourceTitle,' (Subs'));
		if (strpos($resourceTitle,'<BR>') !== false) $resourceTitle = substr($resourceTitle,0,strpos($resourceTitle,'<BR>'));

		//strip out double quotes, escape single quotes and fix &
		$resourceTitle = trim(str_replace ('"','',$resourceTitle));
		$resourceTitle = trim(str_replace ("'","''",$resourceTitle));
		$resourceTitle = trim(str_replace ("&amp;","&",$resourceTitle));


		$titleObj = new Title();
		$titleID = $titleObj->getByTitle($resourceType, $resourceTitle, $pISSN, $eISSN, $pISBN, $eISBN, $publisherPlatformID);

		if ($titleID) $newTitle=0;


		//If it does not already exist, insert it into the Title and identifier tables and get the new ID
		if (!$titleID && $resourceTitle && ((strlen($pISSN) == "8") || !$pISSN)){

			$titleObj = new Title();
			$titleObj->titleID = '';
			$titleObj->title = $resourceTitle;
			$titleObj->resourceType = $resourceType;

			try {
				$titleObj->save();
				$titleID = $titleObj->primaryKey;
			} catch (Exception $e) {
				echo $e->getMessage();
			}

			$newTitle=1;


			#also insert into Title Identifier table
			if ((strlen($pISBN) == "10") || (strlen($pISBN) == "13")) {
				$titleIdentifier = new TitleIdentifier();
				$titleIdentifier->titleIdentifierID = '';
				$titleIdentifier->titleID = $titleID;
				$titleIdentifier->identifier = $pISBN;
				$titleIdentifier->identifierType = 'ISBN';

				try {
					$titleIdentifier->save();
				} catch (Exception $e) {
					echo $e->getMessage();
				}
			}

			if ((strlen($eISBN) == "10") || (strlen($eISBN) == "13")) {
				$titleIdentifier = new TitleIdentifier();
				$titleIdentifier->titleIdentifierID = '';
				$titleIdentifier->titleID = $titleID;
				$titleIdentifier->identifier = $eISBN;
				$titleIdentifier->identifierType = 'eISBN';

				try {
					$titleIdentifier->save();
				} catch (Exception $e) {
					echo $e->getMessage();
				}
			}

			if (strlen($pISSN) == "8") {
				$titleIdentifier = new TitleIdentifier();
				$titleIdentifier->titleIdentifierID = '';
				$titleIdentifier->titleID = $titleID;
				$titleIdentifier->identifier = $pISSN;
				$titleIdentifier->identifierType = 'ISSN';

				try {
					$titleIdentifier->save();
				} catch (Exception $e) {
					echo $e->getMessage();
				}
			}

			if (strlen($eISSN) == "8") {
				$titleIdentifier = new TitleIdentifier();
				$titleIdentifier->titleIdentifierID = '';
				$titleIdentifier->titleID = $titleID;
				$titleIdentifier->identifier = $eISSN;
				$titleIdentifier->identifierType = 'eISSN';

				try {
					$titleIdentifier->save();
				} catch (Exception $e) {
					echo $e->getMessage();
				}
			}

			if ($doi) {
				$titleIdentifier = new TitleIdentifier();
				$titleIdentifier->titleIdentifierID = '';
				$titleIdentifier->titleID = $titleID;
				$titleIdentifier->identifier = $doi;
				$titleIdentifier->identifierType = 'DOI';

				try {
					$titleIdentifier->save();
				} catch (Exception $e) {
					echo $e->getMessage();
				}
			}

			if ($pi) {
				$titleIdentifier = new TitleIdentifier();
				$titleIdentifier->titleIdentifierID = '';
				$titleIdentifier->titleID = $titleID;
				$titleIdentifier->identifier = $pi;
				$titleIdentifier->identifierType = 'Proprietary Identifier';

				try {
					$titleIdentifier->save();
				} catch (Exception $e) {
					echo $e->getMessage();
				}
			}


		}else{
			$titleObj = new Title(new NamedArguments(array('primaryKey' => $titleID)));

			if (($resourceTitle && ((strlen($pISSN) == "8") || !$pISSN))){

				//still should check for new online ISSN since they can be added in later spreadsheets
				if (strlen($eISSN) == "8") {
					if (!$titleObj->getExistingIdentifier($eISSN)) {
						$titleIdentifier = new TitleIdentifier();
						$titleIdentifier->titleIdentifierID = '';
						$titleIdentifier->titleID = $titleID;
						$titleIdentifier->identifier = $eISSN;
						$titleIdentifier->identifierType = 'eISSN';

						try {
							$titleIdentifier->save();
						} catch (Exception $e) {
							echo $e->getMessage();
						}
					}
				}

				if ($doi) {
					if (!$titleObj->getExistingIdentifier($doi)) {
						$titleIdentifier = new TitleIdentifier();
						$titleIdentifier->titleIdentifierID = '';
						$titleIdentifier->titleID = $titleID;
						$titleIdentifier->identifier = $doi;
						$titleIdentifier->identifierType = 'doi';

						try {
							$titleIdentifier->save();
						} catch (Exception $e) {
							echo $e->getMessage();
						}
					}
				}

			}

		}


		$holdYear = $year;
		$holdPubPlat = $publisherName . " / " . $platformName;
		$holdPublisher = $publisherName;
		$holdPlatform = $platformName;


		//do the same for Publisher/Platform text name
		$pubPlat = $publisherName . " / " . $platformName;

		if ($pubPlat != $holdPubPlat) {
			if (trim($pubPlat)){
				$logOutput .= "<br /><br />Publisher / Platform: " . $pubPlat;
			}
		}

		$mergeInd = 0;
		if ($titleID) {
			$rownumber++;
			//Add Title to log output
			if (trim($resourceTitle)){
				$logOutput .="<br /><br />Title: " . $resourceTitle;
			}

			//now we can insert the actual stats
			for ($i=$startMonth; $i<=$endMonth; $i++){
				$usageCount = '';

				if ((isset($month[$i])) && ($month[$i] != '')){
					$usageCount = $month[$i];
					$usageCount = str_replace(',','',$usageCount);
					$usageCount = str_replace('"','',$usageCount);
				}

				//must do this or will not insert '0' count records
				if ($usageCount >= "0"){
					$logOutput .="\r<br />";

					//skip if month is current month or in the future
					if ( mktime(0,0,0,$i,1,$year) < mktime(0,0,0,date('m'),1,date('Y'))){

						//if this is an override or the print ISSN already existed on the spreadsheet (value in array for each print identifier is set to 1 later on)

						//if (($overrideInd == 1) || ($pISSNArray[$pISSN] == 1)) {

						//this is a merged title
						if (($resourceType == "Journal") && ($pISSN) && (isset($pISSNArray[$pISSN]) && $pISSNArray[$pISSN] == 1)) {
							//add the other titles count in with this titles counts to merge the two together ($i = month)
							$usageCount+=$titleObj->getUsageCountByMonth($archiveInd, $year, $i, $publisherPlatformID);

							//now delete the old one ($i = month)
							$titleObj->deleteMonth($archiveInd, $year, $i, $publisherPlatformID);
							$logOutput .= "Merged";

							//flag when inserted into db that this is a merged statistic
							$mergeInd = 1;

							$logOutput .= "Duplicate record for this Print ISSN in same spreadsheet: Month: " . $i . "  New Count: " . $usageCount;
						}

						#calculate Outlier - dont bother if this is a new Title
						if (($newTitle == 0) && (count($outlier) > 0)){
							#figure out which months to pull - start with this month previous year
							$prevYear = $year-1;
							$prevMonths='';
							$currMonths='';
							$yearAddWhere='';
							$outlierID = '0';
							$outlierLevel = '';

							if ($i == 1){
								$yearAddWhere = "(year = " . $prevYear . ")";
							}else{
								for ($j=$i; $j<=11; $j++){
									$prevMonths .= $j . ", ";
								}
								$prevMonths .= "12";

								for ($j=1; $j<$i-1; $j++){
									$currMonths .= $j . ", ";
								}
								$currMonths .= $j;
								$yearAddWhere .= "((year = $prevYear and month in ($prevMonths)) or (year = $year and month in ($currMonths)))";
							}

							//get the previous 12 months data in an array
							$usageCountArray = array();
							$usageCountArray = $titleObj->get12MonthUsageCount($archiveInd, $publisherPlatformID, $yearAddWhere);

							$avgCount = 0;
							if (count($usageCountArray) == "12"){

								foreach ($usageCountArray as $usageCountRec) {
									$avgCount += $usageCountRec['usageCount'];
								}

								$avgCount = $avgCount / 12;

								foreach ($outlier as $k => $outlierArray) {
									if ($usageCount > ((($avgCount * ($outlierArray['overagePercent']/100)) + $outlierArray['overageCount'])) ) {
										//we can overwrite previous Outlier level so that we just take the highest Outlier level
										$outlierID = $k;
										$outlierLevel = $outlierArray['outlierLevel'];
									}
								}

							}else{
								$outlierID = '0';
							}

						}else{
							$outlierID = '0';
						}

						//if override and this is not a merged title delete original data so we don't have duplicates in system ($i = month)
						if ((!$mergeInd) && ($overrideInd == 1)){
							if ($multYear && $i >= $startMonth) {
								$titleObj->deleteMonth($archiveInd, $startYear, $i, $publisherPlatformID);
							}
							else if ($multYear && $i < $startMonth && $i <= $endMonth) {
								$titleObj->deleteMonth($archiveInd, $endYear, $i, $publisherPlatformID);
							}
							else {
								$titleObj->deleteMonth($archiveInd, $startYear, $i, $publisherPlatformID);
							}
						}

						$monthlyUsageSummary = new MonthlyUsageSummary();
						$monthlyUsageSummary->titleID = $titleID;
						$monthlyUsageSummary->publisherPlatformID = $publisherPlatformID;
						$monthlyUsageSummary->year = $year;
						$monthlyUsageSummary->month = $i;
						$monthlyUsageSummary->archiveInd = $archiveInd;
						$monthlyUsageSummary->usageCount = $usageCount;
						$monthlyUsageSummary->outlierID = $outlierID;
						$monthlyUsageSummary->mergeInd = $mergeInd;
						$monthlyUsageSummary->ignoreOutlierInd = '0';
						$monthlyUsageSummary->overrideUsageCount = null;
						$monthlyUsageSummary->sectionType = $sectionType;
						$monthlyUsageSummary->activityType = $activityType;

						try {
							$monthlyUsageSummary->save();
						} catch (Exception $e) {
							echo $e->getMessage();
						}


						if (is_numeric($usageCount)){
							$logOutput .= "New Usage Count Record Added: Month: " . $i . " - " . $year .  "  Count: " . $usageCount;
						}else{
							$logOutput .= "Usage Count Record is not numeric for month: " . $i . "  Count: " . $usageCount . " imported as 0.";
						}


						$monthlyInsert=1;

						if ($outlierID){
							$logOutput .= "<br /><font color=\"red\">Outlier found for this record: Level " . $outlierLevel . "</font>";
						}


						//}else{
						//	$logOutput .= "Record already exists: Month: " . $i . "  Count: " . $usageCount;
						//}

					}else{
						$logOutput .= "Current or future month will not be imported: " . $i . "-" . $year . ": " . $usageCount;
					}

					//end usage count is entered
				}
				if ($i == 12 && $multYear) {
					$year = $endYear;
					$startMonth = 1;
					$endMonth = $holdEndMonth;
					$i = 0;
				}

				//end month for loop
			}
			if ($multYear) {
				$year = $startYear;
				$startMonth = $holdStartMonth;
				$endMonth = 12;
			}



			//Insert YTD data
			//First delete existing, we will always overlay this data

			if ($ytd == true || $ytd == "0"){
				//only do this if any monthly data was imported
				if ($monthlyInsert == "1"){

					//make HTML and PDF counts 0 if they dont exist
					if (!$ytdHTML) $ytdHTML=0;
					if (!$ytdPDF) $ytdPDF=0;

					$ytd = str_replace(',','',$ytd);
					$ytd = str_replace('"','',$ytd);
					$ytdHTML = str_replace(',','',$ytdHTML);
					$ytdHTML = str_replace('"','',$ytdHTML);
					$ytdPDF = str_replace(',','',$ytdPDF);
					$ytdPDF = str_replace('"','',$ytdPDF);

					//this is a merged title
					if (($resourceType == "Journal") && ($pISSN) && (isset($pISSNArray[$pISSN]) && $pISSNArray[$pISSN] == 1)) {

						$yearCountArray = array();
						$yearCountArray = $titleObj->getTotalCountByYear($archiveInd, $year, $publisherPlatformID);

						$ytd += $yearCountArray['usageCount'];
						$ytdHTML += $yearCountArray['ytdHTMLCount'];
						$ytdPDF += $yearCountArray['ytdPDFCount'];

						$logOutput .= "<br />YTD Already Exists for this Print ISSN, counts are added together.";
					}

					//delete these yearly stats since we will next overwrite them
					$titleObj->deleteYearlyStats($archiveInd, $year, $publisherPlatformID, $activityType);

					$yearlyUsageSummary = new YearlyUsageSummary();
					$yearlyUsageSummary->yearlyUsageSummaryID = '';
					$yearlyUsageSummary->titleID = $titleID;
					$yearlyUsageSummary->publisherPlatformID = $publisherPlatformID;
					$yearlyUsageSummary->year = $year;
					$yearlyUsageSummary->archiveInd = $archiveInd;
					$yearlyUsageSummary->totalCount = $ytd;
					$yearlyUsageSummary->ytdHTMLCount = $ytdHTML;
					$yearlyUsageSummary->ytdPDFCount = $ytdPDF;
					$yearlyUsageSummary->mergeInd = $mergeInd;
					$yearlyUsageSummary->overrideTotalCount = '';
					$yearlyUsageSummary->overrideHTMLCount = '';
					$yearlyUsageSummary->overridePDFCount = '';
					$yearlyUsageSummary->sectionType = $sectionType;
					$yearlyUsageSummary->activityType = $activityType;

					try {
						$yearlyUsageSummary->save();
						$logOutput .= "<br />YTD Total Count: " . $ytd . "<br />YTD HTML Count: " . $ytdHTML . "<br />YTD PDF Count: " . $ytdPDF;
					} catch (Exception $e) {
						echo $e->getMessage();
					}


				}else{
					$logOutput .= "<br />No YTD import performed since monthly stats were not imported";
				}

				//end ytd if statement
			}

			# add to array so we can determine if print ISSN already exists in this spreadsheet to add counts together
			$pISSNArray[$pISSN] = 1;

		}else{ //end if for if Title match found
			$topLogOutput .= "<font color='red'>Title match did not complete correctly, please check ISBN / ISSN to verify for Title:  " . $resourceTitle . ".</font><br />";
		}


		//end start flag if
	}


	#check "Total for all" is in first column  - set flag to start import after this
	if ((substr($line,0,5) == "Total") || ($formatCorrectFlag == "Y")){
		$startFlag = "Y";
	}

	//reset all ID variables that were just set
	$titleID='';
	$publisherID='';
	$platformID='';
	$publisherPlatformID='';


//end loop for each line
}
fclose($file_handle);


#Save log output on server
$logfile = 'logs/' . date('Ymdhi') . '.php';
$excelfile = 'logs/' . date('Ymdhi') . '.xls';
$fp = fopen($logfile, 'w');
fwrite($fp, "<?php header(\"Content-type: application/vnd.ms-excel\");\nheader(\"Content-Disposition: attachment; filename=" . $excelfile . "\"); ?>");
fwrite($fp, "<html><head></head><body>");
fwrite($fp, $topLogOutput);  //for major errors
fwrite($fp, "<br />");
fwrite($fp, $logOutput);
fwrite($fp, "</body></html>");
fclose($fp);

//send email to email addresses listed in DB
$logEmailAddress = new LogEmailAddress();
$emailAddresses = array();

foreach ($logEmailAddress->allAsArray() as $emailAddress){
	$emailAddresses[] = $emailAddress['emailAddress'];
}

$util = new Utility();
$Base_URL = $util->getCORALURL() . "usage/";

$mailOutput='';
if (count($emailAddresses) > 0){
	$email = new Email();
	$email->to 			= implode(", ", $emailAddresses);
	$email->subject		= "Log Output for $uploadedFile";
	$email->message		= "Usage Statistics File Import Run!\n\nPlease find log file: \n\n" . $Base_URL . $logfile;


	if ($email->send()) {
		$mailOutput = "Log has been emailed to " . implode(", ", $emailAddresses);
	}else{
		$mailOutput = "Email to " . implode(", ", $emailAddresses) . " Failed!";
	}
}

$logSummary .= date("F Y", mktime(0,0,0,$startMonth,10,$startYear)) . " - " . date("F Y", mktime(0,0,0,$holdEndMonth,10,$endYear));

include 'templates/header.php';

//Log import in database
if ($importLogID != ""){
	$importLog = new ImportLog(new NamedArguments(array('primaryKey' => $importLogID)));
	$importLog->fileName = $importLog->fileName;
	$importLog->archiveFileURL = $importLog->fileName;
	$importLog->details = $importLog->details . "\n" . $rownumber . " titles processed." . $logSummary;

}else{
	$importLog = new ImportLog();
	$importLog->importLogID = '';
	$importLog->fileName = $orgFileName;
	$importLog->archiveFileURL = 'archive/' . $uploadedFilename;
	$importLog->details = $rownumber . " titles processed." . $logSummary;
}

$importLog->loginID = $user->loginID;
$importLog->layoutCode = $layoutCode;
$importLog->logFileURL = $logfile;

try {
	$importLog->save();
	$importLogID = $importLog->primaryKey;
} catch (Exception $e) {
	echo $e->getMessage();
}


//only get unique platforms
$platformArray = array_unique($platformArray, SORT_REGULAR);
foreach ($platformArray AS $platformID){
	$importLogPlatformLink = new ImportLogPlatformLink();
	$importLogPlatformLink->importLogID = $importLogID;
	$importLogPlatformLink->platformID = $platformID;


	try {
		$importLogPlatformLink->save();
	} catch (Exception $e) {
		echo $e->getMessage();
	}
}



?>


	<table class="headerTable">
		<tr><td>
				<div class="headerText">Status</div>
				<br />
				<p>File archived as <?php echo $Base_URL . 'archive/' . $uploadedFilename; ?>.</p>
				<p>Log file available at: <a href='<?php echo $Base_URL . $logfile; ?>'><?php echo $Base_URL . $excelfile; ?></a>.</p>
				<p>Process completed.  <?php echo $mailOutput; ?></p>
				<br />
				Summary:<?php echo $rownumber . " titles processed.<br />" . nl2br($logSummary); ?><br />
				<br />
				<?php echo $screenOutput; ?><br />
				<p>&nbsp; </p>

			</td>
		</tr>
	</table>


<?php include 'templates/footer.php'; ?>
