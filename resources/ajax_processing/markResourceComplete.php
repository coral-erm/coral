<?php
		if ($_GET['resourceAcquisitionID']){
			$resourceAcquisitionID = $_GET['resourceAcquisitionID'];
			$resourceAcquisition = new ResourceAcquisition(new NamedArguments(array('primaryKey' => $resourceAcquisitionID)));
			//log who set off the completion
			$resourceAcquisition->workflowRestartLoginID = $loginID;
			$resourceAcquisition->workflowRestartDate = date( 'Y-m-d' );

			try {
				$resourceAcquisition->save();

				//updates status and sends notification
				$resourceAcquisition->completeWorkflow();
			} catch (Exception $e) {
				echo "<span class='error'>";
				echo $e->getMessage();
				echo "</span>";
			}

		}

?>
