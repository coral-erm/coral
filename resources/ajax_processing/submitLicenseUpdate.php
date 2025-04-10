<?php
		$resourceAcquisitionID = $_POST['resourceAcquisitionID'];
		$resourceAcquisition = new ResourceAcquisition(new NamedArguments(array('primaryKey' => $resourceAcquisitionID)));


		if (($_POST['licenseStatusID']) && ($_POST['licenseStatusID'] != $resourceAcquisition->getCurrentResourceLicenseStatus())){
			$resourceLicenseStatus = new ResourceLicenseStatus();
			$resourceLicenseStatus->resourceAcquisitionID    	= $resourceAcquisitionID;
			$resourceLicenseStatus->licenseStatusID 			= $_POST['licenseStatusID'];
			$resourceLicenseStatus->licenseStatusChangeLoginID 	= $loginID;
			$resourceLicenseStatus->licenseStatusChangeDate 	= date( 'Y-m-d H:i:s' );
			$resourceLicenseStatus->save();

		}

		try {

			//first remove all license links, then we'll add them back
			$resourceAcquisition->removeResourceLicenses();

			foreach (explode(':::',$_POST['licenseList']) as $key => $value){
				if ($value){
					$resourceLicenseLink = new ResourceLicenseLink();
					$resourceLicenseLink->resourceAcquisitionID = $resourceAcquisitionID;
					$resourceLicenseLink->licenseID = $value;
					try {
						$resourceLicenseLink->save();
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
		}

?>
