<?php

/*
**************************************************************************************************************************
** CORAL Resources Module v. 1.0
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

class NoteType extends DatabaseObject {

	protected function defineRelationships() {}

	protected function overridePrimaryKeyName() {}


	//override the allAsArray function from DatabaseObject to remove the INITIAL note since this is a special note type
	//returns array of note types without
	public function allAsArrayForDD() {
		$query = "SELECT * FROM NoteType WHERE upper(shortName) NOT LIKE '%INITIAL%' ORDER BY 2, 1";
		$result = $this->db->processQuery($query, 'assoc');

		$resultArray = array();
		$rowArray = array();

		if (isset($result[lcfirst($this->tableName) . 'ID'])){
			foreach (array_keys($result) as $attributeName) {
				$rowArray[$attributeName] = $result[$attributeName];
			}
			array_push($resultArray, $rowArray);
		}else{
			foreach ($result as $row) {
				foreach (array_keys($this->attributeNames) as $attributeName) {
					$rowArray[$attributeName] = $row[$attributeName];
				}
				array_push($resultArray, $rowArray);
			}
		}

		return $resultArray;
	}


	//returns noteTypeID for the Creator note type which is
	public function getInitialNoteTypeID() {
		$query = "SELECT * FROM NoteType WHERE upper(shortName) LIKE '%INITIAL%' LIMIT 0,1";
		$result = $this->db->processQuery($query, 'assoc');

		if (isset($result['noteTypeID'])){
			return $result['noteTypeID'];
		}else{
			return '';
		}
	}

    public function getNoteTypeIDByName($name) {
        $query = "SELECT * FROM NoteType WHERE shortName='$name' LIMIT 0,1";
        $result = $this->db->processQuery($query, 'assoc');

        if (isset($result['noteTypeID'])){
            return $result['noteTypeID'];
        }else{
            return null;
        }
    }

	//returns number of children for this particular contact role
	public function getNumberOfChildren(){

		$query = "SELECT count(*) childCount FROM ResourceNote WHERE noteTypeID = '" . $this->noteTypeID . "';";

		$result = $this->db->processQuery($query, 'assoc');

		return $result['childCount'];

	}



}

?>
