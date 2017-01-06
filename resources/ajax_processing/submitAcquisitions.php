<?php
		$resourceID = $_POST['resourceID'];
		$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));

		//first set current start Date for proper saving
		if ((isset($_POST['currentStartDate'])) && ($_POST['currentStartDate'] != '')){
			$resource->currentStartDate = date("Y-m-d", strtotime($_POST['currentStartDate']));
		}else{
			$resource->currentStartDate= NULL;
		}

		//first set current end Date for proper saving
		if ((isset($_POST['currentEndDate'])) && ($_POST['currentEndDate'] != '')){
			$resource->currentEndDate = date("Y-m-d", strtotime($_POST['currentEndDate']));
		}else{
			$resource->currentEndDate= NULL;
		}

		$resource->acquisitionTypeID 				= $_POST['acquisitionTypeID'];
		$resource->orderNumber 						= $_POST['orderNumber'];
		$resource->systemNumber 					= $_POST['systemNumber'];
		$resource->subscriptionAlertEnabledInd 		= $_POST['subscriptionAlertEnabledInd'];

		try {
			$resource->save();

			//first remove all administering sites, then we'll add them back
			$resource->removePurchaseSites();

			foreach (explode(':::',$_POST['purchaseSites']) as $key => $value){
				if ($value){
					$resourcePurchaseSiteLink = new ResourcePurchaseSiteLink();
					$resourcePurchaseSiteLink->resourceID = $resourceID;
					$resourcePurchaseSiteLink->purchaseSiteID = $value;
					try {
						$resourcePurchaseSiteLink->save();
					} catch (Exception $e) {
						echo $e->getMessage();
					}
				}
			}

		} catch (Exception $e) {
			echo $e->getMessage();
		}

?>
