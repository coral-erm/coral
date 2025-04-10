<?php
		$resourceNoteID = $_GET['resourceNoteID'];
		$resourceNote = new ResourceNote(new NamedArguments(array('primaryKey' => $resourceNoteID)));

		try {
			$resourceNote->delete();
			echo "<span class='success'>";
			echo _("Note successfully deleted.");
			echo "</span>";
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}
?>
