<?php

		$deleteID = $_GET['id'];

		$instance = new PricingFormula(new NamedArguments(array('primaryKey' => $deleteID)));

			try {
				$instance->delete();
			} catch (Exception $e) {
				//print out a friendly message...
				echo _("Unable to delete.  Please make sure no resources are set up with this information.");
			}

?>
