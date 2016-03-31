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

class SushiService extends DatabaseObject {

	protected function defineRelationships() {}

	protected function overridePrimaryKeyName() {}

	public $startDate;
	public $endDate;
	private $statusLog = array();
	private $detailLog = array();

	public function getByPlatformID($platformID){
		if (isset($platformID)) {
			$query = "SELECT * FROM `$this->tableName` WHERE `platformID` = '$platformID'";
			$result = $this->db->processQuery($query, 'assoc');

			foreach (array_keys($result) as $attributeName) {
				$this->addAttribute($attributeName);
				$this->attributes[$attributeName] = $result[$attributeName];
			}
		}
	}

	public function getByPublisherPlatformID($publisherPlatformID){
		if (isset($publisherPlatformID)) {
			$query = "SELECT * FROM `$this->tableName` WHERE `publisherPlatformID` = '$publisherPlatformID'";
			$result = $this->db->processQuery($query, 'assoc');

			foreach (array_keys($result) as $attributeName) {
				$this->addAttribute($attributeName);
				$this->attributes[$attributeName] = $result[$attributeName];
			}
		}
	}

	//returns array of sushi service objects that need to be run on a particular day
	public function getByDayOfMonth($serviceDayOfMonth){
		//now formulate query
		$query = "SELECT * FROM SushiService WHERE `serviceDayOfMonth` = '$serviceDayOfMonth';";

		$result = $this->db->processQuery(stripslashes($query), 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['sushiServiceID'])){
			$object = new SushiService(new NamedArguments(array('primaryKey' => $result['sushiServiceID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new SushiService(new NamedArguments(array('primaryKey' => $row['sushiServiceID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}



	public function getPublisherOrPlatform(){
		if (($this->platformID != "") && ($this->platformID > 0)){
			return new Platform(new NamedArguments(array('primaryKey' => $this->platformID)));
		}else{
			return new PublisherPlatform(new NamedArguments(array('primaryKey' => $this->publisherPlatformID)));
		}
	}


	public function getServiceProvider(){
		return str_replace('"','',$this->getPublisherOrPlatform->reportDisplayName);
	}




	public function failedImports(){
		$query = "SELECT ipl.platformID, ss.sushiServiceID, date(il.importDateTime), details, il.importLogID
		FROM ImportLog il
			INNER JOIN ImportLogPlatformLink ipl USING (ImportLogID)
				INNER JOIN SushiService ss ON (ss.platformID = ipl.platformID)
			INNER JOIN (SELECT platformID, max(importLogID) importLogID, max(importDateTime) importDateTime FROM ImportLog mil INNER JOIN ImportLogPlatformLink mipl USING (ImportLogID) GROUP BY platformID) mil ON (mil.importLogID = il.importLogID)
		WHERE ucase(details) like '%FAIL%'
		ORDER BY il.importDateTime desc";

		$result = $this->db->processQuery(stripslashes($query), 'assoc');

		$resultArray = array();
		$importArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['platformID'])){

			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

			array_push($importArray, $resultArray);
		}else{
			foreach ($result as $row) {
				$resultArray = array();
				foreach (array_keys($row) as $attributeName) {
					$resultArray[$attributeName] = $row[$attributeName];
				}
				array_push($importArray, $resultArray);
			}
		}

		return $importArray;

	}

	public function allServices(){
		$query = "SELECT ss.platformID, ss.publisherPlatformID, sushiServiceID, serviceURL, reportLayouts, releaseNumber,
		if(serviceDayOfMonth > day(now()), str_to_date(concat(EXTRACT(YEAR_MONTH FROM NOW()), lpad(serviceDayOfMonth,2,'0')), '%Y%m%d'), str_to_date(concat(EXTRACT(YEAR_MONTH FROM NOW()) + 1, lpad(serviceDayOfMonth,2,'0')), '%Y%m%d') ) next_import
		FROM SushiService ss
			LEFT JOIN Platform p on (p.platformID = ss.platformID)
			LEFT JOIN PublisherPlatform pp 
				INNER JOIN Publisher pub USING(publisherID)
			ON (pp.publisherPlatformID = ss.publisherPlatformID)
		ORDER BY p.name, pub.name";

		$result = $this->db->processQuery(stripslashes($query), 'assoc');

		$resultArray = array();
		$importArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['platformID'])){

			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

			array_push($importArray, $resultArray);
		}else{
			foreach ($result as $row) {
				$resultArray = array();
				foreach (array_keys($row) as $attributeName) {
					$resultArray[$attributeName] = $row[$attributeName];
				}
				array_push($importArray, $resultArray);
			}
		}

		return $importArray;

	}


	//run through ajax function on publisherplatform
	public function runTest(){
		$reportLayouts = $this->reportLayouts;
		$rlArray = explode(";", $reportLayouts);

		//just default test import dates to just be january 1 - 31 of this year
		$sDate = date_format(date_create_from_format("Ymd", date("Y")."0101"), "Y-m-d");
		$eDate = date_format(date_create_from_format("Ymd", date("Y")."0131"), "Y-m-d");
		$this->setImportDates($sDate, $eDate);


		foreach($rlArray as $reportLayout){
			$xmlFile = $this->sushiTransfer($reportLayout);
		}

		if ($reportLayouts == ""){
			echo "At least one report type must be set up!";
		}else{
			echo "Connection test successful!";
		}

	}

	//run through post or through sushi scheduler
	public function runAll($overwritePlatform = TRUE){
		$reportLayouts = $this->reportLayouts;
		$rlArray = explode(";", $reportLayouts);

		$detailsForOutput = array();

		foreach($rlArray as $reportLayout){
			$this->statusLog = array();
			$this->detailLog = array();

			$xmlFile = $this->sushiTransfer($reportLayout);
			$this->parseXML($xmlFile, $reportLayout, $overwritePlatform);

			$detailsForOutput = $this->statusLog;
		}

		if ($reportLayouts == ""){
			return "No report types are set up!";
		}

		return implode("\n", $detailsForOutput);
	}





	public function setDefaultImportDates(){

		// Determine the End Date
		//start with first day of this month
		$endDate = date_create_from_format("Ymd", date("Y") . date("m") . "01" );

		//subtract one day
		date_sub($endDate, date_interval_create_from_date_string('1 days'));
		$this->endDate = date_format($endDate,"Y-m-d");

		//Determine the Start Date
		//first, get this publisher/platform's last day of import
		$lastImportDate = $this->getPublisherOrPlatform->getLastImportDate();
		$lastImportDate = date_create_from_format("Y-m-d", $lastImportDate);
		date_add($lastImportDate, date_interval_create_from_date_string('1 month'));

		//if that date is set and it's sooner than the first of this year, default it to that date
		if (($lastImportDate) && (date_format($lastImportDate, "Y-m-d") > date_format($endDate, "Y") . "-01-01")){
			$this->startDate = date_format($lastImportDate, "Y-m-d");
		}else{
			$this->startDate = date_format($endDate, "Y") . "-01-01";
		}

	}



	public function setImportDates($sDate = null, $eDate = null){

		if (!$sDate){
			$this->setDefaultImportDates();
		}else {
			//using the multiple functions in order to make sure leading zeros, and this is a date
			$this->startDate = date_format(date_create_from_format("Y-m-d", $sDate), "Y-m-d");
			$this->endDate = date_format(date_create_from_format("Y-m-d", $eDate), "Y-m-d");
		}

	}




	//status for storing in DB and displaying in rows
	private function logStatus($logText){
		array_push($this->statusLog, $logText);
		array_push($this->detailLog, $logText);
	}

	//longer log for storing in log file and displaying output
	private function log($logText){
		array_push($this->detailLog, $logText);
	}

	//logs process to import log table and to log file
	public function saveLogAndExit($reportLayout = NULL, $txtFile = NULL, $success = FALSE){


		//First, delete any preexisting Failured records, these shouldn't be needed/interesting after this.
		$this->log("Cleaning up prior failed import logs....");

		$this->getPublisherOrPlatform->removeFailedSushiImports;

		if (!$txtFile){
			$txtFile =  strtotime("now") . '.txt';
		}
		$logFileLocation = 'logs/' . $txtFile;

		$this->log("Log File Name: $logFileLocation");

		if ($success){
			$this->logStatus("Finished processing " . $this->getServiceProvider . ": $reportLayout.");
		}

		//save the actual log file
		$fp = fopen(BASE_DIR . $logFileLocation, 'w');
		fwrite($fp, implode("\n", $this->detailLog));
		fclose($fp);


		//save to import log!!
		$importLog = new ImportLog();
		$importLog->loginID = "sushi";
		$importLog->layoutCode = $reportLayout;
		$importLog->fileName = 'archive/' . $txtFile;
		$importLog->archiveFileURL = 'archive/' . $txtFile;
		$importLog->logFileURL = $logFileLocation;
		$importLog->details = implode("<br />", $this->statusLog);

		try {
			$importLog->save();
			$importLogID = $importLog->primaryKey;
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		$importLogPlatformLink = new ImportLogPlatformLink();
		$importLogPlatformLink->importLogID = $importLogID;
		$importLogPlatformLink->platformID = $this->platformID;


		try {
			$importLogPlatformLink->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		if(!$success){
			throw new Exception(implode("\n", $this->detailLog));
		}


	}



	private function soapConnection($wsdl, $parameters){

		$parameters = array_merge($parameters, array(
				"keep_alive" => true,
				"connection_timeout"=>1000,
				"trace"      => 1,
				"exceptions" => 1,
				"cache_wsdl" => WSDL_CACHE_NONE,
				"stream_context" => stream_context_create(array(
					'http' => array('protocol_version' => 1.0,
						'header' => 'Content-Type: application/soap+xml')))
			)
		);

		try{
			try{
				$client = new SoapClient($wsdl, $parameters);

				//returns soapfault
			}catch (Exception $e){
				$error = $e->__toString();

				//if soap fault returned version mismatch or http headers error, try again with soap 1.2
				if ((preg_match('/Version/i', $error)) || (preg_match('/HTTP/i', $error))){

					$this->log("Using Soap Version 1.2");
					$parameters = array_merge($parameters, array("soap_version" => SOAP_1_2));

					//try connection again with 1.2
					$client = new SoapClient($wsdl, $parameters);
				}
			}

			//throws soap fault
		}catch (Exception $e){
			$error = $e->getMessage();

			$this->logStatus("Failed to establish soap connection: " . $error);
			$this->saveLogAndExit();
		}

		$this->log("");
		$this->log("-- Soap Connection successfully completed --");
		$this->log("");

		return $client;
	}



	private function sushiTransfer($reportLayout){



		$ppObj = $this->getPublisherOrPlatform();
		$serviceProvider = str_replace('"','',$ppObj->reportDisplayName);

		//if report layout is BR and Release is 3, change it to 1
		if((preg_match('/BR/i', $reportLayout)) && ($this->releaseNumber == "3")){
			$releaseNumber = '1';
		}else{
			$releaseNumber = $this->releaseNumber;
		}

		if (($this->wsdlURL == '') || (strtoupper($this->wsdlURL) == 'COUNTER')){
			if ($this->releaseNumber == "4"){
				$wsdl = 'http://www.niso.org/schemas/sushi/counter_sushi4_0.wsdl';
			}else{
				$wsdl = 'http://www.niso.org/schemas/sushi/counter_sushi3_0.wsdl';
			}
		}else{
			$wsdl=$this->wsdlURL;
		}



		$createDate = date("Y-m-d\TH:i:s.0\Z");
		$id = uniqid("CORAL:", true);

		// look at $Security to ses if it uses an extension
		if(preg_match('/Extension=/i', $this->security)){
			$extensions = array();
			$varlist = explode(";", $this->security);
			foreach( $varlist as $params){
				list($extVar, $extVal) = explode("=", $params);
				$extensions[$extVar] = $extVal;
				if ($extVar == 'Extension'){
					$extension = $extVal;
				}
			}
		}

		if (!empty($extension)){
			include BASE_DIR . 'sushiincludes/extension_'.$extension.'.inc.php';
		}else{
			if (preg_match("/http/i", $this->security)){
				$this->log("Using HTTP Basic authentication via login and password.");

				$parameters = array(
					'login'          => $this->login,
					'password'       => $this->password,
					'location' 		=> $this->serviceURL,
				);
			}else{
				if ((strtoupper($this->wsdlURL) != 'COUNTER') && ($this->wsdlURL != '')){
					$this->log("Using provided wsdl: $wsdl");
					$parameters = array();

				}else{
					$this->log("Using COUNTER wsdl, connecting to $this->serviceURL");
					$parameters = array('location'=> $this->serviceURL);
				}
			}

			$client = $this->soapConnection($wsdl, $parameters);
		}

		if (preg_match("/wsse/i", $this->security)){
			// Prepare SoapHeader parameters
			$strWSSENS = "http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd";
			$objSoapVarUser = new SoapVar($this->login, XSD_STRING, NULL, $strWSSENS, NULL, $strWSSENS);
			$objSoapVarPass = new SoapVar($this->password, XSD_STRING, NULL, $strWSSENS, NULL, $strWSSENS);
			$objWSSEAuth = new clsWSSEAuth($objSoapVarUser, $objSoapVarPass);
			$objSoapVarWSSEAuth = new SoapVar($objWSSEAuth, SOAP_ENC_OBJECT, NULL, $strWSSENS, 'UsernameToken', $strWSSENS);
			$objWSSEToken = new clsWSSEToken($objSoapVarWSSEAuth);
			$objSoapVarWSSEToken = new SoapVar($objWSSEToken, SOAP_ENC_OBJECT, NULL, $strWSSENS, 'UsernameToken', $strWSSENS);
			$objSoapVarHeaderVal=new SoapVar($objSoapVarWSSEToken, SOAP_ENC_OBJECT, NULL, $strWSSENS, 'Security', $strWSSENS);
			$objSoapVarWSSEHeader = new SoapHeader($strWSSENS, 'Security', $objSoapVarHeaderVal,false);

			// Prepare Soap Client
			try{
				$client->__setSoapHeaders(array($objSoapVarWSSEHeader));
			}catch (Exception $e){
				$error = $e->getMessage();
				$this->logStatus("Failed to connect to $serviceProvider: " . $error);
				$this->log("Tried: " . var_dump($client));
				$this->saveLogAndExit($reportLayout);
			}

		}
		$uStartArray = explode("-",$this->startDate);
		$usd = $uStartArray[2];//the start day used to find the unix timestamp for the start date
		$usm = $uStartArray[1];//the start month used to find the unix timestamp for the start date
		$usy = $uStartArray[0];//the start year used to find the unix timestamp for the start date
		$uStartDate = mktime(0,0,0,$usm,$usd,$usy);//finds the unix timestamp for the start date
		//Do exactly the same thing for the end date
		$uEndDate = explode("-", $this->endDate);
		$ued = $uEndDate[2];
		$uem = $uEndDate[1];
		$uey = $uEndDate[0];
		$uEndDate = mktime(0,0,0,$uem,$ued,$uey);
		if (($uEndDate-$uStartDate)<31536000){
			try{
				$reportRequest = array
				('Requestor' => array
					('ID' => $this->requestorID,
						'Name' => 'CORAL Processing',
						'Email' => $this->requestorID
					),
					'CustomerReference' => array
					('ID' => $this->customerID,
						'Name' => 'CORAL Processing'
					),
					'ReportDefinition'  => array
					('Filters' => array
						('UsageDateRange' => array
							('Begin' => $this->startDate,
								'End' => $this->endDate
							)
						),
						'Name' => $reportLayout,
						'Release' => $releaseNumber
					),
					'Created' => $createDate,
					'ID' => $id,
					'connection_timeout' => 1000
				);
				$dateError=FALSE;

				$result = $client->GetReport($reportRequest);
			}catch(Exception $e){
				$error = $e->getMessage();

				$this->logStatus("Exception performing GetReport with connection to $serviceProvider: $error");

				//exceptions seem to happen that don't matter, continue processing and if no data or error is found then it will quit.
				//$this->saveLogAndExit($reportLayout);
			}
		}
		else {
			$dateError = TRUE;
			$this->logStatus("Invalid Dates entered. Must enter a start and end date less than or equal to one year apart.");
		}


		$xml = $client->__getLastResponse();

		$fname = $serviceProvider.'_'.$reportLayout.'_'.$this->startDate.'_'.$this->endDate.'.xml';
		$replace="_";
		$pattern="/([[:alnum:]_\.-]*)/";
		$fname = 'sushistore/' . str_replace(str_split(preg_replace($pattern,$replace,$fname)),$replace,$fname);

		$xmlFileName = BASE_DIR . $fname;
		file_put_contents($xmlFileName, $xml);

		//open file to look for errors
		$reader = new XMLReader();
		if (!$reader->open($xmlFileName)) {
			$this->logStatus("Failed trying to open XML File: " . $xmlFileName . ".  This could be due to not having write access to the /sushistore/ directory.");
			$this->saveLogAndExit($reportLayout);
		}

		$message = "";
		while ($reader->read()) {
			if ($reader->nodeType == XMLReader::ELEMENT){
				if ($reader->localName == 'Severity') {
					$reader->read();
					$severity = trim($reader->value);
				}
				if ($reader->localName == 'Message') {
					$reader->read();
					$message = trim($reader->value);
				}

			}
		}

		$reader->close();

		if ($message !=""){
			if (($severity == "Error") || (stripos($message, "Error") !== FALSE)){
				$this->logStatus("Failed to request report from $serviceProvider: " . $message);

				$this->log("Please fix the settings for this provider and try again.");
				$this->saveLogAndExit($reportLayout);
			}else{
				$this->logStatus("$serviceProvider says: $severity: $message");
			}
		}
		if (!$dateError)
			$this->log("$reportLayout successfully retrieved from $serviceProvider for start date:  $this->startDate, end date: $this->endDate");

		$this->log("");
		$this->log("-- Sushi Transfer completed --");

		return $fname;


	}



	private function parseXML($fName, $reportLayout, $overwritePlatform){


		//////////////////////////////////////
		//PARSE XML!!
		//////////////////////////////////////

		$serviceProvider = $this->getServiceProvider();
		$xmlFileName = BASE_DIR . $fName;

		//read layouts ini file to get the available layouts
		$layoutsArray = parse_ini_file(BASE_DIR . "layouts.ini", true);
		$layoutColumns = array();

		$reader = new XMLReader();
		if (!$reader->open($xmlFileName, 'UTF-8')) {
			$this->logStatus("Failed trying to open XML File: " . $xmlFileName . ".  This could be due to not having write access to the /sushistore/ directory.");
			$this->saveLogAndExit($reportLayout);
		}


		$layoutCode = "";
		$countArray = array('ytd'=>null,'pdf'=>null,'html'=>null);
		$txtOut = "";
		$startDateArr = explode("-", $this -> startDate);
		$endDateArr = explode("-", $this -> endDate);
		$startYear = $startDateArr[0];
		$startMonth = $startDateArr[1];
		$endYear = $endDateArr[0];
		$endMonth = $endDateArr[1];
		$numMonths = 0;
		if ($startMonth > $endMonth)
			$numMonths = (13 - ($startMonth - $endMonth));
		else if ($endMonth > $startMonth)
			$numMonths = ($endMonth - $startMonth);
		else
			$numMonths = 1;
		$m = null; //month

		while ($reader->read()) {
			//First - get report information
			if (($reader->nodeType == XMLReader::ELEMENT) && ($reader->localName == 'Report') && (count($layoutColumns) == '0')) {
				$name = $reader->getAttribute("Name");
				$version = $reader->getAttribute("Version");

				$layoutCode = $name;

				if (($version == "3") || ($version =="4")){
					$version = "R" . $version;
				}
				if ($version != ''){
					$layoutCode .= "_" . $version;
				}else{
					$layoutCode .= "_R" . $this->releaseNumber;
				}

				//At this point, determine the format of the report to port to csv from the layouts.ini file
				$layoutKey = $layoutsArray['ReportTypes'][$layoutCode];
				$layoutColumns = $layoutsArray[$layoutKey]['columns'];


				//if this way of determining layout was unsuccessful, just use the layout sent in
				if (count($layoutColumns) == "0"){
					$layoutCode = $reportLayout . "_R" . $this->releaseNumber;

					$layoutKey = $layoutsArray['ReportTypes'][$layoutCode];
					$layoutColumns = $layoutsArray[$layoutKey]['columns'];

					///////////////////////////////////////////////////////
					// Create header for SUSHI file
					///////////////////////////////////////////////////////
					$header = $layoutColumns;
					for ($i = 0; $i < sizeof($header); $i++) {
						if ($header[$i] == "jan" && $startMonth == 1) {
							$header[$i] .= "-" . $startYear;
						}
						else if ($header[$i] == "jan" && $startMonth != 1) {
							$header[$i] .= "-" . $endYear;
						}
						if ($header[$i] == "feb" && $startMonth <= 2) {
							$header[$i] .= "-" . $startYear;
						}
						else if ($header[$i] == "feb" && $startMonth > 2) {

							$header[$i] .= "-" . $endYear;
						}
						if ($header[$i] == "mar" && $startMonth <= 3) {
							$header[$i] .= "-" . $startYear;
						}
						else if ($header[$i] == "mar" && $startMonth > 3) {
							$header[$i] .= "-" . $endYear;
						}
						if ($header[$i] == "apr" && $startMonth <= 4) {
							$header[$i] .= "-" . $startYear;
						}
						else if ($header[$i] == "apr" && $startMonth > 4) {
							$header[$i] .= "-" . $endYear;
						}
						if ($header[$i] == "may" && $startMonth <= 5) {
							$header[$i] .= "-" . $startYear;
						}
						else if ($header[$i] == "may" && $startMonth > 5) {
							$header[$i] .= "-" . $endYear;
						}
						if ($header[$i] == "jun" && $startMonth <= 6) {
							$header[$i] .= "-" . $startYear;
						}
						else if ($header[$i] == "jun" && $startMonth > 6) {
							$header[$i] .= "-" . $endYear;
						}
						if ($header[$i] == "jul" && $startMonth <= 7) {
							$header[$i] .= "-" . $startYear;
						}
						else if ($header[$i] == "jul" && $startMonth > 7) {
							$header[$i] .= "-" . $endYear;
						}
						if ($header[$i] == "aug" && $startMonth <= 8) {
							$header[$i] .= "-" . $startYear;
						}
						else if ($header[$i] == "aug" && $startMonth > 8) {
							$header[$i] .= "-" . $endYear;
						}
						if ($header[$i] == "sep" && $startMonth <= 9) {
							$header[$i] .= "-" . $startYear;
						}
						else if ($header[$i] == "sep" && $startMonth > 9) {
							$header[$i] .= "-" . $endYear;
						}
						if ($header[$i] == "oct" && $startMonth <= 10) {
							$header[$i] .= "-" . $startYear;
						}
						else if ($header[$i] == "oct" && $startMonth > 10) {
							$header[$i] .= "-" . $endYear;
						}
						if ($header[$i] == "nov" && $startMonth <= 11) {
							$header[$i] .= "-" . $startYear;
						}
						else if ($header[$i] == "nov" && $startMonth == 12) {
							$header[$i] .= "-" . $endYear;
						}
						if ($header[$i] == "dec" && $startMonth <= 12) {
							$header[$i] .= "-" . $startYear;
						}
					}
					for ($i = 12; $i > 0; $i--) {
						if ($startMonth > $endMonth && $i < $startMonth && $i > $endMonth)
							$header[(count($header) - 13)+$i] .= "-x";
						else if ($endMonth > $startMonth && ($i < $startMonth || $i > $endMonth))
							$header[(count($header) - 13)+$i] .= "-x";
						else if ($endMonth == $startMonth && $i < $startMonth && $i > $endMonth)
							$header[(count($header) - 13)+$i] .= "-x";
					}
					$txtOut .= implode($header, "\t") . "\n";
				}

				$this->log("Layout validated successfully against layouts.ini : " . $layoutCode);


			}

			if (($reader->nodeType == XMLReader::ELEMENT) && ($reader->localName == 'ReportItems')) {
				if ((count($layoutColumns) == '0') || ($layoutCode == '')){
					$this->logStatus("Failed determining layout:  Reached report items before establishing layout.  Please make sure this layout is set up in layouts.ini");
					$this->saveLogAndExit($reportLayout);
				}

				//reset variables
				$identifierArray=array();
				$reportArray = array('ytd'=>null,'ytdHTML'=>null,'ytdPDF'=>null);

				//loop through each element under "Item"
				while ($reader->read()) {

					//get the element name
					if ($reader->nodeType == XMLReader::ELEMENT){
						$elementName = trim($reader->localName);

						//move to next to get the text
						if (($elementName != "Instance") && ($elementName != "ItemIdentifier") && ($elementName != "Period")){
							$reader->read();
						}



						if ($reader->nodeType == XMLReader::TEXT
							|| $reader->nodeType == XMLReader::CDATA
							|| $reader->nodeType == XMLReader::WHITESPACE
							|| $reader->nodeType == XMLReader::SIGNIFICANT_WHITESPACE) {
							$elementValue = trim($reader->value);

							switch ($elementName) {
								case 'ItemPlatform':
									if ($overwritePlatform){
										$reportArray['platform'] = $serviceProvider;
									}else{
										$reportArray['platform'] = $elementValue;
									}

									break;
								case 'ItemPublisher':
									$reportArray['publisher'] = $elementValue;
									break;
								case 'ItemName':
									$reportArray['title'] = $elementValue;
									break;
								case 'ActivityType':
									$reportArray['activityType'] = strtoupper($reader->value);
									break;
								case 'Type':
									$idType = strtoupper($reader->value);
									break;
								case 'Value':
									$identifierArray[$idType] = $reader->value;
									break;
								case 'Begin':
									$date = new DateTime($reader->value);
									if ($m === null) {
										$m = strtolower($date->format('M'));
										$countArray = array('ytd'=>null,'pdf'=>null,'html'=>null);
									} else if (strtolower($date->format('M')) !== $m){
										$totalCountsArray[$m] = $countArray;

										$m = strtolower($date->format('M'));
										//$y = strtolower($date->format('Y'));

										$countArray = array('ytd'=>null,'pdf'=>null,'html'=>null);
									}

									break;
								case 'MetricType':
									$metricType = strtoupper($reader->value);

									//make sure metric types have conformity
									if (!(strpos($metricType,'HTML') === false)){
										$metricType ='html';
									}else if (!(strpos($metricType,'PDF') === false)){
										$metricType ='pdf';
									}else{
										$metricType ='ytd';
									}

									break;
								case 'Count':
									$countArray[$metricType] = $reader->value;
									break;
							}


						}

						//Finished parsing the Title!!!
					}else if ($reader->nodeType == XMLReader::END_ELEMENT
						&& $reader->localName == "ReportItems") {

						foreach($identifierArray as $key => $value){
							if (!(strrpos($key,'PRINT') === false) && !(strrpos($key,'ISSN') === false)){
								$reportArray['issn'] = $value;
							}else if (!(strrpos($key,'ONLINE') === false) && !(strrpos($key,'ISSN') === false)){
								$reportArray['eissn'] = $value;
							}else if (!(strpos($key,'PRINT') === false) && !(strpos($key,'ISBN') === false)){
								$reportArray['isbn'] = $value;
							}else if (!(strpos($key,'ONLINE') === false) && !(strpos($key,'ISBN') === false)){
								$reportArray['eisbn'] = $value;
							}else if (!(strpos($key,'DOI') === false)){
								$reportArray['doi'] = $value;
							}else if (!(strpos($key,'PROPRIETARY') === false)){
								$reportArray['pi']=$value;
							}

						}

						//get the last array into the totals array
						$totalCountsArray[$m] = $countArray;

						//now figure out the months and the ytd, etc totals
						foreach ($totalCountsArray as $key => $countArray){

							if ($key != ''){

								if (intval($countArray['ytd']) == "0"){
									$reportArray[$key] = intval($countArray['pdf']) + intval($countArray['html']);
								}else{
									$reportArray[$key] = intval($countArray['ytd']);
								}

								if ($reportArray['ytd']===null)
									$reportArray['ytd'] = intval($countArray['ytd']);
								else
									$reportArray['ytd'] += intval($countArray['ytd']);

								if ($reportArray['ytdPDF']===null)
									$reportArray['ytdPDF'] = intval($countArray['pdf']);
								else
									$reportArray['ytdPDF'] += intval($countArray['pdf']);

								if ($reportArray['ytdHTML']===null)
									$reportArray['ytdHTML'] = intval($countArray['html']);
								else
									$reportArray['ytdHTML'] += intval($countArray['html']);
							}

						}


						//Now look at the report's layoutcode's columns to order them properly
						$finalArray=array();
						foreach($layoutColumns as $colName){
							if (isset($reportArray[$colName]))
								$finalArray[] = $reportArray[$colName];
							else
								$finalArray[] = null;
						}

						$txtOut .= implode($finalArray,"\t") . "\n";

						$totalCountsArray=array();
						break;
					}
				}
			}

		}

		$reader->close();

		if (($layoutKey == "") || (count($layoutColumns) == '0') || ($txtOut == "")){
			if (file_exists($xmlFileName)) {
				$this->logStatus("Failed XML parsing or no data was found.");

				$xml = simplexml_load_file($xmlFileName);
				$this->log("The following is the XML response:");

				$this->log(htmlentities(file_get_contents($xmlFileName)));

			}else{
				$this->log("Failed loading XML file.  Please verify you have write permissions on /sushistore/ directory.");
			}

			$this->saveLogAndExit($layoutCode);
		}

		#Save final text delimited "file" and log output on server
		$txtFile =  strtotime("now") . '.txt';
		$fp = fopen(BASE_DIR . 'archive/' . $txtFile, 'w');
		fwrite($fp, $txtOut);
		fclose($fp);


		$this->log("");
		$this->log("-- Sushi XML parsing completed --");

		$this->log("Archive/Text File Name: " . Utility::getPageURL() . 'archive/' . $txtFile);

		$this->saveLogAndExit($layoutCode, $txtFile, true);
	}

}


//for soap headers
class clsWSSEAuth{
	private $username;
	private $password;
	function __construct($username, $password){
		$this->username=$username;
		$this->password=$password;
	}
}
class clsWSSEToken{
	private $usernameToken;
	function __construct ($innerVal){
		$this->usernameToken = $innerVal;
	}
}



?>