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
		return $rarray;
	}

	//returns number of children for this particular contact role
	public function getNumberOfChildren(){

		$query = "SELECT count(*) childCount FROM ResourceOrganizationLink WHERE organizationID = '" . $this->organizationID . "';";

		$result = $this->db->processQuery($query, 'assoc');

		return $result['childCount'];

	}

  public function alreadyExists($shortName) {
		$query = "SELECT count(*) orgcount FROM Organization WHERE UPPER(shortName) = '" . str_replace("'", "''", strtoupper($shortName)) . "';";
		$result = $this->db->processQuery($query, 'assoc');
		return $result['orgcount'];
  }

  public function getOrganizationIDByName($shortName) {
    $query = "SELECT organizationID FROM Organization WHERE UPPER(shortName) = '" . str_replace("'", "''", strtoupper($shortName)) . "';";
		$result = $this->db->processQuery($query, 'assoc');
		return $result['organizationID'];
  }

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

	public function getIssues($archivedOnly=false) {
		$query = "SELECT i.*
			  FROM Issue i
			  LEFT JOIN IssueRelationship ir ON (ir.issueID=i.issueID AND ir.entityTypeID=1)
			  WHERE ir.entityID='{$this->primaryKey}'";
		if ($archivedOnly) {
			$query .= " AND i.dateClosed IS NOT NULL";
		} else {
			$query .= " AND i.dateClosed IS NULL";
		}
		$query .= "	ORDER BY i.dateCreated DESC";
		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();
		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['issueID'])) {
			$object = new Issue(new NamedArguments(array('primaryKey' => $result['issueID'])));
			array_push($objects, $object);
		} else {
			foreach ($result as $row) {
				$object = new Issue(new NamedArguments(array('primaryKey' => $row['issueID'])));
				array_push($objects, $object);
			}
		}
		return $objects;
	}

	public function getExportableIssues($archivedOnly=false) {
		if ($this->db->config->settings->organizationsModule == 'Y' && $this->db->config->settings->organizationsDatabaseName) {
			$orgDB = $this->db->config->settings->organizationsDatabaseName;
			$orgField = 'name';
		} else {
			$orgDB = $this->db->config->database->name;
			$orgField = 'shortName';
		}

		$query = "SELECT i.*,(SELECT GROUP_CONCAT(CONCAT('=HYPERLINK(\"mailto:',sc.emailAddress,'\",\"',COALESCE(sc.name,sc.emailAddress),'\")') SEPARATOR ', ')
								FROM IssueContact sic
								LEFT JOIN `{$orgDB}`.Contact sc ON sc.contactID=sic.contactID
								WHERE sic.issueID=i.issueID) AS `contacts`,
							 (SELECT GROUP_CONCAT(se.{$orgField} SEPARATOR ', ')
								FROM IssueRelationship sir
								LEFT JOIN `{$orgDB}`.Organization se ON (se.organizationID=sir.entityID AND sir.entityTypeID=1)
								WHERE sir.issueID=i.issueID) AS `appliesto`,
							 (SELECT GROUP_CONCAT(sie.email SEPARATOR ', ')
								FROM IssueEmail sie
								WHERE sie.issueID=i.issueID) AS `CCs`
			  FROM Issue i
			  LEFT JOIN IssueRelationship ir ON (ir.issueID=i.issueID AND ir.entityTypeID=1)
			  WHERE ir.entityID='{$this->primaryKey}'";
		if ($archivedOnly) {
			$query .= " AND i.dateClosed IS NOT NULL";
		} else {
			$query .= " AND i.dateClosed IS NULL";
		}
		$query .= "	ORDER BY i.dateCreated DESC";
		$result = $this->db->processQuery($query, 'assoc');

		$objects = array();
		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['issueID'])) {
			return array($result);
		} else {
			return $result;
		}
	}

	private function getDownTimeResults($archivedOnly=false) {
		$query = "SELECT d.*
			  FROM Downtime d
			  WHERE d.entityID='{$this->primaryKey}'
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

	public function getExportableDowntimes($archivedOnly=false) {
		$result = $this->getDownTimeResults($archivedOnly);

		$objects = array();

		//need to do this since it could be that there's only one request and this is how the dbservice returns result
		if (isset($result['downtimeID'])) {
			return array($result);
		} else {
			return $result;
		}
	}
}

?>
