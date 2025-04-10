<?php
		$generalDetailSubjectID = $_GET['generalDetailSubjectID'];
		$resourceID = $_GET['resourceID'];

		$resourceSubject = new ResourceSubject();

		try {

			$resourceSubject->removeResourceSubject($resourceID, $generalDetailSubjectID);
			echo "<span class='success'>";
			echo _("Subject successfully removed.");
			echo "</span>";
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

?>
