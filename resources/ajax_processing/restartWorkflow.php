<?php
		if ($_GET['resourceAcquisitionID']){
			$resourceAcquisitionID = $_GET['resourceAcquisitionID'];
            $workflowID = $_GET['workflow'];
			$resourceAcquisition = new ResourceAcquisition(new NamedArguments(array('primaryKey' => $resourceAcquisitionID)));

			//log who set off the restart
			$resourceAcquisition->workflowRestartLoginID = $loginID;
			$resourceAcquisition->workflowRestartDate = date( 'Y-m-d' );

			try {
				$resourceAcquisition->save();
                $resourceAcquisition->isCurrentWorkflowComplete() ? $resourceAcquisition->archiveWorkflow() : $resourceAcquisition->deleteWorkflow();
                $resourceAcquisition->enterNewWorkflow($workflowID);
			} catch (Exception $e) {
				echo "<span class='error'>";
				echo $e->getMessage();
				echo "</span>";
			}


		}
?>
