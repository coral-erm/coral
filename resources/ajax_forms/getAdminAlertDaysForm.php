<?php
	$alertDaysInAdvanceID = $_GET['alertDaysInAdvanceID'];

	if ($alertDaysInAdvanceID){
		$instance = new AlertDaysInAdvance(new NamedArguments(array('primaryKey' => $alertDaysInAdvanceID)));
	}else{
		$instance = new AlertDaysInAdvance();
	}
?>
		<form id='div_updateForm' class="block-form">

		<input type='hidden' id='editAlertDaysInAdvanceID' value='<?php echo $alertDaysInAdvanceID; ?>'>

		<h2 class='headerText' id="daysInAdvanceLabel"><?php if ($alertDaysInAdvanceID){ echo _("Edit Alert Days In Advance"); } else { echo _("Add Alert Days In Advance"); } ?></h2>

		<span class='error' id='span_errors'></span>
		<p>
			<input type='text' id='daysInAdvanceNumber' value='<?php echo $instance->daysInAdvanceNumber; ?>' aria-labelledby="daysInAdvanceLabel"/>
		</p>
		<div class='error' id='div_form_error'></div>

			<p class="actions">
				<input type='submit' value='<?php echo _("submit");?>' onclick="window.parent.submitAdminAlertDays()" id ='submitAddUpdate' class='submit-button primary'>
				<input type='button' value='<?php echo _("cancel");?>' onclick="myCloseDialog()" class='cancel-button secondary'>
			</p>

		</form>
		</div>

