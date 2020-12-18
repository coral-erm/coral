<?php
/*
**************************************************************************************************************************
** CORAL Licensing Module Terms Tool Add-On v. 1.0
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



class SFXService extends Base_Object {


	protected $issn;
	protected $isbn;
	protected $config;
	protected $error;
	protected $open_url;

	protected function init(NamedArguments $arguments) {
		parent::init($arguments);
		$this->issn = $arguments->issn;
		$this->isbn = $arguments->isbn;
		$this->config = new Configuration;

		//determine the full URL
		//change the string sent to SFX depending on whether ISBN or ISSN was passed in
		if ($this->isbn){
			$stringAppend = "&isbn=" . $this->isbn;
		}else{
			$stringAppend = "&issn=" . $this->issn;
		}


		//get the sfx open URL out of the config terms
		$open_url = $this->config->terms->open_url;

		//check if there is already a ? in the URL so that we don't add another when appending the parms
		if (strpos($open_url, "?") > 0){
			$open_url .= "&";
		}else{
			$open_url .= "?";
		}


		$sid = $this->config->terms->sid;
		if ($sid){
			$open_url .= "rfr_id=info:sid/" . $sid;
		}


		$this->open_url = $open_url . "&sfx.ignore_date_threshold=1&sfx.response_type=simplexml" . $stringAppend;


	}



	public function getTargets() {

		//Make the call to SFX and load results
		@$xml = simplexml_load_file($this->open_url);

		if (!$xml){
			throw new Exception("Error with the SFX open URL:<br />  " . $this->open_url . "<br />");
		}



		$targetArray = array();

		//Loop through results and present selected targets
		if ($xml->targets) {
  		foreach ($xml->targets->target as $target) {
  		 	if($target->service_type == 'getFullTxt'){
  		 	  $resultArray['public_name'] = $target->target_public_name;
  		 	  $resultArray['target_url'] = $target->target_url;

  		 	  array_push($targetArray, $resultArray);
  		 	}
  		}
		} else {
		  // When there are multiple objects the xml is structured differently
		  foreach ($xml->ctx_obj as $obj) {
		    foreach ($obj->ctx_obj_targets->target as $target) {
		      if($target->service_type == 'getFullTxt'){
    		 	  $resultArray['public_name'] = $target->target_public_name;
    		 	  $resultArray['target_url'] = $target->target_url;

    		 	  array_push($targetArray, $resultArray);
    		 	}
		    }
		  }
		}

		return $targetArray;

	}


	public function getTitle() {

		//Make the call to SFX and load results
		@$xml = file_get_contents($this->open_url);

		if (!$xml){
			throw new Exception("Error with the SFX open URL:<br />  " . $this->open_url . "<br />");
		}



		preg_match("/item key=\\\"rft.title\\\"(.*)\/item/", $xml, $match);

		$title = $match[1];

		//if there was no title found there, try jtitle instead
		if (!$title){
			preg_match("/item key=\\\"rft.jtitle\\\"(.*)\/item/", $xml, $match);
			$title = $match[1];
		}


		$title = str_replace("&gt;", "", $title);
		$title = str_replace("&lt;", "", $title);

		return $title;
	}

}

?>
