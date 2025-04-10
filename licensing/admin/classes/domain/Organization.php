<?php
/*
**************************************************************************************************************************
** CORAL Licensing Module v. 1.0
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


class Organization extends DatabaseObject {

	protected function defineRelationships() {}

	protected function overridePrimaryKeyName() {}



	//returns array of parent organization objects
	public function getParentOrganizations(){

		$query = "SELECT O.name, parentOrganizationID
			FROM Organization O, OrganizationHierarchy OH
			WHERE OH.parentOrganizationID = O.organizationID
			AND OH.organizationID = '" . $this->organizationID . "'";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['parentOrganizationID'])){
			$object = new Organization(new NamedArguments(array('primaryKey' => $result['parentOrganizationID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new Organization(new NamedArguments(array('primaryKey' => $row['parentOrganizationID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}


	//removes child organization
	public function getChildOrganizations(){

		$query = "SELECT O.*
			FROM Organization O, OrganizationHierarchy OH
			WHERE OH.organizationID = O.organizationID
			AND OH.parentOrganizationID = '" . $this->organizationID . "'";


		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['organizationID'])){
			$object = new Organization(new NamedArguments(array('primaryKey' => $result['organizationID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new Organization(new NamedArguments(array('primaryKey' => $row['organizationID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}




	//removes organization hierarchy records
	public function removeOrganizationHierarchy(){

		$query = "DELETE
			FROM OrganizationHierarchy
			WHERE organizationID = '" . $this->organizationID . "' OR parentOrganizationID = '" . $this->organizationID . "'";

		$result = $this->db->processQuery($query);
	}



	//returns array of role objects
	public function getOrganizationRoles(){

		$query = "SELECT OrganizationRole.* FROM OrganizationRole, OrganizationRoleProfile ORP where ORP.organizationRoleID = OrganizationRole.organizationRoleID AND organizationID = '" . $this->organizationID . "'";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['organizationRoleID'])){
			$object = new OrganizationRole(new NamedArguments(array('primaryKey' => $result['organizationRoleID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new OrganizationRole(new NamedArguments(array('primaryKey' => $row['organizationRoleID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}




	//deletes all org roles associated with this org
	public function removeOrganizationRoles(){

		$query = "DELETE FROM OrganizationRoleProfile WHERE organizationID = '" . $this->organizationID . "'";

		return $this->db->processQuery($query);
	}


	//deletes all parent orgs associated with this org
	public function removeParentOrganizations(){

		$query = "DELETE FROM OrganizationHierarchy WHERE organizationID = '" . $this->organizationID . "'";

		return $this->db->processQuery($query);
	}

	//returns array of alias objects
	public function getAliases(){

		$query = "SELECT * FROM Alias WHERE organizationID = '" . $this->organizationID . "' order by name";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['aliasID'])){
			$object = new Alias(new NamedArguments(array('primaryKey' => $result['aliasID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new Alias(new NamedArguments(array('primaryKey' => $row['aliasID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}



	//returns array of contact objects
	public function getContacts(){

		$query = "SELECT * FROM Contact WHERE organizationID = '" . $this->organizationID . "' order by name";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['contactID'])){
			$object = new Contact(new NamedArguments(array('primaryKey' => $result['contactID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new Contact(new NamedArguments(array('primaryKey' => $row['contactID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}




	//returns array of contact objects
	public function getUnarchivedContacts(){

		$query = "SELECT * FROM Contact WHERE (archiveDate = '0000-00-00' || archiveDate = '') AND organizationID = '" . $this->organizationID . "' order by name";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['contactID'])){
			$object = new Contact(new NamedArguments(array('primaryKey' => $result['contactID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new Contact(new NamedArguments(array('primaryKey' => $row['contactID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}




	//returns array of contact objects
	public function getArchivedContacts(){

		$query = "SELECT * FROM Contact WHERE (archiveDate != '0000-00-00' && archiveDate != '') AND organizationID = '" . $this->organizationID . "' order by name";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['contactID'])){
			$object = new Contact(new NamedArguments(array('primaryKey' => $result['contactID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new Contact(new NamedArguments(array('primaryKey' => $row['contactID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}



	//returns array of external login objects
	public function getExternalLogins(){

		$query = "SELECT * FROM ExternalLogin WHERE organizationID = '" . $this->organizationID . "' order by externalLoginTypeID";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['externalLoginID'])){
			$object = new ExternalLogin(new NamedArguments(array('primaryKey' => $result['externalLoginID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new ExternalLogin(new NamedArguments(array('primaryKey' => $row['externalLoginID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}


	//returns array of issue log objects
	public function getIssueLog(){

		$query = "SELECT * FROM IssueLog WHERE organizationID = '" . $this->organizationID . "' order by issueDate desc";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['issueLogID'])){
			$object = new IssueLog(new NamedArguments(array('primaryKey' => $result['issueLogID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new IssueLog(new NamedArguments(array('primaryKey' => $row['issueLogID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}



	//returns array of licenses
	public function getLicenses(){
		$config = new Configuration;

		$licenseArray = array();

		//if the licensing module is installed get the licenses for this organization
		$dbName = $config->settings->licensingDatabaseName;
		$query = "SELECT distinct L.licenseID, L.shortName licenseName, O2.name consortiumName, S.shortName status
								FROM Organization O, " . $dbName . ".License L
								LEFT JOIN Organization O2 ON (O2.organizationID = L.consortiumID)
								LEFT JOIN " . $dbName . ".Status S ON (S.statusID = L.statusID)
								WHERE (O.organizationID = L.organizationID OR O.organizationID = L.consortiumID)
								AND O.organizationID = '" . $this->organizationID . "'
								ORDER BY 1;";


		$result = $this->db->processQuery($query, 'assoc');

		$resultArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['licenseID'])){

			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

			array_push($licenseArray, $resultArray);
		}else{
			foreach ($result as $row) {
				$resultArray = array();
				foreach (array_keys($row) as $attributeName) {
					$resultArray[$attributeName] = $row[$attributeName];
				}
				array_push($licenseArray, $resultArray);
			}
		}

		return $licenseArray;

	}


	//returns array based on search
	public function search($whereAdd, $orderBy, $limit){

		if (is_array($whereAdd) && count($whereAdd) > 0) {
			$whereStatement = " WHERE " . implode(" AND ", $whereAdd);
		}else{
			$whereStatement = "";
		}

		if ($limit != ""){
			$limitStatement = " LIMIT " . $limit;
		}else{
			$limitStatement = "";
		}


		//now actually execute query
		$query = "SELECT O.organizationID, O.name,
						GROUP_CONCAT(DISTINCT Alias.name ORDER BY Alias.name DESC SEPARATOR '<br />') aliases,
						GROUP_CONCAT(DISTINCT OrganizationRole.shortName ORDER BY OrganizationRole.shortName DESC SEPARATOR '<br />') orgRoles,
						OHP.parentOrganizationID,
						OP.name parentOrganizationName,
						GROUP_CONCAT(DISTINCT C.name ORDER BY C.name DESC SEPARATOR '<br />') contacts,
						GROUP_CONCAT(DISTINCT CR.shortName ORDER BY C.name DESC SEPARATOR '<br />') contactroles
								FROM Organization O
									LEFT JOIN Alias ON O.organizationID = Alias.organizationID
									LEFT JOIN OrganizationRoleProfile ORP ON O.organizationID = ORP.organizationID
									LEFT JOIN OrganizationRole ON OrganizationRole.organizationRoleID = ORP.organizationRoleID
									LEFT JOIN OrganizationHierarchy OHP ON O.organizationID = OHP.organizationID
									LEFT JOIN Organization OP ON OHP.parentOrganizationID = OP.organizationID
									LEFT JOIN Contact C ON C.organizationID = O.organizationID
									LEFT JOIN ContactRoleProfile CRP ON C.contactID = CRP.contactID
									LEFT JOIN ContactRole CR ON CR.contactRoleID = CRP.contactRoleID
								" . $whereStatement . "
								GROUP By O.organizationID
								ORDER BY " . $orderBy . $limitStatement;


		$result = $this->db->processQuery(stripslashes($query), 'assoc');

		$searchArray = array();
		$resultArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['organizationID'])){

			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

			array_push($searchArray, $resultArray);
		}else{
			foreach ($result as $row) {
				$resultArray = array();
				foreach (array_keys($row) as $attributeName) {
					$resultArray[$attributeName] = $row[$attributeName];
				}
				array_push($searchArray, $resultArray);
			}
		}

		return $searchArray;
	}



	//removes this organization
	public function removeOrganization(){
		//delete organization roles
		$this->removeOrganizationRoles();

		$instance = new Alias();
		foreach ($this->getAliases() as $instance) {
			$instance->delete();
		}

		$instance = new Contact();
		foreach ($this->getContacts() as $instance) {
			$instance->removeContactRoles();
			$instance->delete();
		}

		$instance = new ExternalLogin();
		foreach ($this->getExternalLogins() as $instance) {
			$instance->delete();
		}

		$instance = new IssueLog();
		foreach ($this->getIssueLog() as $instance) {
			$instance->delete();
		}

		//delete parent and child relationships
		$this->removeOrganizationHierarchy();



		$this->delete();
	}



	//search used for the autocomplete
	public function autocompleteSearch($q){
		$orgArray = array();
		$result = mysqli_query($this->db->getDatabase(), "SELECT CONCAT(A.name, ' (', O.name, ')') name, O.organizationID
								FROM Alias A, Organization O
								WHERE A.organizationID=O.organizationID
								AND upper(A.name) like upper('%" . $q . "%')
								UNION
								SELECT name, organizationID
								FROM Organization
								WHERE upper(name) like upper('%" . $q . "%')
								ORDER BY 1;");

		while ($row = mysqli_fetch_assoc($result)){
			$orgArray[] = $row['name'] . "|" . $row['organizationID'];
		}

		return $orgArray;
	}


// TODO: i18n; remove other articles

	//used for A-Z on search (index)
	public function getAlphabeticalList(){
		$alphArray = array();
		$result = mysqli_query($this->db->getDatabase(), "SELECT DISTINCT UPPER(SUBSTR(TRIM(LEADING 'The ' FROM name),1,1)) letter, COUNT(SUBSTR(TRIM(LEADING 'The ' FROM name),1,1)) letter_count
								FROM Organization O
								GROUP BY SUBSTR(TRIM(LEADING 'The ' FROM name),1,1)
								ORDER BY 1;");

		while ($row = mysqli_fetch_assoc($result)){
			$alphArray[$row['letter']] = $row['letter_count'];
		}

		return $alphArray;
	}

}

?>
