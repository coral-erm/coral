<?php

/*
**************************************************************************************************************************
** CORAL Organizations Module
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

    public function asArray() {
		$rarray = array();
		foreach (array_keys($this->attributeNames) as $attributeName) {
			if ($this->$attributeName != null) {
				$rarray[$attributeName] = $this->$attributeName;
			}
		}

		$aliases = $this->getAliases();
		$rarray['aliases'] = array();
		foreach ($aliases as $alias) {
            array_push($rarray['aliases'], $alias->name);
		}

		return $rarray;
    }


	// retrieves an organization by it's ebscoKbID
    public function getOrganizationByEbscoKbId($ebscoKbId) {

        $query = "SELECT organizationID
			FROM Organization
			WHERE ebscoKbID = $ebscoKbId
			LIMIT 0,1";
        $result = $this->db->processQuery($query, 'assoc');

        if (isset($result['organizationID'])) {
            return new Organization(new NamedArguments(array('primaryKey' => $result['organizationID'])));
        } else {
            return false;
        }
    }

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

    public function hasILSVendorRole() {
        $config = new Configuration();
        $organizationRoles = $this->getOrganizationRoles();
        $roleMatch = false;
        foreach ($organizationRoles as $organizationRole) {
           if ($organizationRole->shortName == $config->ils->ilsVendorRole) {
                $roleMatch = true;
            }

        }
        return $roleMatch;;
    }

    public function isLinkedToILS() {
        return $this->ilsID != null && $this->hasILSVendorRole();
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

		$query = "SELECT * FROM Contact WHERE (archiveDate = '0000-00-00' OR archiveDate IS NULL) AND organizationID = '" . $this->organizationID . "' order by name";

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

		$query = "SELECT * FROM Contact WHERE (archiveDate != '0000-00-00' AND archiveDate IS NOT NULL) AND organizationID = '" . $this->organizationID . "' order by name";

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

	public function getIssues($archivedOnly=false) {
		$query = "SELECT i.*
				  FROM `{$this->db->config->settings->resourcesDatabaseName}`.Issue i
				  LEFT JOIN `{$this->db->config->settings->resourcesDatabaseName}`.IssueRelationship ir ON ir.issueID=i.issueID
				  WHERE ir.entityID='$this->organizationID' AND ir.entityTypeID=1";
		if ($archivedOnly) {
			$query .= " AND i.dateClosed IS NOT NULL";
		} else {
			$query .= " AND i.dateClosed IS NULL";
		}
		$query .= "	ORDER BY i.dateCreated DESC";
		$result = $this->db->processQuery($query, 'assoc');
		$objects = array();
		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['issueID'])){
			$object = new Issue(new NamedArguments(array('primaryKey' => $result['issueID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new Issue(new NamedArguments(array('primaryKey' => $row['issueID'])));
				array_push($objects, $object);
			}
		}
		return $objects;
	}

	public function getExportableIssues($archivedOnly=false){
		$orgDB = $this->db->config->database->name;
		$resourceDB = $this->db->config->settings->resourcesDatabaseName;
		$query = "SELECT i.*,(SELECT GROUP_CONCAT(CONCAT('=HYPERLINK(\"mailto:',sc.emailAddress,'\",\"',COALESCE(sc.name,sc.emailAddress),'\")') SEPARATOR ', ')
								FROM `{$resourceDB}`.IssueContact sic
								LEFT JOIN `{$orgDB}`.Contact sc ON sc.contactID=sic.contactID
								WHERE sic.issueID=i.issueID) AS `contacts`,
							 (SELECT GROUP_CONCAT(se.name SEPARATOR ', ')
								FROM `{$resourceDB}`.IssueRelationship sir
								LEFT JOIN `{$orgDB}`.Organization se ON (se.organizationID=sir.entityID AND sir.entityTypeID=1)
								WHERE sir.issueID=i.issueID) AS `appliesto`,
							 (SELECT GROUP_CONCAT(sie.email SEPARATOR ', ')
								FROM `{$resourceDB}`.IssueEmail sie
								WHERE sie.issueID=i.issueID) AS `CCs`
				  FROM `{$resourceDB}`.Issue i
				  LEFT JOIN `{$resourceDB}`.IssueRelationship ir ON ir.issueID=i.issueID
				  WHERE ir.entityID='$this->organizationID' AND ir.entityTypeID=1";

		if ($archivedOnly) {
			$query .= " AND i.dateClosed IS NOT NULL";
		} else {
			$query .= " AND i.dateClosed IS NULL";
		}
		$query .= "	ORDER BY i.dateCreated DESC";
		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['issueID'])){
			return array($result);
		}else{
			return $result;
		}
	}

	private function getDownTimeResults($archivedOnly=false) {
		$query = "SELECT d.*
			  FROM `{$this->db->config->settings->resourcesDatabaseName}`.Downtime d
			  WHERE d.entityID='{$this->organizationID}'
			  AND d.entityTypeID=1";

		if ($archivedOnly) {
			$query .= " AND d.endDate < CURDATE()";
		} else {
			$query .= " AND (d.endDate >= CURDATE() OR d.endDate IS NULL)";
		}
		$query .= "	ORDER BY d.dateCreated DESC";

		return $this->db->processQuery($query, 'assoc');
	}

	public function getDowntime($archivedOnly=false) {
		$result = $this->getDownTimeResults($archivedOnly);

		$objects = array();
		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['downtimeID'])) {
			$object = new Downtime(new NamedArguments(array('primaryKey' => $result['downtimeID'])));
			array_push($objects, $object);
		} else {
			foreach ($result as $row) {
				$object = new Downtime(new NamedArguments(array('primaryKey' => $row['downtimeID'])));
				array_push($objects, $object);
			}
		}
		return $objects;
	}

	public function getExportableDowntimes($archivedOnly=false){
		$result = $this->getDownTimeResults($archivedOnly);

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['downtimeID'])){
			return array($result);
		}else{
			return $result;
		}
	}

	//returns array of issue log objects
	public function getIssueLog(){

		$query = "SELECT * FROM IssueLog
              LEFT JOIN IssueLogType ON IssueLogType.issueLogTypeID = IssueLog.issueLogTypeID
              WHERE organizationID = '" . $this->organizationID . "' order by issueStartDate desc";

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


	//returns array of resources
	public function getResources($organizationRoleID = null){
		$resourceArray = array();

		//make sure we have the resourcesModule
		$config = new Configuration;
		if ($config->settings->resourcesModule != 'Y') {
			return $resourceArray;
		}

		if (isset($organizationRoleID)) {
			$whereOptions = " AND organizationRoleID = '$organizationRoleID' ";
		} else {
			$whereOptions = '';
		}
		$dbName = $config->settings->resourcesDatabaseName;
		if ($dbName == '') {
			return $resourceArray;
		}
		$query = "SELECT R.resourceID, R.titleText, R.statusID, CASE
									WHEN UPPER(S.shortName) LIKE '%ARCHIVE%' THEN 1 ELSE 0 END as archived
								FROM " . $dbName . ".ResourceOrganizationLink ROL
								NATURAL JOIN " . $dbName .".Resource R
								NATURAL JOIN " . $dbName .".Status S
								WHERE ROL.organizationID = '" . $this->organizationID . "' "
								. $whereOptions . "
								ORDER BY 4,2;";
		$result = $this->db->processQuery($query, 'assoc');
		//this is because processQuery has a bad habit of mixed return values
		//TODO: change this, maybe, someday
		if (isset($result['resourceID'])) {
			$result = array($result);
		}

		return $result;

	}


	//returns array of statuses from resources database
	public function getResourceStatuses(){
		$statusArray = array();

		//make sure we have the resourcesModule
		$config = new Configuration;
		if ($config->settings->resourcesModule != 'Y') {
			return $statusArray;
		}

		$dbName = $config->settings->resourcesDatabaseName;
		if ($dbName == '') {
			return $statusArray;
		}
		$query = "SELECT S.statusID, S.shortName FROM " . $dbName . ".Status S;";

		$result = $this->db->processQuery($query, 'assoc');
		//this is because processQuery has a bad habit of mixed return values
		//TODO: change this, maybe, someday
		if ($result['statusID']) {
			$result = array($result);
		}
		foreach ($result as $row) {
			$ids_by_name[$row["shortName"]] = $row["statusID"];
		}

		return $ids_by_name;
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
								GROUP BY O.organizationID, OHP.parentOrganizationID
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
