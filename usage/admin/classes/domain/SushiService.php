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

class SushiService extends DatabaseObject
{

  protected function defineRelationships()
  {
  }

  protected function overridePrimaryKeyName()
  {
  }

  public $startDate;
  public $endDate;
  private $statusLog = array();
  private $detailLog = array();

  public function getByPlatformID($platformID)
  {
    if (isset($platformID)) {
      $query = "SELECT * FROM `$this->tableName` WHERE `platformID` = '$platformID'";
      $result = $this->db->processQuery($query, 'assoc');

      foreach (array_keys($result) as $attributeName) {
        $this->addAttribute($attributeName);
        $this->attributes[$attributeName] = $result[$attributeName];
      }
    }
  }

  public function getByPublisherPlatformID($publisherPlatformID)
  {
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
  public function getByDayOfMonth($serviceDayOfMonth)
  {
    //now formulate query
    $query = "SELECT * FROM SushiService WHERE `serviceDayOfMonth` = '$serviceDayOfMonth';";

    $result = $this->db->processQuery(stripslashes($query), 'assoc');

    $objects = array();

    //need to do this since it could be that there's only one request and this is how the dbservice returns result
    if (isset($result['sushiServiceID'])) {
      $object = new SushiService(new NamedArguments(array('primaryKey' => $result['sushiServiceID'])));
      array_push($objects, $object);
    } else {
      foreach ($result as $row) {
        $object = new SushiService(new NamedArguments(array('primaryKey' => $row['sushiServiceID'])));
        array_push($objects, $object);
      }
    }

    return $objects;
  }


  public function getPublisherOrPlatform()
  {
    if (($this->platformID != "") && ($this->platformID > 0)) {
      return new Platform(new NamedArguments(array('primaryKey' => $this->platformID)));
    } else {
      return new PublisherPlatform(new NamedArguments(array('primaryKey' => $this->publisherPlatformID)));
    }
  }

  public function getServiceProvider()
  {
    return str_replace('"', '', $this->getPublisherOrPlatform->reportDisplayName);
  }


  public function failedImports()
  {
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
    if (isset($result['platformID'])) {

      foreach (array_keys($result) as $attributeName) {
        $resultArray[$attributeName] = $result[$attributeName];
      }

      array_push($importArray, $resultArray);
    } else {
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

  public function allServices()
  {
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
    if (isset($result['platformID'])) {

      foreach (array_keys($result) as $attributeName) {
        $resultArray[$attributeName] = $result[$attributeName];
      }

      array_push($importArray, $resultArray);
    } else {
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

  /*********************************************************************************************************************
   * Runners
   */
  //run through ajax function on publisherplatform
  public function runTest()
  {
    $reportLayouts = $this->reportLayouts;
    $rlArray = explode(";", $reportLayouts);

    //just default test import dates to just be january 1 - 31 of this year
    $sDate = date_format(date_create_from_format("Ymd", date("Y") . "0101"), "Y-m-d");
    $eDate = date_format(date_create_from_format("Ymd", date("Y") . "0131"), "Y-m-d");
    $this->setImportDates($sDate, $eDate);

    $serviceProvider = $this->getServiceProvider();
    $errors = [];
    foreach ($rlArray as $reportLayout) {
      try {
        $this->sushiTransfer($reportLayout, $serviceProvider);
      } catch(Exception $e) {
        $errors[$reportLayout] = $e->getMessage();
      }
    }

    if ($reportLayouts == "") {
      echo _("At least one report type must be set up!");
    } elseif (!empty($errors)) {
      foreach($errors as $rl => $er) {
        echo '<h3> Report '.$rl.'</h3>';
        echo '<p>'.$er.'</p>';
      }
    } else {
      echo _("Connection test successful!");
    }

  }

  //run through post or through sushi scheduler
  public function runAll($overwritePlatform = TRUE)
  {
    $reportLayouts = $this->reportLayouts;

    if ($reportLayouts == "") {
      return _("No report types are set up!");
    }

    $detailsForOutput = array();

    $rlArray = explode(";", $reportLayouts);
    $serviceProvider = $this->getServiceProvider();
    foreach ($rlArray as $reportLayout) {
      try {
        $detailsForOutput[] = implode("\n", $this->run($reportLayout, $serviceProvider, $overwritePlatform));
        sleep(3);
      } catch(Exception $e) {
        $detailsForOutput[] = $e->getMessage();
      }
    }

    return implode("\r\n", $detailsForOutput);
  }

  public function run($reportLayout, $serviceProvider, $overwritePlatform) {
    $this->statusLog = array();
    $this->detailLog = array();

    // get sushi report
    $response = $this->sushiTransfer($reportLayout, $serviceProvider);
    $ext = $this->releaseNumber > 4 ? '.json' : '.xml';

    // Save the response
    $filename = $serviceProvider . '_' . $reportLayout . '_' . $this->startDate . '_' . $this->endDate;
    $replace = "_";
    $pattern = "/([[:alnum:]_\.-]*)/";
    $file = BASE_DIR . 'counterstore/' . str_replace(str_split(preg_replace($pattern, $replace, $filename)), $replace, $filename) . $ext;

    file_put_contents($file, $response);

    //Test that response was saved
    if (!file_get_contents($file)) {
      $this->logStatus("Failed trying to open file: " . $file . ".  This could be due to not having write access to the /counterstore/ directory.");
      $this->saveLogAndExit($reportLayout);
    }

    // parse the report
    if ($this->releaseNumber < 5) {
      $report = $this->parseXML($file, $reportLayout, $serviceProvider, $serviceProvider);
    } else {
      $report = $this->parseJson($file, $reportLayout, $overwritePlatform, $serviceProvider);
    }
    $this->log("Finished parsing " . $this->getServiceProvider . ": $reportLayout.");

    // validate and save the report
    $txtOut = '';
    //determine the format of the report to port to csv from the layouts.ini file
    $layoutCode = $reportLayout . '_R' . $this->releaseNumber;
    $layoutsArray = parse_ini_file(BASE_DIR . "layouts.ini", true);
    $layoutKey = $layoutsArray['ReportTypes'][$layoutCode];
    $layoutColumns = $layoutsArray[$layoutKey]['columns'];

    if (count($layoutColumns) == 0 || $layoutCode == ''){
      $this->logStatus("Failed determining layout:  Reached report items before establishing layout.  Please make sure this layout is set up in layouts.ini");
      $this->saveLogAndExit($reportLayout);
    }

    $monthColumns = array_map(function($m) {
      return $m['columnName'];
    },$report['header']['months']);
    $header = array_merge($layoutColumns,$monthColumns);

    $txtOut .= implode($header, "\t") . "\n";
    $this->log("Layout validated successfully against layouts.ini : " . $layoutCode);


    // Add rows
    foreach($report['rows'] as $row) {
      $finalArray = array();
      // Use the report's layoutcode's columns to order them properly
      foreach($layoutColumns as $colName){
        $finalArray[] = isset($row[$colName]) ? $row[$colName] : null;
      }
      foreach($row['months'] as $m) {
        $finalArray[] = $m;
      }
      $txtOut .= implode($finalArray,"\t") . "\n";
    }

    #Save final text delimited "file" and log output on server
    $txtFile =  $filename . '_' . strtotime('now') . '.txt';
    $fp = fopen(BASE_DIR . 'archive/' . $txtFile, 'w');
    fwrite($fp, $txtOut);
    fclose($fp);

    $this->log("");
    $this->log("-- Sushi report writing completed --");
    $this->log("Archive/Text File Name: " . Utility::getPageURL() . 'archive/' . $txtFile);
    $this->saveLogAndExit($layoutCode, $txtFile, true);

    return $this->statusLog;
  }

  /*********************************************************************************************************************
   * Requestors
   */

  private function sushiTransfer($reportLayout, $serviceProvider)
  {
    if ($this->releaseNumber < 5) {
      $response = $this->xmlTransfer($reportLayout, $serviceProvider);
    } else {
      $response = $this->jsonTransfer($reportLayout, $serviceProvider);
    }
    return $response;
  }

  private function jsonTransfer($reportLayout, $serviceProvider, $retry = 0)
  {

    $startDate = date_create($this->startDate);
    $endDate = date_create($this->endDate);
    // End Date is earlier than start date
    if ($this->startDate > $this->endDate) {
      $this->logStatus("Invalid Dates entered. Must enter a start before end date.");
      $this->saveLogAndExit($reportLayout);
    }

    // setup params
    $params = array(
      'begin_date' => date_format($startDate, 'Y-m'),
      'end_date' => date_format($endDate, 'Y-m'),
      'customer_id' => $this->customerID
    );

    if ($this->requestorID) {
      $params['requestor_id'] = $this->requestorID;
    }

    if ($this->apiKey) {
      $params['api_key'] = $this->apiKey;
    }

    // If a master report, need to add 'attributes_to_show'
    if (in_array($reportLayout,['PR','DR','TR','IR'])) {
      $params['attributes_to_show'] = 'Data_Type|Access_Method';
      if ($reportLayout == 'TR') {
        $params['attributes_to_show'] .= '|Access_Type|Section_Type|YOP';
      }
      if ($reportLayout == 'IR') {
        $params['attributes_to_show'] .= '|Access_Type|YOP|Publication_Date|Authors|Article_Version';
      }
    }

    // If an IR or IR_A1 report get the parent and component details
    if (in_array($reportLayout, ['IR', 'IR_A1'])) {
      $params['include_component_details'] = true;
      $params['include_parent_details'] = true;
    }

    // setup curl client
    $trailingSlash = substr($this->serviceURL, -1) == '/' ? '' : '/';
    $endpoint = $this->serviceURL . $trailingSlash . 'reports/' . strtolower($reportLayout) . '?' . http_build_query($params);
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    if (preg_match("/http/i", $this->security)) {
      curl_setopt($ch, CURLOPT_USERPWD, $this->login . ":" . $this->password);
    }
    curl_setopt($ch, CURLOPT_TIMEOUT, 600);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'CoralSushiService');
    $this->log("Connecting to $this->serviceURL");

    // try executing curl
    try {
      $response = curl_exec($ch);
    } catch (Exception $e) {
      $error = $e->getMessage();
      $this->logStatus("Exception performing curl request with connection to $serviceProvider: $error");
      $this->saveLogAndExit($reportLayout);
    }

    // check for curl errors
    if (curl_errno($ch)) {
      $this->logStatus("Request Error with connection to $serviceProvider:" . curl_error($ch));
      $this->saveLogAndExit($reportLayout);
    }
    curl_close($ch);


    // Check for errors
    try {
      $json = json_decode($response);
    } catch (Exception $e) {
      $error = $e->getMessage();
      $this->logStatus("There was an error trying to parse the SUSHI report from $serviceProvider. This could be due to a malformed response from the sushi service. Error: $error");
      $this->saveLogAndExit($reportLayout);
    }

    // if the json is empty
    if (empty($json)) {
      $this->logStatus("No data was returned from $serviceProvider using the endpoint: $endpoint");
      $this->saveLogAndExit($reportLayout);
    }

    // If there is an error, the model will have Code and Message in the top level
    if (!empty($json->Code) && !(empty($json->Message))) {
      $this->logStatus("Received an error from $serviceProvider: $json->Message");
      $this->saveLogAndExit($reportLayout);
    }

    // Also check for report exceptions
    if (!empty($json->Report_Header->Exceptions)) {

      //https://www.projectcounter.org/appendix-f-handling-errors-exceptions/
      $reportFatalCodes = array(1000, 1010, 1020, 1030);
      $reportErrorCodes = array(2000, 2010, 2020, 3000, 3010, 3020, 3030);
      $errorsOrFatalCount = 0;
      foreach($json->Report_Header->Exceptions as $exception) {

        // 1011 means the report was queued, in this case, try again after 1 second
        // after three retries, exit
        if ($exception->Code == 1011) {
          if ($retry == 3) {
            $this->logStatus("Report was queued for processing, but was not processable after 3 retries.");
            $this->saveLogAndExit($reportLayout);
          }
          sleep(1);
          $this->jsonTransfer($reportLayout, $serviceProvider);
        }

        $aAn = 'a';
        if (in_array($exception->Code, $reportFatalCodes)) {
          $errorKey = 'fatal';
          $errorsOrFatalCount += 1;
        } elseif (in_array($exception->Code, $reportErrorCodes)) {
          $errorKey = 'error';
          $aAn = 'an';
          $errorsOrFatalCount += 1;
        } else {
          $errorKey = 'warning';
        }
        $message = "Received $aAn $errorKey message from $serviceProvider: $exception->Message";
        if (!empty($exception->Data)) {
          $message .= " Additional Data: $exception->Data";
        }
        if (!empty($exception->Help_URL)) {
          $message .= " Help Url: $exception->Help_URL";
        }
        $this->log($message);
      }
      if ($errorsOrFatalCount > 0) {
        $this->logStatus($this->logStatus("Received $errorsOrFatalCount errors from $serviceProvider. Details can be found in the log."));
        $this->saveLogAndExit($reportLayout);
      }

    }

    $this->log("$reportLayout successfully retrieved from $serviceProvider for start date:  $this->startDate, end date: $this->endDate");
    $this->log("");
    $this->log("-- Sushi Transfer completed --");

    return $response;
  }

  private function soapConnection($wsdl, $parameters)
  {

    $parameters = array_merge($parameters, array(
        "keep_alive" => true,
        "connection_timeout" => 1000,
        "trace" => 1,
        "exceptions" => 1,
        "cache_wsdl" => WSDL_CACHE_NONE,
        "stream_context" => stream_context_create(array(
          'http' => array('protocol_version' => 1.0,
            'header' => 'Content-Type: application/soap+xml')))
      )
    );

    try {
      try {
        $client = new SoapClient($wsdl, $parameters);

        //returns soapfault
      } catch (Exception $e) {
        $error = $e->__toString();

        //if soap fault returned version mismatch or http headers error, try again with soap 1.2
        if ((preg_match('/Version/i', $error)) || (preg_match('/HTTP/i', $error))) {

          $this->log("Using Soap Version 1.2");
          $parameters = array_merge($parameters, array("soap_version" => SOAP_1_2));

          //try connection again with 1.2
          $client = new SoapClient($wsdl, $parameters);
        }
      }

      //throws soap fault
    } catch (Exception $e) {
      $error = $e->getMessage();

      $this->logStatus("Failed to establish soap connection: " . $error);
      $this->saveLogAndExit();
    }

    $this->log("");
    $this->log("-- Soap Connection successfully completed --");
    $this->log("");

    return $client;
  }


  private function xmlTransfer($reportLayout, $serviceProvider)
  {

    //if report layout is BR and Release is 3, change it to 1
    if ((preg_match('/BR/i', $reportLayout)) && ($this->releaseNumber == "3")) {
      $releaseNumber = '1';
    } else {
      $releaseNumber = $this->releaseNumber;
    }

    $createDate = date("Y-m-d\TH:i:s.0\Z");
    $id = uniqid("CORAL:", true);

    if (($this->wsdlURL == '') || (strtoupper($this->wsdlURL) == 'COUNTER')) {
      if ($this->releaseNumber == "4") {
        $wsdl = 'http://www.niso.org/schemas/sushi/counter_sushi4_0.wsdl';
      } else {
        $wsdl = 'http://www.niso.org/schemas/sushi/counter_sushi3_0.wsdl';
      }
    } else {
      $wsdl = $this->wsdlURL;
    }

    // look at $Security to ses if it uses an extension
    if (preg_match('/Extension=/i', $this->security)) {
      $extensions = array();
      $varlist = explode(";", $this->security);
      foreach ($varlist as $params) {
        list($extVar, $extVal) = explode("=", $params);
        $extensions[$extVar] = $extVal;
        if ($extVar == 'Extension') {
          $extension = $extVal;
        }
      }
    }

    if (!empty($extension)) {
      include BASE_DIR . 'sushiincludes/extension_' . $extension . '.inc.php';
    } else {
      if (preg_match("/http/i", $this->security)) {
        $this->log("Using HTTP Basic authentication via login and password.");

        $parameters = array(
          'login' => $this->login,
          'password' => $this->password,
          'location' => $this->serviceURL,
        );
      } else {
        if ((strtoupper($this->wsdlURL) != 'COUNTER') && ($this->wsdlURL != '')) {
          $this->log("Using provided wsdl: $wsdl");
          $parameters = array();

        } else {
          $this->log("Using COUNTER wsdl, connecting to $this->serviceURL");
          $parameters = array('location' => $this->serviceURL);
        }
      }

      $client = $this->soapConnection($wsdl, $parameters);
    }

    if (preg_match("/wsse/i", $this->security)) {
      // Prepare SoapHeader parameters
      $strWSSENS = "http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd";
      $objSoapVarUser = new SoapVar($this->login, XSD_STRING, NULL, $strWSSENS, NULL, $strWSSENS);
      $objSoapVarPass = new SoapVar($this->password, XSD_STRING, NULL, $strWSSENS, NULL, $strWSSENS);
      $objWSSEAuth = new clsWSSEAuth($objSoapVarUser, $objSoapVarPass);
      $objSoapVarWSSEAuth = new SoapVar($objWSSEAuth, SOAP_ENC_OBJECT, NULL, $strWSSENS, 'UsernameToken', $strWSSENS);
      $objWSSEToken = new clsWSSEToken($objSoapVarWSSEAuth);
      $objSoapVarWSSEToken = new SoapVar($objWSSEToken, SOAP_ENC_OBJECT, NULL, $strWSSENS, 'UsernameToken', $strWSSENS);
      $objSoapVarHeaderVal = new SoapVar($objSoapVarWSSEToken, SOAP_ENC_OBJECT, NULL, $strWSSENS, 'Security', $strWSSENS);
      $objSoapVarWSSEHeader = new SoapHeader($strWSSENS, 'Security', $objSoapVarHeaderVal, false);

      // Prepare Soap Client
      try {
        $client->__setSoapHeaders(array($objSoapVarWSSEHeader));
      } catch (Exception $e) {
        $error = $e->getMessage();
        $this->logStatus("Failed to connect to $serviceProvider: " . $error);
        $this->log("Tried: " . var_dump($client));
        $this->saveLogAndExit($reportLayout);
      }

    }
    $startDate = new DateTime($this->startDate);
    $startDate->modify('first day of this month');
    $endDate = new DateTime($this->endDate);
    $endDate->modify('last day of this month');
    // End Date is earlier than start date
    if ($startDate > $endDate) {
      $this->logStatus("Invalid Dates entered. Must enter a start before end date.");
      $this->saveLogAndExit($reportLayout);
    }
    try {
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
        'ReportDefinition' => array
        ('Filters' => array
          ('UsageDateRange' => array
            ('Begin' => $startDate->format('Y-m-d'),
              'End' => $endDate->format('Y-m-d')
            )
          ),
          'Name' => $reportLayout,
          'Release' => $releaseNumber
        ),
        'Created' => $createDate,
        'ID' => $id,
        'connection_timeout' => 1000
      );
      $dateError = FALSE;

      $result = $client->GetReport($reportRequest);
    } catch (Exception $e) {
      $error = $e->getMessage();

      $this->logStatus("Exception performing GetReport with connection to $serviceProvider: $error");

      //exceptions seem to happen that don't matter, continue processing and if no data or error is found then it will quit.
      //$this->saveLogAndExit($reportLayout);
    }
    $xml = $client->__getLastResponse();
    // Check for errors
    try {
      $reader = XMLReader::xml($xml);
    } catch (Exception $e) {
      $error = $e->getMessage();
      $this->logStatus("There was an error trying to parse the SUSHI report from $serviceProvider. This could be due to a malformed response from the sushi service. Error: $error");
      $this->saveLogAndExit($reportLayout);
    }

    $message = "";
    while ($reader->read()) {
      if ($reader->nodeType == XMLReader::ELEMENT) {
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

    if ($message != "") {
      if (($severity == "Error") || (stripos($message, "Error") !== FALSE)) {
        $this->logStatus("Failed to request report from $serviceProvider: " . $message);

        $this->log("Please fix the settings for this provider and try again.");
        $this->saveLogAndExit($reportLayout);
      } else {
        $this->logStatus("$serviceProvider says: $severity: $message");
      }
    }

    return $xml;
  }

  /*********************************************************************************************************************
   * Parsers
   */

  private function parseXML($fName, $reportLayout, $overwritePlatform, $serviceProvider)
  {
    //////////////////////////////////////
    //PARSE XML!!
    //////////////////////////////////////

    // Setup report months
    $reportMonths = $this->reportMonths($this->startDate, $this->endDate);

    //read layouts ini file to get the available layouts

    $metrics = parse_ini_file(BASE_DIR . "metrics.ini");

    $string = file_get_contents($fName);
    // Gets rid of all namespace references
    $clean_xml = $this->stripNamespaces($string);
    $xml = simplexml_load_string($clean_xml);

    $report = array(
      'header' => array(),
      'rows' => array(),
    );

    //First - get report information
    $data = $xml->Body->ReportResponse->Report->Report;
    $report['header']['id'] = $data->attributes()->Name;
    $report['header']['release'] = $data->attributes()->Version;
    $report['header']['months'] = $reportMonths;

    foreach ($data->Customer->ReportItems as $resource) {

      //reset variables
      /**
       * Each $reportArray is slightly different
       * JR1: Need aggregated count columns of ytd, ytdPDF, ytdHTML
       * BR1: Need aggregated count columns of ytd
       * DB1: Need separate rows based on activity type, but aggregated counts for those activity types
       */
      $row = array();

      if ($overwritePlatform) {
        $row['platform'] = $serviceProvider;
      } else {
        $row['platform'] = $resource->ItemPlatform[0]->__toString();
      }
      $row['publisher'] = $resource->ItemPublisher->__toString();
      $row['title'] = $resource->ItemName->__toString();
      foreach ($resource->ItemIdentifier as $identifier) {
        $idType = strtoupper($identifier->Type);
        if (!(strrpos($idType, 'PRINT') === false) && !(strrpos($idType, 'ISSN') === false)) {
          $row['issn'] = $identifier->Value->__toString();
        } else if (!(strrpos($idType, 'ONLINE') === false) && !(strrpos($idType, 'ISSN') === false)) {
          $row['eissn'] = $identifier->Value->__toString();
        } else if (!(strpos($idType, 'PRINT') === false) && !(strpos($idType, 'ISBN') === false)) {
          $row['isbn'] = $identifier->Value->__toString();
        } else if (!(strpos($idType, 'ONLINE') === false) && !(strpos($idType, 'ISBN') === false)) {
          $row['eisbn'] = $identifier->Value->__toString();
        } else if (!(strpos($idType, 'DOI') === false)) {
          $row['doi'] = $identifier->Value->__toString();
        } else if (!(strpos($idType, 'PROPRIETARY') === false)) {
          $row['pi'] = $identifier->Value->__toString();
        }
      }

      // Get all possible metric types for the resource
      $metricTypes = array();
      foreach ($resource->ItemPerformance as $monthlyStat) {
        foreach ($monthlyStat->Instance as $metricStat) {
          $type = $metricStat->MetricType->__toString();
          if (!in_array($type, $metricTypes)) {
            $metricTypes[] = $type;
          }
        }
      }

      $stashedRow = $row;

      foreach ($metricTypes as $type) {
        $metricRow = $stashedRow;
        $metricRow['activityType'] = empty($metrics[$type]) ? $type : $metrics[$type];
        $metricRow['ytd'] = 0;
        $metricRow['months'] = array();
        foreach ($reportMonths as $month) {
          $metricRow['months'][$month['columnName']] = 0;
          foreach ($resource->ItemPerformance as $monthlyStat) {
            if ($month['start'] == $monthlyStat->Period->Begin) {
              foreach ($monthlyStat->Instance as $metricStat) {
                if ($metricStat->MetricType->__toString() == $type) {
                  $metricRow['months'][$month['columnName']] = intval($metricStat->Count);
                }
              }
            }
          }
        }
        $metricRow['ytd'] = array_reduce($metricRow['months'], function ($carry, $item) {
          $carry += $item;
          return $carry;
        });
        $report['rows'][] = $metricRow;
      }
    }

    return $report;
  }

  private function parseJson($fName, $reportLayout, $overwritePlatform, $serviceProvider)
  {

    // Setup report months
    $reportMonths = $this->reportMonths($this->startDate, $this->endDate);

    $string = file_get_contents($fName);
    $data = json_decode($string, true);

    $report = array(
      'header' => array(),
      'rows' => array(),
    );

    $report['header']['id'] = $data['Report_Header']['Report_ID'];
    $report['header']['release'] = $data['Report_Header']['Release'];
    $report['header']['months'] = $reportMonths;

    foreach ($data['Report_Items'] as $resource) {

      $row = array();

      // platform
      if ($overwritePlatform) {
        $row['platform'] = $serviceProvider;
      } else {
        $row['platform'] = $resource['Platform'];
      }

      // Platform reports need to have the platform as the title
      if (preg_match("/pr/i", $reportLayout)) {
        $row['title'] = $row['platform'];
      }

      // Publisher ID
      if (is_array($resource['Publisher_ID']) && count($resource['Publisher_ID'] > 0)) {
        $row['publisherID'] = strtolower($resource['Publisher_ID'][0]['Type']) . '=' . $resource['Publisher_ID'][0]['Value'];
      }

      // all string values
      foreach (array_keys($resource) as $key) {
        if (is_array($resource[$key]) || is_object($resource[$key]) || $key == 'Platform') {
          continue;
        }
        $row[$this->r5Attr($key)] = $resource[$key];
      }

      // contributors (for Item reports)
      if (isset($resource['Item_Contributors']) && is_array($resource['Item_Contributors'])) {
        $authorsArray = array();
        foreach($resource['Item_Contributors'] as $author) {
          $string = $author['Name'];
          if ($author['Identifier']) {
            $string .= '(' . $author['Identifier'] . ')';
          }
          $authorsArray[] = $string;
        }
        $row['authors'] = implode(';', $authorsArray);
      }

      // publication date (for Item reports)
      if (isset($resource['Item_Dates']) && is_array($resource['Item_Dates'])) {
        foreach($resource['Item_Dates'] as $date) {
          if($date['Type'] == 'Publication_Date') {
            $row['publicationDate'] = $date['Value'];
          }
        }
      }

      // article version (for Item reports)
      if (isset($resource['Item_Attributes']) && is_array($resource['Item_Attributes'])) {
        foreach($resource['Item_Attributes'] as $attr) {
          if($attr['Type'] == 'Article_Version') {
            $row['articleVersion'] = $attr['Value'];
          }
        }
      }

      // parent (for Item reports)
      if (isset($resource['Item_Parent'])) {
        $parent = is_array($resource['Item_Parent']) ? $resource['Item_Parent'][0] : $resource['Item_Parent'];
        $row['parentTitle'] = $parent['Item_Name'];
        $row['parentDateType'] = isset($parent['Data_Type']) ? $parent['Data_Type'] : '';
        if (isset($parent['Item_ID']) && is_array($parent['Item_ID'])) {
          foreach ($parent['Item_ID'] as $id) {
            $row[$this->r5Attr('Parent_'.$id['Type'])] = $id['Value'];
          }
        }
      }

      // component (for Item reports)
      if (isset($resource['Item_Component'])) {
        $parent = is_array($resource['Item_Component']) ? $resource['Item_Component'][0] : $resource['Item_Component'];
        $row['componentTitle'] = $parent['ItemName'];
        $row['componentDateType'] = isset($parent['Data_Type']) ? $parent['Data_Type'] : '';
        if (isset($parent['Item_ID']) && is_array($parent['Item_ID'])) {
          foreach ($parent['Item_ID'] as $id) {
            $row[$this->r5Attr('Component_'.$id['Type'])] = $id['Value'];
          }
        }
      }


      // identifiers
      foreach ($resource['Item_ID'] as $id) {
        $row[$this->r5Attr($id['Type'])] = $id['Value'];
      }

      // Get all possible metric types for the resource
      $metricTypes = array();
      foreach ($resource['Performance'] as $monthlyStat) {
        foreach ($monthlyStat['Instance'] as $metricStat) {
          $type = $metricStat['Metric_Type'];
          if (!in_array($type, $metricTypes)) {
            $metricTypes[] = $type;
          }
        }
      }
      $stashedRow = $row;

      foreach ($metricTypes as $type) {
        $metricRow = $stashedRow;
        $metricRow['activityType'] = $type;
        $metricRow['months'] = array();
        foreach ($reportMonths as $month) {
          $metricRow['months'][$month['columnName']] = 0;
          foreach ($resource['Performance'] as $monthlyStat) {
            if ($month['start'] == $monthlyStat['Period']['Begin_Date']) {
              foreach ($monthlyStat['Instance'] as $metricStat) {
                if ($metricStat['Metric_Type'] == $type) {
                  $metricRow['months'][$month['columnName']] = intval($metricStat['Count']);
                }
              }
            }
          }
        }
        $metricRow['ytd'] = array_reduce($metricRow['months'], function ($carry, $item) {
          $carry += $item;
          return $carry;
        });
        if ($metricRow['ytd'] > 0) {
          $report['rows'][] = $metricRow;
        }
      }
    }
    return $report;
  }

  /*********************************************************************************************************************
   * Loggers
   */

//status for storing in DB and displaying in rows
  private function logStatus($logText)
  {
    array_push($this->statusLog, $logText);
    array_push($this->detailLog, $logText);
  }

  //longer log for storing in log file and displaying output
  private function log($logText)
  {
    array_push($this->detailLog, $logText);
  }

  //logs process to import log table and to log file
  public function saveLogAndExit($reportLayout = NULL, $filename = NULL, $success = FALSE)
  {


    //First, delete any preexisting Failured records, these shouldn't be needed/interesting after this.
    $this->log("Cleaning up prior failed import logs....");

    $this->getPublisherOrPlatform->removeFailedSushiImports;

    if (!$filename) {
      $logFilename = strtotime("now") . '.txt';
    } else {
      $logFilename = $filename;
    }
    $logFileLocation = 'logs/' . $logFilename;

    $this->log("Log File Name: $logFileLocation");

    if ($success) {
      $this->logStatus("Finished processing " . $this->getServiceProvider . ": $reportLayout.");
    }

    //save the actual log file
    $fp = fopen(BASE_DIR . $logFileLocation, 'w');
    fwrite($fp, implode("\n", $this->detailLog));
    fclose($fp);


    //save to import log!!
    $importLog = new ImportLog();
    $importLog->loginID = "sushi";
    $importLog->importDateTime = date('Y-m-d H:i:s');
    $importLog->layoutCode = $reportLayout;
    $importLog->fileName = 'archive/' . $filename;
    $importLog->archiveFileURL = 'archive/' . $filename;
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

    if (!$success) {
      throw new Exception(implode("\n", $this->detailLog));
    }

  }

  /*********************************************************************************************************************
   * Utility
   */

  public function setDefaultImportDates()
  {

    // Determine the End Date
    //start with first day of this month
    $endDate = date_create_from_format("Ymd", date("Y") . date("m") . "01");

    //subtract one day
    date_sub($endDate, date_interval_create_from_date_string('1 days'));
    $this->endDate = date_format($endDate, "Y-m-d");

    //Determine the Start Date
    //first, get this publisher/platform's last day of import
    $lastImportDate = $this->getPublisherOrPlatform->getLastImportDate();
    $lastImportDate = date_create_from_format("Y-m-d", $lastImportDate);
    date_add($lastImportDate, date_interval_create_from_date_string('1 month'));

    //if that date is set and it's sooner than the first of this year, default it to that date
    if (($lastImportDate) && (date_format($lastImportDate, "Y-m-d") > date_format($endDate, "Y") . "-01-01")) {
      $this->startDate = date_format($lastImportDate, "Y-m-d");
    } else {
      $this->startDate = date_format($endDate, "Y") . "-01-01";
    }

  }

  public function setImportDates($sDate = null, $eDate = null)
  {

    if (!$sDate) {
      $this->setDefaultImportDates();
    } else {
      //using the multiple functions in order to make sure leading zeros, and this is a date
      $this->startDate = date_format(date_create_from_format("Y-m-d", $sDate), "Y-m-d");
      $this->endDate = date_format(date_create_from_format("Y-m-d", $eDate), "Y-m-d");
    }

  }

  private function stripNamespaces($string)
  {
    $sxe = new SimpleXMLElement($string);
    $doc = new DOMDocument();
    $doc->loadXML($string);
    foreach ($sxe->getNamespaces(true) as $name => $uri) {
      if (!empty($name)) {
        $finder = new DOMXPath($doc);
        $nodes = $finder->query("//*[namespace::{$name} and not(../namespace::{$name})]");
        foreach ($nodes as $n) {
          $ns_uri = $n->lookupNamespaceURI($name);
          $n->removeAttributeNS($ns_uri, $name);
        }
      }
    }
    return $doc->saveXML(null, LIBXML_NOEMPTYTAG);
  }

  public function reportMonths($startDate, $endDate) {
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);

    $start->modify('first day of this month');
    $end->modify('last day of this month');
    $months = array();
    while($start < $end) {
      $month = array();
      $month['start'] = $start->format('Y-m-d');
      $start->modify('last day of this month');
      $month['end'] = $start->format('Y-m-d');
      $month['columnName'] = strtolower($start->format('M-Y'));

      $months[] = $month;
      $start->modify('last day of this month')->add(new DateInterval('P1D'));
    }

    return $months;
  }

  public function r5Attr($key) {
    $map = array(
      'Database' => 'title',
      'Item' => 'title',
      'Data_Type' => 'dataType',
      'Access_Method' => 'accessMethod',
      'Metric_Type' => 'activityType',
      'Reporting_Period_Total' => 'ytd',
      'DOI' => 'doi',
      'Proprietary_ID' => 'pi',
      'Proprietary' => 'pi',
      'ISBN' => 'isbn',
      'Print_ISSN' => 'issn',
      'Online_ISSN' => 'eissn',
      'URI' => 'uri',
      'Section_Type' => 'sectionType',
      'Access_Type' => 'accessType',
      'Publication_Date' => 'publicationDate',
      'Article_Version' => 'articleVersion',
      'Parent_Title' => 'parentTitle',
      'Parent_Data_Type' => 'parentDataType',
      'Parent_DOI' => 'parentDoi',
      'Parent_Proprietary_ID' => 'parentPi',
      'Parent_Proprietary' => 'parentPi',
      'Parent_ISBN' => 'parentIsbn',
      'Parent_Print_ISSN' => 'parentIssn',
      'Parent_Online_ISSN' => 'parentEissn',
      'Parent_URI' => 'parentURI',
      'Component_Title' => 'componentTitle',
      'Component_Data_Type' => 'componentDataType',
      'Component_DOI' => 'componentDoi',
      'Component_Property_ID' => 'componentPi',
      'Component_ISBN' => 'componentIsbn',
      'Component_Print_ISSN' => 'componentIssn',
      'Component_Online_ISSN' => 'componentEissn',
      'Component_URI' => 'componentURI',
    );
    return isset($map[$key]) ? $map[$key] : strtolower($key);
  }

  public function processContributors($array) {

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

