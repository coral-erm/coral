<?php
	$resourceID = $_GET['resourceID'];
	if (isset($_GET['externalLoginID'])) $externalLoginID = $_GET['externalLoginID']; else $externalLoginID = '';
	$externalLogin = new ExternalLogin(new NamedArguments(array('primaryKey' => $externalLoginID)));

	//get all contact roles for output in drop down
	$externalLoginTypeArray = array();
	$externalLoginTypeObj = new ExternalLoginType();
	$externalLoginTypeArray = $externalLoginTypeObj->allAsArray();
?>
		<div id='div_accountForm'>
		<form id='accountForm' class="block-form">
		<input type='hidden' name='editResourceID' id='editResourceID' value='<?php echo $resourceID; ?>'>
		<input type='hidden' name='editExternalLoginID' id='editExternalLoginID' value='<?php echo $externalLoginID; ?>'>

		<h2 class='headerText'><?php if ($externalLoginID){ echo _("Edit Account"); } else { echo _("Add Account"); } ?></h2>
		
		<span class='error' id='span_errors'></span>
	
			<p>
				<label for='externalLoginTypeID'><?php echo _("Login Type:");?></label>
				<select name='externalLoginTypeID' id='externalLoginTypeID' class='changeSelect'>
				<?php
				foreach ($externalLoginTypeArray as $externalLoginType){
					if ($externalLoginType['externalLoginTypeID'] == $externalLogin->externalLoginTypeID){
						echo "<option value='" . $externalLoginType['externalLoginTypeID'] . "' selected>" . $externalLoginType['shortName'] . "</option>\n";
					}else{
						echo "<option value='" . $externalLoginType['externalLoginTypeID'] . "'>" . $externalLoginType['shortName'] . "</option>\n";
					}
				}
				?>
				</select>
			</p>

				<p>
					<label for='loginURL'><?php echo _("URL:");?></label>
					<input type='url' id='loginURL' name='loginURL' value = '<?php echo $externalLogin->loginURL; ?>' style='width:200px' class='changeInput' />
				</p>

				<p>
					<label for='emailAddress'><?php echo _("Registered Email:");?></label>
					<input type='text' id='emailAddress' name='emailAddress' value = '<?php echo $externalLogin->emailAddress; ?>' class='changeInput' />
				</p>

			<p>
				<label for='username'><?php echo _("Username:");?></label>
				<input type='text' id='username' name='username' value = '<?php echo $externalLogin->username; ?>' class='changeInput' />
			</p>

				<p>
				<label for='password'><?php echo _("Password:");?></label>
				<input type='text' id='password' name='password' value = '<?php echo $externalLogin->password; ?>' class='changeInput' />
				</p>

				<p>
					<label for='noteText'><b><?php echo _("Notes:");?></b></label>
					<textarea rows='3' id='noteText' name='noteText'><?php echo $externalLogin->noteText; ?></textarea>
				</p>

			<p class="actions">
				<input type='submit' value='<?php echo _("submit");?>' name='submitExternalLoginForm' id ='submitExternalLoginForm' class='submit-button primary'>
				<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='cancel-button secondary'>
			</p>

		</form>
		</div>


		<script type="text/javascript" src="js/forms/externalLoginForm.js?random=<?php echo rand(); ?>"></script>

