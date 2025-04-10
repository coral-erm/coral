<?php

		$orgloginID = $_POST['orgloginID'];

		if ($orgloginID){
			$user = new User(new NamedArguments(array('primaryKey' => $orgloginID)));
		}else{
			$user = new User();
			$user->loginID=$_POST['loginID'];
		}


		$user->firstName=$_POST['firstName'];
		$user->lastName=$_POST['lastName'];
		$user->privilegeID=$_POST['privilegeID'];
		$user->emailAddress=$_POST['emailAddress'];
		if ($_POST['accountTabIndicator'] == '1'){
			$user->accountTabIndicator='1';
		}else{
			$user->accountTabIndicator='0';
		}


		try {
			$user->save();
			echo "<span class='success'>";
			echo _("User successfully saved.");
			echo "</span>";
		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

?>
