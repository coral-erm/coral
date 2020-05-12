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

/*
 * Includes
 */
ini_set("auto_detect_line_endings", true); //sometimes with macs...
include_once 'directory.php';
$util = new Utility();

/*
 * Helpers
 */

function cleanValue($value) {
  //strip everything after (Subs from Title
  if (strpos($value,' (Subs') !== false) {
    $value = substr($value, 0, strpos($value, ' (Subs'));
  }
  //remove " quotes
  $value = str_replace("\"","",$value);
  // remove any html encodings like &amp;
  $value = html_entity_decode($value);

  if (strpos($value,'<BR>') !== false) {
    $value = substr($value,0,strpos($value,'<BR>'));
  }

  // set value to &nbsp; if value is empty
  $value = (($value == '') || ($value == ' ')) ? null : $value;
  return trim($value);
}

function firstOrCreatePlatform($name) {
  if(empty($name)) {
    return false;
  }
  global $screenOutput;
  // check for existing platform
  $platform = new Platform();
  $searchName = trim(str_replace ("'","''",$name));
  $platform = $platform->getByName($searchName);
  $platformID = $platform->primaryKey;

  if (is_object($platform) && !empty($platformID)) {
    return $platform;
  } else {
    $platform = new Platform();
    $platform->platformID = '';
    $platform->name = $name;
    $platform->reportDisplayName = $name;
    $platform->reportDropDownInd = '0';
    $platform->organizationID = '';

    try {
      $platform->save();
    } catch (Exception $e) {
      echo "<div>Error saving platform: " . $e->getMessage() . "</div>";
      return false;
    }

    //also insert into Platform Note
    $platformNote = new PlatformNote();
    $platformNote->platformID = $platform->primaryKey;
    $platformNote->startYear = date('Y');
    $platformNote->counterCompliantInd = '';

    try {
      $platformNote->save();
      $screenOutput[] = _("New Platform set up: ") . $name . "   <a href='publisherPlatform.php?platformID=" . $platform->primaryKey . "'>" . _("edit") . "</a></b>";
    } catch (Exception $e) {
      echo "<div>Error saving platform note: " . $e->getMessage() . "</div>";
    }
    return $platform;
  }
}

function firstOrCreatePublisher($counterID, $name) {
  //get the publisher object
  // first check by counterID
  $publisher = new Publisher();
  $publisher = $publisher->getByCounterPublisherID($counterID);
  $publisherID = $publisher->primaryKey;

  // else get by name
  if(!is_object($publisher) || empty($publisherID)) {

    // if name is blank or Undefined, skip
    if(empty($name) || strtolower($name) == 'undefined') {
      return false;
    }

    $publisher = new Publisher();
    $searchName = trim(str_replace ("'","''",$name));
    $publisher = $publisher->getByName($searchName);
    $publisherID = $publisher->primaryKey;
  }

  if (is_object($publisher) && !empty($publisherID)) {
    return $publisher;
  } else {
    $publisher = new Publisher();
    $publisher->publisherID = '';
    $publisher->counterPublisherID = $counterID;
    $publisher->name = $name;
    try {
      $publisher->save();
    } catch (Exception $e) {
      echo "<div>Error saving publisher: " . $e->getMessage() . "</div>";
      return false;
    }
    return $publisher;
  }
}

function firstOrCreatePublisherPlatform($platformID, $publisherID, $platformName, $publisherName) {
  global $logOutput;
  $publisherPlatform = new PublisherPlatform();
  $publisherPlatform = $publisherPlatform->getPublisherPlatform($publisherID, $platformID);
  $publisherPlatformID = $publisherPlatform->primaryKey;
  if (is_object($publisherPlatform) && !empty($publisherPlatformID)) {
    return $publisherPlatform;
  } else {
    $publisherPlatform = new PublisherPlatform();
    $publisherPlatform->publisherPlatformID = '';
    $publisherPlatform->publisherID = $publisherID;
    $publisherPlatform->platformID = $platformID;
    $publisherPlatform->organizationID = '';
    $publisherPlatform->reportDropDownInd = '0';
    $publisherPlatform->reportDisplayName = $publisherName;
    try {
      $publisherPlatform->save();
      $logOutput[] = _("New Publisher / Platform set up: ") . $publisherName . " / " . $platformName;
    } catch (Exception $e) {
      echo "<div>Error saving publisher platform: " . $e->getMessage() . "</div>";
      return false;
    }
    return $publisherPlatform;
  }
}

function getTitleIdentifiers($reportModel, $prefix = null) {
  $titleIdentifierMaps = array(
    'pi' => 'Proprietary Identifier',
    'doi' => 'DOI',
    'isbn' => 'ISBN',
    'eisbn' => 'eISBN',
    'issn' => 'ISSN',
    'eissn' => 'eISSN',
    'uri' => 'URI'
  );
  $titleIdentifiers = array();

  // This creates a structure of title identifier type strings and values
  // e.g. array('eISSN' => 12345678)
  foreach($reportModel as $key => $value) {
    // if there is a prefix, e.g. parentDoi
    if (!empty($prefix)) {
      // if the prefix is not found in the key
      if (!strpos($key, $prefix)) {
        continue;
      } else {
        // else update the key to match the map above
        $key = strtolower(str_replace($prefix, '', $key));
      }
    }
    if(array_key_exists($key, $titleIdentifierMaps)) {
      $normalized = normalizeTitleIdentifier($value, $key);
      if (!empty($normalized)) {
        $titleIdentifiers[$titleIdentifierMaps[$key]] = $normalized;
      }
    }
  }
  return $titleIdentifiers;
}

function createOrUpdateTitle($titleName, $titleIdentifiers, $resourceType, $publisherPlatformID, $publicationDate = null, $authors = null, $articleVersion = null, $parentID = null, $componentID = null) {

  $titleID = null;
  $queryObject = new Title();
  // try to find the title via title identifiers
  foreach($titleIdentifiers as $type => $value){
    // if a title ID was found, skip
    // also skip proprietary ID, as those are not unique
    if(!empty($titleID) || $type == 'Proprietary Identifier') {
      continue;
    }
    $result = $queryObject->getTitleIdByTitleIdentifier($value, $type, $resourceType);
    if($result) {
      $titleID = $result;
    }
  }
  // try to find the title via name, type, and publisherplatform
  // ultimate, this query ensures that two titles with the same name (but different publisherPlatforms) are seen as different titles
  // because they lack any other identifiers
  if(empty($titleID)) {
    $searchName = trim(str_replace ("'","''",$titleName));
		$titleID = $queryObject->getByTitle($resourceType, $searchName, $publisherPlatformID, $publicationDate, $authors, $articleVersion, $parentID, $componentID);
  }

  // if there is still no title, need to add one
  if(empty($titleID)) {
      $isNewTitle = true;
      $title = new Title();
			$title->titleID = '';
			$title->title = $titleName;
			$title->resourceType = $resourceType;
			$title->publicationDate = $publicationDate;
      $title->authors = $authors;
      $title->articleVersion = $articleVersion;
      $title->parentID = $parentID;
      $title->componentID = $componentID;
			try {
				$title->save();
				$titleID = $title->primaryKey;
			} catch (Exception $e) {
			  echo "<div>Error saving title: " . $e->getMessage() . "</div>";
        return false;
			}
  }

  // Now add any new identifiers to the title
  // By default, we're going to add all the title identifiers
  $identifiersToAdd = $titleIdentifiers;
  $title = new Title(new NamedArguments(array('primaryKey' => $titleID)));
  // get the list of existing identifiers
  $existingIdentifiers = $title->getIdentifiers();
  // for each existing identifier, check the value against the title identifiers from the report
  foreach($existingIdentifiers as $existingIdentifier) {
    // if they are the same, remove the identifier from the identifiersToAdd
    if($titleIdentifiers[$existingIdentifier->type] == $existingIdentifier->identifier) {
      unset($identifiersToAdd[$existingIdentifier->type]);
    }
  }

  // add identifiers
  foreach($identifiersToAdd as $type => $value) {
    $titleIdentifier = new TitleIdentifier();
    $titleIdentifier->titleIdentifierID = '';
    $titleIdentifier->titleID = $titleID;
    $titleIdentifier->identifier = $value;
    $titleIdentifier->identifierType = $type;
    try {
      $titleIdentifier->save();
    } catch (Exception $e) {
      echo "<div>Error saving title identifier: " . $e->getMessage() . "</div>";
    }
  }

  // now we can return the new or updated title
  return $titleID;

}

function normalizeTitleIdentifier($identifier, $identifierType) {
  if(in_array(strtolower($identifierType),array('isbn','eisbn','issn','eissn'))) {
    $identifier = strtoupper(trim(str_replace('-', '', $identifier)));
    $identifier = strtoupper(trim(str_replace(' ', '', $identifier)));
    if (strpos(strtoupper($identifier), 'N/A') !== false) {
      $identifier = null;
    }
    if ($identifier == '00000000') {
      $identifier = null;
    }
    if (strtoupper($identifier) == 'XXXXXXXX') {
      $identifier = null;
    }
    if (strtoupper($identifier) == '.') {
      $identifier = null;
    }
  }
  // catches 0, false, null, and empty strings
  if (empty($identifier) || $identifier == ' ') {
    $identifier = null;
  }
  return $identifier;
}

function sumCounts($counts, $year) {
  global $logOutput;
  // if there are more than 12 months in the year, log the issue and do not save anything
  if (is_array($counts)) {
    if(count($counts) > 12) {
      $logOutput[] = '<span style="color: red;">' ._('There are more than 12 months of stats for this title for the year ')
        . $year . _('. Yearly Usage Summary will not be saved.') .'</span>';
      return false;
    }
    return array_sum($counts);
  } else {
    // there are no monthly stats
    return 0;
  }
}


/*
 * Setup Page vars
 */
$pageTitle = _('Upload Process Complete');
$monthlyInsert = array();
$screenOutput = array();

$importLogID = filter_input(INPUT_POST, 'importLogID', FILTER_VALIDATE_INT);
$fromSushi = !empty($importLogID) && $importLogID > 0;

if($fromSushi) {
  $importLog = new ImportLog(new NamedArguments(array('primaryKey' => $importLogID)));
  $layout = new Layout();
  $layout->getByLayoutCode($importLog->layoutCode);
  $file = BASE_DIR . $importLog->fileName;
  $overrideInd = 1;
} else {
  $layoutID = filter_input(INPUT_POST, 'layoutID', FILTER_VALIDATE_INT);
  $layout = new Layout(new NamedArguments(array('primaryKey' => $layoutID)));
  $file = $_POST['file'];
  $overrideInd = filter_input(INPUT_POST, 'overrideInd', FILTER_VALIDATE_INT) == 1 ? 1 : 0;
}

/*
 * Setup Layout
 */
//read layouts ini file to get the layouts to map to columns in the database
$layoutsArray = parse_ini_file("layouts.ini", true);
$layoutKey = $layoutsArray['ReportTypes'][$layout->layoutCode];
$reportTypeDisplay = $layout->name;
$resourceType = $layout->resourceType;
$layoutCode = $layout->layoutCode;
$layoutID = $layout->layoutID;
$release = intval(substr($layoutCode,-1));
$columnsToCheck = $layoutsArray[$layoutKey]['columnToCheck'];
$layoutColumns = $layoutsArray[$layoutKey]['columns'];
// if this is a JR1 or JR1a, need to add the two additional columns
if (!$fromSushi && in_array($layoutCode, array('JR1_R4', 'JR1a_R4'))) {
  $layoutColumns[] = 'ytdHTML';
  $layoutColumns[] = 'ytdPDF';
}
$archiveInd="0";
if (strpos($reportTypeDisplay,'archive') > 1){
  $archiveInd = "1";
}

/*
 * Setup Logging
 */
$logOutput = array();
$logOutput[] = _("Process started on ") . date('l \t\h\e jS \o\f F Y \a\t h:i A');
$logOutput[] = _("File: ") . $file;
$logOutput[] = _("Report Format: ") . $reportTypeDisplay;

/*
 * determine config settings for outlier usage
 */
$config = new Configuration();
$outlier = array();

if ($config->settings->useOutliers == "Y"){

  $logOutput[] = _("Outlier Parameters:");

  $outliers = new Outlier();
  $outlierArray = array();

  foreach($outliers->allAsArray as $outlierArray) {

    $logOutput[] =_("Level ") . $outlierArray['outlierLevel'] . ": " . $outlierArray['overageCount'] . _(" over plus ") .  $outlierArray['overagePercent'] . _("% over ") . "<br />";

    $outlier[$outlierArray['outlierID']]['overageCount'] = $outlierArray['overageCount'];
    $outlier[$outlierArray['outlierID']]['overagePercent'] = $outlierArray['overagePercent'];
    $outlier[$outlierArray['outlierID']]['outlierLevel'] = $outlierArray['outlierLevel'];
  }

}

if($overrideInd) {
  $logOutput[] = _("Override indicator set - all months will be imported.");
}

//initialize some variables
$rownumber=0;
$holdPlatform = null;
$holdPublisher = null;
$holdPublisherPlatformID = null;
$monthIds = array(
  1 => 'jan',
  2 => 'feb',
  3 => 'mar',
  4 => 'apr',
  5 => 'may',
  6 => 'jun',
  7 => 'jul',
  8 => 'aug',
  9 => 'sep',
  10 => 'oct',
  11 => 'nov',
  12 => 'dec'
);



/*
 * Read file and start importing
 */
$file_handle = $util->utf8_fopen_read($file, $fromSushi);


// parse header
$header = stream_get_line($file_handle, 10000000, "\n");
$headerArray = explode("\t", $header);

// get the months from the header
$reportMonths = array_splice($headerArray, count($layoutColumns));
$logOutput[] = _("Year: ") . $reportMonths[0] . ' - ' . $reportMonths[count($reportMonths) - 1];
$platformArray = array();

// default report model
$baseReportModel = array(
  'months' => array()
);
// Some Release 5 reports are filtered, so we need to add the defaults
if ($release == 5) {
  // accessType is 'Controlled'
  $accessTypeControlledLayouts = array('PR_P1_R5','DR_D1_R5','TR_B1_R5','TR_J1_R5','TR_J4_R5');
  $accessMethodRegularLayouts = array_merge($accessTypeControlledLayouts, array('DR_D1_R5','TR_B2_R5','TR_B3_R5','TR_J2_R5','TR_J3_R5','IR_A1_R5','IR_M1_R5'));
  if(in_array($layoutCode, $accessTypeControlledLayouts)){
    $baseReportModel['accessType'] = 'Controlled';
  }
  if(in_array($layoutCode, $accessMethodRegularLayouts)){
    $baseReportModel['accessType'] = 'Regular';
  }
  if($layoutCode == 'IR_A1_R5') {
    $baseReportModel['sectionType'] = 'Article';
  }
}


//loop through each line of file
while (!feof($file_handle)) {

  //get each line out of the file handler
  $line = stream_get_line($file_handle, 10000000, "\n");
  $lineArray = explode("\t", $line);

  // Skip the second line if it begins with "Total", this happens when the report isn't from sushi
  if (!$fromSushi && strtolower(substr($lineArray[0], 0, 5)) == 'total') {
    continue;
  }
  $rownumber++;

  // reinstantiate the baseReportModel
  $reportModel = $baseReportModel;

  // associate the report columns with the layout columns
  // all remaining columns are months
  $monthIndex = 0;
  foreach($lineArray as $rowIndex => $value) {
    if(!empty($layoutColumns[$rowIndex]) && empty($reportModel[$layoutColumns[$rowIndex]])) {
      // second check is meant to prevent overriding the R5 baseReportModel values with nulls
      $reportModel[$layoutColumns[$rowIndex]] = cleanValue($value);
    } else {
      $reportModel['months'][$reportMonths[$monthIndex]] = cleanValue($value);
      $monthIndex++;
    }
  }

  ################################################################
  // PLATFORM
  // Query to see if the Platform already exists, if so, get the ID
  #################################################################
  // held platform matches
  if (!empty($holdPlatform) && $reportModel['platform'] == $holdPlatform['name']){
    $platformID = $holdPlatform['id'];
  } else {
    $platform = firstOrCreatePlatform($reportModel['platform']);
    if($platform) {
      $platformID = $platform->primaryKey;
      $holdPlatform = array(
        'name' => $platform->name,
        'id' => $platform->primaryKey
      );
      $platformArray[] = $platformID;
    } else {
      continue;
    }
  }



  #################################################################
  // PUBLISHER
  // Query to see if the Publisher already exists, if so, get the ID
  #################################################################

  // skips this if the report is a release 5 platform report
  if ($release == 5 && ($layoutCode == 'PR_R5' || $layoutCode == 'PR_P1_R5')) {
    $holdPublisher = null;
    $publisherID = null;
  } else {
    //previous row matches
    if (!empty($holdPublisher) && ($reportModel['publisher'] == $holdPublisher['name'])){
      $publisherID = $holdPublisher['id'];
    } else {
      $publisher = firstOrCreatePublisher($reportModel['counterPublisherID'], $reportModel['publisher']);
      if($publisher) {
        $publisherID = $publisher->primaryKey;
        $holdPublisher = array(
          'name' => $publisher->name,
          'id' => $publisher->primaryKey
        );
      } else {
        continue;
      }
    }
  }

  #################################################################
  // PUBLISHER / PLATFORM
  // Query to see if the Publisher / Platform already exists, if so, get the ID
  #################################################################
  //get the publisher platform object
  // skips this if the report is a release 5 platform report
  if ($release == 5 && ($layoutCode == 'PR_R5' || $layoutCode == 'PR_P1_R5')) {
    $holdPublisherPlatform = null;
    $publisherPlatformID = null;
  } else {
    if (!empty($holdPublisherPlatform) && $platformID == $holdPublisherPlatform['platformID'] && $publisherID == $holdPublisherPlatform['publisherID']){
      $publisherPlatformID = $holdPublisherPlatform['id'];
    } else {
      $publisherPlatform = firstOrCreatePublisherPlatform($platformID, $publisherID, $reportModel['platform'], $reportModel['publisher']);
      if($publisherPlatform) {
        $publisherPlatformID = $publisherPlatform->primaryKey;
        $holdPublisherPlatform = array(
          'platformID' => $platformID,
          'publisherID' => $publisherID,
          'id' => $publisherPlatform->primaryKey
        );
      } else {
        continue;
      }
      $logOutput[] = _("Publisher / Platform: ") . $holdPublisher['name'] . ' / ' . $holdPlatform['name'];
    }
  }

  #################################################################
  // TITLE
  // Query to see if the Title already exists, if so, get the ID
  #################################################################

  $parentTitleID = null;
  $componentTitleID = null;

  // First check if there is a need to process parent/component
  if (!empty($reportModel['parentTitle']) && !empty($reportModel['parentDataType'])) {
    $parentTitleIdentifiers = getTitleIdentifiers($reportModel, 'parent');
    $parentTitle = createOrUpdateTitle($reportModel['parentTitle'], $parentTitleIdentifiers, $reportModel['parentDataType'], $publisherPlatformID);
  }
  if (!empty($reportModel['componentTitle']) && !empty($reportModel['componentDataType'])) {
    $componentTitleIdentifiers = getTitleIdentifiers($reportModel, 'component');
    $componentTitleID = createOrUpdateTitle($reportModel['componentTitle'], $parentTitleIdentifiers, $reportModel['componentDataType'], $publisherPlatformID);
  }

  // Get the title identifiers
  $titleIdentifiers = getTitleIdentifiers($reportModel);
  $publicationDate = !empty($reportModel['publicationDate']) ? $reportModel['publicationDate'] : null;
  $authors = !empty($reportModel['authors']) ? $reportModel['authors'] : null;
  $articleVersion = !empty($reportModel['articleVersion']) ? $reportModel['articleVersion'] : null;
  // If this is a Release 5 Title master report, the resource type might be book or journal
  if($layoutCode == 'TR_R5') {
    $resourceType == $reportModel['dataType'];
  }
  $titleID = createOrUpdateTitle($reportModel['title'], $titleIdentifiers, $resourceType, $publisherPlatformID, $publicationDate, $authors, $articleVersion, $parentTitleID, $componentTitleID);

  if ($titleID) {
    // Log the title
    $logOutput[] = _("Title: ") . $reportModel['title'];
  } else {
    array_unshift($logOutput, '<span style="color: red">' . _("Title match did not complete correctly, please check ISBN / ISSN to verify for Title:  ") . $reportModel['title'] . ".</span>");
    continue;
  }

  #################################################################
  // MonthlyUsageStats
  #################################################################

  $yearsToUpdate = array();
  // default values for the stat
  $sectionType = empty($reportModel['sectionType']) ? null : $reportModel['sectionType'];
  $accessType = empty($reportModel['accessType']) ? null : $reportModel['accessType'];
  $accessMethod = empty($reportModel['accessMethod']) ? null : $reportModel['accessMethod'];
  $yop = empty($reportModel['yop']) ? null : $reportModel['yop'];
  // 1. almost all reports, the activityType is either null or whatever came from the report
  $activityType = empty($reportModel['activityType']) ? null : $reportModel['activityType'];
  // 2. if this is a Release 4 JR1 or JR1a manual import (i.e. not from sushi), we do not know which part of the
  // usage count is a PDF or HTML lookup, so we only store a 'ft_total' as the activity type
  if (!$fromSushi && in_array($layoutCode, array('JR1_R4', 'JR1a_R4'))) {
    $activityType = 'ft_total';
  }

  foreach($reportModel['months'] as $month => $usageCount) {
    // reset vars
    $notNumericUsageCount = false;

    $splitMonth = explode('-', $month);
    $monthID = intval(array_search(strtolower($splitMonth[0]), $monthIds));
    $year = $splitMonth[1];
    // try to fix year
    if(strlen($year) == 2) {
      $year = $year < 90 ? '20'.$year : '19'.$year;
    }
    if (empty($monthID) || $monthID < 1 || $monthID > 12 || empty($year) || strlen($year) !== 4) {
      $logOutput[] = _("Improperly formatted month name, must be formatted as MMM-YYYY. Found ") . $month;
      continue;
    }
    if(!array_key_exists($year, $yearsToUpdate)) {
      $yearsToUpdate[$year] = false;
    }

    $outlierID = '0';
    // calculate the outlier
    if (count($outlier) > 0){
      // figure out which months to pull - start with this month previous year
      $prevYear = $year-1;
      $prevMonths='';
      $currMonths='';
      $yearAddWhere='';
      $outlierLevel = '';

      if ($monthID == 1){
        $yearAddWhere = "(year = " . $prevYear . ")";
      }else{
        for ($j=$monthID; $j<=11; $j++){
          $prevMonths .= $j . ", ";
        }
        $prevMonths .= "12";

        for ($j=1; $j<$monthID-1; $j++){
          $currMonths .= $j . ", ";
        }
        $currMonths .= $j;
        $yearAddWhere .= "((year = $prevYear and month in ($prevMonths)) or (year = $year and month in ($currMonths)))";
      }

      //get the previous 12 months data in an array
      $usageCountArray = array();
      $titleObj = new Title(new NamedArguments(array('primaryKey' => $titleID)));
      $usageCountArray = $titleObj->get12MonthUsageCount($archiveInd, $publisherPlatformID, $layoutID, $yearAddWhere);

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
      }
    }

    if(!is_numeric($usageCount)) {
      $notNumericUsageCount = true;
      $usageCount = 0;
    }
    // create the monthly stat
    $monthlyUsageSummary = new MonthlyUsageSummary();
    $monthlyUsageSummary->titleID = $titleID;
    $monthlyUsageSummary->publisherPlatformID = $publisherPlatformID;
    $monthlyUsageSummary->year = $year;
    $monthlyUsageSummary->month = $monthID;
    $monthlyUsageSummary->archiveInd = $archiveInd;
    $monthlyUsageSummary->usageCount = $usageCount;
    $monthlyUsageSummary->outlierID = $outlierID;
    $monthlyUsageSummary->mergeInd = 0;
    $monthlyUsageSummary->ignoreOutlierInd = '0';
    $monthlyUsageSummary->overrideUsageCount = null;
    $monthlyUsageSummary->sectionType = $sectionType;
    $monthlyUsageSummary->activityType = $activityType;
    $monthlyUsageSummary->accessType = $accessType;
    $monthlyUsageSummary->accessMethod = $accessMethod;
    $monthlyUsageSummary->yop = $yop;
    $monthlyUsageSummary->layoutID = $layoutID;


    // check if the stat already exists
    $alreadyExists = $monthlyUsageSummary->alreadyExists();
    if($alreadyExists) {
      $monthlyUsageSummary = new MonthlyUsageSummary(new NamedArguments(array('primaryKey' => $alreadyExists)));
      $monthlyUsageSummary->usageCount = $usageCount;
    }

    // if this doesn't already exist or the $overrideInd is set to 1, then save the stat
    if(!$alreadyExists || $overrideInd == 1) {
      try {
        $monthlyUsageSummary->save();
        if($notNumericUsageCount) {
          $logOutput[] = _("Usage Count Record is not numeric for month: ") . $month . _("  Count: ") . $usageCount . _(" imported as 0.");
        } else {
          $logOutput[] = _("New Usage Count Record Added: Month: ") . $month .  _("  Count: ") . $usageCount;
        }
        if ($outlierID != 0) {
          $logOutput[] = '<span style="color: red;">' . _("Outlier found for this record: Level ") . $outlierLevel . '</span>';
        }
        $yearsToUpdate[$year] = true;
      } catch (Exception $e) {
        echo "<div>Error saving monthly usage stat: " . $e->getMessage() . "</div>";
      }
    } else {
      $logOutput[] = _("Current or future month will not be imported: ") . $month . ": " . $usageCount;
    }
  }

  #################################################################
  // YearlyUsageStats
  #################################################################

  $yearlyActivityType = $activityType;
  $yearlyLogLine = '';
  // For Release 4, only DB1 has an activity type
  if($release < 5 && $layoutCode != 'DB1_R4') {
    $yearlyActivityType = null;
  }

  foreach ($yearsToUpdate as $year => $needsToBeUpdated) {
    if (!$needsToBeUpdated) {
        $logOutput[] = $year . ': '. _("No YTD import performed since monthly stats were not imported");
        continue;
    }
    // Set up the year object
    $yearlyUsageSummary = new YearlyUsageSummary();
    $yearlyUsageSummary->titleID = $titleID;
    $yearlyUsageSummary->publisherPlatformID = $publisherPlatformID;
    $yearlyUsageSummary->year = $year;
    $yearlyUsageSummary->archiveInd = $archiveInd;
    $yearlyUsageSummary->sectionType = $sectionType;
    $yearlyUsageSummary->activityType = $yearlyActivityType;
    $yearlyUsageSummary->accessType = $accessType;
    $yearlyUsageSummary->accessMethod = $accessMethod;
    $yearlyUsageSummary->yop = $yop;
    $yearlyUsageSummary->layoutID = $layoutID;

    $alreadyExists = $yearlyUsageSummary->alreadyExists();
    if($alreadyExists) {
      $yearlyUsageSummary = new YearlyUsageSummary(new NamedArguments(array('primaryKey' => $alreadyExists)));
    }

    // The yearly summary is ready to be updated or created, now get the monthly totals
    // here there are three options

    // 1. On all reports except Release 4 JR1 && JR1a, if any monthly counts for a year were updated, update that year
    if (!in_array($layout->layoutCode, array('JR1_R4', 'JR1a_R4'))) {
      $counts = $yearlyUsageSummary->getMonthCounts();
      $totalCounts = sumCounts($counts, $year);
      if (!$totalCounts) {
        // there was an error with the counts, do not save
        continue;
      }
      $yearlyUsageSummary->totalCount = $totalCounts;
      $yearlyUsageSummary->ytdHTMLCount = null;
      $yearlyUsageSummary->ytdPDFCount = null;
      $yearlyLogLine = _("YTD Total Count: ") . $totalCounts;
    } else {
      if($fromSushi) {
        // 2.a if this is from sushi, get the counts for total, ytdHTML, and ytdPDF
        $jr4CountingError = false;
        $yearlyLogLineParts = array();
        foreach(array('ft_total' => 'totalCount', 'ft_html' => 'ytdHTMLCount', 'ft_pdf' => 'ytdPDFCount') as $activityTypeString => $countAttribute) {
          // set the activity type so getMonthCounts add the correct activity type to the query
          $yearlyUsageSummary->activityType = $activityTypeString;
          $counts = $yearlyUsageSummary->getMonthCounts();
          $totalCounts = sumCounts($counts, $year);
          if (!$totalCounts) {
            // there was an error with the counts, do not import
            $jr4CountingError = true;
            break;
          }
          $yearlyUsageSummary->{$countAttribute} = $totalCounts;
          switch($activityTypeString) {
            case 'ft_html':
              $countType = 'HTML';
              break;
            case 'ft_pdf':
              $countType = 'PDF';
              break;
            default:
              $countType = 'Total';
              break;
          }
          $yearlyLogLineParts[] = _("YTD $countType Count: ") . $totalCounts;
        }
        // if there was an error with any of the counts, do not save
        if ($jr4CountingError) {
          continue;
        }
        // reset the activityType
        $yearlyUsageSummary->activityType = null;
        $yearlyLogLine = implode('  |  ', $yearlyLogLineParts);
      } else {
        // 2.b this was a manual import, with a manual import, the user selects if they want to import the YTD totals
        $storeJR1Totals = $_POST['storeJR1Totals'];
        if (!empty($storeJR1Totals)) {
          $yearlyUsageSummary->totalCount = $reportModel['ytd'];
          $yearlyUsageSummary->ytdHTMLCount = $reportModel['ytdHTML'];
          $yearlyUsageSummary->ytdPDFCount = $reportModel['ytdPDF'];
          $yearlyLogLine = _("YTD Total Count: ") . $reportModel['ytd'] . "  |  "
            . _("YTD HTML Count: ") . $reportModel['ytdHTML'] . "  |  "
            ._("YTD PDF Count: ") . $reportModel['ytdPDF'];
        } else {
          continue;
        }
      }
    }

    // Not the yearly usage summary can be saved
    try {
      $yearlyUsageSummary->save();
      $logOutput[] = $yearlyLogLine;
    } catch (Exception $e) {
      echo "<div>Error saving yearly usage summary: " . $e->getMessage() . "</div>";
    }

  }

}

fclose($file_handle);

$fileInfo = pathinfo($file);

#Save log output on server
$logfile = 'logs/' . date('Ymdhi') . '.php';
$excelfile = 'logs/' . date('Ymdhi') . '.xls';
$fp = fopen($logfile, 'w');
fwrite($fp, "<?php header(\"Content-type: application/vnd.ms-excel\");\nheader(\"Content-Disposition: attachment; filename=" . $excelfile . "\"); ?>");
fwrite($fp, "<html><head></head><body>");
fwrite($fp, implode('<br/>', $logOutput));
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
	$email->subject		= _("Log Output for ") . $fileInfo['basename'];
	$email->message		= _("Usage Statistics File Import Run!") . "\n\n" . _("Please find log file: ") . "\n\n" . $Base_URL . $logfile;


	if ($email->send()) {
		$mailOutput = _("Log has been emailed to ") . implode(", ", $emailAddresses);
	}else{
		$mailOutput = _("Email to ") . implode(", ", $emailAddresses) . _(" Failed!");
	}
}

$logSummary = $fileInfo['basename'] . ": $reportTypeDisplay for " . $reportMonths[0] . ' - ' . end($reportMonths);

include 'templates/header.php';

//Log import in database
if ($importLogID != ""){
	$importLog = new ImportLog(new NamedArguments(array('primaryKey' => $importLogID)));
	$importLog->fileName = $importLog->fileName;
	$importLog->archiveFileURL = $importLog->fileName;
	$importLog->details = $importLog->details . "\n" . $rownumber . _(" titles processed.") . $logSummary;
	$archvieFileName = $importLog->fileName;
}else{
  // copy the uploaded file to the archive
  $archvieFileName = 'archive/' . $fileInfo['filename'] . '_' .strtotime('now') . '.' . $fileInfo['extension'];
  copy($file, BASE_DIR . $archvieFileName);
	$importLog = new ImportLog();
	$importLog->importLogID = '';
	$importLog->fileName = $fileInfo['basename'];
	$importLog->archiveFileURL = $archvieFileName;
	$importLog->details = $rownumber . _(" titles processed.") . $logSummary;
}

$importLog->loginID = $user->loginID;
$importLog->layoutCode = $layoutCode;
$importLog->logFileURL = $logfile;

try {
	$importLog->save();
	$importLogID = $importLog->primaryKey;
} catch (Exception $e) {
  echo "<div>Error saving import log: " . $e->getMessage() . "</div>";
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
    echo "<div>Error saving import log platfomr link: " . $e->getMessage() . "</div>";
	}
}



?>


<table class="headerTable">
<tr><td>
<div class="headerText"><?php echo _("Status");?></div>
	<br />
    <p><?php echo _("File archived as") . ' ' . $Base_URL . $archvieFileName; ?>.</p>
    <p><?php echo _("Log file available at:");?> <a href='<?php echo $Base_URL . $logfile; ?>'><?php echo $Base_URL . $excelfile; ?></a>.</p>
    <p><?php echo _("Process completed.") . " " . $mailOutput; ?></p>
    <br />
    <?php echo _("Summary:") . ' ' .$rownumber . _(" titles processed.") . "<br />" . nl2br($logSummary); ?><br />
    <br />
    <?php echo implode('<br/>',$screenOutput); ?><br />
    <p>&nbsp; </p>

			</td>
		</tr>
	</table>


<?php include 'templates/footer.php'; ?>
