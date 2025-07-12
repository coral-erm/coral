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

class UserGroup extends DatabaseObject {

	protected function defineRelationships() {}

	protected function overridePrimaryKeyName() {}




	//returns array of user objects associated with this group
	public function getUsers(){

		$query = "SELECT U.* FROM User U, UserGroupLink UGL
					WHERE UGL.userGroupID = '" . $this->userGroupID . "'
					AND UGL.loginID = U.loginID
					ORDER BY U.lastName, U.firstName, U.loginID";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['loginID'])){
			$object = new User(new NamedArguments(array('primaryKey' => $result['loginID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new User(new NamedArguments(array('primaryKey' => $row['loginID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}



	//Deletes users that aren't in a provided List.
	public function removeUsersNotInList($list = []){
		$listExists = (count($list)>0);
		$listString = implode("','", $list);
		$loginIDs = ($listExists) ? $listString : "";
		$query = "DELETE FROM UserGroupLink WHERE userGroupID = {$this->userGroupID} AND loginID NOT IN ('{$loginIDs}')";

		return $this->db->processQuery($query);
	}
	//deletes all user links associated with this user group
	public function removeAllUsers(){

		$query = "DELETE FROM UserGroupLink WHERE userGroupID = '" . $this->userGroupID . "'";

		return $this->db->processQuery($query);
	}






}

?>
