<?php

/*
**************************************************************************************************************************
** CORAL Authentication Module v. 1.0
**
** Copyright (c) 2011 University of Notre Dame
**
** This file is part of CORAL.
**
** CORAL is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
**
** CORAL is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License along with CORAL.  If not, see <http://www.gnu.org/licenses/>.
**
**************************************************************************************************************************
*/


include_once 'directory.php';


switch ($_GET['action']) {


	case 'getAdminUserUpdateForm':
		$existingUser = (isset($_GET['loginID'])) ? $_GET['loginID'] : NULL;
		$userID = ($existingUser) ? new NamedArguments(array('primaryKey' => $_GET['loginID'])) : NULL;
		$currentUser = new User($userID);
		$isAdmin = ($currentUser->isAdmin()) ? "checked" : "";
		$formTitle = ($existingUser) ? "Edit User" : "Add New User";
		$passwordLabel = ($existingUser) ? _("New Password") : _("Password");
		?>
		<div id='div_updateForm'>
			<div class='formTitle'>
				<h2><?php echo _($formTitle); ?></h2>
			</div>
			<span class='error' id='span_errors'></span>

			<input type='hidden' id='editLoginID' value='<?php echo $existingUser; ?>' />

			<div class="block-form">
				<p>
						<label for='textLoginID'><?php echo _("Login ID");?></label>
						<?php 
							//Historically this code did not allow users to edit loginIDs. I cannot think of why, but I'm not going to change it at present. An excellent future refactor idea!
							$disabled = ($existingUser) ? "disabled" : "";
							$passwordError = _("edit password or admin status");
							$descriptiveText = ($existingUser) ? "<p class='error'>{$passwordError}</p>" : "";
						?>
						<input type='text' id='textLoginID' value='<?php echo $existingUser; ?>' <?php echo $disabled; ?> />
						<?php echo $descriptiveText; ?> 
				</p>
				<p>
					<label for='password'><b><?php echo $passwordLabel; ?></b></label>
					
					<input type='password' id='password' value="" />
					</p>
					<p>
					<label for='passwordReenter'><?php echo _("Reenter Password");?></label>
					<input type='password' id='passwordReenter' value="" />
					</p>
					<p class="checkbox">
					<input type='checkbox' id='adminInd' value='Y' <?php echo $isAdmin; ?> />
					<label for='adminInd' class='formLabel'><b><?php echo _("Admin?");?></b></label>
				</p>
			</div>

		<p class="actions">
				<input type='submit' value='<?php echo _("submit");?>' onclick="submitUserForm();" id ='submitUser' class='submit-button primary' />
				<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog(); return false;" class='cancel-button secondary' />
			</p>
			
		</div>

		<script type="text/javascript" src="js/admin.js"></script>
		<?php

		break;

	default:
				if (empty($action))
          return;
       printf(_("Action %s not set up!"), $action);
       break;


}


?>


