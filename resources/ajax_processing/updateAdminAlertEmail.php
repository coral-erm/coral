<?php
		$alertEmailAddressID = $_POST['alertEmailAddressID'];
		$emailAddress = $_POST['emailAddress'];

		if ($alertEmailAddressID != ''){
			$instance = new AlertEmailAddress(new NamedArguments(array('primaryKey' => $alertEmailAddressID)));
		}else{
			$instance = new AlertEmailAddress();
		}

		$instance->emailAddress = $emailAddress;

		try {
			$instance->save();
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

?>
