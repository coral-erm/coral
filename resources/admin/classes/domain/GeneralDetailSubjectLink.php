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

class GeneralDetailSubjectLink extends DatabaseObject {


	protected function defineRelationships() {}

	protected function overridePrimaryKeyName() {}


	//returns the General Detail Subject Link ID when a general subject / detail subject is known.
	public function getGeneralDetailID($generalSubjectID, $detailedSubjectID) {

		$query = "SELECT * FROM GeneralDetailSubjectLink
					WHERE generalSubjectID = " . $generalSubjectID .
					" AND detailedSubjectID = " . $detailedSubjectID;

		try {
			$result = $this->db->processQuery($query, 'assoc');
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}


		if ($result) {
			return $result['generalDetailSubjectLinkID'];
		} else {
			return -1;  // None is found
		}

	}


	public function duplicateCheck() {

		$query = "SELECT count(*) duplicateCount FROM GeneralDetailSubjectLink where generalSubjectID = " . $this->generalSubjectID . " AND detailedSubjectID = " . $this->detailedSubjectID;
		$result = $this->db->processQuery($query, 'assoc');

		return $result['duplicateCount'];

	}

	public function deleteNotInuse($generalSubjectID, $detailSubjectIDs) {

		$query = "delete FROM GeneralDetailSubjectLink where generalSubjectID = ". $generalSubjectID . " AND
			detailedSubjectID NOT in ". $detailSubjectIDs;

			$result = $this->db->processQuery($query, 'assoc');

		return;

	}

}

?>
