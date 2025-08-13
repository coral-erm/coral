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


class Utility {

	static public function unixTimeFromMysqlTimestamp($timestamp) {

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

	static public function secondsFromDays($days) {
		return $days * 24 * 60 * 60;
	}

	static public function objectFromArray($array) {
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

	//returns file path up to /coral
	static public function getCORALPath(){
		$documentRoot = rtrim($_SERVER['DOCUMENT_ROOT'],'/\\');
		$currentFile = $_SERVER['SCRIPT_NAME'];
		$hasSlash = (substr($currentFile, 0, 1) == '/'); //Confirms whether the currentFile has a leading forward slash.
		$pathStart = ($hasSlash) ? '' : '/';
		/* There is a presumption in the code that we are always using every element of the array EXCEPT the last two parts 
		(typically things like "organizations" ; "index.php"). If there is ever a reason CORALPath is used in a deeper subdirectory 
		this may need to be modified. However, for now I'm just going to keep the "don't use the last two elements of the array" assumption.
		One place we know this runs afoul is if the function is called in the root directory of coral itself, where we should only remove the last element.
		Future improvement for future people!
		*/
		$parts = Explode('/', $currentFile);
		$pathArray = array_slice($parts, 0, count($parts)-2);
		$moduleLessPathString = implode("/", $pathArray);
		$pathway = $documentRoot.$pathStart.$moduleLessPathString;
		return $pathway;
	}

	//returns page URL up to /coral/
	static public function getCORALURL(){
		$pageURL = 'http';
		if (isset($_SERVER["HTTPS"]) and ($_SERVER["HTTPS"] == "on")) {$pageURL .= "s";}
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

	//returns page URL up to /usage/
	static public function getPageURL(){
		return self::getCORALURL() . "usage/";
	}

	static public function getLicensingURL(){
		return self::getCORALURL() . "licensing/license.php?licenseID=";
	}


	static public function getOrganizationURL(){
		return self::getCORALURL() . "organizations/orgDetail.php?organizationID=";
	}


	static public function getLoginCookie(){

		if(array_key_exists('CORALLoginID', $_COOKIE)){
			return $_COOKIE['CORALLoginID'];
		}

	}

	static public function getSessionCookie(){

		if(array_key_exists('CORALSessionID', $_COOKIE)){
			return $_COOKIE['CORALSessionID'];
		}

	}

	static public function utf8_fopen_read($fileName, $isSushiFile) {

		$handle=fopen("php://memory", "rw");

		// If we were passed a URL, just send back an empty file handle.
		if (preg_match("/^[^:]+:\/\//", $fileName)){ // starts with a URL scheme...
			return $handle;
		}

		//if the string isn't already ut8
		if ($isSushiFile){
			$fc = file_get_contents($fileName);
		}else{
			$fc = file_get_contents($fileName);
			// remove a UTF-8 BOM
			if(substr($fc,0,3)==chr(hexdec('EF')).chr(hexdec('BB')).chr(hexdec('BF'))){
				$fc = substr($fc,3);
			} else {
				$fc = iconv('windows-1250', 'utf-8', file_get_contents($fileName));
				if(empty($fc)){
					$fc = mb_convert_encoding(file_get_contents($fileName),'utf-8');
				}
			}
		}

		fwrite($handle, $fc);
		fseek($handle, 0);
		return $handle;
	}

}

?>
