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

ini_set("auto_detect_line_endings", true);
include_once 'directory.php';

$util = new Utility();


//this file has been created from SUSHI
if ($_GET['importLogID'] > 0){
	$importLog = new ImportLog(new NamedArguments(array('primaryKey' => $_GET['importLogID'])));

	$layout = new Layout();
	$layout->getByLayoutCode($importLog->layoutCode);
	$layoutID = $layout->layoutID;

	$pageTitle = 'SUSHI Import Confirmation';

	$target_path = $importLog->fileName;
	//$checkYear = date("Y"); //checkYear is not used for SUSHI imports anymore
	$formatCorrectFlag = "Y";
	$errorFlag = "N";
	$startFlag = "Y";
	$reportTypeDisplay = $layout->name;

	#read this file
	$file_handle = $util->utf8_fopen_read($target_path, true);

	//takes the first line of the SUSHI import file and finds the year(s) associated
	$firstLine = stream_get_line($file_handle, 10000000, "\n");
	$firstArray = explode("\t",$firstLine);
	$numCol = count($firstArray);
	//gets the start and ending years from the first line of the SUSHI import
	$startYearArr = explode("-",$firstArray[($numCol-1)]);
	$startYear = $startYearArr[1];//the month of december is always the starting year
	$endYearArrHelp = explode("-",$firstLine);
	$endYearArr = explode("\t",$endYearArrHelp[1]);
	$endYear = $endYearArr[0];//find january's year, which is always the end year
	for ($i = 0; $i < 12; $i++) {
		$monthArray[$i] = $firstArray[($numCol-12+$i)];
	}
	$startMonth = NULL;
	//find the startMonth if only one year is present
	if ($startYear == $endYear) {
		$cMonth = explode("-",$monthArray[0]);
		for ($i = 0; $cMonth[2] == 'x'; $i++) {
			$cMonth	= explode("-",$monthArray[$i]);
			if ($cMonth[2] != 'x') {
				$startMonth = $cMonth[0];
			}
		}
		$startMonth = $cMonth[0];//this will set the startMonth to January if the program never enters the for loop
	}
	//find the startMonth if multiple years are represented in the file
	else {
		$cMonth = explode("-",$monthArray[0]);
		for ($i = 0; $cMonth[1] != $startYear; $i++) {
			$cMonth = explode("-",$monthArray[$i]);
			if($cMonth[1] == $startYear) {
				$startMonth = $cMonth[0];
			}
		}
	}
	$startDate = array($startMonth, $startYear);
	$startDate = implode("-", $startDate);
	$numMonths = 12;
	for ($i = 11; $i >= 0; $i--) {
		$cMonth = explode("-",$monthArray[$i]);
		if ($cMonth[2] == 'x')
			$numMonths--;
	}
//came from file import

//came from file import
}else{

	//First, move the uploaded file
	// Where the file is going to be placed

	if (count(explode (".", basename( $_FILES['usageFile']['name']))) == "2"){
		list ($fileNameStart, $fileNameExt) = explode (".", basename( $_FILES['usageFile']['name']));
	}else if (count(explode (".", basename( $_FILES['usageFile']['name']))) == "3"){
		list ($dateStamp, $fileNameStart, $fileNameExt) = explode (".", basename( $_FILES['usageFile']['name']));
	}else{
		header( 'Location: import.php?error=3' ) ;
	}

	$orgFileName = $fileNameStart .  "." . $fileNameExt;

	$ts = date("Ymd");
	$target_path = BASE_DIR . "archive/" . $ts . "." . $fileNameStart .  "." . $fileNameExt;
	$checkYear = '';

	if ($fileNameExt != "txt") {
		header( 'Location: import.php?error=1' ) ;
	}else{

		if(move_uploaded_file($_FILES['usageFile']['tmp_name'], $target_path)) {
			$uploadConfirm = "The file ".  basename( $_FILES['usageFile']['name'])." has been uploaded successfully.<br />Please confirm the following data:<br />";
		} else{
			header( 'Location: import.php?error=2' ) ;
		}
	}


	#file upload was OK, now we can read the file to output for confirmation
	$formatCorrectFlag = "N";
	$foundColumns = "";
	$errorFlag = "N";
	$startFlag = "N";
	$unmatched = "";
	$del = ""; //delimiter
	$layoutID = $_POST['layoutID'];

	#read this file
	$file_handle = $util->utf8_fopen_read($target_path, false);


	$pageTitle = 'Upload Process Confirmation';
}


include 'templates/header.php';

?>

<script language="javascript">

	function updateSubmit(){
		document.confirmForm.submitForm.disabled=true;
		document.confirmForm.submitForm.value="Processing Contents...";
		document.confirmForm.submit();
	}
</script>




<table class="headerTable">
	<tr><td>
			<div class="headerText"><?php echo $pageTitle; ?></div>
			<br />


			<?php


			#read layouts ini file to get the available layouts
			$layoutsArray = parse_ini_file("layouts.ini", true);

			if ($layoutID != ""){
				$reportTypeSet = 'Y';
				$layout = new Layout(new NamedArguments(array('primaryKey' => $layoutID)));
				$layoutKey = $layoutsArray['ReportTypes'][$layout->layoutCode];
				$columnsToCheck = $layoutsArray[$layoutKey]['columnToCheck'];
				$reportTypeDisplay = $layout->name;
				$layoutColumns = $layoutsArray[$layoutKey]['columns'];
				$numberOfColumns = count($layoutColumns);
			}

			if ((isset($uploadConfirm)) and (!empty($uploadConfirm))) {
				echo $uploadConfirm;
			}
			echo "<table class='dataTable' style='width:895px;'>";

			//print out headers automaticall if this was from sushi
			if ($importLog->loginID == "sushi"){
				//print out report type and year
				echo "<tr><td colspan='" . $numberOfColumns . "'>" . $reportTypeDisplay . " for " . date("F", strtotime($startMonth)) . " " . $startYear;
				echo " - " . date("F", mktime(0,0,0,date("n",strtotime($startMonth))+$numMonths-1)) . " " . $endYear . "</td></tr>";
				#also print out column headers
				echo "<tr>";
				foreach ($layoutColumns as $value){
					echo "<th>" . strtoupper($value) . "</th>";
				}
				echo "</tr>";
			}


			while (!feof($file_handle)) {
				//get each line out of the file handler
				$line = stream_get_line($file_handle, 10000000, "\n");

				//if report type hasn't been figured out, check for it in the first row / column
				if ($reportTypeSet == ""){

					foreach ($layoutsArray['ReportTypes'] as $reportTypeKey => $layoutKey){
						list($report,$release) = explode("_",$reportTypeKey);
						if ((strpos($line, $report) !== false) && (strpos($line, $release) !== false)){
							$reportTypeSet = 'Y';
							$columnsToCheck = $layoutsArray[$layoutKey]['columnToCheck'];
							$reportTypeDisplay = $line;
							$layout = $layoutKey;
						}
					}


				}

				//set delimiter
				$del = "\t";


				//check column formats if the format correct flag has not been set yet
				if (($formatCorrectFlag == "N") && (count(explode($del,$line)) >= count($columnsToCheck)) && (strlen($line) > 20)){
					//positive unless proven negative
					$formatCorrectFlag = "Y";
					$lineArray = explode("\t",$line);

					if (($columnsToCheck) && (count($lineArray) >=5)){
						foreach ($columnsToCheck as $key => $colCheckName){
							$fileColName = strtolower(trim($lineArray[$key]));

							if (strpos($fileColName, strtolower($colCheckName)) === false){
								if (!$unmatched){
									$unmatched = "Looking for \"$colCheckName\" in column $key but found \"$fileColName\"";
								}
								$formatCorrectFlag='N';
							}

						}
					}

					if ($formatCorrectFlag == 'Y'){
						$numberOfColumns = count($lineArray);

						if ($checkYear == ""){
							list ($checkMonth,$checkYear) = preg_split("/[-\/.]/",$fileColName);
							if ($checkYear < 100) $checkYear = 2000 + $checkYear;
						}

						//print out report type and year
						echo "<tr><td colspan='" . $numberOfColumns . "'>" . $reportTypeDisplay . " for " . $checkYear . "</td></tr>";

						#also print out column headers
						echo "<tr>";
						foreach ($lineArray as $value){
							echo "<th>" . $value . "</th>";
						}
						echo "</tr>";
					}else{
						if (!$foundColumns){
							$foundColumns = implode(", ", $lineArray);
						}
					}
				}


				//as long as the flags are set to print out, and the line exists, print the line formatted in table
				//(strpos($line,"\t\t\t\t") === false)
//if (($startFlag == "Y") && ($formatCorrectFlag == "Y")  && !(strpos($line,"\t") == "0") && (substr($line,0,5) != "Total") && (count(explode("\t",$line)) > 5)) {
				if (($formatCorrectFlag == "Y") && (substr($line,0,5) != "Total") && ($startFlag == "Y")  && (strpos($line,$del) != "0" ) && (count(explode("\t",$line)) > 5)) {
					echo "<tr>";
					$lineArray = explode($del,$line);

					foreach($lineArray as $value){

						//Clean some of the data

						//strip everything after (Subs from Title
						if (strpos($value,' (Subs') !== false) $value = substr($value,0,strpos($value,' (Subs'));

						//remove " quotes
						$value = str_replace("\"","",$value);

						if (($value == '') || ($value == ' ')) {
							echo "<td>&nbsp;</td>";
						}else{
							echo "<td>" . $value . "</td>";
						}
					}
					echo "</tr>";

				}


				#check "Total for all" is in first column  - set flag to start import after this
				if ((substr($line,0,5) == "Total") || ($formatCorrectFlag == "Y")){
					$startFlag = "Y";
				}


			}
			echo "</table>";
			fclose($file_handle);


			$errrorFlag="N";

			if (($formatCorrectFlag == "N")){
				echo "<br /><font color='red'><b>Error with Format</b>:  Report format is set to <b>" . $reportTypeDisplay . "</b> but does not match the column names listed in layouts.ini for this format - $unmatched.<br /><br />Expecting columns: " . implode(", ", $columnsToCheck) . "<br /><br />Found columns: " . $foundColumns . "</font><br /><br />If problems persist you can copy an existing header that works into this file.";
				$errorFlag="Y";
			}

			if (!$layoutKey){
				echo "<br /><font color='red'>Error with Setup:  This report format is not set up in layouts.ini.</font><br />";
				$errorFlag="Y";
			}

			if (($startFlag == "N")){
				echo "<br /><font color='red'>Error with Format:  The line preceding the first should start with 'Total'.</font><br />";
				$errorFlag="Y";
			}

			if ($checkYear > date('Y')){
				echo "<br /><font color='red'>Error with Year:  Year listed in header (" . $checkYear . ") may not be ahead of current year.  Please correct and submit again.</font><br />";
				$errorFlag="Y";
			}

			if (isset($_POST['overrideInd'])){
				echo "<br /><font color='red'>File is flagged to override verifications of previous month data.  If this is incorrect use 'Cancel' to fix.</font><br />";
				$overrideInd = 1;
			}else{
				$overrideInd = 0;
			}

			if ($errorFlag != "Y"){
				echo "<br />Report Format: <b>" . $reportTypeDisplay . "</b><br />If this is incorrect, please use 'Cancel' to go back and fix the headers of the file.<br />";
			}

			?>

			<br />
			<form id="confirmForm" name="confirmForm" enctype="multipart/form-data" method="post" action="uploadComplete.php">
				<input type="hidden" name="upFile" value="<?php echo $target_path; ?>">
				<input type="hidden" name="overrideInd" value="<?php echo $overrideInd; ?>">
				<input type="hidden" name="orgFileName" value="<?php echo $orgFileName; ?>">
				<input type="hidden" name="importLogID" value="<?php echo $importLog->importLogID; ?>">
				<input type="hidden" name="checkYear" value="<?php echo $checkYear; ?>">
				<input type="hidden" name="layoutID" value="<?php echo $layoutID; ?>">
				<input type="hidden" name="startDate" value="<?php echo $startDate; ?>">
				<input type="hidden" name="numMonths" value="<?php echo $numMonths; ?>">
				<table>
					<tr valign="center">
						<td>
							<input type="button" name="submitForm" id="submitForm" value="Confirm" <?php if ($errorFlag == "Y"){ echo "disabled"; } ?> onclick="javascript:updateSubmit();" />
						</td>
						<td>
							<input type="button" value="Cancel" onClick="javascript:history.back();">
						</td>
					</tr>
				</table>