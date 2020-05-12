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

class Publisher extends DatabaseObject {

	protected function defineRelationships() {}

	protected function overridePrimaryKeyName() {}

	protected function defineAttributes() {
		$this->addAttribute('publisherID');
		$this->addAttribute('name');
	}


	//returns array of the first listed issn objects
	public function getByName($publisherName){

		$query = "select publisherID from Publisher where upper(name) = upper('" . str_replace("'","''", $publisherName) . "') LIMIT 1;";

		$result = $this->db->processQuery($query, 'assoc');

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['publisherID'])){
			$obj = new Publisher(new NamedArguments(array('primaryKey' => $result['publisherID'])));
			return $obj;
		}else{
			return false;
		}

	}

  //returns array of the first listed issn objects
  public function getByCounterPublisherID($counterPublisherID){

    $query = "select publisherID from Publisher where counterPublisherID = '$counterPublisherID' LIMIT 1;";

    $result = $this->db->processQuery($query, 'assoc');

    //need to do this since it could be that there's only one request and this is how the dbservice returns result
    if (isset($result['publisherID'])){
      $obj = new Publisher(new NamedArguments(array('primaryKey' => $result['publisherID'])));
      return $obj;
    }else{
      return false;
    }

  }


}

?>
