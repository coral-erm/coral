<?php

		$className = $_GET['class'];
		$deleteID = $_GET['id'];

		//since we're using MyISAM which doesn't support FKs, must verify that there are no records of children or they could disappear
		$instance = new $className(new NamedArguments(array('primaryKey' => $deleteID)));
		$numberOfChildren = $instance->getNumberOfChildren();

		if ($numberOfChildren > 0){
			//print out a friendly message...
			echo "<span class='error'>";
			printf(_("Unable to delete  - this %s is in use.  Please make sure no resources are set up with this information."), strtolower(preg_replace("/[A-Z]/", " \\0" , lcfirst($className))));
			echo "</span>";
		}else{
			try {
				$instance->delete();
			} catch (Exception $e) {
				//print out a friendly message...
				echo "<span class='error'>";
				echo _("Unable to delete.  Please make sure no resources are set up with this information.");
				echo "</span>";
			}
		}
?>
