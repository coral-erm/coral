<?php

		$resourceID = $_POST['resourceID'];

		if ($resourceID){
			//get this resource
			$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));
		}else{
			//set up new resource
			$resource = new Resource();
			$resource->createLoginID 		= $loginID;
			$resource->createDate			= date( 'Y-m-d' );
			$resource->updateLoginID 		= '';
			$resource->updateDate			= '';

		}


		//determine status id
		$status = new Status();
		$statusID = $status->getIDFromName($_POST['resourceStatus']);



		$resource->resourceTypeID 		= $_POST['resourceTypeID'];
		$resource->resourceFormatID 	= $_POST['resourceFormatID'];
		$resource->titleText 			= $_POST['titleText'];
		$resource->descriptionText 		= $_POST['descriptionText'];
		$resource->isbnOrISSN	 		= [];
		$resource->statusID		 		= $statusID;
		$resource->orderNumber	 		= '';
		$resource->systemNumber 		= '';
		$resource->userLimitID	 		= '';
		$resource->authenticationUserName 	= '';
		$resource->authenticationPassword 	= '';
		$resource->storageLocationID		= '';
		$resource->registeredIPAddresses 	= '';
		$resource->providerText			 	= $_POST['providerText'];
		$resource->coverageText 			= '';

		if ($_POST['resourceURL'] != 'http://'){
			$resource->resourceURL = $_POST['resourceURL'];
		}else{
			$resource->resourceURL = '';
		}

		if ($_POST['resourceAltURL'] != 'http://'){
			$resource->resourceAltURL = $_POST['resourceAltURL'];
		}else{
			$resource->resourceAltURL = '';
		}

		try {
			$resource->save();
			echo $resource->primaryKey;
			$resourceID=$resource->primaryKey;

            // Create the default order
            //first, remove existing order in case this was saved before
            $resource->removeResourceAcquisitions();

            $resourceAcquisition = new ResourceAcquisition();
            $resourceAcquisition->resourceID = $resourceID;
			$resourceAcquisition->acquisitionTypeID = $_POST['acquisitionTypeID'];
			$resourceAcquisition->subscriptionStartDate = date("Y-m-d");
            $resourceAcquisition->subscriptionEndDate = date("Y-m-d");
            $resourceAcquisition->save();

			//get the provider ID in case we insert what was entered in the provider text box as an organization link
			$organizationRole = new OrganizationRole();
			$organizationRoleID = $organizationRole->getProviderID();

			//add notes
			if (($_POST['noteText']) || (($_POST['providerText']) && (!$_POST['organizationID']))){
				//first, remove existing notes in case this was saved before
				$resource->removeResourceNotes();

				//this is just to figure out what the creator entered note type ID is
				$noteType = new NoteType();

				$resourceNote = new ResourceNote();
				$resourceNote->resourceNoteID 	= '';
				$resourceNote->updateLoginID 	= $loginID;
				$resourceNote->updateDate		= date( 'Y-m-d' );
				$resourceNote->noteTypeID 		= $noteType->getInitialNoteTypeID();
				$resourceNote->tabName 			= 'Product';
				$resourceNote->entityID 		= $resourceID;

				//only insert provider as note if it's been submitted
				if (($_POST['providerText']) && (!$_POST['organizationID']) && ($_POST['resourceStatus'] == 'progress')){
					$resourceNote->noteText 	= "Provider:  " . $_POST['providerText'] . "\n\n" . $_POST['noteText'];
				}else{
					$resourceNote->noteText 	= $_POST['noteText'];
				}

				$resourceNote->save();
			}


			//first remove the organizations if this is a saved request
			$resource->removeResourceOrganizations();
			if (($_POST['organizationID']) && ($organizationRoleID)){

				$resourceOrganizationLink = new ResourceOrganizationLink();
				$resourceOrganizationLink->resourceID = $resourceID;
				$resourceOrganizationLink->organizationID = $_POST['organizationID'];
				$resourceOrganizationLink->organizationRoleID = $organizationRoleID;

				$resourceOrganizationLink->save();
			}

			$yearArray          = array();  $yearArray          = explode(':::',$_POST['years']);
			$subStartArray      = array();  $subStartArray      = explode(':::',$_POST['subStarts']);
			$subEndArray        = array();  $subEndArray        = explode(':::',$_POST['subEnds']);
			$fundIDArray        = array();  $fundIDArray        = explode(':::',$_POST['fundIDs']);
			$paymentAmountArray = array();  $paymentAmountArray = explode(':::',$_POST['paymentAmounts']);
			$currencyCodeArray  = array();  $currencyCodeArray  = explode(':::',$_POST['currencyCodes']);
			$orderTypeArray     = array();  $orderTypeArray     = explode(':::',$_POST['orderTypes']);
			$costDetailsArray   = array();  $costDetailsArray   = explode(':::',$_POST['costDetails']);
			$costNoteArray      = array();  $costNoteArray      = explode(':::',$_POST['costNotes']);
			$invoiceArray       = array();  $invoiceArray       = explode(':::',$_POST['invoices']);

            // Is this really needed ?
/*
			//first remove all payment records, then we'll add them back
			$resource->removeResourcePayments();

			foreach ($orderTypeArray as $key => $value){
				if (($value) && ($paymentAmountArray[$key] || $yearArray[$key] || $fundIDArray[$key] || $costNoteArray[$key])){
					$resourcePayment = new ResourcePayment();
					$resourcePayment->resourceID    = $resourceID;
					$resourcePayment->year          = $yearArray[$key];
					$resourcePayment->subscriptionStartDate = $subStartArray[$key];
					$resourcePayment->subscriptionEndDate   = $subEndArray[$key];
					$resourcePayment->fundID        = $fundIDArray[$key];
					$resourcePayment->paymentAmount = cost_to_integer($paymentAmountArray[$key]);
					$resourcePayment->currencyCode  = $currencyCodeArray[$key];
					$resourcePayment->orderTypeID   = $value;
					$resourcePayment->costDetails   = $costDetailsArray[$key];
					$resourcePayment->costNote      = $costNoteArray[$key];
					$resourcePayment->invoice       = $invoiceArray[$key];
					try {
						$resourcePayment->save();
					} catch (Exception $e) {
						echo $e->getMessage();
					}
				}
			}
*/


			//next if the resource was submitted, enter into workflow
			if ($statusID == $status->getIDFromName('progress')){
				$resourceAcquisition->enterNewWorkflow();
			}



		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

?>
