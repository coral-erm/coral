<?php
	$alertEmailAddressID = $_GET['alertEmailAddressID'];

	if ($alertEmailAddressID){
		$instance = new AlertEmailAddress(new NamedArguments(array('primaryKey' => $alertEmailAddressID)));
	}else{
		$instance = new AlertEmailAddress();
	}
?>
		<form id='div_updateForm'>

		<input type='hidden' id='editAlertEmailAddressID' value='<?php echo $alertEmailAddressID; ?>'>

		<h2 class='headerText' id="alertEmailAddressLabel"><?php if ($alertEmailAddressID){ echo _("Edit Alert Email Address"); } else { echo _("Add Alert Email Address"); } ?></h2>

		<span class='error' id='span_errors'></span>

		<p>
			<input type='text' id='emailAddress' value='<?php echo $instance->emailAddress; ?>' aria-labelledby="alertEmailAddressLabel"/>
		</p>
			
		<div class='error' id='div_form_error'></div>
		
			<p class="actions">
				<input type='button' value='<?php echo _("submit");?>' onclick="window.parent.submitAdminAlertEmail()" id ='submitAddUpdate' class='submit-button primary'>
				<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog(); return false;" class='cancel-button secondary'>
			</p>


		</form>
		</div>

