<?php
		$importConfigID = $_GET['importConfigID'];
		$importConfig = new ImportConfig(new NamedArguments(array('primaryKey' => $importConfigID)));

		try {
			$importConfig->removeImportConfig();
			echo "<span class='success'>";
			echo _("Import configuration successfully deleted.");
			echo "</span>";
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}
?>
