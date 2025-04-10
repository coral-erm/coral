<?php

		$userGroupID = $_POST['userGroupID'];

		if ($userGroupID!=''){
			$userGroup = new UserGroup(new NamedArguments(array('primaryKey' => $userGroupID)));
		}else{
			$userGroup = new UserGroup();
		}

		$userGroup->groupName = $_POST['groupName'];
		$userGroup->emailAddress = $_POST['emailAddress'];

		try {
			$userGroup->save();

			$userGroupID=$userGroup->primaryKey;

			$usersArray = array();
			$usersArray = explode(':::',$_POST['usersList']);

			//first remove all payment records, then we'll add them back
			$userGroup->removeUsers();

			foreach ($usersArray as $key => $value){
				if ($value){
					$userGroupLink = new UserGroupLink();
					$userGroupLink->loginID = $value;
					$userGroupLink->userGroupID = $userGroupID;

					try {
						$userGroupLink->save();
					} catch (Exception $e) {
						echo "<span class='error'>";
						echo $e->getMessage();
						echo "</span>";
					}
				}
			}



		} catch (Exception $e) {
			echo "<span class='error'>";
			echo $e->getMessage();
			echo "</span>";
		}

?>
