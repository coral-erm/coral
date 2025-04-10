<?php

		$resourceSubject = new ResourceSubject();

		$resourceID = $_GET['resourceID'];
		$generalSubjectID = $_GET['generalSubjectID'];
		$detailSubjectID = $_GET['detailSubjectID'];

		if (!isset($detailSubjectID)) {
			$detailSubjectID = -1;
		}

		if (!isset($generalSubjectID)) {
			$generalSubjectID = -1;
		}

		$generalDetailSubjectLink = new GeneralDetailSubjectLink();
		$generalDetailSubjectLinkID = $generalDetailSubjectLink->getGeneralDetailID($generalSubjectID, $detailSubjectID);

		$resourceSubject->resourceID = $resourceID;
		$resourceSubject->generalDetailSubjectLinkID = $generalDetailSubjectLinkID;

		// Check to see if the subject has already been associated with the resouce.  If not then save.
		if ($resourceSubject->duplicateCheck($resourceID, $generalDetailSubjectLinkID) == 0) {
			try {
				$resourceSubject->save();
			} catch (Exception $e) {
				echo "<span class='error'>";
				echo $e->getMessage();
				echo "</span>";
			}
		}

?>
