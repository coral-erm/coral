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

		<div class='formTitle'><h2 class='headerText'><?php if ($loginID){ echo _("Edit User"); } else { echo _("Add New User"); } ?></h2></div>

		<span class='error' id='span_errors'></span>

		<div class="form-grid">
				<label for='loginID'><b><?php echo _("Login ID");?></b></label>
				<?php if (!$loginID) { ?><input type='text' id='loginID' value='<?php echo $loginID; ?>' /> <?php } else { echo $loginID; } ?>

				<label for='firstName'><b><?php echo _("First Name");?></b></label>
				<input type='text' id='firstName' value="<?php echo $user->firstName; ?>" />
				
				<label for='lastName'><b><?php echo _("Last Name");?></b></label>
				<input type='text' id='lastName' value="<?php echo $user->lastName; ?>" />
				
				<label for='emailAddress'><b><?php echo _("Email Address");?></b></label>
				<input type='text' id='emailAddress' value="<?php echo $user->emailAddress; ?>" />

				<label for='privilegeID'><b><?php echo _("Privilege");?></b></label>
				<select id='privilegeID'>
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

			<label for='accountTab'><b><?php echo _("View Accounts");?></b></label>
			<span class="form-group">
				<input type='checkbox' id='accountTab' value='1' <?php echo $accountTab; ?> />
			</span>
		
		</div>		

		<p class='actions'>
			<input type='submit' value='<?php echo _("submit");?>' id ='submitAddUpdate' class='submit-button primary'>
			<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog(); return false;" class='cancel-button secondary'>
		</p>


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

