<?php
/*
**************************************************************************************************************************
** CORAL Management Module v. 1.0
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


class License extends DatabaseObject {

	protected function defineRelationships() {}

	protected function overridePrimaryKeyName() {}

	public function load() {
		//if exists in the database
		if ($this->primaryKey) {
			$query = "SELECT * FROM `$this->tableName` WHERE `$this->primaryKeyName` = '$this->primaryKey'";
			$result = $this->db->processQuery($query, 'assoc');
			foreach (array_keys($result) as $attributeName) {
				$this->addAttribute($attributeName);
				$this->attributes[$attributeName] = $result[$attributeName];
			}
		}else{
			// Figure out attributes from existing database
			$query = "SELECT COLUMN_NAME FROM information_schema.`COLUMNS` WHERE table_schema = '";
			$query .= $this->db->config->database->name . "' AND table_name = '$this->tableName'";// MySQL-specific
			foreach ($this->db->processQuery($query) as $result) {
				$attributeName = $result[0];
				$this->addAttribute($attributeName);
			}

		}
	}

	//returns all Consortiums associated with a license
	public function getConsortiumsByLicense() {
		$sql = "SELECT `consortiumID` FROM `license_consortium` c WHERE c.`licenseID`='$this->primaryKey'";
		if ($result = $this->db->query($sql)) {
			$rows = array();
			while ($row = $result->fetch_array()) {
				$rows[] = $row['consortiumID'];
			}
			return $rows;
		}
		return false;
	}

	//returns array of Document objects - used by forms to get dropdowns of available documents
	public function getDocuments() {

		$query = "SELECT D.*
						FROM Document D
						LEFT JOIN Signature S ON (S.documentID = D.documentID)
						LEFT JOIN DocumentType DT ON (DT.documentTypeID = D.documentTypeID)
						WHERE licenseID = '" . $this->licenseID . "'
						AND (D.expirationDate is null OR D.expirationDate = '0000-00-00')
						GROUP BY D.documentID
						ORDER BY D.shortName;";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['documentID'])) {
			$object = new Document(new NamedArguments(array('primaryKey' => $result['documentID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new Document(new NamedArguments(array('primaryKey' => $row['documentID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}

	public function getAllDocumentNamesAsIndexedArray() {

		$query = "SELECT D.`documentID`,D.`shortName`
						FROM Document D
						WHERE licenseID = '" . $this->licenseID . "'
						ORDER BY D.shortName;";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();
		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['documentID'])) {
			return array($result['documentID']=>$result);
		}else{
			$temp = array();
			foreach ($result as $row) {
				$temp[$row['documentID']] = $row;
			}
			return $temp;
		}
	}

	//returns array of Document objects (parent documents) - used for document display on license record
	public function getDocumentsWithoutParents($orderBy,$documentID=NULL) {

		$query = "SELECT D.*
						FROM Document D
						LEFT JOIN Signature S ON (S.documentID = D.documentID)
						LEFT JOIN DocumentType DT ON (DT.documentTypeID = D.documentTypeID)
						WHERE licenseID = '" . $this->licenseID . "'";
		if ($documentID) {
			$query .= " AND D.documentID != '$documentID'";
		}
		$query .= "		AND (D.expirationDate is null OR D.expirationDate = '0000-00-00')
						AND (D.parentDocumentID is null OR D.parentDocumentID=0)
						GROUP BY D.documentID
						ORDER BY " . $orderBy . ";";
		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['documentID'])) {
			$object = new Document(new NamedArguments(array('primaryKey' => $result['documentID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new Document(new NamedArguments(array('primaryKey' => $row['documentID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}



	//returns array of Document objects that are archived - used by forms to get dropdowns of available documents
	public function getArchivedDocuments() {

		$query = "SELECT D.* FROM Document D
					LEFT JOIN Signature S ON (S.documentID = D.documentID)
					LEFT JOIN DocumentType DT ON (DT.documentTypeID = D.documentTypeID)
					WHERE D.documentTypeID = DT.documentTypeID
					AND licenseID = '" . $this->licenseID . "'
					AND (D.expirationDate is not null AND D.expirationDate != '0000-00-00')
					GROUP BY D.documentID
					ORDER BY D.shortName asc;";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['documentID'])) {
			$object = new Document(new NamedArguments(array('primaryKey' => $result['documentID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new Document(new NamedArguments(array('primaryKey' => $row['documentID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}

	//returns array of Document objects that are archived - used by document display on license record
	public function getArchivedDocumentsWithoutParents($orderBy) {

		$query = "SELECT D.* FROM Document D
					LEFT JOIN Signature S ON (S.documentID = D.documentID)
					LEFT JOIN DocumentType DT ON (DT.documentTypeID = D.documentTypeID)
					WHERE D.documentTypeID = DT.documentTypeID
					AND licenseID = '" . $this->licenseID . "'
					AND (D.expirationDate is not null AND D.expirationDate != '0000-00-00')
					AND (D.parentDocumentID is null OR D.parentDocumentID=0)
					GROUP BY D.documentID
					ORDER BY " . $orderBy . ";";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['documentID'])) {
			$object = new Document(new NamedArguments(array('primaryKey' => $result['documentID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new Document(new NamedArguments(array('primaryKey' => $row['documentID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}





	//returns array of Attachment objects
	public function getAttachments() {

		$query = "SELECT * FROM Attachment where licenseID = '" . $this->licenseID . "' ORDER BY attachmentID";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['attachmentID'])) {
			$object = new Attachment(new NamedArguments(array('primaryKey' => $result['attachmentID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new Attachment(new NamedArguments(array('primaryKey' => $row['attachmentID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}

	public function getNotes() {

		$query = "SELECT `documentNoteID` FROM `DocumentNote` WHERE `licenseID`='$this->primaryKey' ORDER BY `createDate` DESC";

		if ($result = $this->db->processQuery($query, 'assoc')) {
			$objects = array();
			if (is_array($result[0])) {
				foreach ($result as $row) {
					$object = new DocumentNote(new NamedArguments(array('primaryKey' => $row['documentNoteID'])));
					array_push($objects, $object);
				}
			}else{
				$object = new DocumentNote(new NamedArguments(array('primaryKey' => $result['documentNoteID'])));
				array_push($objects, $object);
			}
			return $objects;
		}
		return false;
	}

	public function getNotesCount() {
		if ($this->primaryKey) {
			$sql = "SELECT COUNT(*) AS `total` FROM `DocumentNote` WHERE `licenseID`='$this->primaryKey'";
			if ($result = $this->db->processQuery($sql,'assoc')) {
				return $result['total'];
			}
		}
		return false;
	}


	//removes this license
	public function removeLicense() {

		//delete all documents and associated expressions and SFX providers
		$document = new Document();
		foreach ($this->getDocuments() as $document) {
			//delete all expressions and expression notes
			$expression = new Expression();
			foreach ($document->getExpressions() as $expression) {
				$expressionNote = new ExpressionNote();
				foreach ($expression->getExpressionNotes() as $expressionNote) {
					$expressionNote->delete();
				}

				$expression->removeQualifiers();
				$expression->delete();
			}

			$sfxProvider = new SFXProvider();
			foreach ($document->getSFXProviders() as $sfxProvider) {
				$sfxProvider->delete();
			}

			$signature = new Signature();
			foreach ($document->getSignatures() as $signature) {
				$signature->delete();
			}

			$document->delete();
		}


		//delete all attachments
		$attachment = new Attachment();
		foreach ($this->getAttachments() as $attachment) {
			$attachmentFile = new AttachmentFile();
			foreach ($attachment->getAttachmentFiles() as $attachmentFile) {
				$attachmentFile->delete();
			}
			$attachment->delete();
		}


		$this->delete();
	}

	public function searchQuery($whereAdd, $orderBy = '', $limit = '', $count = false) {
		if (count($whereAdd) > 0) {
			$whereStatement = " AND " . implode(" AND ", $whereAdd);
		}else{
			$whereStatement = "";
		}

		if ($limit != "") {
			$limitStatement = " LIMIT " . $limit;
		}else{
			$limitStatement = "";
		}

		$config = new Configuration;

		//if the org module is installed get the org name from org database
		if ($config->settings->organizationsModule == 'Y') {
			$dbName = $config->settings->organizationsDatabaseName;
			if ($count) {
				$select = "SELECT COUNT(DISTINCT L.licenseID) count";
			} else {
				$select = "SELECT distinct DT.shortName as Type, D.revisionDate, L.licenseID, L.shortName licenseName, O.name providerName, S.shortName status";
			}
			//now formulate query
			$query = $select . " FROM " . $dbName . ".Organization O, License L
								LEFT JOIN `license_consortium` lc ON (lc.`licenseID` = L.`licenseID`)
								LEFT JOIN " . $dbName . ".`Organization` O2 ON (O2.`organizationID` = lc.`consortiumID`)
								LEFT JOIN " . $dbName . ".Alias A ON (A.organizationID = L.organizationID)
								LEFT JOIN Status S ON (S.statusID = L.statusID)
								LEFT JOIN Document D ON (D.licenseID = L.licenseID)
								LEFT JOIN DocumentType DT ON (DT.documentTypeID = L.TypeID)
								LEFT JOIN Expression E ON (D.documentID = E.documentID)
								WHERE O.organizationID = L.organizationID and D.expirationDate is null
								" . $whereStatement;

		} else {
			if ($count) {
				$select = "SELECT COUNT(DISTINCT L.licenseID) count";
			} else {
				$select = "SELECT distinct DT.shortName as Type, D.revisionDate, L.licenseID, L.shortName licenseName, O.shortName providerName, S.shortName status";
			}
			//now formulate query
			$query = $select . " FROM Organization O, License L
								LEFT JOIN `license_consortium` lc ON (lc.`licenseID` = L.`licenseID`)
								LEFT JOIN `Consortium` c ON (c.`consortiumID` = lc.`consortiumID`)
								LEFT JOIN Status S ON (S.statusID = L.statusID)
								LEFT JOIN Document D ON (D.licenseID = L.licenseID)
								LEFT JOIN DocumentType DT ON (DT.documentTypeID = L.TypeID)
								LEFT JOIN Expression E ON (D.documentID = E.documentID)
								WHERE O.organizationID = L.organizationID and D.expirationDate is null
								" . $whereStatement;

		}

		if ($orderBy) {
			$query .= "\nORDER BY " . $orderBy;
		}

		if ($limit) {
			$query .= "\nLIMIT " . $limit;
		}
		return $query;
	}

	public function searchCount($whereAdd) {
		$query = $this->searchQuery($whereAdd, '', '', true);
		$result = $this->db->processQuery(stripslashes($query), 'assoc');
		return $result['count'];
	}

	//returns array based on search
	public function search($whereAdd, $orderBy, $limit) {
		$query = $this->searchQuery($whereAdd, $orderBy, $limit);

		$result = $this->db->processQuery(stripslashes($query), 'assoc');


		$searchArray = array();
		$resultArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['licenseID'])) {

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






	//returns array
	public function getInProgressLicenses() {
		$config = new Configuration;

		//if the org module is installed get the org name from org database
		if ($config->settings->organizationsModule == 'Y') {
			$dbName = $config->settings->organizationsDatabaseName;

			//execute query to get default licenses
			$query = "SELECT L.licenseID, L.shortName licenseName, O2.name consortiumName, O.name providerName, S.shortName status
								FROM " . $dbName . ".Organization O, License L
								LEFT JOIN " . $dbName . ".Organization O2 ON (O2.organizationID = L.consortiumID)
								LEFT JOIN Status S ON (S.statusID = L.statusID)
								WHERE O.organizationID = L.organizationID
								AND (S.shortName in ('Editing Expressions','Awaiting Document') OR L.statusID IS NULL)
								ORDER BY L.shortName";
		}else{

			//execute query to get default licenses
			$query = "SELECT L.licenseID, L.shortName licenseName, C.shortName consortiumName, O.shortName providerName, S.shortName status
								FROM Organization O, License L
								LEFT JOIN Consortium C ON (C.consortiumID = L.consortiumID)
								LEFT JOIN Status S ON (S.statusID = L.statusID)
								WHERE O.organizationID = L.organizationID
								AND (S.shortName in ('Editing Expressions','Awaiting Document') OR L.statusID IS NULL)
								ORDER BY L.shortName";

		}

		$result = $this->db->processQuery($query, 'assoc');

		$searchArray = array();
		$resultArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['licenseID'])) {

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



	//returns array of Expressions
	public function getAllDocumentsForExpressionDisplay() {

		$query="SELECT distinct D.documentID, D.shortName documentName
								FROM Document D, Expression E
								WHERE E.documentID = D.documentID
								AND licenseID = '" . $this->licenseID . "' AND (D.expirationDate is null or D.expirationDate ='0000-00-00')
								ORDER BY documentID;";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['documentID'])) {
			$object = new Document(new NamedArguments(array('primaryKey' => $result['documentID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new Document(new NamedArguments(array('primaryKey' => $row['documentID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}


	//search used for the autocomplete
	public function searchOrganizations($q) {
		$config = new Configuration;

		$q = str_replace("+", " ",$q);
		$q = str_replace("%", "&",$q);

		$orgArray = array();

		//if the org module is installed get the org names from org database
		if ($config->settings->organizationsModule == 'Y') {

			$dbName = $config->settings->organizationsDatabaseName;


			$query = "SELECT CONCAT(A.name, ' (', O.name, ')') name, O.organizationID
									FROM " . $dbName . ".Alias A, " . $dbName . ".Organization O
									WHERE A.organizationID=O.organizationID
									AND upper(A.name) like upper('%" . $q . "%')
									UNION
									SELECT name, organizationID
									FROM " . $dbName . ".Organization
									WHERE upper(name) like upper('%" . $q . "%')
									ORDER BY 1;";

			$result = $this->db->query($query);

			while ($row = $result->fetch_assoc()) {
				$orgArray[] = $row['organizationID'] . "|" . $row['name'];
			}


		//otherwise get the orgs from this database
		}else{
			$query = "SELECT shortName, organizationID
									FROM Organization
									WHERE upper(shortName) like upper('%" . $q . "%')
									ORDER BY 1;";

			$result = $this->db->query($query);

			while ($row = $result->fetch_assoc()) {
				$orgArray[] = $row['organizationID'] . "|" . $row['shortName'];
			}


		}


		return $orgArray;
	}




	//search used index page drop down
	public function getOrganizationList() {
		$config = new Configuration;

		$orgArray = array();

		//if the org module is installed get the org names from org database
		if ($config->settings->organizationsModule == 'Y') {
			$dbName = $config->settings->organizationsDatabaseName;
			$query = "SELECT name, organizationID FROM " . $dbName . ".Organization ORDER BY 1;";

		//otherwise get the orgs from this database
		}else{
			$query = "SELECT shortName name, organizationID FROM Organization ORDER BY 1;";
		}


		$result = $this->db->processQuery($query, 'assoc');

		$resultArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['organizationID'])) {

			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

			array_push($orgArray, $resultArray);
		}else{
			foreach ($result as $row) {
				$resultArray = array();
				foreach (array_keys($row) as $attributeName) {
					$resultArray[$attributeName] = $row[$attributeName];
				}
				array_push($orgArray, $resultArray);
			}
		}

		return $orgArray;

	}



	//go to organizations and get the org name for this license
	public function getOrganizationName() {
		$config = new Configuration;

		//if the org module is installed get the org name from org database
		if ($config->settings->organizationsModule == 'Y') {
			$dbName = $config->settings->organizationsDatabaseName;

			$orgArray = array();
			$query = "SELECT name FROM " . $dbName . ".Organization WHERE organizationID = " . $this->organizationID;

			if ($result = $this->db->query($query)) {

				while ($row = $result->fetch_assoc()) {
					return $row['name'];
				}
			}
		//otherwise if the org module is not installed get the org name from this database
		}else{
			$organization = new Organization(new NamedArguments(array('primaryKey' => $this->organizationID)));
			return $organization->shortName;
		}
	}



	//insert the organization / provider if it doesn't already exist
	public function setOrganization($orgID, $orgName) {
		$config = new Configuration;

		//if the org module is installed get the org name from org database
		if ($config->settings->organizationsModule == 'Y') {
			//if no org ID was passed in then we need to create a new organization shell
			if (!$orgID) {

				$dbName = $config->settings->organizationsDatabaseName;
				$orgName = str_replace("'", "''",$orgName);

				$query = "INSERT INTO " . $dbName . ".Organization (name, createDate, createLoginID) VALUES ('" . $orgName . "', NOW(), '" . $_SESSION['loginID'] . "')";

				$this->organizationID = $this->db->processQuery($query);

			}else{
				$this->organizationID = $orgID;
			}

		//otherwise if the org module is not installed get the org name from this database
		}else{

			//if no org ID was passed in then we need to create a new provider
			if (!$orgID) {
				$organization = new Organization();
				$organization->organizationID = '';
				$organization->shortName = $orgName;
				$organization->save();

				$this->organizationID = $organization->primaryKey;
			}else{
				$this->organizationID = $orgID;
			}
		}
	}

	public function setConsortiums($consortiumids=NULL) {
		if ($consortiumids) {
			//clear out any previous consortiums tied to this license
			$this->deleteLicenseConsortiums();
			//tie the new consortiums to this license
			$sql = "INSERT INTO `license_consortium` (`licenseID`,`consortiumID`) VALUES ";
			foreach ($consortiumids as $cid) {
				$sql .= "('$this->primaryKey',".$this->db->escapeString($cid)."),";
			}
			$sql = rtrim($sql,',');
			$this->db->processQuery($sql);
		}
	}



	//search used index page drop down
	public function getConsortiumList() {
		$config = new Configuration;

		$consortiumArray = array();

		//if the org module is installed get the consortia names from org database
		if ($config->settings->organizationsModule == 'Y') {
			$dbName = $config->settings->organizationsDatabaseName;
			$query = "SELECT name, O.organizationID consortiumID
				FROM " . $dbName . ".Organization O,
				" . $dbName . ".OrganizationRoleProfile ORP,
				" . $dbName . ".OrganizationRole
				WHERE OrganizationRole.organizationRoleID = ORP.organizationRoleID
				AND ORP.organizationID = O.organizationID
				AND ORP.organizationRoleID=1
				ORDER BY 1;";
// Jason S: switched to querying by index, rather than string matching
//	AND UPPER(OrganizationRole.shortName) LIKE 'CONSORT%'

		//otherwise get the consortium from this database
		}else{
			$query = "SELECT shortName name, consortiumID FROM Consortium ORDER BY 1;";
		}


		$result = $this->db->processQuery($query, 'assoc');

		$resultArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['consortiumID'])) {

			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

			array_push($consortiumArray, $resultArray);
		}else{
			foreach ($result as $row) {
				$resultArray = array();
				foreach (array_keys($row) as $attributeName) {
					$resultArray[$attributeName] = $row[$attributeName];
				}
				array_push($consortiumArray, $resultArray);
			}
		}

		return $consortiumArray;

	}



	//search used index page drop down
	public function getTypeList() {
		$config = new Configuration;

		$typeArray = array();

		$query = "SELECT shortName name, typeID FROM Type ORDER BY 1;";

		$result = $this->db->processQuery($query, 'assoc');

		$resultArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['typeID'])) {

			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

			array_push($consortiumArray, $resultArray);
		}else{
			foreach ($result as $row) {
				$resultArray = array();
				foreach (array_keys($row) as $attributeName) {
					$resultArray[$attributeName] = $row[$attributeName];
				}
				array_push($typeArray, $resultArray);
			}
		}

		return $typeArray;

	}


	//go to organizations and get the consortia name for this license
	public function getConsortiumName($consortiumid=NULL) {
		$config = new Configuration;

		//if the org module is installed get the org name from org database
		if ($config->settings->organizationsModule == 'Y') {
			$dbName = $config->settings->organizationsDatabaseName;

			$orgArray = array();
			$query = "SELECT `name` FROM `$dbName`.`Organization` WHERE `organizationID`=".(($consortiumid) ? $consortiumid:$this->consortiumID);
			$result = $this->db->query($query);

			while ($row = $result->fetch_assoc()) {
				return $row['name'];
			}
		//otherwise if the org module is not installed get the consortium name from this database
		}else{
			$consortium = new Consortium(new NamedArguments(array('primaryKey' => $consortiumid)));
			return $consortium->shortName;
		}
	}

	//used for A-Z on search (index)
	public function getAlphabeticalList() {
		$alphArray = array();
		$result = $this->db->query("
			SELECT
				DISTINCT UPPER(SUBSTR(TRIM(LEADING 'The ' FROM shortName),1,1)) letter,
				COUNT(SUBSTR(TRIM(LEADING 'The ' FROM shortName),1,1)) letter_count
			FROM License L
			GROUP BY UPPER(SUBSTR(TRIM(LEADING 'The ' FROM shortName),1,1))
			ORDER BY 1;");

		while ($row = $result->fetch_assoc()) {
			$alphArray[$row['letter']] = $row['letter_count'];
		}

		return $alphArray;
	}

	function deleteLicenseConsortiums() {
		$sql = "DELETE FROM `license_consortium` WHERE `licenseID`='$this->primaryKey'";
		$this->db->processQuery($sql);
	}

	public function delete() {
		$this->deleteLicenseConsortiums();
		parent::delete();
	}
}

?>
