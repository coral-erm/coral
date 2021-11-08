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



class TermsService extends Objects {

	protected $config;
	protected $error;
	protected $issn;
	protected $isbn;

	protected function init(NamedArguments $arguments) {
		parent::init($arguments);
		$this->issn = $arguments->issn;
		$this->isbn = $arguments->isbn;
		$this->config = new Configuration;
	}


	//returns one of the terms tool service objects depending on settings in config.
	public function getTermsToolObj() {

		//return correct object for terms tool
    $resolver = $this->config->terms->resolver;
    if (empty($resolver)) {
      throw new Exception("Terms tool service is not configured. Please setup a terms tool service in the Licensing > Admin module");
    }
		$className = $this->config->terms->resolver . "Service";

		try{
			$obj=new $className(new NamedArguments(array('issn' => $this->issn, 'isbn' => $this->isbn)));
		}catch(Exception $e){
			throw new Exception("configuration.ini is using an invalid resolver:  " . $this->config->settings->resolver);
		}

		return $obj;

	}

}

?>
