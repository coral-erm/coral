<?php

/*
**************************************************************************************************************************
** CORAL Resources Module v. 1.2
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

class Resource extends DatabaseObject {

	protected function defineRelationships() {}

  protected function defineIsbnOrIssn() {}

	protected function overridePrimaryKeyName() {}

	//returns resource objects by title
	public function getResourceByTitle($title){

		$query = "SELECT *
			FROM Resource
			WHERE UPPER(titleText) = '" . str_replace("'", "''", strtoupper($title)) . "'
			ORDER BY 1";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceID'])){
			$object = new Resource(new NamedArguments(array('primaryKey' => $result['resourceID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new Resource(new NamedArguments(array('primaryKey' => $row['resourceID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}

  //returns resource objects by title
	public function getResourceByIsbnOrISSN($isbnOrISSN){

		$query = "SELECT DISTINCT(resourceID)
			FROM IsbnOrIssn";

    $i = 0;

    if (!is_array($isbnOrISSN)) {
      $value = $isbnOrISSN;
      $isbnOrISSN = array($value);
    }

    foreach ($isbnOrISSN as $value) {
      $query .= ($i == 0) ? " WHERE " : " OR ";
      $query .= "isbnOrIssn = '" . $this->db->escapeString($value) . "'";
      $i++;
    }

		$query .=	" ORDER BY 1";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceID'])){
			$object = new Resource(new NamedArguments(array('primaryKey' => $result['resourceID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new Resource(new NamedArguments(array('primaryKey' => $row['resourceID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}

  public function getIsbnOrIssn() {
    $query = "SELECT *
      FROM IsbnOrIssn
      WHERE resourceID = '" . $this->resourceID . "'
      ORDER BY 1";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['isbnOrIssnID'])){
			$object = new IsbnOrIssn(new NamedArguments(array('primaryKey' => $result['isbnOrIssnID'])));
			array_push($objects, $object);
		} else {
      foreach ($result as $row) {
				$object = new IsbnOrIssn(new NamedArguments(array('primaryKey' => $row['isbnOrIssnID'])));
				array_push($objects, $object);
			}

    }

		return $objects;

  }

	//returns array of parent resource objects
	public function getParentResources(){
    return $this->getRelatedResources('resourceID');
	}


	//returns array of child resource objects
	public function getChildResources(){
    return $this->getRelatedResources('relatedResourceID');
	}

  // return array of related resource objects
  private function getRelatedResources($key) {

    $query = "SELECT *
			FROM ResourceRelationship
			WHERE $key = '" . $this->resourceID . "'
			AND relationshipTypeID = '1'
			ORDER BY 1";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result[$key])){
			$object = new ResourceRelationship(new NamedArguments(array('primaryKey' => $result['resourceRelationshipID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new ResourceRelationship(new NamedArguments(array('primaryKey' => $row['resourceRelationshipID'])));
				array_push($objects, $object);
			}
		}

		return $objects;

  }

	//returns array of purchase site objects
	public function getResourcePurchaseSites(){

		$query = "SELECT PurchaseSite.* FROM PurchaseSite, ResourcePurchaseSiteLink RPSL where RPSL.purchaseSiteID = PurchaseSite.purchaseSiteID AND resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['purchaseSiteID'])){
			$object = new PurchaseSite(new NamedArguments(array('primaryKey' => $result['purchaseSiteID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new PurchaseSite(new NamedArguments(array('primaryKey' => $row['purchaseSiteID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}




	//returns array of ResourcePayment objects
	public function getResourcePayments(){

		$query = "SELECT * FROM ResourcePayment WHERE resourceID = '" . $this->resourceID . "' ORDER BY year DESC, fundName, subscriptionStartDate DESC";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourcePaymentID'])){
			$object = new ResourcePayment(new NamedArguments(array('primaryKey' => $result['resourcePaymentID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new ResourcePayment(new NamedArguments(array('primaryKey' => $row['resourcePaymentID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}


	//returns array of associated licenses
	public function getLicenseArray(){
		$config = new Configuration;

		//if the lic module is installed get the lic name from lic database
		if ($config->settings->licensingModule == 'Y'){
			$dbName = $config->settings->licensingDatabaseName;

			$resourceLicenseArray = array();

			$query = "SELECT * FROM ResourceLicenseLink WHERE resourceID = '" . $this->resourceID . "'";

			$result = $this->db->processQuery($query, 'assoc');

			$objects = array();

			//need to do this since it could be that there's only one request and this is how the dbservice returns result
			if (isset($result['licenseID'])){
				$licArray = array();

				//first, get the license name
				$query = "SELECT shortName FROM " . $dbName . ".License WHERE licenseID = " . $result['licenseID'];

				if ($licResult = $this->db->query($query)){
					while ($licRow = $licResult->fetch_assoc()){
						$licArray['license'] = $licRow['shortName'];
						$licArray['licenseID'] = $result['licenseID'];
					}
				}

				array_push($resourceLicenseArray, $licArray);
			}else{
				foreach ($result as $row) {
					$licArray = array();

					//first, get the license name
					$query = "SELECT shortName FROM " . $dbName . ".License WHERE licenseID = " . $row['licenseID'];

					if ($licResult = $this->db->query($query)){
						while ($licRow = $licResult->fetch_assoc()){
							$licArray['license'] = $licRow['shortName'];
							$licArray['licenseID'] = $row['licenseID'];
						}
					}

					array_push($resourceLicenseArray, $licArray);

				}

			}

			return $resourceLicenseArray;
		}
	}




	//returns array of resource license status objects
	public function getResourceLicenseStatuses(){

		$query = "SELECT * FROM ResourceLicenseStatus WHERE resourceID = '" . $this->resourceID . "' ORDER BY licenseStatusChangeDate desc;";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceLicenseStatusID'])){
			$object = new ResourceLicenseStatus(new NamedArguments(array('primaryKey' => $result['resourceLicenseStatusID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new ResourceLicenseStatus(new NamedArguments(array('primaryKey' => $row['resourceLicenseStatusID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}




	//returns LicenseStatusID of the most recent resource license status
	public function getCurrentResourceLicenseStatus(){

		$query = "SELECT licenseStatusID FROM ResourceLicenseStatus RLS WHERE resourceID = '" . $this->resourceID . "' AND licenseStatusChangeDate = (SELECT MAX(licenseStatusChangeDate) FROM ResourceLicenseStatus WHERE ResourceLicenseStatus.resourceID = '" . $this->resourceID . "') LIMIT 0,1;";

		$result = $this->db->processQuery($query, 'assoc');

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['licenseStatusID'])){
			return $result['licenseStatusID'];
		}

	}


	//returns array of authorized site objects
	public function getResourceAuthorizedSites(){

		$query = "SELECT AuthorizedSite.* FROM AuthorizedSite, ResourceAuthorizedSiteLink RPSL where RPSL.authorizedSiteID = AuthorizedSite.authorizedSiteID AND resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['authorizedSiteID'])){
			$object = new AuthorizedSite(new NamedArguments(array('primaryKey' => $result['authorizedSiteID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new AuthorizedSite(new NamedArguments(array('primaryKey' => $row['authorizedSiteID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}




	//returns array of administering site objects
	public function getResourceAdministeringSites(){

		$query = "SELECT AdministeringSite.* FROM AdministeringSite, ResourceAdministeringSiteLink RPSL where RPSL.administeringSiteID = AdministeringSite.administeringSiteID AND resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['administeringSiteID'])){
			$object = new AdministeringSite(new NamedArguments(array('primaryKey' => $result['administeringSiteID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new AdministeringSite(new NamedArguments(array('primaryKey' => $row['administeringSiteID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}




	//deletes all parent resources associated with this resource
	public function removeParentResources(){

		$query = "DELETE FROM ResourceRelationship WHERE resourceID = '" . $this->resourceID . "'";

		return $this->db->processQuery($query);
	}




	//returns array of alias objects
	public function getAliases(){

		$query = "SELECT * FROM Alias WHERE resourceID = '" . $this->resourceID . "' order by shortName";

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
	public function getUnarchivedContacts($moduleFilter=false){
		$config = new Configuration;
		$resultArray = array();
		$contactsArray = array();

		if (!$moduleFilter || $moduleFilter == 'resources') {
			//get resource specific contacts first
			$query = "SELECT C.*, GROUP_CONCAT(CR.shortName SEPARATOR '<br /> ') contactRoles
				FROM Contact C, ContactRole CR, ContactRoleProfile CRP
				WHERE (archiveDate = '0000-00-00' OR archiveDate is null)
				AND C.contactID = CRP.contactID
				AND CRP.contactRoleID = CR.contactRoleID
				AND resourceID = '" . $this->resourceID . "'
				GROUP BY C.contactID
				ORDER BY C.name";

			$result = $this->db->processQuery($query, 'assoc');


			//need to do this since it could be that there's only one request and this is how the dbservice returns result
			if (isset($result['contactID'])){

				foreach (array_keys($result) as $attributeName) {
					$resultArray[$attributeName] = $result[$attributeName];
				}

				array_push($contactsArray, $resultArray);

			}else{
				foreach ($result as $row) {
					$resultArray = array();
					foreach (array_keys($row) as $attributeName) {
						$resultArray[$attributeName] = $row[$attributeName];
					}

					array_push($contactsArray, $resultArray);
				}
			}
		}


		//if the org module is installed also get the org contacts from org database
		if ($config->settings->organizationsModule == 'Y' && (!$moduleFilter || $moduleFilter == 'organizations')) {
			$dbName = $config->settings->organizationsDatabaseName;

			$query = "SELECT distinct OC.*, O.name organizationName, GROUP_CONCAT(DISTINCT CR.shortName SEPARATOR '<br /> ') contactRoles
					FROM " . $dbName . ".Contact OC, " . $dbName . ".ContactRole CR, " . $dbName . ".ContactRoleProfile CRP, " . $dbName . ".Organization O, Resource R, ResourceOrganizationLink ROL
					WHERE (OC.archiveDate = '0000-00-00' OR OC.archiveDate is null)
					AND R.resourceID = ROL.resourceID
					AND ROL.organizationID = OC.organizationID
					AND CRP.contactID = OC.contactID
					AND CRP.contactRoleID = CR.contactRoleID
					AND O.organizationID = OC.organizationID
					AND R.resourceID = '" . $this->resourceID . "'
					GROUP BY OC.contactID, O.name
					ORDER BY OC.name";

			$result = $this->db->processQuery($query, 'assoc');


			//need to do this since it could be that there's only one request and this is how the dbservice returns result
			if (isset($result['contactID'])){

				foreach (array_keys($result) as $attributeName) {
					$resultArray[$attributeName] = $result[$attributeName];
				}

				array_push($contactsArray, $resultArray);

			}else{
				foreach ($result as $row) {
					$resultArray = array();
					foreach (array_keys($row) as $attributeName) {
						$resultArray[$attributeName] = $row[$attributeName];
					}

					array_push($contactsArray, $resultArray);
				}
			}



		}


		return $contactsArray;
	}




	//returns array of contact objects
	public function getArchivedContacts(){

		$config = new Configuration;
		$contactsArray = array();

		//get resource specific contacts
		$query = "SELECT C.*, GROUP_CONCAT(CR.shortName SEPARATOR '<br /> ') contactRoles
			FROM Contact C, ContactRole CR, ContactRoleProfile CRP
			WHERE (archiveDate != '0000-00-00' && archiveDate != '')
			AND C.contactID = CRP.contactID
			AND CRP.contactRoleID = CR.contactRoleID
			AND resourceID = '" . $this->resourceID . "'
			GROUP BY C.contactID
			ORDER BY C.name";

		$result = $this->db->processQuery($query, 'assoc');


		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['contactID'])){

			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

			array_push($contactsArray, $resultArray);

		}else{
			foreach ($result as $row) {
				$resultArray = array();
				foreach (array_keys($row) as $attributeName) {
					$resultArray[$attributeName] = $row[$attributeName];
				}

				array_push($contactsArray, $resultArray);
			}
		}



		//if the org module is installed also get the org contacts from org database
		if ($config->settings->organizationsModule == 'Y'){
			$dbName = $config->settings->organizationsDatabaseName;

			$query = "SELECT DISTINCT OC.*, O.name organizationName, GROUP_CONCAT(DISTINCT CR.shortName SEPARATOR '<br /> ') contactRoles
					FROM " . $dbName . ".Contact OC, " . $dbName . ".ContactRole CR, " . $dbName . ".ContactRoleProfile CRP, " . $dbName . ".Organization O, Resource R, ResourceOrganizationLink ROL
					WHERE (OC.archiveDate != '0000-00-00' && OC.archiveDate is not null)
					AND R.resourceID = ROL.resourceID
					AND ROL.organizationID = OC.organizationID
					AND CRP.contactID = OC.contactID
					AND CRP.contactRoleID = CR.contactRoleID
					AND O.organizationID = OC.organizationID
					AND R.resourceID = '" . $this->resourceID . "'
					GROUP BY OC.contactID, O.name
					ORDER BY OC.name";


			$result = $this->db->processQuery($query, 'assoc');


			//need to do this since it could be that there's only one request and this is how the dbservice returns result
			if (isset($result['contactID'])){

				foreach (array_keys($result) as $attributeName) {
					$resultArray[$attributeName] = $result[$attributeName];
				}

				array_push($contactsArray, $resultArray);

			}else{
				foreach ($result as $row) {
					$resultArray = array();
					foreach (array_keys($row) as $attributeName) {
						$resultArray[$attributeName] = $row[$attributeName];
					}

					array_push($contactsArray, $resultArray);
				}
			}



		}

		return $contactsArray;



	}





	//returns array of contact objects
	public function getCreatorsArray(){

		$creatorsArray = array();
		$resultArray = array();

		//get resource specific creators
		$query = "SELECT distinct loginID, firstName, lastName
			FROM Resource R, User U
			WHERE U.loginID = R.createLoginID
			ORDER BY lastName, firstName, loginID";

		$result = $this->db->processQuery($query, 'assoc');


		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['loginID'])){

			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

			array_push($creatorsArray, $resultArray);

		}else{
			foreach ($result as $row) {
				$resultArray = array();
				foreach (array_keys($row) as $attributeName) {
					$resultArray[$attributeName] = $row[$attributeName];
				}

				array_push($creatorsArray, $resultArray);
			}
		}

		return $creatorsArray;


	}




	//returns array of external login records
	public function getExternalLoginArray(){


		$config = new Configuration;
		$elArray = array();

		//get resource specific accounts first
		$query = "SELECT EL.*,  ELT.shortName externalLoginType
				FROM ExternalLogin EL, ExternalLoginType ELT
				WHERE EL.externalLoginTypeID = ELT.externalLoginTypeID
				AND resourceID = '" . $this->resourceID . "'
				ORDER BY ELT.shortName;";

		$result = $this->db->processQuery($query, 'assoc');


		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['externalLoginID'])){

			foreach (array_keys($result) as $attributeName) {
				$resultArray[$attributeName] = $result[$attributeName];
			}

			array_push($elArray, $resultArray);

		}else{
			foreach ($result as $row) {
				$resultArray = array();
				foreach (array_keys($row) as $attributeName) {
					$resultArray[$attributeName] = $row[$attributeName];
				}

				array_push($elArray, $resultArray);
			}
		}



		//if the org module is installed also get the external logins from org database
		if ($config->settings->organizationsModule == 'Y'){
			$dbName = $config->settings->organizationsDatabaseName;

			$query = "SELECT DISTINCT EL.*, ELT.shortName externalLoginType, O.name organizationName
						FROM " . $dbName . ".ExternalLogin EL, " . $dbName . ".ExternalLoginType ELT, " . $dbName . ".Organization O,
							Resource R, ResourceOrganizationLink ROL
						WHERE EL.externalLoginTypeID = ELT.externalLoginTypeID
						AND R.resourceID = ROL.resourceID
						AND ROL.organizationID = EL.organizationID
						AND O.organizationID = EL.organizationID
						AND R.resourceID = '" . $this->resourceID . "'
						ORDER BY ELT.shortName;";


			$result = $this->db->processQuery($query, 'assoc');


			//need to do this since it could be that there's only one request and this is how the dbservice returns result
			if (isset($result['externalLoginID'])){

				foreach (array_keys($result) as $attributeName) {
					$resultArray[$attributeName] = $result[$attributeName];
				}

				array_push($elArray, $resultArray);

			}else{
				foreach ($result as $row) {
					$resultArray = array();
					foreach (array_keys($row) as $attributeName) {
						$resultArray[$attributeName] = $row[$attributeName];
					}

					array_push($elArray, $resultArray);
				}
			}



		}



		return $elArray;
	}



	//returns array of notes objects
	public function getNotes($tabName = NULL){

		if ($tabName){
			$query = "SELECT * FROM ResourceNote RN
						WHERE resourceID = '" . $this->resourceID . "'
						AND UPPER(tabName) = UPPER('" . $tabName . "')
						ORDER BY updateDate desc";
		}else{
			$query = "SELECT RN.*
						FROM ResourceNote RN
						LEFT JOIN NoteType NT ON NT.noteTypeID = RN.noteTypeID
						WHERE resourceID = '" . $this->resourceID . "'
						ORDER BY updateDate desc, NT.shortName";
		}

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceNoteID'])){
			$object = new ResourceNote(new NamedArguments(array('primaryKey' => $result['resourceNoteID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new ResourceNote(new NamedArguments(array('primaryKey' => $row['resourceNoteID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}

	//returns array of the initial note object
	public function getInitialNote(){
		$noteType = new NoteType();

		$query = "SELECT * FROM ResourceNote RN
					WHERE resourceID = '" . $this->resourceID . "'
					AND noteTypeID = " . $noteType->getInitialNoteTypeID . "
					ORDER BY noteTypeID desc LIMIT 0,1";


		$result = $this->db->processQuery($query, 'assoc');

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceNoteID'])){
			$resourceNote = new ResourceNote(new NamedArguments(array('primaryKey' => $result['resourceNoteID'])));
			return $resourceNote;
		} else{
			$resourceNote = new ResourceNote();
			return $resourceNote;
		}

	}

	public function getIssues($archivedOnly=false){
		$query = "SELECT i.* 
				  FROM Issue i
				  LEFT JOIN IssueRelationship ir ON ir.issueID=i.issueID
				  WHERE ir.entityID={$this->resourceID} AND ir.entityTypeID=2";
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

	public function getDowntime($archivedOnly=false){
		$query = "SELECT d.*
				  FROM Downtime d
				  WHERE d.entityID={$this->resourceID} AND d.entityTypeID=2";
		if ($archivedOnly) {
			$query .= " AND d.endDate < CURDATE()";
		} else {
			$query .= " AND d.endDate >= CURDATE()";
		}
		$query .= "	ORDER BY d.dateCreated DESC";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['downtimeID'])){
			$object = new Downtime(new NamedArguments(array('primaryKey' => $result['downtimeID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new Downtime(new NamedArguments(array('primaryKey' => $row['downtimeID'])));
				array_push($objects, $object);
			}
		}
		return $objects;
	}

	public function getExportableIssues($archivedOnly=false){
		if ($this->db->config->settings->organizationsModule == 'Y' && $this->db->config->settings->organizationsDatabaseName) {
			$contactsDB = $this->db->config->settings->organizationsDatabaseName;
		} else {
			$contactsDB = $this->db->config->database->name;
		}

		$query = "SELECT i.*,(SELECT GROUP_CONCAT(CONCAT(sc.name,' - ',sc.emailAddress) SEPARATOR ', ')
								FROM IssueContact sic 
								LEFT JOIN `{$contactsDB}`.Contact sc ON sc.contactID=sic.contactID
								WHERE sic.issueID=i.issueID) AS `contacts`,
							 (SELECT GROUP_CONCAT(se.titleText SEPARATOR ', ')
								FROM IssueRelationship sir 
								LEFT JOIN Resource se ON (se.resourceID=sir.entityID AND sir.entityTypeID=2)
								WHERE sir.issueID=i.issueID) AS `appliesto`,
							 (SELECT GROUP_CONCAT(sie.email SEPARATOR ', ')
								FROM IssueEmail sie 
								WHERE sie.issueID=i.issueID) AS `CCs`
				  FROM Issue i
				  LEFT JOIN IssueRelationship ir ON ir.issueID=i.issueID
				  WHERE ir.entityID={$this->resourceID} AND ir.entityTypeID=2";
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

	public function getExportableDowntimes($archivedOnly=false){
		
		$query = "SELECT d.*
				  FROM Downtime d
				  WHERE d.entityID={$this->resourceID} AND d.entityTypeID=2";
		if ($archivedOnly) {
			$query .= " AND d.endDate < CURDATE()";
		} else {
			$query .= " AND d.endDate >= CURDATE()";
		}
		$query .= "	ORDER BY d.dateCreated DESC";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['downtimeID'])){
			return array($result);
		}else{
			return $result;
		}
	}

	//returns array of attachments objects
	public function getAttachments(){

		$query = "SELECT * FROM Attachment A, AttachmentType AT
					WHERE AT.attachmentTypeID = A.attachmentTypeID
					AND resourceID = '" . $this->resourceID . "'
					ORDER BY AT.shortName";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['attachmentID'])){
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




	//returns array of contact objects
	public function getContacts(){

		$query = "SELECT * FROM Contact
					WHERE resourceID = '" . $this->resourceID . "'";

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




	//returns array of externalLogin objects
	public function getExternalLogins(){

		$query = "SELECT * FROM ExternalLogin
					WHERE resourceID = '" . $this->resourceID . "'";

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


  public static function setSearch($search) {
	$config = new Configuration;

	  if ($config->settings->defaultsort) {
		$orderBy = $config->settings->defaultsort;
	  } else {
		$orderBy = "R.createDate DESC, TRIM(LEADING 'THE ' FROM UPPER(R.titleText)) asc";
	  }

    $defaultSearchParameters = array(
		"orderBy" => $orderBy,
		"page" => 1,
		"recordsPerPage" => 25,
    );
    foreach ($defaultSearchParameters as $key => $value) {
      if (!$search[$key]) {
        $search[$key] = $value;
      }
    }
    foreach ($search as $key => $value) {
    	$search[$key] = trim($value);
    }
    CoralSession::set('resourceSearch', $search);
  }

  public static function resetSearch() {
    Resource::setSearch(array());
  }

  public static function getSearch() {
    if (!CoralSession::get('resourceSearch')) {
      Resource::resetSearch();
    }
    return CoralSession::get('resourceSearch');
  }

  public static function getSearchDetails() {
    // A successful mysqli_connect must be run before mysqli_real_escape_string will function.  Instantiating a resource model will set up the connection
    $resource = new Resource();

    $search = Resource::getSearch();

		$whereAdd = array();
		$searchDisplay = array();
		$config = new Configuration();


		//if name is passed in also search alias, organizations and organization aliases
		if ($search['name']) {
			$nameQueryString = $resource->db->escapeString(strtoupper($search['name']));
			$nameQueryString = preg_replace("/ +/", "%", $nameQueryString);
		  $nameQueryString = "'%" . $nameQueryString . "%'";

			if ($config->settings->organizationsModule == 'Y'){
				$dbName = $config->settings->organizationsDatabaseName;

				$whereAdd[] = "((UPPER(R.titleText) LIKE " . $nameQueryString . ") OR (UPPER(A.shortName) LIKE " . $nameQueryString . ") OR (UPPER(O.name) LIKE " . $nameQueryString . ") OR (UPPER(OA.name) LIKE " . $nameQueryString . ") OR (UPPER(RP.titleText) LIKE " . $nameQueryString . ") OR (UPPER(RC.titleText) LIKE " . $nameQueryString . ") OR (UPPER(R.recordSetIdentifier) LIKE " . $nameQueryString . "))";

			}else{

				$whereAdd[] = "((UPPER(R.titleText) LIKE " . $nameQueryString . ") OR (UPPER(A.shortName) LIKE " . $nameQueryString . ") OR (UPPER(O.shortName) LIKE " . $nameQueryString . ") OR (UPPER(RP.titleText) LIKE " . $nameQueryString . ") OR (UPPER(RC.titleText) LIKE " . $nameQueryString . ") OR (UPPER(R.recordSetIdentifier) LIKE " . $nameQueryString . "))";

			}

			$searchDisplay[] = "Name contains: " . $search['name'];
		}

		//get where statements together (and escape single quotes)
		if ($search['resourceID']) {
		  $whereAdd[] = "R.resourceID = '" . $resource->db->escapeString($search['resourceID']) . "'";
		  $searchDisplay[] = "Resource ID: " . $search['resourceID'];
	  }
		if ($search['resourceISBNOrISSN']) {
		  $resourceISBNOrISSN = $resource->db->escapeString(str_replace("-","",$search['resourceISBNOrISSN']));
		  $whereAdd[] = "REPLACE(I.isbnOrIssn,'-','') = '" . $resourceISBNOrISSN . "'";
		  $searchDisplay[] = "ISSN/ISBN: " . $search['resourceISBNOrISSN'];
		}
		if ($search['fund']) {
		  $fund = $resource->db->escapeString(str_replace("-","",$search['fund']));
		  $whereAdd[] = "REPLACE(RPAY.fundName,'-','') = '" . $fund . "'";
		  $searchDisplay[] = "Fund: " . $search['fund'];
	  }

    if ($search['stepName']) {
      $status = new Status();
      $completedStatusID = $status->getIDFromName('complete');
      $whereAdd[] = "(R.statusID != $completedStatusID AND RS.stepName = '" . $resource->db->escapeString($search['stepName']) . "' AND RS.stepStartDate IS NOT NULL AND RS.stepEndDate IS NULL)";
      $searchDisplay[] = "Routing Step: " . $search['stepName'];
    }


    if ($search['parent'] != null) {
      $parentadd = "(" . $search['parent'] . ".relationshipTypeID = 1";
      $parentadd .= ")";
      $whereAdd[] = $parentadd;
    }



		if ($search['statusID']) {
		  $whereAdd[] = "R.statusID = '" . $resource->db->escapeString($search['statusID']) . "'";
		  $status = new Status(new NamedArguments(array('primaryKey' => $search['statusID'])));
    	$searchDisplay[] = "Status: " . $status->shortName;
	  }

		if ($search['creatorLoginID']) {
		  $whereAdd[] = "R.createLoginID = '" . $resource->db->escapeString($search['creatorLoginID']) . "'";

		  $createUser = new User(new NamedArguments(array('primaryKey' => $search['creatorLoginID'])));
    	if ($createUser->firstName){
    		$name = $createUser->lastName . ", " . $createUser->firstName;
    	}else{
    		$name = $createUser->loginID;
    	}
    	$searchDisplay[] = "Creator: " . $name;
	  }

		if ($search['resourceFormatID']) {
		  $whereAdd[] = "R.resourceFormatID = '" . $resource->db->escapeString($search['resourceFormatID']) . "'";
		  $resourceFormat = new ResourceFormat(new NamedArguments(array('primaryKey' => $search['resourceFormatID'])));
    	$searchDisplay[] = "Resource Format: " . $resourceFormat->shortName;
	  }

		if ($search['acquisitionTypeID']) {
		  $whereAdd[] = "R.acquisitionTypeID = '" . $resource->db->escapeString($search['acquisitionTypeID']) . "'";
		  $acquisitionType = new AcquisitionType(new NamedArguments(array('primaryKey' => $search['acquisitionTypeID'])));
    	$searchDisplay[] = "Acquisition Type: " . $acquisitionType->shortName;
	  }


		if ($search['resourceNote']) {
		  $whereAdd[] = "UPPER(RN.noteText) LIKE UPPER('%" . $resource->db->escapeString($search['resourceNote']) . "%')";
		  $searchDisplay[] = "Note contains: " . $search['resourceNote'];
	  }

		if ($search['createDateStart']) {
		  $whereAdd[] = "R.createDate >= STR_TO_DATE('" . $resource->db->escapeString($search['createDateStart']) . "','%m/%d/%Y')";
		  if (!$search['createDateEnd']) {
		    $searchDisplay[] = "Created on or after: " . $search['createDateStart'];
	    } else {
	      $searchDisplay[] = "Created between: " . $search['createDateStart'] . " and " . $search['createDateEnd'];
	    }
	  }

		if ($search['createDateEnd']) {
		  $whereAdd[] = "R.createDate <= STR_TO_DATE('" . $resource->db->escapeString($search['createDateEnd']) . "','%m/%d/%Y')";
		  if (!$search['createDateStart']) {
		    $searchDisplay[] = "Created on or before: " . $search['createDateEnd'];
	    }
	  }

		if ($search['startWith']) {
		  $whereAdd[] = "TRIM(LEADING 'THE ' FROM UPPER(R.titleText)) LIKE UPPER('" . $resource->db->escapeString($search['startWith']) . "%')";
		  $searchDisplay[] = "Starts with: " . $search['startWith'];
	  }

		//the following are not-required fields with dropdowns and have "none" as an option
		if ($search['resourceTypeID'] == 'none'){
			$whereAdd[] = "((R.resourceTypeID IS NULL) OR (R.resourceTypeID = '0'))";
			$searchDisplay[] = "Resource Type: none";
		}else if ($search['resourceTypeID']){
			$whereAdd[] = "R.resourceTypeID = '" . $resource->db->escapeString($search['resourceTypeID']) . "'";
			$resourceType = new ResourceType(new NamedArguments(array('primaryKey' => $search['resourceTypeID'])));
    	$searchDisplay[] = "Resource Type: " . $resourceType->shortName;
		}


		if ($search['generalSubjectID'] == 'none'){
			$whereAdd[] = "((GDLINK.generalSubjectID IS NULL) OR (GDLINK.generalSubjectID = '0'))";
			$searchDisplay[] = "Resource Type: none";
		}else if ($search['generalSubjectID']){
			$whereAdd[] = "GDLINK.generalSubjectID = '" . $resource->db->escapeString($search['generalSubjectID']) . "'";
			$generalSubject = new GeneralSubject(new NamedArguments(array('primaryKey' => $search['generalSubjectID'])));
    	$searchDisplay[] = "General Subject: " . $generalSubject->shortName;
		}

		if ($search['detailedSubjectID'] == 'none'){
			$whereAdd[] = "((GDLINK.detailedSubjectID IS NULL) OR (GDLINK.detailedSubjectID = '0') OR (GDLINK.detailedSubjectID = '-1'))";
			$searchDisplay[] = "Resource Type: none";
		}else if ($search['detailedSubjectID']){
			$whereAdd[] = "GDLINK.detailedSubjectID = '" . $resource->db->escapeString($search['detailedSubjectID']) . "'";
			$detailedSubject = new DetailedSubject(new NamedArguments(array('primaryKey' => $search['detailedSubjectID'])));
    	$searchDisplay[] = "Detailed Subject: " . $detailedSubject->shortName;
		}

		if ($search['noteTypeID'] == 'none'){
			$whereAdd[] = "(RN.noteTypeID IS NULL) AND (RN.noteText IS NOT NULL)";
			$searchDisplay[] = "Note Type: none";
		}else if ($search['noteTypeID']){
			$whereAdd[] = "RN.noteTypeID = '" . $resource->db->escapeString($search['noteTypeID']) . "'";
			$noteType = new NoteType(new NamedArguments(array('primaryKey' => $search['noteTypeID'])));
    	$searchDisplay[] = "Note Type: " . $noteType->shortName;
		}


		if ($search['purchaseSiteID'] == 'none'){
			$whereAdd[] = "RPSL.purchaseSiteID IS NULL";
			$searchDisplay[] = "Purchase Site: none";
		}else if ($search['purchaseSiteID']){
			$whereAdd[] = "RPSL.purchaseSiteID = '" . $resource->db->escapeString($search['purchaseSiteID']) . "'";
			$purchaseSite = new PurchaseSite(new NamedArguments(array('primaryKey' => $search['purchaseSiteID'])));
    	$searchDisplay[] = "Purchase Site: " . $purchaseSite->shortName;
		}


		if ($search['authorizedSiteID'] == 'none'){
			$whereAdd[] = "RAUSL.authorizedSiteID IS NULL";
			$searchDisplay[] = "Authorized Site: none";
		}else if ($search['authorizedSiteID']){
			$whereAdd[] = "RAUSL.authorizedSiteID = '" . $resource->db->escapeString($search['authorizedSiteID']) . "'";
			$authorizedSite = new AuthorizedSite(new NamedArguments(array('primaryKey' => $search['authorizedSiteID'])));
    	$searchDisplay[] = "Authorized Site: " . $authorizedSite->shortName;
		}


		if ($search['administeringSiteID'] == 'none'){
			$whereAdd[] = "RADSL.administeringSiteID IS NULL";
			$searchDisplay[] = "Administering Site: none";
		}else if ($search['administeringSiteID']){
			$whereAdd[] = "RADSL.administeringSiteID = '" . $resource->db->escapeString($search['administeringSiteID']) . "'";
			$administeringSite = new AdministeringSite(new NamedArguments(array('primaryKey' => $search['administeringSiteID'])));
    	$searchDisplay[] = "Administering Site: " . $administeringSite->shortName;
		}


		if ($search['authenticationTypeID'] == 'none'){
			$whereAdd[] = "R.authenticationTypeID IS NULL";
			$searchDisplay[] = "Authentication Type: none";
		}else if ($search['authenticationTypeID']){
			$whereAdd[] = "R.authenticationTypeID = '" . $resource->db->escapeString($search['authenticationTypeID']) . "'";
			$authenticationType = new AuthenticationType(new NamedArguments(array('primaryKey' => $search['authenticationTypeID'])));
			$searchDisplay[] = "Authentication Type: " . $authenticationType->shortName;
		}

		if ($search['catalogingStatusID'] == 'none') {
		  $whereAdd[] = "(R.catalogingStatusID IS NULL)";
		  $searchDisplay[] = "Cataloging Status: none";
		} else if ($search['catalogingStatusID']) {
			$whereAdd[] = "R.catalogingStatusID = '" . $resource->db->escapeString($search['catalogingStatusID']) . "'";
			$catalogingStatus = new CatalogingStatus(new NamedArguments(array('primaryKey' => $search['catalogingStatusID'])));
		  $searchDisplay[] = "Cataloging Status: " . $catalogingStatus->shortName;
	  }



		$orderBy = $search['orderBy'];


		$page = $search['page'];
		$recordsPerPage = $search['recordsPerPage'];
		return array("where" => $whereAdd, "page" => $page, "order" => $orderBy, "perPage" => $recordsPerPage, "display" => $searchDisplay);
  }

  public function searchQuery($whereAdd, $orderBy = '', $limit = '', $count = false) {
  	$config = new Configuration();
		$status = new Status();

		if ($config->settings->organizationsModule == 'Y'){
			$dbName = $config->settings->organizationsDatabaseName;

			$orgJoinAdd = "LEFT JOIN " . $dbName . ".Organization O ON O.organizationID = ROL.organizationID
						   LEFT JOIN " . $dbName . ".Alias OA ON OA.organizationID = ROL.organizationID";

		}else{
			$orgJoinAdd = "LEFT JOIN Organization O ON O.organizationID = ROL.organizationID";
		}

    $savedStatusID = intval($status->getIDFromName('saved'));
		//also add to not retrieve saved records
		$whereAdd[] = "R.statusID != " . $savedStatusID;

		if (count($whereAdd) > 0){
			$whereStatement = " WHERE " . implode(" AND ", $whereAdd);
		}else{
			$whereStatement = "";
		}

		if ($count) {
      $select = "SELECT COUNT(DISTINCT R.resourceID) count";
      $groupBy = "";
    } else {
      $select = "SELECT R.resourceID, R.titleText, AT.shortName acquisitionType, R.createLoginID, CU.firstName, CU.lastName, R.createDate, S.shortName status,
						GROUP_CONCAT(DISTINCT A.shortName, I.isbnOrIssn ORDER BY A.shortName DESC SEPARATOR '<br />') aliases";
      $groupBy = "GROUP BY R.resourceID";
    }

    $referenced_tables = array();

    $table_matches = array();

    // Build a list of tables that are referenced by the select and where statements in order to limit the number of joins performed in the search.
    preg_match_all("/[A-Z]+(?=[.][A-Z]+)/i", $select, $table_matches);
    $referenced_tables = array_unique($table_matches[0]);

    preg_match_all("/[A-Z]+(?=[.][A-Z]+)/i", $whereStatement, $table_matches);
    $referenced_tables = array_unique(array_merge($referenced_tables, $table_matches[0]));

    // These join statements will only be included in the query if the alias is referenced by the select and/or where.
    $conditional_joins = explode("\n", "LEFT JOIN ResourceFormat RF ON R.resourceFormatID = RF.resourceFormatID
									LEFT JOIN ResourceType RT ON R.resourceTypeID = RT.resourceTypeID
									LEFT JOIN AcquisitionType AT ON R.acquisitionTypeID = AT.acquisitionTypeID
									LEFT JOIN Status S ON R.statusID = S.statusID
									LEFT JOIN User CU ON R.createLoginID = CU.loginID
									LEFT JOIN ResourcePurchaseSiteLink RPSL ON R.resourceID = RPSL.resourceID
									LEFT JOIN ResourceAuthorizedSiteLink RAUSL ON R.resourceID = RAUSL.resourceID
									LEFT JOIN ResourceAdministeringSiteLink RADSL ON R.resourceID = RADSL.resourceID
									LEFT JOIN ResourcePayment RPAY ON R.resourceID = RPAY.resourceID
									LEFT JOIN ResourceNote RN ON R.resourceID = RN.resourceID
									LEFT JOIN ResourceStep RS ON R.resourceID = RS.resourceID
                  LEFT JOIN IsbnOrIssn I ON R.resourceID = I.resourceID
                  ");

		$additional_joins = array();

		foreach($conditional_joins as $join) {
			$match = array();
			preg_match("/[A-Z]+(?= ON )/i", $join, $match);
			$table_name = $match[0];
			if (in_array($table_name, $referenced_tables)) {
        $additional_joins[] = $join;
      }
		}

		$query = $select . "
								FROM Resource R
									LEFT JOIN Alias A ON R.resourceID = A.resourceID
									LEFT JOIN ResourceOrganizationLink ROL ON R.resourceID = ROL.resourceID
									" . $orgJoinAdd . "
									LEFT JOIN ResourceRelationship RRC ON RRC.relatedResourceID = R.resourceID
									LEFT JOIN ResourceRelationship RRP ON RRP.resourceID = R.resourceID
									LEFT JOIN ResourceSubject RSUB ON R.resourceID = RSUB.resourceID
									LEFT JOIN Resource RC ON RC.resourceID = RRC.resourceID
									LEFT JOIN Resource RP ON RP.resourceID = RRP.relatedResourceID
									LEFT JOIN GeneralDetailSubjectLink GDLINK ON RSUB.generalDetailSubjectLinkID = GDLINK.generalDetailSubjectLinkID
                  " . implode("\n", $additional_joins) . "
								  " . $whereStatement . "
								  " . $groupBy;

		if ($orderBy) {
		  $query .= "\nORDER BY " . $orderBy;
		}

		if ($limit) {
  	  $query .= "\nLIMIT " . $limit;
		}
		return $query;
  }

	//returns array based on search
	public function search($whereAdd, $orderBy, $limit){
		$query = $this->searchQuery($whereAdd, $orderBy, $limit, false);

		$result = $this->db->processQuery($query, 'assoc');

		$searchArray = array();
		$resultArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['resourceID'])){

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

	public function searchCount($whereAdd) {
    $query = $this->searchQuery($whereAdd, '', '', true);
    $result = $this->db->processQuery($query, 'assoc');

	//echo $query;

    return $result['count'];
  }


	//used for A-Z on search (index)
	public function getAlphabeticalList(){
		$alphArray = array();
		$result = $this->db->query("SELECT DISTINCT UPPER(SUBSTR(TRIM(LEADING 'The ' FROM titleText),1,1)) letter, COUNT(SUBSTR(TRIM(LEADING 'The ' FROM titleText),1,1)) letter_count
								FROM Resource R
								GROUP BY SUBSTR(TRIM(LEADING 'The ' FROM titleText),1,1)
								ORDER BY 1;");

		while ($row = $result->fetch_assoc()){
			$alphArray[$row['letter']] = $row['letter_count'];
		}

		return $alphArray;
	}





	//returns array based on search for excel output (export.php)
	public function export($whereAdd, $orderBy){

		$config = new Configuration();

		if ($config->settings->organizationsModule == 'Y'){
			$dbName = $config->settings->organizationsDatabaseName;

			$orgJoinAdd = "LEFT JOIN " . $dbName . ".Organization O ON O.organizationID = ROL.organizationID
						   LEFT JOIN " . $dbName . ".Alias OA ON OA.organizationID = ROL.organizationID";

			$orgSelectAdd = "GROUP_CONCAT(DISTINCT O.name ORDER BY O.name DESC SEPARATOR '; ') organizationNames";
		}else{
			$orgJoinAdd = "LEFT JOIN Organization O ON O.organizationID = ROL.organizationID";

			$orgSelectAdd = "GROUP_CONCAT(DISTINCT O.shortName ORDER BY O.shortName DESC SEPARATOR '; ') organizationNames";
		}


		$licSelectAdd = '';
		$licJoinAdd = '';
		if ($config->settings->licensingModule == 'Y'){
			$dbName = $config->settings->licensingDatabaseName;

			$licJoinAdd = " LEFT JOIN ResourceLicenseLink RLL ON RLL.resourceID = R.resourceID
							LEFT JOIN " . $dbName . ".License L ON RLL.licenseID = L.licenseID
							LEFT JOIN ResourceLicenseStatus RLS ON RLS.resourceID = R.resourceID
							LEFT JOIN LicenseStatus LS ON LS.licenseStatusID = RLS.licenseStatusID";

			$licSelectAdd = "GROUP_CONCAT(DISTINCT L.shortName ORDER BY L.shortName DESC SEPARATOR '; ') licenseNames,
			        GROUP_CONCAT(DISTINCT LS.shortName, ': ', DATE_FORMAT(RLS.licenseStatusChangeDate, '%m/%d/%Y') ORDER BY RLS.licenseStatusChangeDate DESC SEPARATOR '; ') licenseStatuses, ";

		}


    $status = new Status();
		//also add to not retrieve saved records
		$savedStatusID = intval($status->getIDFromName('saved'));
		$whereAdd[] = "R.statusID != " . $savedStatusID;

		if (count($whereAdd) > 0){
			$whereStatement = " WHERE " . implode(" AND ", $whereAdd);
		}else{
			$whereStatement = "";
		}


		//now actually execute query
		$query = "SELECT R.resourceID, R.titleText, AT.shortName acquisitionType, CONCAT_WS(' ', CU.firstName, CU.lastName) createName,
						R.createDate createDate, CONCAT_WS(' ', UU.firstName, UU.lastName) updateName,
						R.updateDate updateDate, S.shortName status,
						RT.shortName resourceType, RF.shortName resourceFormat, R.orderNumber, R.systemNumber, R.resourceURL, R.resourceAltURL,
						R.currentStartDate, R.currentEndDate, R.subscriptionAlertEnabledInd, AUT.shortName authenticationType,
						AM.shortName accessMethod, SL.shortName storageLocation, UL.shortName userLimit, R.authenticationUserName,
						R.authenticationPassword, R.coverageText, CT.shortName catalogingType, CS.shortName catalogingStatus, R.recordSetIdentifier, R.bibSourceURL,
						R.numberRecordsAvailable, R.numberRecordsLoaded, R.hasOclcHoldings, I.isbnOrIssn,
						" . $orgSelectAdd . ",
						" . $licSelectAdd . "
						GROUP_CONCAT(DISTINCT A.shortName ORDER BY A.shortName DESC SEPARATOR '; ') aliases,
						GROUP_CONCAT(DISTINCT PS.shortName ORDER BY PS.shortName DESC SEPARATOR '; ') purchasingSites,
						GROUP_CONCAT(DISTINCT AUS.shortName ORDER BY AUS.shortName DESC SEPARATOR '; ') authorizedSites,
						GROUP_CONCAT(DISTINCT ADS.shortName ORDER BY ADS.shortName DESC SEPARATOR '; ') administeringSites,
						GROUP_CONCAT(DISTINCT RP.titleText ORDER BY RP.titleText DESC SEPARATOR '; ') parentResources,
						GROUP_CONCAT(DISTINCT RC.titleText ORDER BY RC.titleText DESC SEPARATOR '; ') childResources,
						GROUP_CONCAT(DISTINCT RPAY.fundName, ': ', ROUND(COALESCE(RPAY.paymentAmount, 0) / 100, 2), ' ', RPAY.currencyCode, ' ', OT.shortName ORDER BY RPAY.paymentAmount ASC SEPARATOR '; ') payments
								FROM Resource R
									LEFT JOIN Alias A ON R.resourceID = A.resourceID
									LEFT JOIN ResourceOrganizationLink ROL ON R.resourceID = ROL.resourceID
									" . $orgJoinAdd . "
									LEFT JOIN ResourceRelationship RRC ON RRC.relatedResourceID = R.resourceID
									LEFT JOIN ResourceRelationship RRP ON RRP.resourceID = R.resourceID
									LEFT JOIN Resource RC ON RC.resourceID = RRC.resourceID
									LEFT JOIN ResourceSubject RSUB ON R.resourceID = RSUB.resourceID
									LEFT JOIN Resource RP ON RP.resourceID = RRP.relatedResourceID
									LEFT JOIN GeneralDetailSubjectLink GDLINK ON RSUB.generalDetailSubjectLinkID = GDLINK.generalDetailSubjectLinkID
									LEFT JOIN ResourceFormat RF ON R.resourceFormatID = RF.resourceFormatID
									LEFT JOIN ResourceType RT ON R.resourceTypeID = RT.resourceTypeID
									LEFT JOIN AcquisitionType AT ON R.acquisitionTypeID = AT.acquisitionTypeID
									LEFT JOIN ResourceStep RS ON R.resourceID = RS.resourceID
									LEFT JOIN ResourcePayment RPAY ON R.resourceID = RPAY.resourceID
									LEFT JOIN OrderType OT ON RPAY.orderTypeID = OT.orderTypeID
									LEFT JOIN Status S ON R.statusID = S.statusID
									LEFT JOIN ResourceNote RN ON R.resourceID = RN.resourceID
									LEFT JOIN NoteType NT ON RN.noteTypeID = NT.noteTypeID
									LEFT JOIN User CU ON R.createLoginID = CU.loginID
									LEFT JOIN User UU ON R.updateLoginID = UU.loginID
									LEFT JOIN CatalogingStatus CS ON R.catalogingStatusID = CS.catalogingStatusID
									LEFT JOIN CatalogingType CT ON R.catalogingTypeID = CT.catalogingTypeID
									LEFT JOIN ResourcePurchaseSiteLink RPSL ON R.resourceID = RPSL.resourceID
									LEFT JOIN PurchaseSite PS ON RPSL.purchaseSiteID = PS.purchaseSiteID
									LEFT JOIN ResourceAuthorizedSiteLink RAUSL ON R.resourceID = RAUSL.resourceID
									LEFT JOIN AuthorizedSite AUS ON RAUSL.authorizedSiteID = AUS.authorizedSiteID
									LEFT JOIN ResourceAdministeringSiteLink RADSL ON R.resourceID = RADSL.resourceID
									LEFT JOIN AdministeringSite ADS ON RADSL.administeringSiteID = ADS.administeringSiteID
									LEFT JOIN AuthenticationType AUT ON AUT.authenticationTypeID = R.authenticationTypeID
									LEFT JOIN AccessMethod AM ON AM.accessMethodID = R.accessMethodID
									LEFT JOIN StorageLocation SL ON SL.storageLocationID = R.storageLocationID
									LEFT JOIN UserLimit UL ON UL.userLimitID = R.userLimitID
                  LEFT JOIN IsbnOrIssn I ON I.resourceID = R.resourceID
									" . $licJoinAdd . "
								" . $whereStatement . "
								GROUP BY R.resourceID
								ORDER BY " . $orderBy;

		$result = $this->db->processQuery(stripslashes($query), 'assoc');

		$searchArray = array();
		$resultArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['resourceID'])){

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







	//search used index page drop down
	public function getOrganizationList(){
		$config = new Configuration;

		$orgArray = array();

		//if the org module is installed get the org names from org database
		if ($config->settings->organizationsModule == 'Y'){
			$dbName = $config->settings->organizationsDatabaseName;
			$query = "SELECT name, organizationID FROM " . $dbName . ".Organization ORDER BY 1;";

		//otherwise get the orgs from this database
		}else{
			$query = "SELECT shortName name, organizationID FROM Organization ORDER BY 1;";
		}


		$result = $this->db->processQuery($query, 'assoc');

		$resultArray = array();

		//need to do this since it could be that there's only one result and this is how the dbservice returns result
		if (isset($result['organizationID'])){

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



	//gets an array of organizations set up for this resource (organizationID, organization, organizationRole)
	public function getOrganizationArray(){
		$config = new Configuration;

		//if the org module is installed get the org name from org database
		if ($config->settings->organizationsModule == 'Y'){
			$dbName = $config->settings->organizationsDatabaseName;

			$resourceOrgArray = array();

			$query = "SELECT * FROM ResourceOrganizationLink WHERE resourceID = '" . $this->resourceID . "'";

			$result = $this->db->processQuery($query, 'assoc');

			$objects = array();

			//need to do this since it could be that there's only one request and this is how the dbservice returns result
			if (isset($result['organizationID'])){
				$orgArray = array();

				//first, get the organization name
				$query = "SELECT name FROM " . $dbName . ".Organization WHERE organizationID = " . $result['organizationID'];

				if ($orgResult = $this->db->query($query)){
					while ($orgRow = $orgResult->fetch_assoc()){
						$orgArray['organization'] = $orgRow['name'];
						$orgArray['organizationID'] = $result['organizationID'];
					}
				}

				//then, get the role name
				$query = "SELECT * FROM " . $dbName . ".OrganizationRole WHERE organizationRoleID = " . $result['organizationRoleID'];

				if ($orgResult = $this->db->query($query)){
					while ($orgRow = $orgResult->fetch_assoc()){
						$orgArray['organizationRoleID'] = $orgRow['organizationRoleID'];
						$orgArray['organizationRole'] = $orgRow['shortName'];
					}
				}

				array_push($resourceOrgArray, $orgArray);
			}else{
				foreach ($result as $row) {

					$orgArray = array();

					//first, get the organization name
					$query = "SELECT name FROM " . $dbName . ".Organization WHERE organizationID = " . $row['organizationID'];

					if ($orgResult = $this->db->query($query)){
						while ($orgRow = $orgResult->fetch_assoc()){
							$orgArray['organization'] = $orgRow['name'];
							$orgArray['organizationID'] = $row['organizationID'];
						}
					}

					//then, get the role name
					$query = "SELECT * FROM " . $dbName . ".OrganizationRole WHERE organizationRoleID = " . $row['organizationRoleID'];


					if ($orgResult = $this->db->query($query)){
						while ($orgRow = $orgResult->fetch_assoc()){
							$orgArray['organizationRoleID'] = $orgRow['organizationRoleID'];
							$orgArray['organizationRole'] = $orgRow['shortName'];
						}
					}

					array_push($resourceOrgArray, $orgArray);

				}

			}






		//otherwise if the org module is not installed get the org name from this database
		}else{



			$resourceOrgArray = array();

			$query = "SELECT * FROM ResourceOrganizationLink WHERE resourceID = '" . $this->resourceID . "'";

			$result = $this->db->processQuery($query, 'assoc');

			$objects = array();

			//need to do this since it could be that there's only one request and this is how the dbservice returns result
			if (isset($result['organizationID'])){
				$orgArray = array();

				//first, get the organization name
				$query = "SELECT shortName FROM Organization WHERE organizationID = " . $result['organizationID'];

				if ($orgResult = $this->db->query($query)){
					while ($orgRow = $orgResult->fetch_assoc()){
						$orgArray['organization'] = $orgRow['shortName'];
						$orgArray['organizationID'] = $result['organizationID'];
					}
				}

				//then, get the role name
				$query = "SELECT * FROM OrganizationRole WHERE organizationRoleID = " . $result['organizationRoleID'];

				if ($orgResult = $this->db->query($query)){
					while ($orgRow = $orgResult->fetch_assoc()){
						$orgArray['organizationRoleID'] = $orgRow['organizationRoleID'];
						$orgArray['organizationRole'] = $orgRow['shortName'];
					}
				}

				array_push($resourceOrgArray, $orgArray);
			}else{
				foreach ($result as $row) {

					$orgArray = array();

					//first, get the organization name
					$query = "SELECT shortName FROM Organization WHERE organizationID = " . $row['organizationID'];

					if ($orgResult = $this->db->query($query)){
						while ($orgRow = $orgResult->fetch_assoc()){
							$orgArray['organization'] = $orgRow['shortName'];
							$orgArray['organizationID'] = $row['organizationID'];
						}
					}

					//then, get the role name
					$query = "SELECT * FROM OrganizationRole WHERE organizationRoleID = " . $row['organizationRoleID'];


					if ($orgResult = $this->db->query($query)){
						while ($orgRow = $orgResult->fetch_assoc()){
							$orgArray['organizationRoleID'] = $orgRow['organizationRoleID'];
							$orgArray['organizationRole'] = $orgRow['shortName'];
						}
					}

					array_push($resourceOrgArray, $orgArray);

				}

			}

		}


		return $resourceOrgArray;
	}

	public function getSiblingResourcesArray($organizationID) {

			$query = "SELECT DISTINCT r.resourceID, r.titleText FROM ResourceOrganizationLink rol 
					  LEFT JOIN Resource r ON r.resourceID=rol.resourceID
					  WHERE rol.organizationID=".$organizationID." AND r.archiveDate IS NULL
					  ORDER BY r.titleText";

			$result = $this->db->processQuery($query, 'assoc');

			if($result["resourceID"]) {
				return array($result);
			}
					  
			return $result;
	}

	//gets an array of distinct organizations set up for this resource (organizationID, organization)
	public function getDistinctOrganizationArray(){
		$config = new Configuration;

		//if the org module is installed get the org name from org database
		if ($config->settings->organizationsModule == 'Y'){
			$dbName = $config->settings->organizationsDatabaseName;

			$resourceOrgArray = array();

			$query = "SELECT DISTINCT organizationID FROM ResourceOrganizationLink WHERE resourceID = '" . $this->resourceID . "'";

			$result = $this->db->processQuery($query, 'assoc');

			$objects = array();

			//need to do this since it could be that there's only one request and this is how the dbservice returns result
			if (isset($result['organizationID'])){
				$orgArray = array();

				//first, get the organization name
				$query = "SELECT name FROM " . $dbName . ".Organization WHERE organizationID = " . $result['organizationID'];

				if ($orgResult = $this->db->query($query)){
					while ($orgRow = $orgResult->fetch_assoc()){
						$orgArray['organization'] = $orgRow['name'];
						$orgArray['organizationID'] = $result['organizationID'];
					}
				}

				array_push($resourceOrgArray, $orgArray);
			}else{
				foreach ($result as $row) {

					$orgArray = array();

					//first, get the organization name
					$query = "SELECT DISTINCT name FROM " . $dbName . ".Organization WHERE organizationID = " . $row['organizationID'];

					if ($orgResult = $this->db->query($query)){
						while ($orgRow = $orgResult->fetch_assoc()){
							$orgArray['organization'] = $orgRow['name'];
							$orgArray['organizationID'] = $row['organizationID'];
						}
					}

					array_push($resourceOrgArray, $orgArray);

				}

			}






		//otherwise if the org module is not installed get the org name from this database
		}else{



			$resourceOrgArray = array();

			$query = "SELECT DISTINCT organizationID FROM ResourceOrganizationLink WHERE resourceID = '" . $this->resourceID . "'";

			$result = $this->db->processQuery($query, 'assoc');

			$objects = array();

			//need to do this since it could be that there's only one request and this is how the dbservice returns result
			if (isset($result['organizationID'])){
				$orgArray = array();

				//first, get the organization name
				$query = "SELECT DISTINCT shortName FROM Organization WHERE organizationID = " . $result['organizationID'];

				if ($orgResult = $this->db->query($query)){
					while ($orgRow = $orgResult->fetch_assoc()){
						$orgArray['organization'] = $orgRow['shortName'];
						$orgArray['organizationID'] = $result['organizationID'];
					}
				}

				array_push($resourceOrgArray, $orgArray);
			}else{
				foreach ($result as $row) {

					$orgArray = array();

					//first, get the organization name
					$query = "SELECT DISTINCT shortName FROM Organization WHERE organizationID = " . $row['organizationID'];

					if ($orgResult = $this->db->query($query)){
						while ($orgRow = $orgResult->fetch_assoc()){
							$orgArray['organization'] = $orgRow['shortName'];
							$orgArray['organizationID'] = $row['organizationID'];
						}
					}

					array_push($resourceOrgArray, $orgArray);

				}

			}





		}


		return $resourceOrgArray;
	}


  public function hasCatalogingInformation() {
    return ($this->recordSetIdentifier || $this->recordSetIdentifier || $this->bibSourceURL || $this->catalogingTypeID || $this->catalogingStatusID || $this->numberRecordsAvailable || $this->numberRecordsLoaded || $this->hasOclcHoldings);
  }




	//removes this resource and its children
	public function removeResourceAndChildren(){

    // for each children
    foreach ($this->getChildResources() as $instance) {
      $removeChild = true;
      $child = new Resource(new NamedArguments(array('primaryKey' => $instance->resourceID)));

      // get parents of this children
      $parents = $child->getParentResources();

      // If the child ressource belongs to another parent than the one we're removing
      foreach ($parents as $pinstance) {
        $parentResourceObj = new Resource(new NamedArguments(array('primaryKey' => $pinstance->relatedResourceID)));
        if ($parentResourceObj->resourceID != $this->resourceID) {
          // We do not delete this child.
          $removeChild = false;
        }
      }
      if ($removeChild == true) {
        $child->removeResource();
      }
    }
    // Finally, we remove the parent
    $this->removeResource();
	}




	//removes this resource
	public function removeResource(){
		//delete data from child linked tables
		$this->removeResourceRelationships();
		$this->removePurchaseSites();
		$this->removeAuthorizedSites();
		$this->removeAdministeringSites();
		$this->removeResourceLicenses();
		$this->removeResourceLicenseStatuses();
		$this->removeResourceOrganizations();
		$this->removeResourcePayments();
		$this->removeAllSubjects();
    $this->removeAllIsbnOrIssn();


		$instance = new Contact();
		foreach ($this->getContacts() as $instance) {
			$instance->removeContactRoles();
			$instance->delete();
		}

		$instance = new ExternalLogin();
		foreach ($this->getExternalLogins() as $instance) {
			$instance->delete();
		}

		$instance = new ResourceNote();
		foreach ($this->getNotes() as $instance) {
			$instance->delete();
		}

		$instance = new Attachment();
		foreach ($this->getAttachments() as $instance) {
			$instance->delete();
		}

		$instance = new Alias();
		foreach ($this->getAliases() as $instance) {
			$instance->delete();
		}


		$this->delete();
	}



	//removes resource hierarchy records
	public function removeResourceRelationships(){

		$query = "DELETE
			FROM ResourceRelationship
			WHERE resourceID = '" . $this->resourceID . "' OR relatedResourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query);
	}



	//removes resource purchase sites
	public function removePurchaseSites(){

		$query = "DELETE
			FROM ResourcePurchaseSiteLink
			WHERE resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query);
	}




	//removes resource authorized sites
	public function removeAuthorizedSites(){

		$query = "DELETE
			FROM ResourceAuthorizedSiteLink
			WHERE resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query);
	}



	//removes resource administering sites
	public function removeAdministeringSites(){

		$query = "DELETE
			FROM ResourceAdministeringSiteLink
			WHERE resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query);
	}



	//removes payment records
	public function removeResourcePayments(){

		$query = "DELETE
			FROM ResourcePayment
			WHERE resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query);
	}

	//removes resource licenses
	public function removeResourceLicenses(){

		$query = "DELETE
			FROM ResourceLicenseLink
			WHERE resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query);
	}

	//removes resource license statuses
	public function removeResourceLicenseStatuses(){

		$query = "DELETE
			FROM ResourceLicenseStatus
			WHERE resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query);
	}

	//removes resource organizations
	public function removeResourceOrganizations(){

		$query = "DELETE
			FROM ResourceOrganizationLink
			WHERE resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query);
	}


	//removes resource note records
	public function removeResourceNotes(){

		$query = "DELETE
			FROM ResourceNote
			WHERE resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query);
	}




	//removes resource steps
	public function removeResourceSteps(){

		$query = "DELETE
			FROM ResourceStep
			WHERE resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query);
	}





	//search used for the resource autocomplete
	public function resourceAutocomplete($q){
		$resourceArray = array();
		$result = $this->db->query("SELECT titleText, resourceID
								FROM Resource
								WHERE upper(titleText) like upper('%" . $q . "%')
								ORDER BY 1;");

		while ($row = $result->fetch_assoc()){
			$resourceArray[] = $row['titleText'] . "|" . $row['resourceID'];
		}

		return $resourceArray;
	}


	//search used for the organization autocomplete
	public function organizationAutocomplete($q){
		$config = new Configuration;
		$organizationArray = array();

		//if the org module is installed get the org name from org database
		if ($config->settings->organizationsModule == 'Y'){
			$dbName = $config->settings->organizationsDatabaseName;

			$result = $this->db->query("SELECT CONCAT(A.name, ' (', O.name, ')') shortName, O.organizationID
									FROM " . $dbName . ".Alias A, " . $dbName . ".Organization O
									WHERE A.organizationID=O.organizationID
									AND upper(A.name) like upper('%" . $q . "%')
									UNION
									SELECT name shortName, organizationID
									FROM " . $dbName . ".Organization
									WHERE upper(name) like upper('%" . $q . "%')
									ORDER BY 1;");

		}else{

			$result = $this->db->query("SELECT organizationID, shortName
									FROM Organization O
									WHERE upper(O.shortName) like upper('%" . $q . "%')
									ORDER BY shortName;");

		}


		while ($row = $result->fetch_assoc()){
			$organizationArray[] = $row['shortName'] . "|" . $row['organizationID'];
		}



		return $organizationArray;
	}




	//search used for the license autocomplete
	public function licenseAutocomplete($q){
		$config = new Configuration;
		$licenseArray = array();

		//if the org module is installed get the org name from org database
		if ($config->settings->licensingModule == 'Y'){
			$dbName = $config->settings->licensingDatabaseName;

			$result = $this->db->query("SELECT shortName, licenseID
									FROM " . $dbName . ".License
									WHERE upper(shortName) like upper('%" . $q . "%')
									ORDER BY 1;");

		}

		while ($row = $result->fetch_assoc()){
			$licenseArray[] = $row['shortName'] . "|" . $row['licenseID'];
		}



		return $licenseArray;
	}


	///////////////////////////////////////////////////////////////////////////////////
	//
	//  Workflow functions follow
	//
	///////////////////////////////////////////////////////////////////////////////////


	//returns array of ResourceStep objects for this Resource
	public function getResourceSteps(){


		$query = "SELECT * FROM ResourceStep
					WHERE resourceID = '" . $this->resourceID . "'
					ORDER BY displayOrderSequence, stepID";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceStepID'])){
			$object = new ResourceStep(new NamedArguments(array('primaryKey' => $result['resourceStepID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new ResourceStep(new NamedArguments(array('primaryKey' => $row['resourceStepID'])));
				array_push($objects, $object);
			}
		}

		return $objects;

	}




	//returns current step location in the workflow for this resource
	//used to display the group on the tabs
	public function getCurrentStepGroup(){


		$query = "SELECT groupName FROM ResourceStep RS, UserGroup UG
					WHERE resourceID = '" . $this->resourceID . "'
					ORDER BY stepID";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceStepID'])){

		}

	}



	//returns first steps (object) in the workflow for this resource
	public function getFirstSteps(){

		$query = "SELECT * FROM ResourceStep
					WHERE resourceID = '" . $this->resourceID . "'
					AND (priorStepID is null OR priorStepID = '0')
					ORDER BY stepID";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['resourceStepID'])){
			$object = new ResourceStep(new NamedArguments(array('primaryKey' => $result['resourceStepID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new ResourceStep(new NamedArguments(array('primaryKey' => $row['resourceStepID'])));
				array_push($objects, $object);
			}
		}

		return $objects;


	}



	//enters resource into new workflow
	public function enterNewWorkflow(){
		$config = new Configuration();

		//remove any current workflow steps
		$this->removeResourceSteps();

		//make sure this resource is marked in progress in case it was archived
		$status = new Status();
		$this->statusID = $status->getIDFromName('progress');
		$this->save();


		//Determine the workflow this resource belongs to
		$workflowObj = new Workflow();
		$workflowID = $workflowObj->getWorkflowID($this->resourceTypeID, $this->resourceFormatID, $this->acquisitionTypeID);

		if ($workflowID){
			$workflow = new Workflow(new NamedArguments(array('primaryKey' => $workflowID)));


			//Copy all of the step attributes for this workflow to a new resource step
			foreach ($workflow->getSteps() as $step){
				$resourceStep = new ResourceStep();

				$resourceStep->resourceStepID 		= '';
				$resourceStep->resourceID 			= $this->resourceID;
				$resourceStep->stepID 				= $step->stepID;
				$resourceStep->priorStepID			= $step->priorStepID;
				$resourceStep->stepName				= $step->stepName;
				$resourceStep->userGroupID			= $step->userGroupID;
				$resourceStep->displayOrderSequence	= $step->displayOrderSequence;

				$resourceStep->save();

			}


			//Start the first step
			//this handles updating the db and sending notifications for approval groups
			foreach ($this->getFirstSteps() as $resourceStep){
				$resourceStep->startStep();

			}
		}


		//send an email notification to the feedback email address and the creator
		$cUser = new User(new NamedArguments(array('primaryKey' => $this->createLoginID)));
		$acquisitionType = new AcquisitionType(new NamedArguments(array('primaryKey' => $this->acquisitionTypeID)));

		if ($cUser->firstName){
			$creator = $cUser->firstName . " " . $cUser->lastName;
		}else if ($this->createLoginID){  //for some reason user isn't set up or their firstname/last name don't exist
			$creator = $this->createLoginID;
		}else{
			$creator = "(unknown user)";
		}


		if (($config->settings->feedbackEmailAddress) || ($cUser->emailAddress)){
			$email = new Email();
			$util = new Utility();

			$email->message = $util->createMessageFromTemplate('NewResourceMain', $this->resourceID, $this->titleText, '', '', $creator);

			if ($cUser->emailAddress){
				$emailTo[] 			= $cUser->emailAddress;
			}

			if ($config->settings->feedbackEmailAddress != ''){
				$emailTo[] 			=  $config->settings->feedbackEmailAddress;
			}

			$email->to = implode(",", $emailTo);

			if ($acquisitionType->shortName){
				$email->subject		= "CORAL Alert: New " . $acquisitionType->shortName . " Resource Added: " . $this->titleText;
			}else{
				$email->subject		= "CORAL Alert: New Resource Added: " . $this->titleText;
			}

			$email->send();

		}

	}




	//completes a workflow (changes status to complete and sends notifications to creator and "master email")
	public function completeWorkflow(){
		$config = new Configuration();
		$util = new Utility();
		$status = new Status();
		$statusID = $status->getIDFromName('complete');

		if ($statusID){
			$this->statusID = $statusID;
			$this->save();
		}



		//send notification to creator and master email address

		$cUser = new User(new NamedArguments(array('primaryKey' => $this->createLoginID)));

		//formulate emil to be sent
		$email = new Email();
		$email->message = $util->createMessageFromTemplate('CompleteResource', $this->resourceID, $this->titleText, '', $this->systemNumber, '');

		if ($cUser->emailAddress){
			$emailTo[] 			= $cUser->emailAddress;
		}

		if ($config->settings->feedbackEmailAddress != ''){
			$emailTo[] 			=  $config->settings->feedbackEmailAddress;
		}

		$email->to = implode(",", $emailTo);

		$email->subject		= "CORAL Alert: Workflow completion for " . $this->titleText;


		$email->send();
	}

	//returns array of subject objects
	public function getGeneralDetailSubjectLinkID(){

		$query = "SELECT
				  GDL.generalDetailSubjectLinkID
				FROM
				  Resource R
				  INNER JOIN ResourceSubject RSUB ON (R.resourceID = RSUB.resourceID)
				  INNER JOIN GeneralDetailSubjectLink GDL ON (RSUB.generalDetailSubjectLinkID = GDL.generalDetailSubjectLinkID)
				  LEFT OUTER JOIN GeneralSubject GS ON (GDL.generalSubjectID = GS.generalSubjectID)
				  LEFT OUTER JOIN DetailedSubject DS ON (GDL.detailedSubjectID = DS.detailedSubjectID)
				WHERE
				  R.resourceID = '" . $this->resourceID . "'
				ORDER BY
				  GS.shortName,
				  DS.shortName";


		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['generalDetailSubjectLinkID'])){
			$object = new GeneralDetailSubjectLink(new NamedArguments(array('primaryKey' => $result['generalDetailSubjectLinkID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new GeneralDetailSubjectLink(new NamedArguments(array('primaryKey' => $row['generalDetailSubjectLinkID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}

	//returns array of subject objects
	public function getDetailedSubjects($resourceID, $generalSubjectID){

		$query = "SELECT
			  RSUB.resourceID,
			  GDL.detailedSubjectID,
			  DetailedSubject.shortName,
			  GDL.generalSubjectID
			FROM
			  ResourceSubject RSUB
			  INNER JOIN GeneralDetailSubjectLink GDL ON (RSUB.GeneralDetailSubjectLinkID = GDL.GeneralDetailSubjectLinkID)
			  INNER JOIN DetailedSubject ON (GDL.detailedSubjectID = DetailedSubject.detailedSubjectID)
			WHERE
			  RSUB.resourceID = " . $resourceID . " AND GDL.generalSubjectID = " . $generalSubjectID . " ORDER BY DetailedSubject.shortName";

		//echo $query . "<br>";

		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['detailedSubjectID'])){
			$object = new DetailedSubject(new NamedArguments(array('primaryKey' => $result['detailedSubjectID'])));
			array_push($objects, $object);
		}else{
			foreach ($result as $row) {
				$object = new DetailedSubject(new NamedArguments(array('primaryKey' => $row['detailedSubjectID'])));
				array_push($objects, $object);
			}
		}

		return $objects;
	}


	//removes all resource subjects
	public function removeAllSubjects(){

		$query = "DELETE
			FROM ResourceSubject
			WHERE resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query);

	}

  public function removeAllIsbnOrIssn() {
    $query = "DELETE
			FROM IsbnOrIssn
			WHERE resourceID = '" . $this->resourceID . "'";

		$result = $this->db->processQuery($query);

  }

  public function setIsbnOrIssn($isbnorissns) {
    $this->removeAllIsbnOrIssn();
    foreach ($isbnorissns as $isbnorissn) {
      if (trim($isbnorissn) != '') {
        $isbnOrIssn = new IsbnOrIssn();
        $isbnOrIssn->resourceID = $this->resourceID;
        $isbnOrIssn->isbnOrIssn = $isbnorissn;
        $isbnOrIssn->save();
      }
    }
  }

}

?>
