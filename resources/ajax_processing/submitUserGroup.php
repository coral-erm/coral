<?php
		$userGroupID = $_POST['userGroupID'];
		$userGroupArguments = ($userGroupID != '') ? new NamedArguments(array('primaryKey' => $userGroupID)) : null;
		$userGroup = new UserGroup($userGroupArguments);

		$userGroup->groupName = $_POST['groupName'];
		$userGroup->emailAddress = $_POST['emailAddress'];

		try {
			$userGroup->save();
			$userGroupID=$userGroup->primaryKey;
			$submittedUsers = (isset($_POST['usersList'])) ? $_POST['usersList'] : [];

			//Remove all users not in the submitted Users Array.
			$userGroup->removeUsersNotInList($submittedUsers);
			//Get all the current Users and remove them from the submitted Users Array, leaving only new entries.
			$currentUsers = [];
			foreach($userGroup->getUsers() as $user){
				$currentUsers[] = $user->loginID;
			};
			$newUsers = array_diff($submittedUsers, $currentUsers);
			foreach ($newUsers as $loginID){
				if ($loginID){
					$userGroupLink = new UserGroupLink();
					$userGroupLink->loginID = $loginID;
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