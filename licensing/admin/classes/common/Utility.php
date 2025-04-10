<?php
/*
**************************************************************************************************************************
** CORAL Organizations Module v. 1.0
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

	//returns file path for this module, i.e. /coral/licensing/
	public function getModulePath(){
	  $replace_path = preg_quote(DIRECTORY_SEPARATOR."admin".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."common");
	  return preg_replace("@$replace_path$@", "", dirname(__FILE__));
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

	//returns page URL up to /licensing/
	public function getPageURL(){
		return $this->getCORALURL() . "licensing/";
	}

	public function getOrganizationURL(){
		return $this->getCORALURL() . "organizations/orgDetail.php?organizationID=";
	}

	public function getResourceURL(){
		return $this->getCORALURL() . "resources/resource.php?resourceID=";
	}




	//this is a workaround for a bug between autocomplete and thickbox causing a page refresh on the add/edit license form when 'enter' key is hit on the autocomplete provider field
	//this will redirect back to the correct license record
	public function fixLicenseFormEnter($editLicenseID){
		//this was an add
		if ($editLicenseID == ""){
			//need to get the most recent added license since it will have been added but we didn''t get the resonse of the new license ID
			//since this will have happened instantly we can be safe to assume this is the correct record
			$this->db = DBService::getInstance();

			$result = $this->db->processQuery("select max(licenseID) max_licenseID from License;", 'assoc');

			if ($result['max_licenseID']){
				header('Location: license.php?licenseID=' . $result['max_licenseID']);
				exit; //PREVENT SECURITY HOLE
			}

		}else{
			header('Location: license.php?licenseID=' . $editLicenseID);
			exit; //PREVENT SECURITY HOLE
		}
	}


	//return true if there is a setting in config to use the terms tool
	//setting could be called either useSFXTermsToolFunctionality or useTermsToolFunctionality
	public function useTermsTool(){
		$config = new Configuration();

		if (($config->settings->useSFXTermsToolFunctionality == "Y") || ($config->settings->useTermsToolFunctionality == "Y")){
			return true;
		}else{
			return false;
		}
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
