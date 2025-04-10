<?php
		$fundID = $_POST['fundID'];
		$fundCode = $_POST['fundCode'];
		$shortName = $_POST['shortName'];
		$archived = $_POST['archived'];

		if ($fundID != ''){
			$instance = new Fund(new NamedArguments(array('primaryKey' => $fundID)));
		}else{
			$instance = new Fund();
		}

		$instance->fundCode = $fundCode;
		$instance->shortName = $shortName;

		if($archived == 'true'){
			$archived = 1;
		}
		else{
			$archived = 0;
		}

		$instance->archived = $archived;
		try {
			$instance->save();
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

?>
