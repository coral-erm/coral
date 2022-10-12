<?php

/*
**************************************************************************************************************************
** CORAL Organizations Module
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


session_start();

include_once 'directory.php';

$organizationID = '';

if(isset($_GET['organizationID'])){
	$organizationID = $_GET['organizationID'];
}

function escape_csv($value) {
  // replace \n with \r\n
  $value = preg_replace("/(?<!\r)\n/", "\r\n", $value);
  // escape quotes
  $value = str_replace('"', '""', $value);
  return '"'.$value.'"';
}

function array_to_csv_row($array) {
  $escaped_array = array_map("escape_csv", $array);
  return implode(",",$escaped_array)."\r\n";
}

$issueLogObj = new IssueLog();
$issues = $issueLogObj->allExpandedAsArray($organizationID);

$replace = array("/", "-");
$excelfile = "issues_export_" . str_replace( $replace, "_", format_date( date( 'Y-m-d' ) ) ).".csv";

header("Pragma: public");
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=\"" . $excelfile . "\"");

echo array_to_csv_row(array("Issues Export " . format_date( date( 'Y-m-d' ))));

$columnHeaders = array(
  "Organization",
  "Issue Type",
  "Start Date",
  "End Date",
  "Issue Text"
);
echo array_to_csv_row($columnHeaders);

foreach($issues as $issue) {

	if (isset($resource['updateDate']) && $resource['updateDate'] == "0000-00-00"){     
		$updateDateFormatted="";
	}else{
		if(isset($resource['updateDate'])){
			$updateDateFormatted=format_date($resource['updateDate']);
		}		   
	}
  $issueValues = array(
    $issue['name'],
    $issue['shortName'],
    $issue['issueStartDate'],
    $issue['issueEndDate'],
    preg_replace('/\s+/', ' ', trim($issue['noteText'])),
  );

	echo array_to_csv_row($issueValues);
}
?>
