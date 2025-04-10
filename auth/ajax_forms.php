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
		if (isset($_GET['loginID'])) $loginID = $_GET['loginID']; else $loginID = '';

		$eUser = new User(new NamedArguments(array('primaryKey' => $loginID)));

		if ($eUser->isAdmin()){
			$adminInd = 'checked';
		}else{
			$adminInd = '';
		}
		?>


		<div id='div_updateForm'>


		<div class='formTitle'>
			<h2><?php if ($loginID){ echo _("Edit User"); } else { echo _("Add New User"); } ?></h2>
		</div>


		<span class='error' id='span_errors'></span>

		<input type='hidden' id='editLoginID' value='<?php echo (int)$loginID; ?>' />

		<div class="block-form">
		
		<p>
				<label for='textLoginID'><?php echo _("Login ID");?></label>
				<?php if (!$loginID) { ?><input type='text' id='textLoginID' value=''/> <?php } else { echo $loginID; } ?>
				<?php if ($loginID) { ?><p class='error'><?php echo _("Enter password for changes only")?></p> <?php } ?>
	</p>
	<p>
				<label for='password'><b><?php if ($loginID) { echo _("New Password"); } else { echo _("Password"); }?></b></label>
				
				<input type='password' id='password' value="" />
				</p>
				<p>
				<label for='passwordReenter'><?php echo _("Reenter Password");?></label>
				<input type='password' id='passwordReenter' value="" />
				</p>
				<p class="checkbox">
				<input type='checkbox' id='adminInd' value='Y' <?php echo $adminInd; ?> />
				<label for='adminInd' class='formLabel'><b><?php echo _("Admin?");?></b></label>
				</p>
			</div>

		<p class="actions">
				<input type='submit' value='<?php echo _("submit");?>' id ='submitUser' class='submit-button primary' />
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


