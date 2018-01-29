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


class Utility {

	public function unixTimeFromMysqlTimestamp($timestamp) {

		// taken from Dan Green, and then modified to be correct
		// http://www.weberdev.com/get_example-1427.html

		$year = substr($timestamp,0,4);
		$month = substr($timestamp,5,2);
		$day = substr($timestamp,8,2);
		$hour = substr($timestamp,11,2);
		$minute = substr($timestamp,14,2);
		$second = substr($timestamp,17,2);
		$newdate = mktime($hour,$minute,$second,$month,$day,$year);

		return $newdate;

	}

	public function secondsFromDays($days) {
		return $days * 24 * 60 * 60;
	}

	public function objectFromArray($array) {
		$object = new DynamicObject;
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$object->$key = Utility::objectFromArray($value);
			} else {
				$object->$key = $value;
			}
		}
		return $object;
	}

	//returns file path up to /coral/
	public function getCORALPath(){
                $pagePath = rtrim($_SERVER['DOCUMENT_ROOT'],'/\\').'/';

		$currentFile = $_SERVER["SCRIPT_NAME"];
		$parts = Explode('/', $currentFile);
		for($i=0; $i<count($parts) - 2; $i++){
			$pagePath .= $parts[$i] . '/';
		}

		return $pagePath;
	}

	//returns page URL up to /coral/
	public function getCORALURL(){
		$pageURL = 'http';
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
		  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
		} else {
		  $pageURL .= $_SERVER["SERVER_NAME"];
		}

		$currentFile = $_SERVER["PHP_SELF"];
		$parts = Explode('/', $currentFile);
		for($i=0; $i<count($parts) - 2; $i++){
			$pageURL .= $parts[$i] . '/';
		}

		return $pageURL;
	}

	//returns page URL up to /organizations/
	public function getPageURL(){
		return $this->getCORALURL() . "organizations/";
	}

	public function getLicensingURL(){
		return $this->getCORALURL() . "licensing/license.php?licenseID=";
	}

	//returns page URL for resource record
	public function getResourceRecordURL(){
		return $this->getCORALURL() . "resources/resource.php?resourceID=";
	}

	public function getLoginCookie(){

		if(array_key_exists('CORALLoginID', $_COOKIE)){
			return $_COOKIE['CORALLoginID'];
		}

	}

	public function getSessionCookie(){

		if(array_key_exists('CORALSessionID', $_COOKIE)){
			return $_COOKIE['CORALSessionID'];
		}

	}


}

?>
