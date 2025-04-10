<?php
		$resourceAcquisitionID = $_GET['resourceAcquisitionID'];
		$resourceAcquisition = new ResourceAcquisition(new NamedArguments(array('primaryKey' => $resourceAcquisitionID)));

		try {
			$resourceAcquisition->removeResourceAcquisition();
			echo "<span class='success'>";
			echo _("Order successfully deleted.");
			echo "</span>";
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}
?>
