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



class EBSCOService extends Base_Object {

	protected $issn;
	protected $isbn;
	protected $config;
	protected $error;
	protected $open_url;
	protected $object_title;

	protected function init(NamedArguments $arguments) {
		parent::init($arguments);
		$this->issn = $arguments->issn;
		$this->isbn = $arguments->isbn;
		$this->config = new Configuration;

		//determine the full open URL
		//get the ISBN or ISSN passed in
		if ($this->isbn){
			$stringAppend = $this->isbn;
		}else{
			$stringAppend = $this->issn;
		}

        //get the client identifier out of the config terms
        $client_identifier = $this->config->terms->client_identifier;

		$this->open_url = "https://api.ebsco.io/rm/rmaccounts/" . $client_identifier . "/titles?search=" . $stringAppend . "&searchfield=isxn&selection=selected&orderby=relevance&count=5&offset=1";
	}


	public function getTargets() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$this->open_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'x-api-key: '.$this->config->terms->sid,
        ]);

		//Make the call to EBSCO KB and load results
        $response = curl_exec($ch);
        curl_close ($ch);

		$responseArray = json_decode($response, true);

		//get the title of the resource to use in getTitle()
		$this->object_title = $responseArray['titles'][0]['titleName'];

		$targetArray = array();

		//get the package name and URL for all packages and put into target array
		foreach($responseArray['titles'] as $title) {
			foreach($title['customerResourcesList'] as $package)
				if ($package['url']) {
					$targetArray[] = array(
						'public_name' => $package['packageName'],
						'target_url' => $package['url']
					);
				}
		}

		return $targetArray;
	}


	public function getTitle() {

		//get title for object from variable
		$title = $this->object_title;

		//alternatively like SFX and SerialsSolutions: make a new call to EBSCO KB and load results
		/*
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$this->open_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'x-api-key: '.$this->config->terms->sid,
        ]);

        $response = curl_exec($ch);
        curl_close ($ch);

		$responseArray = json_decode($response, true);

		$title = $responseArray['titles'][0]['titleName'];
		*/

		return $title;
	}

}

?>
