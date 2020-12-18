<?php
		$resourceAcquisitionID = $_POST['resourceAcquisitionID'];
		$resourceAcquisition = new ResourceAcquisition(new NamedArguments(array('primaryKey' => $resourceAcquisitionID)));

		$resourceAcquisition->authenticationTypeID 	    = $_POST['authenticationTypeID'];
		$resourceAcquisition->accessMethodID 			= $_POST['accessMethodID'];
		$resourceAcquisition->coverageText 			    = $_POST['coverageText'];
		$resourceAcquisition->authenticationUserName 	= $_POST['authenticationUserName'];
		$resourceAcquisition->authenticationPassword	= $_POST['authenticationPassword'];
		$resourceAcquisition->storageLocationID 		= $_POST['storageLocationID'];
		$resourceAcquisition->userLimitID		    	= $_POST['userLimitID'];

		try {
			$resourceAcquisition->save();

			//first remove all administering sites, then we'll add them back
			$resourceAcquisition->removeAdministeringSites();

			foreach (explode(':::',$_POST['administeringSites']) as $key => $value){
				if ($value){
					$resourceAdministeringSiteLink = new ResourceAdministeringSiteLink();
					$resourceAdministeringSiteLink->resourceAcquisitionID = $resourceAcquisitionID;
					$resourceAdministeringSiteLink->administeringSiteID = $value;
					try {
						$resourceAdministeringSiteLink->save();
					} catch (Exception $e) {
						echo $e->getMessage();
					}
				}
			}



			//first remove all authorized sites, then we'll add them back
			$resourceAcquisition->removeAuthorizedSites();

			foreach (explode(':::',$_POST['authorizedSites']) as $key => $value){
				if ($value){
					$resourceAuthorizedSiteLink = new ResourceAuthorizedSiteLink();
					$resourceAuthorizedSiteLink->resourceAcquisitionID = $resourceAcquisitionID;
					$resourceAuthorizedSiteLink->authorizedSiteID = $value;
					try {
						$resourceAuthorizedSiteLink->save();
					} catch (Exception $e) {
						echo $e->getMessage();
					}
				}
			}

		} catch (Exception $e) {
			echo $e->getMessage();
		}

?>
