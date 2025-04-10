<?php
		$importConfigID = $_POST['importConfigID'];
		$shortName = $_POST['shortName'];
		$configuration = $_POST['configuration'];
		$orgNameImported = $_POST['orgNameImported'];
		$orgNameMapped = $_POST['orgNameMapped'];

		if ($importConfigID != '') {
			$instance = new ImportConfig(new NamedArguments(array('primaryKey' => $importConfigID)));
		} else {
			$instance = new ImportConfig();
		}

		$instance->shortName = $shortName;
		$instance->configuration = $configuration;

		try {
			$instance->save();
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

		try {
			//first remove all payment records, then we'll add them back
			$instance->removeOrgNameMappings();

			$orgNameImportedArray = array();  $orgNameImportedArray = explode(':::',$_POST['orgNameImported']);
			$orgNameMappedArray   = array();  $orgNameMappedArray   = explode(':::',$_POST['orgNameMapped']);
			foreach ($orgNameImportedArray as $key => $value) {
				if (($value) && ($orgNameMapped[$key])) {
					$orgNameMapping = new OrgNameMapping();
					$orgNameMapping->importConfigID = $instance->primaryKey;
					$orgNameMapping->orgNameImported = $orgNameImportedArray[$key];
					$orgNameMapping->orgNameMapped = $orgNameMappedArray[$key];
					try {
						$orgNameMapping->save();
					} catch (Exception $e) {
						echo "<span class='error'>";
						echo $e->getMessage();
						echo "</span>";
					}
				}
			}

		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}?>
