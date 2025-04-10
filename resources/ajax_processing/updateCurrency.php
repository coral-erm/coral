<?php
		$editCurrencyCode = $_POST['editCurrencyCode'];
		$currencyCode = $_POST['currencyCode'];
		$shortName = $_POST['shortName'];

		if ($editCurrencyCode != ''){
			$instance = new Currency(new NamedArguments(array('primaryKey' => $editCurrencyCode)));
		}else{
			$instance = new Currency();
		}


		$instance->currencyCode = $currencyCode;
		$instance->shortName = $shortName;

		try {
			$instance->save();
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

?>
