<?php
		$alertDaysInAdvanceID = $_POST['alertDaysInAdvanceID'];
		$daysInAdvanceNumber = $_POST['daysInAdvanceNumber'];

		if ($alertDaysInAdvanceID != ''){
			$instance = new AlertDaysInAdvance(new NamedArguments(array('primaryKey' => $alertDaysInAdvanceID)));
		}else{
			$instance = new AlertDaysInAdvance();
		}

		$instance->daysInAdvanceNumber = $daysInAdvanceNumber;

		try {
			$instance->save();
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

?>
