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



class SerialsSolutionsService extends Base_Object {


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

		//determine the full open URL
		//change the string sent depending on whether ISBN or ISSN was passed in
		if ($this->isbn){
			$stringAppend = "&rft.isbn=" . $this->isbn;
		}else{
			$stringAppend = "&rft.issn=" . $this->issn;
		}

		//get the client identifier out of the config terms
		$client_identifier = $this->config->terms->client_identifier;

		$this->open_url = "http://" . $client_identifier . ".openurl.xml.serialssolutions.com/openurlxml?version=1.0&url_ver=Z39.88-2004" . $stringAppend;

	}




	public function getTargets() {

		//load xml from the open url into DOM
		$dom = new DomDocument();
		$dom->load($this->open_url);

		$targetArray = array();

		$linkGroups = array();
		$linkGroups = $dom->getElementsByTagName('linkGroup');

		// Working our way through each each element in the collection
		foreach ($linkGroups as $linkGroup) {
			$resultArray = array();
			$holdingData = array();
			$holdingData = $linkGroup->getElementsByTagName('holdingData');

			foreach ($holdingData as $holding) {
				foreach($holding->childNodes as $h) {
					if ($h->nodeName == "ssopenurl:databaseName"){
						$resultArray['public_name'] = $h->nodeValue;
						//echo "<br />" . $resultArray['public_name'];
					}
				}
			}

			$urlData = array();
			$urlData = $linkGroup->getElementsByTagName('url');


			foreach ($urlData as $url) {
				if ($this->issn){
					if ($url->getAttribute('type') == "journal"){
						$resultArray['target_url'] = $url->nodeValue;
					}
				}else{
					if ($url->getAttribute('type') == "book"){
						$resultArray['target_url'] = $url->nodeValue;
					}
				}
			}

			array_push($targetArray, $resultArray);

		}


		return $targetArray;

	}


	public function getTitle() {

		//load xml from the open url into DOM
		$dom = new DomDocument();
		$dom->load($this->open_url);

		$citations = array();
		$citations = $dom->getElementsByTagName('citation');

		foreach ($citations as $citation) {
			foreach($citation->childNodes as $c) {
				if ($c->nodeName == "dc:source"){
					$title = $c->nodeValue;
				}
			}
		}

		return $title;
	}

}

?>
