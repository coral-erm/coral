<?php
		$className = $_POST['className'];
		$updateID = $_POST['updateID'];
		$shortName = $_POST['shortName'];
		if($className=="ResourceType"){
			$includeStats = $_POST['stats'];
		}

		if ($updateID != ''){
			$instance = new $className(new NamedArguments(array('primaryKey' => $updateID)));
		}else{
			$instance = new $className();
		}

		$instance->shortName = $shortName;
		if($className == "ResourceType"){
			if($includeStats == 'true'){
				$includeStats = 1;
			}else{
				$includeStats = 0;
			}
			$instance->includeStats = $includeStats;
		}


		try {
			$instance->save();
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

?>
