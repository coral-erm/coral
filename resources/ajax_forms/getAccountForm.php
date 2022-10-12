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
		<form id='accountForm'>
		<input type='hidden' name='editResourceID' id='editResourceID' value='<?php echo $resourceID; ?>'>
		<input type='hidden' name='editExternalLoginID' id='editExternalLoginID' value='<?php echo $externalLoginID; ?>'>


		<div class='formTitle' style='width:385px;'><span class='headerText' style='margin-left:7px;'><?php if ($externalLoginID){ echo _("Edit Account"); } else { echo _("Add Account"); } ?></span></div>

		<span class='smallDarkRedText' id='span_errors'></span>

		<table class="surroundBox" style="width:390px;">
		<tr>
		<td>

			<table class='noBorder' style='width:350px; margin:10px 15px;'>
				<tr>
				<td style='vertical-align:top;text-align:left;'><label for='externalLoginTypeID'><b><?php echo _("Login Type:");?></b></label></td>
				<td>
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
				</td>
				</tr>

				<tr>
				<td style='vertical-align:top;text-align:left;'><label for='loginURL'><b><?php echo _("URL:");?></b></label></td>
				<td>
				<input type='text' id='loginURL' name='loginURL' value = '<?php echo $externalLogin->loginURL; ?>' style='width:200px' class='changeInput' />
				</td>
				</tr>

				<tr>
				<td style='vertical-align:top;text-align:left;'><label for='emailAddress'><b><?php echo _("Registered Email:");?></b></label></td>
				<td>
				<input type='text' id='emailAddress' name='emailAddress' value = '<?php echo $externalLogin->emailAddress; ?>' style='width:200px' class='changeInput' />
				</td>
				</tr>

				<tr>
				<td style='vertical-align:top;text-align:left;'><label for='username'><b><?php echo _("Username:");?></b></label></td>
				<td>
				<input type='text' id='username' name='username' value = '<?php echo $externalLogin->username; ?>' style='width:200px' class='changeInput' />
				</td>
				</tr>

				<tr>
				<td style='vertical-align:top;text-align:left;'><label for='password'><b><?php echo _("Password:");?></b></label></td>
				<td>
				<input type='text' id='password' name='password' value = '<?php echo $externalLogin->password; ?>' style='width:200px' class='changeInput' />
				</td>
				</tr>

				<tr>
				<td style='vertical-align:top;text-align:left;'><label for='noteText'><b><?php echo _("Notes:");?></b></label></td>
				<td><textarea rows='3' id='noteText' name='noteText' style='width:200px'><?php echo $externalLogin->noteText; ?></textarea></td>
				</td>
				</tr>
			</table>

		</td>
		</tr>
		</table>

		<br />

		<table class='noBorderTable' style='width:125px;'>
			<tr>
				<td style='text-align:left'><input type='button' value='<?php echo _("submit");?>' name='submitExternalLoginForm' id ='submitExternalLoginForm' class='submit-button'></td>
				<td style='text-align:right'><input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='cancel-button'></td>
			</tr>
		</table>

		</form>
		</div>


		<script type="text/javascript" src="js/forms/externalLoginForm.js?random=<?php echo rand(); ?>"></script>

