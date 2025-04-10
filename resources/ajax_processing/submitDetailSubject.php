<?php

	$generalSubjectID = $_POST['generalSubjectID'];

	if ($generalSubjectID!=''){
		$generalSubject = new GeneralSubject(new NamedArguments(array('primaryKey' => $generalSubjectID)));
	}else{
		$generalSubject = new GeneralSubject();
	}

	// Update the General Subject if needed
	$generalSubject->shortName = str_replace("'", "''", $_POST['shortName']);

	try {
		$generalSubject->save();

		$generalSubjectID=$generalSubject->primaryKey;

		$detailSubjectArray = array();
		$detailSubjectArray = explode(':::',$_POST['detailSubjectsList']);

		$detailSubjectIDs = "(-1";

		// Update the GeneralDetailSubject Links
		foreach ($detailSubjectArray as $key => $value){
			if ($value){

				$generalDetailSubjectLink = new GeneralDetailSubjectLink();
				$generalDetailSubjectLink->detailedSubjectID = $value;
				$generalDetailSubjectLink->generalSubjectID = $generalSubjectID;

				// Add any Detail Subject Links that are new
				if ($generalDetailSubjectLink->duplicateCheck() == 0 ) {
					// Add the new link
					try {
						$generalDetailSubjectLink->save();
					} catch (Exception $e) {
						echo "<span class='error'>";
						echo $e->getMessage();
						echo "</span>";
					}
				}
				// Build list of detailid's that are in use
				$detailSubjectIDs = $detailSubjectIDs . ', ' . $value;
			}
		}

		$detailSubjectIDs = $detailSubjectIDs . ')';
		$generalDetailSubjectLink = new GeneralDetailSubjectLink();
		// Delete the links that are no longer in use.
		$generalDetailSubjectLink->deleteNotInuse($generalSubjectID, $detailSubjectIDs);

	} catch (Exception $e) {
		echo "<span class='error'>";
		echo $e->getMessage();
		echo "</span>";
	}

?>
