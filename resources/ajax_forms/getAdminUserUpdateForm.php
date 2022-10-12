<?php
	if (isset($_GET['loginID'])) $loginID = $_GET['loginID']; else $loginID = '';

	$user = new User(new NamedArguments(array('primaryKey' => $loginID)));

	//get all roles for output in drop down
	$privilegeArray = array();
	$privilegeObj = new Privilege();
	$privilegeArray = $privilegeObj->allAsArray();

	if ($user->accountTabIndicator == '1') {
		$accountTab = 'checked';
	}else{
		$accountTab = '';
	}
?>
		<div id='div_updateForm'>

		<input type='hidden' id='editLoginID' value='<?php echo $loginID; ?>'>

		<div class='formTitle' style='width:295px;'><span class='headerText' style='margin-left:7px;'><?php if ($loginID){ echo _("Edit User"); } else { echo _("Add New User"); } ?></span></div>

		<span class='smallDarkRedText' id='span_errors'></span>

		<table class="surroundBox" style="width:300px;">
		<tr>
		<td>

			<table class='noBorder' style='width:260px; margin:10px;'>


				<tr><td><label for='loginID'><b><?php echo _("Login ID");?></b></label</td><td><?php if (!$loginID) { ?><input type='text' id='loginID' value='<?php echo $loginID; ?>' style='width:150px;'/> <?php } else { echo $loginID; } ?></td></tr>
				<tr><td><label for='firstName'><b><?php echo _("First Name");?></b></label</td><td><input type='text' id='firstName' value="<?php echo $user->firstName; ?>" style='width:150px;'/></td></tr>
				<tr><td><label for='lastName'><b><?php echo _("Last Name");?></b></label</td><td><input type='text' id='lastName' value="<?php echo $user->lastName; ?>" style='width:150px;'/></td></tr>
				<tr><td><label for='emailAddress'><b><?php echo _("Email Address");?></b></label</td><td><input type='text' id='emailAddress' value="<?php echo $user->emailAddress; ?>" style='width:150px;'/></td></tr>
				<tr><td><label for='privilegeID'><b><?php echo _("Privilege");?></b></label</td>
				<td>
				<select id='privilegeID' style='width:155px'>
				<?php

				foreach ($privilegeArray as $privilege){
					if ($privilege['privilegeID'] == $user->privilegeID){
						echo "<option value='" . $privilege['privilegeID'] . "' selected>" . $privilege['shortName'] . "</option>\n";
					}else{
						echo "<option value='" . $privilege['privilegeID'] . "'>" . $privilege['shortName'] . "</option>\n";
					}
				}

				?>
				</select>
				</td>
				</tr>

				<tr><td><label for='accountTab'><b><?php echo _("View Accounts");?></b></label</td><td><input type='checkbox' id='accountTab' value='1' <?php echo $accountTab; ?> /></td></tr>


			</table>

		</td>
		</tr>
		</table>

		<br />
		<table class='noBorderTable' style='width:125px;'>
			<tr>
				<td style='text-align:left'><input type='button' value='<?php echo _("submit");?>' id ='submitAddUpdate' class='submit-button'></td>
				<td style='text-align:right'><input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog(); return false;" class='cancel-button'></td>
			</tr>
		</table>


		</form>
		</div>

		<script type="text/javascript">
		   //attach enter key event to new input and call add data when hit
		   $('#loginID').keyup(function(e) {
				   if(e.keyCode == 13) {
					   window.parent.submitUserData();
				   }
		});

		   $('#firstName').keyup(function(e) {
				   if(e.keyCode == 13) {
					   window.parent.submitUserData();
				   }
		});

		   $('#lastName').keyup(function(e) {
				   if(e.keyCode == 13) {
					   window.parent.submitUserData();
				   }
		});

		   $('#emailAddress').keyup(function(e) {
				   if(e.keyCode == 13) {
					   window.parent.submitUserData();
				   }
		});

		   $('#privilegeID').keyup(function(e) {
				   if(e.keyCode == 13) {
					   window.parent.submitUserData();
				   }
		});

		   $('#submitAddUpdate').click(function () {
			       window.parent.submitUserData();
		   });


	</script>

