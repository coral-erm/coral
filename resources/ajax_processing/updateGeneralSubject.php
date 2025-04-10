<?php
		$className = $_POST['className'];
		$updateID = $_POST['updateID'];
		$shortName = trim($_POST['shortName']);

		if ($updateID != ''){
			$instance = new $className(new NamedArguments(array('primaryKey' => $updateID)));
		}else{
			$instance = new $className();
		}

		$instance->shortName = $shortName;
		// Check to see if the general subject name exists.  If not then save.
		if ($instance->duplicateCheck($shortName) == 0)  {
			try {
				$instance->save();
			} catch (Exception $e) {
				echo "<span class='error'>";
				echo $e->getMessage();
				echo "</span>";
			}
		} else {
			echo "<span class='warning'>";
			printf(_("A duplicate %s exists."), strtolower(preg_replace("/[A-Z]/", " \\0" , lcfirst($className))));
			echo "</span>";
		}

?>
