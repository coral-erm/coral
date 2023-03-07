<?php
		$resourceAcquisitionID = $_POST['resourceAcquisitionID'];
		$resourceAcquisition = new ResourceAcquisition(new NamedArguments(array('primaryKey' => $resourceAcquisitionID)));


		try {
			$resourceAcquisition->save();

			//first remove all payment records, then we'll add them back
			$resourceAcquisition->removeResourcePayments();

			$yearArray          = array();  $yearArray          = explode(':::',$_POST['years']);
			$subStartArray      = array();  $subStartArray      = explode(':::',$_POST['subStarts']);
			$subEndArray        = array();  $subEndArray        = explode(':::',$_POST['subEnds']);
			$fundIDArray        = array();  $fundIDArray      = explode(':::',$_POST['fundIDs']);
            $pteArray           = array();  $pteArray           = explode(':::',$_POST['pricesTaxExcluded']);
            $taxRateArray       = array();  $taxRateArray       = explode(':::',$_POST['taxRates']);
            $ptiArray           = array();  $ptiArray           = explode(':::',$_POST['pricesTaxIncluded']);
			$paymentAmountArray = array();  $paymentAmountArray = explode(':::',$_POST['paymentAmounts']);
			$currencyCodeArray  = array();  $currencyCodeArray  = explode(':::',$_POST['currencyCodes']);
			$orderTypeArray     = array();  $orderTypeArray     = explode(':::',$_POST['orderTypes']);
			$costDetailsArray   = array();  $costDetailsArray   = explode(':::',$_POST['costDetails']);
			$costNoteArray      = array();  $costNoteArray      = explode(':::',$_POST['costNotes']);
			$invoiceArray       = array();  $invoiceArray       = explode(':::',$_POST['invoices']);
			$formulaIDArray     = array();  $formulaIDArray     = explode(':::',$_POST['formulaIDS']);
			$formulaArray       = array();  $formulaArray       = explode(':::',$_POST['formulas']);
			foreach ($orderTypeArray as $key => $value){
				if (($value) && ($paymentAmountArray[$key] || $yearArray[$key] || $fundIDArray[$key] || $costNoteArray[$key])){
					$resourcePayment = new ResourcePayment();
					$resourcePayment->resourceAcquisitionID    = $resourceAcquisitionID;
					$resourcePayment->year          = $yearArray[$key];
					$start = $subStartArray[$key] ? create_date_from_js_format($subStartArray[$key])->format('Y-m-d') : null;
					$end   = $subEndArray[$key]   ? create_date_from_js_format($subEndArray[$key])->format('Y-m-d') : null;
					$resourcePayment->subscriptionStartDate = $start;
					$resourcePayment->subscriptionEndDate   = $end;
					$resourcePayment->fundID        = $fundIDArray[$key];
					$resourcePayment->priceTaxExcluded = $pteArray[$key];
					$resourcePayment->taxRate       = $taxRateArray[$key];
					$resourcePayment->priceTaxIncluded = $ptiArray[$key];
					$resourcePayment->paymentAmount = $paymentAmountArray[$key];
					$resourcePayment->currencyCode  = $currencyCodeArray[$key];
					$resourcePayment->orderTypeID   = $value;
					$resourcePayment->costDetailsID = $costDetailsArray[$key];
					$resourcePayment->costNote      = $costNoteArray[$key];
					$resourcePayment->invoiceNum    = $invoiceArray[$key];
                    $resourcePayment->pricingFormulaID = $formulaIDArray[$key];
                    $formulaValues = explode(':', $formulaArray[$key]);
                    $i = 1;
                    foreach ($formulaValues as $formulaValue) {
                        $memberName = "field${i}Value";
                        $resourcePayment->$memberName = ($formulaValue ? cost_to_integer($formulaValue) : 0);
                        $i++;
                    }
					try {
						$resourcePayment->save();
					} catch (Exception $e) {
						echo $e->getMessage();
					}
				}
			}

		} catch (Exception $e) {
			echo $e->getMessage();
		}

?>
