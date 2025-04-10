<?php
		$resourceID = $_GET['resourceID'];
		$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));

		try {
			$resource->removeResource();
			echo "<span class='success'>";
			echo _("Resource successfully deleted.");
			echo "</span>";
		} catch (Exception $e) {
            http_response_code(404);
						echo "<span class='error'>";
            echo _("Resource not found. Error: ".$e->getMessage());
						echo "</span>";
		}
?>
