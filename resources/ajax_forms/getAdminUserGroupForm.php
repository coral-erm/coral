<?php
	$userGroupID = (isset($_GET['userGroupID'])) ? $_GET['userGroupID'] : '';
	$userGroup = new UserGroup(new NamedArguments(array('primaryKey' => $userGroupID)));

	//get all users for output in drop down
	$allUserArray = array();
	$userObj = new User();
	$allUserArray = $userObj->allAsArray();

	//get users already set up for this user group in case it's an edit
	$ugUserArray = $userGroup->getUsers();
	$userGroupIDArray = [];
	foreach($ugUserArray as $user){$userGroupIDArray[] = $user->loginID;}
?>
<div id='div_userGroupForm'>
	<!-- Note that userGroupForm.js validation logic uses row/input/button classes -->
	<form id='userGroupForm'>
		<input type='hidden' name='userGroupID' id='userGroupID' value='<?php echo $userGroupID; ?>'>

		<h2 class='headerText'><?php if ($userGroupID){ echo _("Edit User Group"); } else { echo _("Add User Group"); } ?></h2>

		<p class='error' id='span_errors'></p>
		<h3 class="wide"><?php echo _("User Group");?></h3>
		
		<div class="form-grid">
			<label for='groupName'><b><?php echo _("Group Name:");?></b></label>
			<input type='text' id='groupName' name='groupName' value = '<?php echo $userGroup->groupName; ?>' class='changeInput' aria-describedby="span_error_groupName" />
			<p id='span_error_groupName' class='error'></p>

			<label for='emailAddress'><b><?php echo _("Email Addresses:");?></b></label>
			<input type='text' id='emailAddress' name='emailAddress' value = '<?php echo $userGroup->emailAddress; ?>' class='changeInput' aria-describedby="email-hint" />
			<p class="form-text indent" id="email-hint"><?php echo _("(use comma and a space between each email address)"); ?></p>

			<fieldset class="subgrid">
			<legend id="add-user-heading"><?php echo _("Add User");?></legend>
			<div class='newUserTR form-group'>
				<select id="loginSelect" class='changeSelect loginID' aria-labelledby="add-user-heading">
					<option value='' disabled selected></option>
					<?php
					foreach ($allUserArray as $ugUser){
						$disabled = (in_array($ugUser['loginID'], $userGroupIDArray)) ? "disabled" : "";
						$userObj = new User(new NamedArguments(array('primaryKey' => $ugUser['loginID'])));
						$ddDisplayName = $userObj->getDDDisplayName;
						echo "<option value='{$ugUser['loginID']}' {$disabled}>{$ddDisplayName}</option>";
					}
					?>
				</select>
				<span class="actions">
					<button class='addUser add-button btn link' title='<?php echo _("add user");?>' type='button'><?php echo _("Add");?></button>
				</span>
			</div>
			<p class='error' id='div_errorUser'></p>
			</fieldset>
		</div>
		<h3 class="wide"><?php echo _("Assigned Users");?></h3>
		<ul class='unstyled userTable wide form-grid'>
		<?php
				$haveUsers = true;
				if (is_array($ugUserArray) && count($ugUserArray) > 0) {
					$haveUsers = false;
					foreach ($ugUserArray as $ugUser){ ?>
						<li class='validUser newUser subgrid'>
							<input type="text" name="loginIDs[]" data-loginID="<?php echo $ugUser->loginID; ?>" aria-labelledby="assigned-user-heading" value="<?php echo $ugUser->getDDDisplayName; ?>" readonly />
							<button type="button" class="btn start remove">
								<img src='images/cross.gif' alt="<?php printf(_("remove %s from %s group"), $ddDisplayName, $userGroup->groupName);?>" title="<?php printf(_("remove %s from %s group"), $ddDisplayName, $userGroup->groupName);?>" />
							</button>
						</li>
					<?php
					}
				}
			?>
				<li class='newUser subgrid' id="newUserSkeleton" hidden>
					<input type="text" name="loginIDs[]" data-loginID="" id="newUserDisplayName" aria-labelledby="assigned-user-heading" value="" readonly />
					<button type="button" class="btn start remove">
						<img src='images/cross.gif' alt="<?php _('remove user from group') ?>" />
					</button>
				</li>
				<li id='noUsers' class='wide' <?php if (!$haveUsers) echo 'hidden'; ?>><i><?php printf(_('No users assigned to %s group'), $userGroup->groupName) ?></i></li>
			</ul>

			<p class="actions">
				<input type='submit' value='<?php echo _("update group");?>' name='submitUserGroupForm' id ='submitUserGroupForm' class='submit-button primary'>
				<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='cancel-button secondary'>
			</p>

	</form>
</div>

<script type="text/javascript" src="js/forms/userGroupForm.js?random=<?php echo rand(); ?>"></script>

